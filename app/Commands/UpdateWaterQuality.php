<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\SensorModel;

class UpdateWaterQuality extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'db:update-water-quality';
    protected $description = 'Update water quality status untuk semua data sensor yang sudah ada';
    protected $usage = 'db:update-water-quality';

    public function run(array $params)
    {
        $sensorModel = new SensorModel();
        
        CLI::write('Memulai update kualitas air untuk semua data sensor...', 'yellow');
        
        // Get all sensors
        $sensors = $sensorModel->findAll();
        $totalSensors = count($sensors);
        
        CLI::write("Total data sensor: {$totalSensors}", 'cyan');
        
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
            
            // Show progress every 100 records
            if ($updated % 100 === 0) {
                CLI::write("Progress: {$updated}/{$totalSensors} data diupdate...", 'green');
            }
        }
        
        CLI::newLine();
        CLI::write('=== Update Selesai ===', 'green');
        CLI::write("Total data diupdate: {$updated}", 'cyan');
        CLI::write("Kualitas Bagus: {$bagus}", 'green');
        CLI::write("Kualitas Kurang Bagus: {$kurangBagus}", 'red');
        CLI::newLine();
    }
}
