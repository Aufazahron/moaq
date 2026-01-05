<?php

namespace App\Controllers\Api;

use App\Models\LogModel;
use App\Models\SensorModel;
use App\Models\WaterParameterModel;
use CodeIgniter\RESTful\ResourceController;

class Sensor extends ResourceController
{
    protected $format = 'json';

    /**
     * Endpoint untuk menerima data dari sensor ESP32
     * Format JSON yang diterima:
     * {
     *   "sensors": {
     *     "ph": 7.2,
     *     "tds": 450,
     *     "turb": 12.1,
     *     "temp": 28.5,          // opsional
     *     "do": 6.8,             // dissolved oxygen, opsional
     *     "tank": "80%",
     *     "chamber": "0%"
     *   },
     *   "actuators": {           // opsional
     *     "sol_in": 0,
     *     "sol_ch": 0,
     *     "sol_drain": 1,
     *     "pump_in": 1,
     *     "pump_out": 0,
     *     "mixer": 1,
     *     "aerator": 0,          // opsional
     *     "feeder": 0            // opsional
     *   },
     *   "status": "Monitoring"   // opsional
     * }
     */
    public function receive()
    {
        // 1. Ambil JSON dari Input
        $dataInput = $this->request->getJSON(true);

        if (!$dataInput) {
            return $this->failValidationError("Data kosong atau format JSON salah");
        }

        // Validasi minimal harus ada data sensors
        if (!isset($dataInput['sensors'])) {
            return $this->failValidationError("Data 'sensors' tidak ditemukan dalam payload");
        }

        try {
            // Initialize Models
            $logModel    = new LogModel();
            $sensorModel = new SensorModel();
            $time        = date('Y-m-d H:i:s');

            // 2. Simpan Full JSON ke Log untuk keperluan debugging
            $logModel->insert([
                'payload'    => json_encode($dataInput),
                'created_at' => $time
            ]);

            // 3. Simpan Data Sensor ke Table Sensors
            $sensors = $dataInput['sensors'];
            $sensorData = [
                'ph'         => $sensors['ph'] ?? null,
                'tds'        => $sensors['tds'] ?? null,
                'turb'       => $sensors['turb'] ?? null,
                'tank'       => $sensors['tank'] ?? null,
                'chamber'    => $sensors['Chamber'] ?? $sensors['chamber'] ?? null,
                'created_at' => $time,
            ];
            $sensorModel->insert($sensorData);

            // 4. Simpan ke water_parameters jika ada data suhu/DO
            if (isset($sensors['temp']) || isset($sensors['do'])) {
                $waterModel = new WaterParameterModel();
                $waterData = [
                    'temperature'       => $sensors['temp'] ?? $sensors['temperature'] ?? null,
                    'ph'                => $sensors['ph'] ?? null,
                    'dissolved_oxygen'  => $sensors['do'] ?? $sensors['dissolved_oxygen'] ?? null,
                    'turbidity'         => $sensors['turb'] ?? $sensors['turbidity'] ?? null,
                    'tds'               => $sensors['tds'] ?? null,
                    'timestamp'         => $time,
                ];
                $waterModel->insert($waterData);

                // 5. Cek apakah ada parameter yang keluar dari set-point
                $alerts = $waterModel->checkParameterAlerts($waterData);
                if (!empty($alerts)) {
                    // Simpan notifikasi jika ada peringatan
                    $db = \Config\Database::connect();
                    foreach ($alerts as $alert) {
                        $db->table('notifications')->insert([
                            'type'       => 'warning',
                            'message'    => $alert,
                            'is_read'    => 0,
                            'created_at' => $time
                        ]);
                    }
                }
            }

            // 6. Update status actuators jika ada
            if (isset($dataInput['actuators'])) {
                $db = \Config\Database::connect();
                $actuators = $dataInput['actuators'];
                
                // Insert actuator status
                $db->table('actuators')->insert([
                    'sol_in'     => $actuators['sol_in'] ?? 0,
                    'sol_ch'     => $actuators['sol_ch'] ?? 0,
                    'sol_drain'  => $actuators['sol_drain'] ?? 0,
                    'pump_in'    => $actuators['pump_in'] ?? 0,
                    'pump_out'   => $actuators['pump_out'] ?? 0,
                    'mixer'      => $actuators['mixer'] ?? 0,
                    'aerator'    => $actuators['aerator'] ?? 0,
                    'feeder'     => $actuators['feeder'] ?? 0,
                    'created_at' => $time
                ]);

                // Update device_status table
                foreach ($actuators as $device => $status) {
                    $db->table('device_status')
                       ->set('status', $status ? 'on' : 'off')
                       ->set('last_updated', $time)
                       ->where('device_name', $device)
                       ->update();
                }
            }

            return $this->respondCreated([
                'status'  => 'success',
                'message' => 'Data berhasil disimpan ke database',
                'data'    => [
                    'sensors'   => $sensorData,
                    'timestamp' => $time
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error saving sensor data: ' . $e->getMessage());
            return $this->failServerError('Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint untuk mendapatkan data sensor terbaru
     * GET /api/sensor/latest
     */
    public function latest()
    {
        try {
            $sensorModel = new SensorModel();
            $latestData = $sensorModel->orderBy('created_at', 'DESC')->first();

            if (!$latestData) {
                return $this->failNotFound('Belum ada data sensor');
            }

            return $this->respond([
                'status' => 'success',
                'data'   => $latestData
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Gagal mengambil data: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint untuk mengontrol actuator
     * POST /api/sensor/control
     * Body: { "device": "aerator", "action": "on" }
     */
    public function control()
    {
        $data = $this->request->getJSON(true);

        if (!isset($data['device']) || !isset($data['action'])) {
            return $this->failValidationError('Field "device" dan "action" harus diisi');
        }

        $device = $data['device'];
        $action = strtolower($data['action']);

        if (!in_array($action, ['on', 'off'])) {
            return $this->failValidationError('Action harus "on" atau "off"');
        }

        try {
            $db = \Config\Database::connect();
            
            // Update device status
            $db->table('device_status')
               ->set('status', $action)
               ->set('last_updated', date('Y-m-d H:i:s'))
               ->where('device_name', $device)
               ->update();

            // Di sini bisa ditambahkan publish ke MQTT broker
            // untuk mengirim perintah ke perangkat fisik
            
            return $this->respond([
                'status'  => 'success',
                'message' => "Perintah $action untuk $device berhasil dikirim",
                'data'    => [
                    'device' => $device,
                    'action' => $action
                ]
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Gagal mengirim perintah: ' . $e->getMessage());
        }
    }
}
