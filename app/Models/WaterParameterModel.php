<?php

namespace App\Models;

use CodeIgniter\Model;

class WaterParameterModel extends Model
{
    protected $table = 'water_parameters';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['temperature', 'ph', 'dissolved_oxygen', 'turbidity', 'tds', 'timestamp'];

    // Data dummy untuk parameter air saat ini
    public function getCurrentParameters()
    {
        return [
            'temperature' => 28.5,
            'ph' => 7.2,
            'dissolved_oxygen' => 6.8,
            'turbidity' => 15.3,
            'tds' => 450.0,
            'feed_count' => 3,
            'system_status' => 'Normal'
        ];
    }

    // Data dummy untuk setpoint
    public function getSetpoints()
    {
        return [
            'temp' => ['min' => 26, 'max' => 30],
            'ph' => ['min' => 6.5, 'max' => 7.5],
            'do' => 5.0,
            'ntu' => 20.0
        ];
    }

    // Data dummy untuk jenis ikan
    public function getFishTypes()
    {
        return [
            'lele' => [
                'name' => 'Lele',
                'temp' => [26, 30],
                'ph' => [6.5, 7.5],
                'do' => 5,
                'ntu' => 150
            ],
            'nila' => [
                'name' => 'Nila',
                'temp' => [25, 30],
                'ph' => [6.5, 8.5],
                'do' => 5,
                'ntu' => 50
            ],
            'gurame' => [
                'name' => 'Gurame',
                'temp' => [25, 30],
                'ph' => [6.5, 8.0],
                'do' => 5,
                'ntu' => 25
            ],
            'patin' => [
                'name' => 'Patin',
                'temp' => [26, 32],
                'ph' => [6.5, 7.5],
                'do' => 4,
                'ntu' => 30
            ],
            'mas' => [
                'name' => 'Mas',
                'temp' => [20, 28],
                'ph' => [6.5, 8.5],
                'do' => 5,
                'ntu' => 20
            ]
        ];
    }

    // Data dummy untuk chart
    public function getChartData()
    {
        $labels = [];
        $temperature = [];
        $ph = [];
        $do = [];
        $ntu = [];
        $tds = [];

        // Generate dummy data untuk 20 titik waktu terakhir
        for ($i = 19; $i >= 0; $i--) {
            $time = new \DateTime();
            $time->modify("-{$i} minutes");
            $labels[] = $time->format('H:i');
            
            // Generate nilai yang bervariasi sedikit
            $temperature[] = round(26 + rand(0, 80) / 10, 1);
            $ph[] = round(6.5 + rand(0, 100) / 100, 1);
            $do[] = round(4 + rand(0, 40) / 10, 1);
            $ntu[] = round(10 + rand(0, 200) / 10, 1);
            $tds[] = round(300 + rand(0, 3000) / 10, 1);
        }

        return [
            'labels' => $labels,
            'temperature' => $temperature,
            'ph' => $ph,
            'do' => $do,
            'ntu' => $ntu,
            'tds' => $tds
        ];
    }

    // Data dummy untuk kontrol manual
    public function getControls()
    {
        return [
            'aerator' => false,
            'blower_speed' => 50,
            'dosing_speed' => 30
        ];
    }

    // Data dummy untuk notifikasi
    public function getNotifications()
    {
        return [
            [
                'type' => 'safe',
                'title' => 'Sistem Normal',
                'message' => 'Semua parameter dalam batas normal',
                'time' => date('Y-m-d H:i:s', strtotime('-5 minutes'))
            ],
            [
                'type' => 'warning',
                'title' => 'Peringatan pH',
                'message' => 'Nilai pH mendekati batas minimum',
                'time' => date('Y-m-d H:i:s', strtotime('-15 minutes'))
            ],
            [
                'type' => 'safe',
                'title' => 'Aerator Aktif',
                'message' => 'Aerator telah diaktifkan',
                'time' => date('Y-m-d H:i:s', strtotime('-30 minutes'))
            ]
        ];
    }

    /**
     * Mengecek apakah parameter air sesuai dengan set-point
     * Mengembalikan array berisi pesan alert jika ada parameter yang abnormal
     */
    public function checkParameterAlerts($data)
    {
        $alerts = [];
        $db = \Config\Database::connect();
        
        // Ambil set-point yang sedang aktif
        $setpoint = $db->table('setpoints')
                       ->where('is_active', 1)
                       ->get()
                       ->getRowArray();

        // Jika tidak ada setpoint aktif, gunakan default untuk lele
        if (!$setpoint) {
            $setpoint = [
                'temp_min' => 25.0,
                'temp_max' => 32.0,
                'ph_min' => 6.5,
                'ph_max' => 8.5,
                'do_min' => 5.0,
                'turbidity_max' => 50.0,
                'tds_max' => 1000.0,
                'fish_type' => 'Default'
            ];
        }

        // Cek suhu
        if (isset($data['temperature'])) {
            $temp = $data['temperature'];
            if ($temp < $setpoint['temp_min']) {
                $alerts[] = "⚠️ Suhu terlalu rendah! ({$temp}°C < {$setpoint['temp_min']}°C)";
            } elseif ($temp > $setpoint['temp_max']) {
                $alerts[] = "⚠️ Suhu terlalu tinggi! ({$temp}°C > {$setpoint['temp_max']}°C)";
            }
        }

        // Cek pH
        if (isset($data['ph'])) {
            $ph = $data['ph'];
            if ($ph < $setpoint['ph_min']) {
                $alerts[] = "⚠️ pH terlalu asam! ({$ph} < {$setpoint['ph_min']})";
            } elseif ($ph > $setpoint['ph_max']) {
                $alerts[] = "⚠️ pH terlalu basa! ({$ph} > {$setpoint['ph_max']})";
            }
        }

        // Cek Dissolved Oxygen
        if (isset($data['dissolved_oxygen'])) {
            $do = $data['dissolved_oxygen'];
            if ($do < $setpoint['do_min']) {
                $alerts[] = "⚠️ Kadar oksigen terlalu rendah! ({$do} mg/L < {$setpoint['do_min']} mg/L)";
            }
        }

        // Cek Turbidity
        if (isset($data['turbidity'])) {
            $turb = $data['turbidity'];
            if ($turb > $setpoint['turbidity_max']) {
                $alerts[] = "⚠️ Kekeruhan air terlalu tinggi! ({$turb} NTU > {$setpoint['turbidity_max']} NTU)";
            }
        }

        // Cek TDS
        if (isset($data['tds'])) {
            $tds = $data['tds'];
            if ($tds > $setpoint['tds_max']) {
                $alerts[] = "⚠️ TDS terlalu tinggi! ({$tds} ppm > {$setpoint['tds_max']} ppm)";
            }
        }

        return $alerts;
    }

    /**
     * Mendapatkan data parameter air terbaru dari database
     */
    public function getLatestData()
    {
        return $this->orderBy('timestamp', 'DESC')->first();
    }

    /**
     * Mendapatkan data untuk grafik (N data terakhir)
     */
    public function getChartDataFromDB($limit = 20)
    {
        $data = $this->orderBy('timestamp', 'DESC')
                     ->limit($limit)
                     ->find();
        
        // Reverse agar urutan dari lama ke baru
        $data = array_reverse($data);
        
        $labels = [];
        $temperature = [];
        $ph = [];
        $do = [];
        $turbidity = [];
        $tds = [];

        foreach ($data as $row) {
            $time = new \DateTime($row['timestamp']);
            $labels[] = $time->format('H:i');
            $temperature[] = (float)$row['temperature'];
            $ph[] = (float)$row['ph'];
            $do[] = (float)$row['dissolved_oxygen'];
            $turbidity[] = (float)$row['turbidity'];
            $tds[] = (float)$row['tds'];
        }

        return [
            'labels' => $labels,
            'temperature' => $temperature,
            'ph' => $ph,
            'do' => $do,
            'ntu' => $turbidity,
            'tds' => $tds
        ];
    }
}

