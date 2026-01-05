<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\MqttHandler;

/**
 * Command untuk menjalankan MQTT Subscriber
 * 
 * Mendengarkan data dari sensor via MQTT broker
 * dan menyimpannya ke database
 * 
 * Usage:
 *   php spark mqtt:subscribe
 */
class MqttSubscribe extends BaseCommand
{
    protected $group = 'MQTT';
    protected $name = 'mqtt:subscribe';
    protected $description = 'Subscribe to MQTT broker and listen for sensor data';
    protected $usage = 'mqtt:subscribe [options]';
    protected $arguments = [];
    protected $options = [
        '--broker' => 'MQTT broker address (default: localhost)',
        '--port' => 'MQTT broker port (default: 1883)',
    ];

    public function run(array $params)
    {
        CLI::write('========================================', 'green');
        CLI::write('  MQTT Subscriber - Monitoring Kolam   ', 'green');
        CLI::write('========================================', 'green');
        CLI::newLine();
        
        // Banner
        CLI::write('Starting MQTT subscriber...', 'yellow');
        CLI::write('Press Ctrl+C to stop', 'light_gray');
        CLI::newLine();
        
        try {
            // Initialize MQTT Handler
            $mqttHandler = new MqttHandler();
            
            // Connect to broker
            CLI::write('Connecting to MQTT broker...', 'yellow');
            
            if (!$mqttHandler->connect()) {
                CLI::error('Failed to connect to MQTT broker');
                CLI::newLine();
                CLI::write('Troubleshooting:', 'yellow');
                CLI::write('  1. Check if MQTT broker is running', 'white');
                CLI::write('  2. Verify broker address and port in .env', 'white');
                CLI::write('  3. Check firewall settings', 'white');
                CLI::newLine();
                return;
            }
            
            CLI::write('✓ Connected successfully!', 'green');
            CLI::newLine();
            
            // Subscribe to sensor data topic
            CLI::write('Subscribing to sensor data...', 'yellow');
            
            // Start listening (blocking call)
            $mqttHandler->subscribeSensorData();
            
        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
            CLI::newLine();
            CLI::write('Stack trace:', 'yellow');
            CLI::write($e->getTraceAsString(), 'light_gray');
            CLI::newLine();
        }
    }
}
