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

    // Data parameter air saat ini dari database
    public function getCurrentParameters()
    {
        $db = \Config\Database::connect();
        
        // Ambil data sensor terakhir
        $latestSensor = $db->table('sensors')
                           ->orderBy('created_at', 'DESC')
                           ->limit(1)
                           ->get()
                           ->getRowArray();

        // Ambil data actuator terakhir
        $latestActuator = $db->table('actuators')
                             ->orderBy('created_at', 'DESC')
                             ->limit(1)
                             ->get()
                             ->getRowArray();

        // Default values jika belum ada data
        $data = [
            'temperature'      => 0,
            'ph'               => 0,
            'dissolved_oxygen' => 0,
            'turbidity'        => 0,
            'tds'              => 0,
            'tank'             => '0%',
            'chamber'          => '0%',
            'mixer'            => 0,
            'feed_count'       => 0,
            'system_status'    => 'STANDBY',
            'last_updated'     => '-'
        ];

        if ($latestSensor) {
            $data['ph']           = (float)($latestSensor['ph'] ?? 0);
            $data['tds']          = (float)($latestSensor['tds'] ?? 0);
            $data['turbidity']    = (float)($latestSensor['turb'] ?? 0);
            $data['tank']         = $latestSensor['tank'] ?? '0%';
            $data['chamber']      = $latestSensor['chamber'] ?? '0%';
            $data['last_updated'] = $latestSensor['created_at']; // Tambahan info waktu
            
            // Note: Temperature & DO belum ada di tabel sensors yg baru migrate, 
            // tapi jika nanti ada di tabel sensors, bisa diambil.
            // Untuk sekarang, saya biarkan 0 atau ambil dari water_parameters jika masih dipakai.
            // Sesuai request user, fokus ke data: ph, tds, turb, tank, chamber.
        }

        if ($latestActuator) {
            $data['mixer'] = (int)($latestActuator['mixer'] ?? 0);
        }

        return $data;
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

    // Data asli dari tabel sensors untuk chart dengan filter range & sampling cerdas
    public function getChartData($range = 'latest')
    {
        $db = \Config\Database::connect();
        $builder = $db->table('sensors');
        
        if ($range === '1h') {
            // Gunakan SQL Native DATE_SUB agar sinkron dengan waktu database
            $builder->where('created_at >= DATE_SUB((SELECT MAX(created_at) FROM sensors), INTERVAL 1 HOUR)');
            $targetCount = 1000; 
        } elseif ($range === '24h') {
            // Ambil 24 jam terakhir dari data paling baru yang ada di DB
            $builder->where('created_at >= DATE_SUB((SELECT MAX(created_at) FROM sensors), INTERVAL 24 HOUR)');
            $targetCount = 1000;
        } else {
            $targetCount = 30;
        }

        $builder->orderBy('created_at', 'DESC');
        
        // Ambil limit cukup besar untuk sampling
        $limit = ($range === 'latest') ? 30 : 10000;
        $builder->limit($limit);

        $sensors = $builder->get()->getResultArray();

        // Balik urutan: Dari DESC (terbaru) menjadi ASC (terlama ke terbaru) untuk grafik
        $sensors = array_reverse($sensors);

        // Jika data kosong pada 1H (karena gap data), ambil data terbaru saja
        if (empty($sensors) && ($range === '1h' || $range === '24h')) {
            $sensors = $db->table('sensors')->orderBy('created_at', 'DESC')->limit(30)->get()->getResultArray();
            $sensors = array_reverse($sensors);
        }

        // --- SMART SAMPLING ---
        if (count($sensors) > $targetCount) {
            $sampled = [];
            $step = count($sensors) / ($targetCount - 1);
            for ($i = 0; $i < $targetCount - 1; $i++) {
                $index = floor($i * $step);
                if (isset($sensors[$index])) {
                    $sampled[] = $sensors[$index];
                }
            }
            $sampled[] = end($sensors); 
            $sensors = $sampled;
        }

        $labels = [];
        $ph = [];
        $turb = [];
        $tds = [];

        foreach ($sensors as $sensor) {
            $timeFormat = ($range === '24h') ? 'H:i' : 'H:i:s';
            $time = date($timeFormat, strtotime($sensor['created_at']));
            $labels[] = $time;
            
            $rawPh = (float)($sensor['ph'] ?? 0);
            $rawTurb = (float)($sensor['turb'] ?? 0);
            $rawTds = (float)($sensor['tds'] ?? 0);

            $ph[] = ($rawPh >= 0 && $rawPh <= 14) ? $rawPh : null;
            $turb[] = ($rawTurb >= 0 && $rawTurb <= 2000) ? $rawTurb : null;
            $tds[] = ($rawTds >= 0 && $rawTds <= 5000) ? $rawTds : null;
        }

        return [
            'labels' => $labels,
            'ph' => $ph,
            'turb' => $turb,
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

    // Mendapatkan notifikasi asli dari database
    public function getNotifications()
    {
        $db = \Config\Database::connect();
        
        // 1. Ambil notifikasi dari database (tabel notifications)
        $notifs = $db->table('notifications')
                     ->orderBy('created_at', 'DESC')
                     ->limit(15)
                     ->get()
                     ->getResultArray();

        // 2. Cek status keaktifan sistem berdasarkan data sensor terakhir
        $latestSensor = $db->table('sensors')
                           ->orderBy('created_at', 'DESC')
                           ->limit(1)
                           ->get()
                           ->getRowArray();

        $allNotifications = [];
        
        // Tambahkan status keaktifan sistem di paling atas
        if ($latestSensor) {
            $lastUpdate = new \DateTime($latestSensor['created_at']);
            $now = new \CodeIgniter\I18n\Time('now', 'Asia/Jakarta');
            
            // Konversi created_at sensor ke DateTime object untuk perbandingan
            $lastUpdateTime = \CodeIgniter\I18n\Time::parse($latestSensor['created_at'], 'Asia/Jakarta');
            $diffMinutes = ($now->getTimestamp() - $lastUpdateTime->getTimestamp()) / 60;

            if ($diffMinutes > 5) {
                $allNotifications[] = [
                    'type' => 'critical',
                    'title' => 'Sistem Offline',
                    'message' => 'Sistem tidak mengirimkan data selama ' . floor($diffMinutes) . ' menit. Mohon periksa perangkat ESP32 dan koneksi internet.',
                    'time' => $latestSensor['created_at']
                ];
            } else {
                $allNotifications[] = [
                    'type' => 'safe',
                    'title' => 'Sistem Online',
                    'message' => 'Status sistem aktif dan pengiriman data stabil.',
                    'time' => $latestSensor['created_at']
                ];
            }
        } else {
            $allNotifications[] = [
                'type' => 'warning',
                'title' => 'Sistem Belum Aktif',
                'message' => 'Belum ada data sensor yang diterima oleh server.',
                'time' => date('Y-m-d H:i:s')
            ];
        }

        // 3. Masukkan notifikasi parameter dari DB
        foreach ($notifs as $n) {
            $allNotifications[] = [
                'type' => $n['type'] === 'warning' ? 'warning' : ($n['type'] === 'danger' ? 'critical' : 'safe'),
                'title' => ($n['type'] === 'warning' ? 'Peringatan Parameter' : 'Notifikasi Sistem'),
                'message' => $n['message'],
                'time' => $n['created_at']
            ];
        }

        return $allNotifications;
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

