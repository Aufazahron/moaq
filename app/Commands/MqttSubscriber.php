<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\MqttHandler;

/**
 * MQTT Subscriber Command
 * Menjalankan service untuk subscribe dan listen data sensor dari MQTT broker
 * 
 * Usage: php spark mqtt:subscribe
 */
class MqttSubscriber extends BaseCommand
{
    /**
     * Command group
     *
     * @var string
     */
    protected $group = 'MQTT';

    /**
     * Command name
     *
     * @var string
     */
    protected $name = 'mqtt:subscribe';

    /**
     * Command description
     *
     * @var string
     */
    protected $description = 'Menjalankan MQTT subscriber untuk menerima data sensor dari broker';

    /**
     * Command usage
     *
     * @var string
     */
    protected $usage = 'mqtt:subscribe';

    /**
     * Run MQTT Subscriber
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('========================================', 'green');
        CLI::write('  Sistem Monitoring Air Kolam', 'green');
        CLI::write('  MQTT Subscriber Service', 'green');
        CLI::write('========================================', 'green');
        CLI::newLine();

        CLI::write('Starting MQTT Subscriber...', 'yellow');
        CLI::newLine();

        try {
            // Inisialisasi MQTT Handler
            $mqtt = new MqttHandler();
            
            CLI::write('Connecting to MQTT Broker...', 'yellow');
            
            if ($mqtt->connect()) {
                CLI::write('✓ Connected to MQTT Broker successfully!', 'green');
                CLI::newLine();
                CLI::write('Listening for sensor data...', 'cyan');
                CLI::write('Press Ctrl+C to stop', 'light_gray');
                CLI::newLine();
                
                // Mulai subscribe dan listen (infinite loop)
                $mqtt->subscribeSensorData();
                
            } else {
                CLI::error('✗ Failed to connect to MQTT Broker');
                CLI::newLine();
                CLI::write('Please check:', 'yellow');
                CLI::write('  - MQTT Broker is running', 'light_gray');
                CLI::write('  - Broker address and port in .env file', 'light_gray');
                CLI::write('  - Username and password (if required)', 'light_gray');
                return;
            }

        } catch (\Exception $e) {
            CLI::error('✗ Error: ' . $e->getMessage());
            CLI::newLine();
            CLI::write('Stack trace:', 'yellow');
            CLI::write($e->getTraceAsString(), 'light_gray');
        }
    }
}
