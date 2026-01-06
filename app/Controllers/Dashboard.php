<?php

namespace App\Controllers;

use App\Models\WaterParameterModel;

class Dashboard extends BaseController
{
    protected $waterParameterModel;

    public function __construct()
    {
        $this->waterParameterModel = new WaterParameterModel();
    }

    public function index(): string
    {
        $sensorModel = new \App\Models\SensorModel();
        $latestSensor = $sensorModel->orderBy('created_at', 'DESC')->first();
        
        // Auto-update water_quality if null
        if ($latestSensor && !isset($latestSensor['water_quality'])) {
            $ph = floatval(trim($latestSensor['ph'] ?? '0', '"'));
            $tds = floatval(trim($latestSensor['tds'] ?? '0', '"'));
            $turb = floatval(trim($latestSensor['turb'] ?? '0', '"'));
            
            if ($ph > 0 && $tds > 0 && $turb > 0) {
                $waterQuality = 'Bagus';
                if ($ph < 6.5 || $ph > 8.0) {
                    $waterQuality = 'Kurang Bagus';
                } elseif ($tds > 1000) {
                    $waterQuality = 'Kurang Bagus';
                } elseif ($turb < 5 || $turb > 50) {
                    $waterQuality = 'Kurang Bagus';
                }
                
                $sensorModel->update($latestSensor['id'], ['water_quality' => $waterQuality]);
                $latestSensor['water_quality'] = $waterQuality;
            }
        }
        
        $data = [
            'title' => 'Sistem Monitoring Air Kolam - Dashboard',
            'parameters' => $this->waterParameterModel->getCurrentParameters(),
            'setpoints' => $this->waterParameterModel->getSetpoints(),
            'fishTypes' => $this->waterParameterModel->getFishTypes(),
            'chartData' => $this->waterParameterModel->getChartData(),
            'notifications' => $this->waterParameterModel->getNotifications(),
            'latestSensor' => $latestSensor
        ];

        return view('dashboard/index', $data);
    }


    public function logs()
    {
        $logModel = new \App\Models\LogModel();
        
        // Get pagination parameters
        $perPage = 25; // Default items per page
        $page = $this->request->getVar('page') ?? 1;
        
        $data = [
            'title' => 'Sistem Monitoring Air Kolam - Logs',
            'logs' => $logModel->orderBy('created_at', 'DESC')->paginate($perPage),
            'pager' => $logModel->pager
        ];

        return view('dashboard/logs', $data);
    }

    public function history()
    {
        $sensorModel = new \App\Models\SensorModel();
        
        // Auto-update water_quality for records that don't have it
        $nullQualitySensors = $sensorModel->where('water_quality IS NULL')->findAll(100);
        if (!empty($nullQualitySensors)) {
            foreach ($nullQualitySensors as $sensor) {
                $ph = floatval($sensor['ph'] ?? 0);
                $tds = floatval($sensor['tds'] ?? 0);
                $turb = floatval($sensor['turb'] ?? 0);
                
                if ($ph > 0 && $tds > 0 && $turb > 0) {
                    $waterQuality = 'Bagus';
                    if ($ph < 6.5 || $ph > 8.0) {
                        $waterQuality = 'Kurang Bagus';
                    } elseif ($tds > 1000) {
                        $waterQuality = 'Kurang Bagus';
                    } elseif ($turb < 5 || $turb > 50) {
                        $waterQuality = 'Kurang Bagus';
                    }
                    
                    $sensorModel->update($sensor['id'], ['water_quality' => $waterQuality]);
                }
            }
        }
        
        $perPage = 25;
        
        $data = [
            'title' => 'Sistem Monitoring Air Kolam - Histori Data',
            'sensors' => $sensorModel->orderBy('created_at', 'DESC')->paginate($perPage),
            'pager' => $sensorModel->pager
        ];

        return view('dashboard/history', $data);
    }

    public function exportHistory()
    {
        $sensorModel = new \App\Models\SensorModel();
        
        // Get datetime range from request (format: Y-m-d\TH:i from datetime-local)
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        
        // Build query
        $builder = $sensorModel->orderBy('created_at', 'DESC');
        
        if ($startDate && $endDate) {
            // Convert datetime-local format (Y-m-d\TH:i) to MySQL datetime format (Y-m-d H:i:s)
            $startDateTime = str_replace('T', ' ', $startDate) . ':00';
            $endDateTime = str_replace('T', ' ', $endDate) . ':59';
            
            $builder->where('created_at >=', $startDateTime)
                    ->where('created_at <=', $endDateTime);
        }
        
        $sensors = $builder->findAll();
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="sensor_data_' . date('Y-m-d_His') . '.csv"');
        
        // Create output stream
        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Add CSV headers
        fputcsv($output, ['ID', 'pH', 'TDS (ppm)', 'Kekeruhan (NTU)', 'Tank', 'Chamber', 'Kualitas Air', 'Waktu']);
        
        // Add data rows
        foreach ($sensors as $sensor) {
            $ph = trim($sensor['ph'] ?? 'N/A', '"');
            $tds = trim($sensor['tds'] ?? 'N/A', '"');
            $turb = trim($sensor['turb'] ?? 'N/A', '"');
            $tank = trim($sensor['tank'] ?? 'N/A', '"');
            $chamber = trim($sensor['chamber'] ?? 'N/A', '"');
            $waterQuality = $sensor['water_quality'] ?? 'N/A';
            
            fputcsv($output, [
                $sensor['id'],
                $ph,
                $tds,
                $turb,
                $tank,
                $chamber,
                $waterQuality,
                date('d-m-Y H:i:s', strtotime($sensor['created_at']))
            ]);
        }
        
        fclose($output);
        exit;
    }

    public function updateAllWaterQuality()
    {
        $sensorModel = new \App\Models\SensorModel();
        
        // Get all sensors
        $sensors = $sensorModel->findAll();
        $totalSensors = count($sensors);
        
        $updated = 0;
        $bagus = 0;
        $kurangBagus = 0;
        
        foreach ($sensors as $sensor) {
            // Calculate water quality
            $ph = floatval($sensor['ph'] ?? 0);
            $tds = floatval($sensor['tds'] ?? 0);
            $turb = floatval($sensor['turb'] ?? 0);
            
            // Determine water quality
            $waterQuality = 'Bagus';
            
            if ($ph < 6.5 || $ph > 8.0) {
                $waterQuality = 'Kurang Bagus';
            } elseif ($tds > 1000) {
                $waterQuality = 'Kurang Bagus';
            } elseif ($turb < 5 || $turb > 50) {
                $waterQuality = 'Kurang Bagus';
            }
            
            // Update database
            $sensorModel->update($sensor['id'], [
                'water_quality' => $waterQuality
            ]);
            
            $updated++;
            
            if ($waterQuality === 'Bagus') {
                $bagus++;
            } else {
                $kurangBagus++;
            }
        }
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Update kualitas air selesai',
            'data' => [
                'total' => $totalSensors,
                'updated' => $updated,
                'bagus' => $bagus,
                'kurang_bagus' => $kurangBagus
            ]
        ]);
    }

    public function grafik()
    {
        $range = $this->request->getGet('range') ?? 'latest';
        $data = [
            'title' => 'Sistem Monitoring Air Kolam - Grafik',
            'chartData' => $this->waterParameterModel->getChartData($range),
            'currentRange' => $range
        ];

        return view('dashboard/grafik', $data);
    }

    public function notifikasi()
    {
        $data = [
            'title' => 'Sistem Monitoring Air Kolam - Notifikasi',
            'notifications' => $this->waterParameterModel->getNotifications()
        ];

        return view('dashboard/notifikasi', $data);
    }

    public function realtime()
    {
        $range = $this->request->getGet('range') ?? 'latest';
        $sensorModel = new \App\Models\SensorModel();
        $latestSensor = $sensorModel->orderBy('created_at', 'DESC')->first();
        
        return $this->response->setJSON([
            'latest' => $latestSensor,
            'parameters' => $this->waterParameterModel->getCurrentParameters(),
            'chart' => $this->waterParameterModel->getChartData($range)
        ]);
    }
}

