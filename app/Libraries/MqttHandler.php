<?php

namespace App\Libraries;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Models\LogModel;
use App\Models\SensorModel;
use App\Models\WaterParameterModel;

/**
 * MQTT Handler untuk Sistem Monitor Air Kolam
 * 
 * Class ini menangani:
 * - Subscribe ke topic MQTT dari sensor
 * - Parse dan simpan data ke database
 * - Auto-alert jika parameter abnormal
 * - Publish perintah kontrol ke actuator
 */
class MqttHandler
{
    private $mqtt;
    private $broker;
    private $port;
    private $clientId;
    private $username;
    private $password;
    
    // MQTT Topics
    private $topicSensorData = 'kolam/sensors/data';
    private $topicControl = 'kolam/control';
    private $topicStatus = 'kolam/status';
    
    // Models
    private $logModel;
    private $sensorModel;
    private $waterModel;
    
    public function __construct()
    {
        // Load konfigurasi dari .env atau hardcoded
        $this->broker = getenv('MQTT_BROKER') ?: 'localhost';
        $this->port = (int)(getenv('MQTT_PORT') ?: 1883);
        $this->clientId = getenv('MQTT_CLIENT_ID') ?: 'monitoring_kolam_' . uniqid();
        $this->username = getenv('MQTT_USERNAME') ?: null;
        $this->password = getenv('MQTT_PASSWORD') ?: null;
        
        // Initialize models
        $this->logModel = new LogModel();
        $this->sensorModel = new SensorModel();
        $this->waterModel = new WaterParameterModel();
        
        log_message('info', "MQTT Handler initialized - Broker: {$this->broker}:{$this->port}");
    }
    
    /**
     * Koneksi ke MQTT Broker
     */
    public function connect()
    {
        try {
            $connectionSettings = new ConnectionSettings();
            
            if ($this->username) {
                $connectionSettings->setUsername($this->username);
            }
            
            if ($this->password) {
                $connectionSettings->setPassword($this->password);
            }
            
            // Set keep alive dan timeouts
            $connectionSettings->setKeepAliveInterval(60);
            $connectionSettings->setConnectTimeout(10);
            
            $this->mqtt = new MqttClient($this->broker, $this->port, $this->clientId);
            $this->mqtt->connect($connectionSettings, true);
            
            log_message('info', "✓ Connected to MQTT Broker: {$this->broker}:{$this->port}");
            echo "✓ Connected to MQTT Broker: {$this->broker}:{$this->port}\n";
            
            return true;
            
        } catch (\Exception $e) {
            log_message('error', "MQTT Connection Error: " . $e->getMessage());
            echo "✗ MQTT Connection Error: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Subscribe ke topic sensor data
     */
    public function subscribeSensorData()
    {
        if (!$this->mqtt) {
            echo "✗ MQTT not connected. Please connect first.\n";
            return false;
        }
        
        try {
            echo "📡 Subscribing to topic: {$this->topicSensorData}\n";
            
            $this->mqtt->subscribe(
                $this->topicSensorData,
                function ($topic, $message) {
                    $this->onSensorDataReceived($topic, $message);
                },
                0 // QoS 0
            );
            
            log_message('info', "✓ Subscribed to: {$this->topicSensorData}");
            echo "✓ Subscribed to: {$this->topicSensorData}\n";
            echo "⏳ Waiting for messages...\n\n";
            
            // Loop untuk terus mendengarkan
            $this->mqtt->loop(true);
            
            return true;
            
        } catch (\Exception $e) {
            log_message('error', "MQTT Subscribe Error: " . $e->getMessage());
            echo "✗ Subscribe Error: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Callback ketika menerima data sensor dari MQTT
     */
    private function onSensorDataReceived($topic, $message)
    {
        $timestamp = date('Y-m-d H:i:s');
        
        echo "\n========================================\n";
        echo "📥 Message received at $timestamp\n";
        echo "Topic: $topic\n";
        echo "Message:\n$message\n";
        echo "========================================\n";
        
        try {
            // Parse JSON
            $data = json_decode($message, true);
            
            if (!$data) {
                log_message('error', "Invalid JSON received: $message");
                echo "✗ Invalid JSON format\n";
                return;
            }
            
            // 1. Simpan ke log table (raw JSON)
            $this->logModel->insert([
                'payload' => $message,
                'created_at' => $timestamp
            ]);
            echo "✓ Saved to logs table\n";
            
            // 2. Simpan data sensor
            if (isset($data['sensors'])) {
                $sensors = $data['sensors'];
                
                $sensorData = [
                    'ph' => $sensors['ph'] ?? null,
                    'tds' => $sensors['tds'] ?? null,
                    'turb' => $sensors['turb'] ?? null,
                    'tank' => $sensors['tank'] ?? null,
                    'chamber' => $sensors['Chamber'] ?? $sensors['chamber'] ?? null,
                    'created_at' => $timestamp,
                ];
                
                $this->sensorModel->insert($sensorData);
                echo "✓ Saved sensor data: pH={$sensorData['ph']}, TDS={$sensorData['tds']}, Turb={$sensorData['turb']}\n";
                
                // 3. Simpan ke water_parameters jika ada data lengkap
                if (isset($sensors['temp']) || isset($sensors['do'])) {
                    $waterData = [
                        'temperature' => $sensors['temp'] ?? $sensors['temperature'] ?? null,
                        'ph' => $sensors['ph'] ?? null,
                        'dissolved_oxygen' => $sensors['do'] ?? $sensors['dissolved_oxygen'] ?? null,
                        'turbidity' => $sensors['turb'] ?? $sensors['turbidity'] ?? null,
                        'tds' => $sensors['tds'] ?? null,
                        'timestamp' => $timestamp,
                    ];
                    
                    $this->waterModel->insert($waterData);
                    echo "✓ Saved water parameters\n";
                    
                    // 4. Cek alert
                    $alerts = $this->waterModel->checkParameterAlerts($waterData);
                    if (!empty($alerts)) {
                        echo "⚠️  ALERTS DETECTED:\n";
                        foreach ($alerts as $alert) {
                            echo "   - $alert\n";
                            
                            // Simpan ke notifications
                            $db = \Config\Database::connect();
                            $db->table('notifications')->insert([
                                'type' => 'warning',
                                'message' => $alert,
                                'is_read' => 0,
                                'created_at' => $timestamp
                            ]);
                        }
                    } else {
                        echo "✓ All parameters normal\n";
                    }
                }
            }
            
            // 5. Update actuator status jika ada
            if (isset($data['actuators'])) {
                $db = \Config\Database::connect();
                $actuators = $data['actuators'];
                
                // Insert actuator log
                $db->table('actuators')->insert([
                    'sol_in' => $actuators['sol_in'] ?? 0,
                    'sol_ch' => $actuators['sol_ch'] ?? 0,
                    'sol_drain' => $actuators['sol_drain'] ?? 0,
                    'pump_in' => $actuators['pump_in'] ?? 0,
                    'pump_out' => $actuators['pump_out'] ?? 0,
                    'mixer' => $actuators['mixer'] ?? 0,
                    'aerator' => $actuators['aerator'] ?? 0,
                    'feeder' => $actuators['feeder'] ?? 0,
                    'created_at' => $timestamp
                ]);
                
                // Update device status
                foreach ($actuators as $device => $status) {
                    $db->table('device_status')
                       ->set('status', $status ? 'on' : 'off')
                       ->set('last_updated', $timestamp)
                       ->where('device_name', $device)
                       ->update();
                }
                
                echo "✓ Updated actuator status\n";
            }
            
            echo "✅ Processing complete!\n";
            
        } catch (\Exception $e) {
            log_message('error', "Error processing MQTT message: " . $e->getMessage());
            echo "✗ Error: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Publish perintah kontrol ke actuator
     */
    public function publishControl($device, $action)
    {
        if (!$this->mqtt) {
            log_message('error', "MQTT not connected");
            return false;
        }
        
        try {
            $command = json_encode([
                'device' => $device,
                'action' => $action,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            $this->mqtt->publish($this->topicControl, $command, 0);
            
            log_message('info', "Published control: $command");
            echo "✓ Control published: $device → $action\n";
            
            return true;
            
        } catch (\Exception $e) {
            log_message('error', "MQTT Publish Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Disconnect dari MQTT broker
     */
    public function disconnect()
    {
        if ($this->mqtt) {
            try {
                $this->mqtt->disconnect();
                log_message('info', "Disconnected from MQTT broker");
                echo "\n✓ Disconnected from MQTT broker\n";
            } catch (\Exception $e) {
                log_message('error', "MQTT Disconnect Error: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->disconnect();
    }
}
