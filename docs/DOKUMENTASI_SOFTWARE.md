# 2.5 Perangkat Lunak (Software)

## 2.5.1 Bahasa Pemrograman

Sistem Monitoring Air Kolam menggunakan bahasa pemrograman dan teknologi berikut:

### A. Backend (Server Side)
- **PHP 8.1** - Bahasa pemrograman untuk logika server
- **CodeIgniter 4** - Framework PHP untuk struktur aplikasi
- **MySQL** - Database untuk menyimpan data sensor

### B. Frontend (Tampilan Web)
- **HTML5** - Struktur halaman web
- **CSS3** - Desain dan tampilan
- **JavaScript** - Interaktivitas halaman
- **Bootstrap 5** - Framework untuk tampilan responsive

### C. Protokol Komunikasi
- **MQTT** - Protokol untuk komunikasi antara sensor dan server
- **JSON** - Format data yang digunakan

### D. Library Pendukung
- **Chart.js** - Untuk membuat grafik monitoring
- **jQuery** - Untuk memudahkan manipulasi halaman
- **PhpMqtt** - Library PHP untuk menangani MQTT

---

## 2.5.2 Alur Program Utama

### A. Diagram Arsitektur Sistem

```
┌──────────────┐         ┌──────────────┐         ┌──────────────┐
│  Sensor IoT  │  MQTT   │     MQTT     │  MQTT   │  Web Server  │
│   (ESP32)    │ ──────→ │    Broker    │ ──────→ │   (PHP)      │
│              │         │  (Mosquitto) │         │              │
└──────────────┘         └──────────────┘         └──────────────┘
                                                          │
                                                          ↓
                                                   ┌──────────────┐
                                                   │    MySQL     │
                                                   │   Database   │
                                                   └──────────────┘
                                                          │
                                                          ↓
                                                   ┌──────────────┐
                                                   │   Browser    │
                                                   │  (Tampilan)  │
                                                   └──────────────┘
```

### B. Alur Kerja Sistem

#### 1. Pengiriman Data Sensor ke Database

```
MULAI
   ↓
[Sensor membaca parameter air: suhu, pH, oksigen, kekeruhan, TDS]
   ↓
[Data diubah ke format JSON]
   ↓
[Kirim data ke MQTT Broker dengan topic "kolam/sensors/data"]
   ↓
[MQTT Broker menerima dan meneruskan data]
   ↓
[PHP Server menerima data dari MQTT]
   ↓
[Validasi data - apakah format benar?]
   │
   ├─→ [BENAR] → [Simpan ke database MySQL]
   │                    ↓
   │             [Cek apakah nilai normal?]
   │                    │
   │                    ├─→ [Normal] → [Catat status OK]
   │                    │
   │                    └─→ [Tidak Normal] → [Buat peringatan/notifikasi]
   │
   └─→ [SALAH] → [Catat error di log]
   ↓
SELESAI
```

#### 2. Tampilan Dashboard Web

```
MULAI
   ↓
[User membuka halaman dashboard di browser]
   ↓
[Server PHP mengambil data dari database MySQL]
   ↓
[Data ditampilkan di halaman web]
   ↓
[JavaScript melakukan update otomatis setiap 5 detik]
   ↓
[Tampilan grafik dan angka diperbarui]
   ↓
SELESAI
```

#### 3. Kontrol Manual Perangkat

```
MULAI
   ↓
[User klik tombol kontrol (misal: nyalakan aerator)]
   ↓
[Browser kirim perintah ke server PHP]
   ↓
[Server buat perintah dalam format JSON]
   ↓
[Kirim perintah ke MQTT Broker dengan topic "kolam/control"]
   ↓
[Perangkat IoT menerima perintah]
   ↓
[Perangkat mengaktifkan/mematikan alat]
   ↓
[Perangkat kirim konfirmasi status kembali]
   ↓
[Tampilan web diperbarui menunjukkan status alat]
   ↓
SELESAI
```

---

## 2.5.3 Penjelasan Fungsi Tiap Bagian Kode

### A. Struktur Folder Proyek

```
sistem-monitoring-kolam/
│
├── app/
│   ├── Controllers/      → Logika program utama
│   ├── Models/          → Komunikasi dengan database
│   ├── Views/           → Tampilan halaman web
│   ├── Libraries/       → Library custom (MQTT)
│   └── Config/          → Pengaturan sistem
│
├── public/
│   ├── assets/          → CSS, JavaScript, gambar
│   └── index.php        → File utama aplikasi
│
└── database/            → File database SQL
```

---

### B. Kode Program Utama

#### 1. Controller - Dashboard.php
**Fungsi:** Mengatur halaman dashboard dan mengambil data

```php
<?php
namespace App\Controllers;
use App\Models\WaterParameterModel;

class Dashboard extends BaseController
{
    protected $waterModel;

    public function __construct()
    {
        // Inisialisasi model untuk akses database
        $this->waterModel = new WaterParameterModel();
    }

    public function index()
    {
        // Ambil data dari database
        $data = [
            'title' => 'Sistem Monitoring Air Kolam - Dashboard',
            'parameters' => $this->waterModel->getLatestData(),
            'chartData' => $this->waterModel->getChartData()
        ];
        
        // Tampilkan halaman dashboard
        return view('dashboard/index', $data);
    }
}
```

**Penjelasan:**
- Mengambil data sensor terbaru dari database
- Mengirim data ke halaman tampilan
- Mengatur halaman apa yang ditampilkan

---

#### 2. Model - WaterParameterModel.php
**Fungsi:** Mengurus semua operasi database

```php
<?php
namespace App\Models;
use CodeIgniter\Model;

class WaterParameterModel extends Model
{
    protected $table = 'water_parameters';
    protected $allowedFields = [
        'temperature', 'ph', 'dissolved_oxygen', 
        'turbidity', 'tds', 'timestamp'
    ];

    // Ambil data terbaru
    public function getLatestData()
    {
        return $this->orderBy('timestamp', 'DESC')
                    ->first();
    }

    // Ambil data untuk grafik (20 data terakhir)
    public function getChartData()
    {
        return $this->orderBy('timestamp', 'DESC')
                    ->limit(20)
                    ->find();
    }

    // Simpan data dari MQTT
    public function saveFromMqtt($jsonData)
    {
        $data = json_decode($jsonData, true);
        
        // Cek apakah data valid
        if ($this->validate($data)) {
            return $this->insert($data);
        }
        
        return false;
    }

    // Cek apakah nilai sensor normal
    public function checkStatus($data)
    {
        $alerts = [];

        // Cek suhu (normal: 26-30°C)
        if ($data['temperature'] < 26 || $data['temperature'] > 30) {
            $alerts[] = 'Suhu air tidak normal';
        }

        // Cek pH (normal: 6.5-7.5)
        if ($data['ph'] < 6.5 || $data['ph'] > 7.5) {
            $alerts[] = 'pH air tidak normal';
        }

        // Cek oksigen terlarut (minimal: 5 mg/L)
        if ($data['dissolved_oxygen'] < 5) {
            $alerts[] = 'Kadar oksigen rendah';
        }

        return $alerts;
    }
}
```

**Penjelasan:**
- Mengambil data dari database MySQL
- Menyimpan data baru yang dikirim sensor
- Mengecek apakah nilai sensor masih normal

---

#### 3. Library - MqttHandler.php
**Fungsi:** Menangani komunikasi MQTT dengan sensor

```php
<?php
namespace App\Libraries;
use phpMQTT;
use App\Models\WaterParameterModel;

class MqttHandler
{
    private $mqtt;
    private $broker = 'localhost';  // Alamat MQTT Broker
    private $port = 1883;           // Port MQTT

    // Koneksi ke MQTT Broker
    public function connect()
    {
        $this->mqtt = new phpMQTT($this->broker, $this->port, 'WebServer');

        if ($this->mqtt->connect(true)) {
            return true;
        }
        
        return false;
    }

    // Terima data dari sensor
    public function listenToSensor()
    {
        $topics['kolam/sensors/data'] = [
            'qos' => 1, 
            'function' => [$this, 'onDataReceived']
        ];

        $this->mqtt->subscribe($topics, 0);

        while ($this->mqtt->proc()) {
            // Terus dengarkan message
        }
    }

    // Fungsi ini dipanggil saat ada data masuk
    public function onDataReceived($topic, $message)
    {
        echo "Data diterima: $message\n";

        // Simpan ke database
        $model = new WaterParameterModel();
        $model->saveFromMqtt($message);

        // Cek status dan buat alert jika perlu
        $data = json_decode($message, true);
        $alerts = $model->checkStatus($data);
        
        if (!empty($alerts)) {
            $this->sendAlert($alerts);
        }
    }

    // Kirim perintah ke perangkat
    public function sendCommand($device, $action)
    {
        $command = json_encode([
            'device' => $device,
            'action' => $action,
            'time' => date('Y-m-d H:i:s')
        ]);

        $this->mqtt->publish('kolam/control', $command, 1);
    }

    // Kirim notifikasi alert
    private function sendAlert($alerts)
    {
        foreach ($alerts as $alert) {
            // Simpan ke tabel notifikasi
            $db = \Config\Database::connect();
            $db->table('notifications')->insert([
                'type' => 'warning',
                'message' => $alert,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
```

**Penjelasan:**
- Menerima data dari sensor via MQTT
- Menyimpan data ke database
- Mengirim perintah kontrol ke perangkat
- Membuat alert jika ada masalah

---

#### 4. JavaScript - main.js
**Fungsi:** Update tampilan secara real-time

```javascript
// Update data otomatis setiap 5 detik
setInterval(function() {
    updateDashboard();
}, 5000);

// Fungsi update dashboard
function updateDashboard() {
    $.ajax({
        url: '/dashboard/getLatestData',
        method: 'GET',
        success: function(response) {
            // Update nilai di layar
            $('#temperature').text(response.temperature + ' °C');
            $('#ph').text(response.ph);
            $('#oxygen').text(response.dissolved_oxygen + ' mg/L');
            $('#turbidity').text(response.turbidity + ' NTU');
            $('#tds').text(response.tds + ' ppm');

            // Update grafik
            updateChart(response.chartData);
        }
    });
}

// Fungsi kontrol aerator
function toggleAerator() {
    var action = $('#aeratorBtn').hasClass('active') ? 'off' : 'on';
    
    $.ajax({
        url: '/dashboard/controlDevice',
        method: 'POST',
        data: {
            device: 'aerator',
            action: action
        },
        success: function(response) {
            if (response.success) {
                $('#aeratorBtn').toggleClass('active');
                alert('Perintah berhasil dikirim');
            }
        }
    });
}

// Update grafik dengan data baru
function updateChart(data) {
    myChart.data.labels = data.labels;
    myChart.data.datasets[0].data = data.values;
    myChart.update();
}
```

**Penjelasan:**
- Mengambil data baru dari server setiap 5 detik
- Memperbarui angka dan grafik di layar
- Mengirim perintah kontrol ke perangkat

---

### C. Database MySQL

#### Tabel 1: water_parameters (Data Sensor)

```sql
CREATE TABLE water_parameters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    temperature DECIMAL(5,2) NOT NULL,      -- Suhu (°C)
    ph DECIMAL(4,2) NOT NULL,               -- pH air
    dissolved_oxygen DECIMAL(5,2) NOT NULL, -- Oksigen terlarut (mg/L)
    turbidity DECIMAL(6,2) NOT NULL,        -- Kekeruhan (NTU)
    tds DECIMAL(7,2) NOT NULL,              -- Total padatan terlarut (ppm)
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

**Fungsi:** Menyimpan semua data sensor yang masuk

---

#### Tabel 2: notifications (Peringatan)

```sql
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(20),              -- Jenis: info, warning, danger
    message TEXT NOT NULL,         -- Isi pesan
    is_read BOOLEAN DEFAULT FALSE, -- Sudah dibaca atau belum
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

**Fungsi:** Menyimpan notifikasi dan peringatan sistem

---

#### Tabel 3: device_status (Status Perangkat)

```sql
CREATE TABLE device_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_name VARCHAR(100) NOT NULL,  -- Nama alat: aerator, feeder, dll
    status VARCHAR(20),                 -- Status: on, off, error
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

**Fungsi:** Menyimpan status perangkat (nyala/mati)

---

### D. Format Data JSON

#### Data dari Sensor ke Server:

```json
{
    "temperature": 28.5,
    "ph": 7.2,
    "dissolved_oxygen": 6.8,
    "turbidity": 15.3,
    "tds": 450.0,
    "timestamp": "2024-01-04 15:45:00"
}
```

#### Perintah Kontrol dari Server ke Perangkat:

```json
{
    "device": "aerator",
    "action": "on",
    "time": "2024-01-04 15:45:00"
}
```

---

## 2.5.4 Cara Kerja Integrasi MQTT dengan MySQL

### Langkah 1: Sensor Mengirim Data

Sensor ESP32 membaca parameter air dan mengirim dalam format JSON:

```arduino
// Kode di ESP32
void kirimData() {
    String json = "{";
    json += "\"temperature\":" + String(bacaSuhu()) + ",";
    json += "\"ph\":" + String(bacaPH()) + ",";
    json += "\"dissolved_oxygen\":" + String(bacaOksigen());
    json += "}";
    
    // Kirim ke MQTT
    client.publish("kolam/sensors/data", json);
}
```

### Langkah 2: MQTT Broker Meneruskan

MQTT Broker (Mosquitto) menerima dan meneruskan pesan ke subscriber.

### Langkah 3: PHP Menerima dan Simpan ke Database

```php
// PHP menerima data
public function onDataReceived($topic, $message) {
    // Parse JSON
    $data = json_decode($message, true);
    
    // Simpan ke MySQL
    $this->db->table('water_parameters')->insert([
        'temperature' => $data['temperature'],
        'ph' => $data['ph'],
        'dissolved_oxygen' => $data['dissolved_oxygen'],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
```

### Langkah 4: Tampilan Web Mengambil dari Database

```javascript
// JavaScript ambil data
$.get('/api/latest-data', function(data) {
    $('#temperature').text(data.temperature + ' °C');
    $('#ph').text(data.ph);
});
```

---

## 2.5.5 Menjalankan Sistem

### A. Persyaratan

- PHP 8.1 atau lebih baru
- MySQL/MariaDB database
- XAMPP atau web server lain
- MQTT Broker (Mosquitto)

### B. Instalasi

```bash
# 1. Install Composer dependencies
composer install

# 2. Setup file konfigurasi
cp env .env

# 3. Edit .env untuk setting database
# database.default.database = monitoring_kolam
# database.default.username = root
# database.default.password = 

# 4. Import database
mysql -u root -p < database/schema.sql

# 5. Jalankan MQTT subscriber (buka terminal baru)
php spark mqtt:subscribe

# 6. Jalankan web server
php spark serve
```

### C. Akses Dashboard

Buka browser dan akses: `http://localhost:8080`

---

## 2.5.6 Troubleshooting

### Masalah: Data tidak masuk ke database

**Solusi:**
1. Cek apakah MQTT subscriber berjalan: `php spark mqtt:subscribe`
2. Cek koneksi MQTT Broker: `mosquitto -v`
3. Periksa log error: `writable/logs/log-*.log`

### Masalah: Dashboard tidak update

**Solusi:**
1. Buka console browser (F12)
2. Cek apakah ada error JavaScript
3. Pastikan AJAX request berhasil

---

## Kesimpulan

Sistem Monitoring Air Kolam mengintegrasikan sensor IoT, protokol MQTT, database MySQL, dan aplikasi web untuk memantau kualitas air secara real-time. Data mengalir dari sensor → MQTT → PHP → MySQL → Tampilan Web, memungkinkan user untuk:

1. Memantau parameter air secara real-time
2. Melihat grafik historis data
3. Mengontrol perangkat seperti aerator
4. Menerima notifikasi jika ada masalah

Sistem ini dirancang sederhana, mudah dipahami, dan mudah dikembangkan lebih lanjut.
