<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class MqttListener extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Sensors';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'mqtt:listen';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Listens for MQTT sensor data and saves to database.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'mqtt:listen';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $server   = 'localhost';
        $port     = 1883;
        $clientId = 'moaq-sensor-listener';
        $topic    = 'sensors/#';

        $mqtt = new MqttClient($server, $port, $clientId);
        
        $settings = (new ConnectionSettings)
            ->setKeepAliveInterval(60)
            ->setLastWillTopic('sensors/status')
            ->setLastWillMessage('Listener Disconnected')
            ->setLastWillQualityOfService(1);

        try {
            $mqtt->connect($settings, true);
            CLI::write("Connected to MQTT broker at $server:$port", 'green');
            CLI::write("Subscribing to topic: $topic", 'yellow');

            $mqtt->subscribe($topic, function ($topic, $message) {
                CLI::write("Received message on topic: $topic", 'cyan');
                $this->processMessage($message);
            }, 0);

            $mqtt->loop(true);
            $mqtt->disconnect();
        } catch (\Exception $e) {
            CLI::error($e->getMessage());
        }
    }

    private function processMessage($json)
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            CLI::error("Invalid JSON received: $json");
            return;
        }

        $db = \Config\Database::connect();
        $time = date('Y-m-d H:i:s'); // using app timezone

        // 1. Log Raw Data
        $db->table('logs')->insert([
            'payload'    => $json,
            'created_at' => $time,
        ]);
        CLI::write("Log saved.", 'green');

        // 2. Insert Sensor Data
        if (isset($data['sensors'])) {
            $sensors = $data['sensors'];
            $insertData = [
                'ph'         => $sensors['ph'] ?? null,
                'tds'        => $sensors['tds'] ?? null,
                'turb'       => $sensors['turb'] ?? null,
                'tank'       => $sensors['tank'] ?? null,
                'chamber'    => $sensors['Chamber'] ?? null, // Note: Capital C in JSON example
                'created_at' => $time,
            ];

            $db->table('sensors')->insert($insertData);
            CLI::write("Sensor data saved.", 'green');
        } else {
            CLI::write("No 'sensors' key found in JSON.", 'yellow');
        }
    }
}
