# 🚀 Panduan Cepat - Setup Sistem Monitor Air Kolam

## ✅ Perubahan yang Telah Dilakukan

### 1. Database
- ✓ Nama database diubah dari `moaq_sensor` → `monitoring_kolam`
- ✓ File SQL baru: `database/monitoring_kolam_complete.sql` (lebih lengkap)
- ✓ Total 7 tabel: logs, sensors, water_parameters, actuators, device_status, notifications, setpoints

### 2. API Endpoints (Sudah Terintegrasi)
- ✓ `POST /api/sensor/receive` - Menerima data dari ESP32
- ✓ `GET /api/sensor/latest` - Ambil data terbaru
- ✓ `POST /api/sensor/control` - Kontrol actuator

### 3. Dokumentasi
- ✓ `API_DOCUMENTATION.md` - Dokumentasi API lengkap dengan contoh
- ✓ `README.md` - Panduan instalasi dan penggunaan
- ✓ `CHANGELOG.md` - Riwayat perubahan

### 4. Konfigurasi
- ✓ `.env` sudah dikonfigurasi untuk database `monitoring_kolam`

---

## 📦 Langkah Instalasi (WAJIB!)

### Step 1: Import Database Baru

**PENTING**: Database harus disetup ulang dengan schema yang baru!

**Opsi A - Via phpMyAdmin** (Recommended):
1. Buka http://localhost/phpmyadmin
2. Klik tab "Import"
3. Pilih file: `c:/xampp/htdocs/tormonitor/database/monitoring_kolam_complete.sql`
4. Klik "Go"

**Opsi B - Via Command Line**:
```bash
# Buka Command Prompt di folder project
cd c:\xampp\htdocs\tormonitor

# Import database
mysql -u root -p < database/monitoring_kolam_complete.sql
```

### Step 2: Verifikasi Database

Cek apakah database berhasil dibuat:
```sql
USE monitoring_kolam;
SHOW TABLES;
```

Harusnya muncul 7 tabel:
- actuators
- device_status
- logs
- migrations
- notifications
- sensors
- setpoints
- water_parameters

### Step 3: Test API

Jalankan web server:
```bash
cd c:\xampp\htdocs\tormonitor
php spark serve
```

Test API dengan cURL atau Postman:
```bash
# Test endpoint receive
curl -X POST http://localhost:8080/api/sensor/receive ^
  -H "Content-Type: application/json" ^
  -d "{\"sensors\":{\"ph\":7.2,\"tds\":450,\"turb\":12.1,\"tank\":\"80%\",\"chamber\":\"0%\"}}"

# Test endpoint latest
curl http://localhost:8080/api/sensor/latest
```

Jika berhasil, response akan berupa JSON:
```json
{
  "status": "success",
  "message": "Data berhasil disimpan ke database"
}
```

---

## 🔧 Cara Test dari ESP32/Arduino

### Update Kode ESP32 Anda:

```cpp
const char* serverUrl = "http://192.168.1.XXX:8080/api/sensor/receive";
// Ganti XXX dengan IP komputer server Anda

void kirimDataSensor() {
  HTTPClient http;
  http.begin(serverUrl);
  http.addHeader("Content-Type", "application/json");

  // Buat JSON sesuai format baru
  String jsonData = "{\"sensors\":{";
  jsonData += "\"ph\":" + String(bacaPH()) + ",";
  jsonData += "\"tds\":" + String(bacaTDS()) + ",";
  jsonData += "\"turb\":" + String(bacaTurbidity()) + ",";
  jsonData += "\"temp\":" + String(bacaSuhu()) + ",";
  jsonData += "\"do\":" + String(bacaDO()) + ",";
  jsonData += "\"tank\":\"80%\",";
  jsonData += "\"chamber\":\"0%\"";
  jsonData += "}}";

  int httpCode = http.POST(jsonData);
  
  if (httpCode == 201) {
    Serial.println("✓ Data terkirim!");
  } else {
    Serial.println("Error: " + String(httpCode));
  }
  
  http.end();
}
```

---

## 📊 Format Data yang Diterima API

### Format Lengkap (Semua Field Opsional kecuali sensors):
```json
{
  "sensors": {
    "ph": 7.2,           // WAJIB
    "tds": 450,          // WAJIB
    "turb": 12.1,        // WAJIB
    "temp": 28.5,        // Opsional
    "do": 6.8,           // Opsional (Dissolved Oxygen)
    "tank": "80%",       // Opsional
    "chamber": "0%"      // Opsional
  },
  "actuators": {         // OPSIONAL - Semua field
    "sol_in": 0,
    "sol_ch": 0,
    "sol_drain": 1,
    "pump_in": 1,
    "pump_out": 0,
    "mixer": 1,
    "aerator": 0,
    "feeder": 0
  },
  "status": "Monitoring" // Opsional
}
```

### Format Minimal (Hanya Sensor Wajib):
```json
{
  "sensors": {
    "ph": 7.2,
    "tds": 450,
    "turb": 12.1
  }
}
```

---

## 🎯 Fitur Baru yang Tersedia

### 1. Auto-Alert System
Sistem akan otomatis membuat notifikasi jika:
- pH < 6.5 atau > 8.5
- Suhu < 25°C atau > 32°C
- DO < 5 mg/L
- Turbidity > 50 NTU
- TDS > 1000 ppm

### 2. Multi Set-Point
Anda bisa pilih profil ikan di database (tabel `setpoints`):
- Lele: pH 6.5-8.5, Temp 25-32°C
- Nila: pH 6.5-8.0, Temp 25-30°C
- Gurame: pH 6.5-8.5, Temp 24-30°C
- Patin: pH 6.5-8.0, Temp 26-32°C
- Mas: pH 6.5-8.0, Temp 24-28°C

Update set-point aktif:
```sql
UPDATE setpoints SET is_active = 0; -- Reset semua
UPDATE setpoints SET is_active = 1 WHERE fish_type = 'Nila'; -- Aktifkan Nila
```

### 3. Control Actuator
Kontrol perangkat via API:
```bash
curl -X POST http://localhost:8080/api/sensor/control ^
  -H "Content-Type: application/json" ^
  -d "{\"device\":\"aerator\",\"action\":\"on\"}"
```

Device yang bisa dikontrol:
- aerator
- feeder
- pump_in
- pump_out
- mixer
- sol_in
- sol_ch
- sol_drain

---

## 📁 File-File Penting

| File | Deskripsi |
|------|-----------|
| `database/monitoring_kolam_complete.sql` | Database schema lengkap |
| `API_DOCUMENTATION.md` | Dokumentasi API detail |
| `README.md` | Panduan lengkap |
| `app/Controllers/Api/Sensor.php` | API Controller |
| `app/Models/WaterParameterModel.php` | Model dengan auto-alert |
| `.env` | Konfigurasi database |

---

## ⚠️ Troubleshooting

### Error: Table doesn't exist
- **Solusi**: Import ulang file `database/monitoring_kolam_complete.sql`

### Error: Connection refused
- **Solusi**: Pastikan MySQL dan Apache running di XAMPP

### API return 404
- **Solusi**: Pastikan akses via `http://localhost:8080/api/...`
- Atau via XAMPP: `http://localhost/tormonitor/public/api/...`

### Data tidak masuk database
- **Solusi**: 
  1. Cek file log di `writable/logs/`
  2. Pastikan format JSON benar
  3. Test dengan Postman dulu sebelum ESP32

---

## 🎉 Sistem Siap Digunakan!

Setelah langkah di atas, sistem sudah siap menerima data dari sensor ESP32 dan menyimpannya ke database `monitoring_kolam`.

**Dashboard**: http://localhost:8080
**API Base**: http://localhost:8080/api

---

**Catatan**: Jika ada masalah, lihat dokumentasi lengkap di `API_DOCUMENTATION.md` dan `README.md`
