# API Documentation - Sistem Monitor Air Kolam

## Base URL
```
http://localhost:8080/api
```

---

## Endpoints

### 1. Menerima Data dari Sensor
**Endpoint:** `POST /api/sensor/receive`

**Deskripsi:** Endpoint ini digunakan untuk menerima data dari sensor ESP32/IoT dan menyimpannya ke database.

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "sensors": {
    "ph": 7.2,
    "tds": 450,
    "turb": 12.1,
    "temp": 28.5,
    "do": 6.8,
    "tank": "80%",
    "chamber": "0%"
  },
  "actuators": {
    "sol_in": 0,
    "sol_ch": 0,
    "sol_drain": 1,
    "pump_in": 1,
    "pump_out": 0,
    "mixer": 1,
    "aerator": 0,
    "feeder": 0
  },
  "status": "Monitoring"
}
```

**Field Descriptions:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `sensors.ph` | float | Ya | Nilai pH air (0-14) |
| `sensors.tds` | float | Ya | Total Dissolved Solids dalam ppm |
| `sensors.turb` | float | Ya | Turbidity/Kekeruhan dalam NTU |
| `sensors.temp` | float | Tidak | Suhu air dalam °C |
| `sensors.do` | float | Tidak | Dissolved Oxygen dalam mg/L |
| `sensors.tank` | string | Tidak | Level tangki air (%) |
| `sensors.chamber` | string | Tidak | Level chamber (%) |
| `actuators.*` | int | Tidak | Status aktuator (0=OFF, 1=ON) |
| `status` | string | Tidak | Status sistem |

**Response Success (201):**
```json
{
  "status": "success",
  "message": "Data berhasil disimpan ke database",
  "data": {
    "sensors": {
      "ph": 7.2,
      "tds": 450,
      "turb": 12.1,
      "tank": "80%",
      "chamber": "0%"
    },
    "timestamp": "2026-01-05 17:15:42"
  }
}
```

**Response Error (400):**
```json
{
  "status": 400,
  "error": "Data 'sensors' tidak ditemukan dalam payload",
  "messages": {
    "error": "Data 'sensors' tidak ditemukan dalam payload"
  }
}
```

**Contoh penggunaan dengan cURL:**
```bash
curl -X POST http://localhost:8080/api/sensor/receive \
  -H "Content-Type: application/json" \
  -d '{
    "sensors": {
      "ph": 7.2,
      "tds": 450,
      "turb": 12.1,
      "temp": 28.5,
      "do": 6.8,
      "tank": "80%",
      "chamber": "0%"
    },
    "actuators": {
      "sol_in": 0,
      "pump_in": 1,
      "mixer": 1
    }
  }'
```

**Contoh penggunaan dengan Arduino/ESP32:**
```cpp
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

void kirimDataKeAPI() {
  HTTPClient http;
  http.begin("http://192.168.1.100:8080/api/sensor/receive");
  http.addHeader("Content-Type", "application/json");

  // Buat JSON
  StaticJsonDocument<512> doc;
  JsonObject sensors = doc.createNestedObject("sensors");
  sensors["ph"] = bacaSensorPH();
  sensors["tds"] = bacaSensorTDS();
  sensors["turb"] = bacaSensorTurbidity();
  sensors["temp"] = bacaSensorSuhu();
  sensors["do"] = bacaSensorDO();
  sensors["tank"] = "80%";
  sensors["chamber"] = "0%";

  JsonObject actuators = doc.createNestedObject("actuators");
  actuators["sol_in"] = 0;
  actuators["pump_in"] = 1;
  actuators["mixer"] = 1;

  String jsonString;
  serializeJson(doc, jsonString);

  int httpCode = http.POST(jsonString);
  
  if (httpCode == 201) {
    Serial.println("Data berhasil dikirim!");
  } else {
    Serial.println("Error: " + String(httpCode));
  }
  
  http.end();
}
```

---

### 2. Mendapatkan Data Sensor Terbaru
**Endpoint:** `GET /api/sensor/latest`

**Deskripsi:** Mengambil data sensor terbaru dari database.

**Response Success (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "ph": 7.2,
    "tds": 450,
    "turb": 12.1,
    "tank": "80%",
    "chamber": "0%",
    "created_at": "2026-01-05 17:15:42"
  }
}
```

**Contoh penggunaan:**
```bash
curl -X GET http://localhost:8080/api/sensor/latest
```

---

### 3. Mengontrol Actuator
**Endpoint:** `POST /api/sensor/control`

**Deskripsi:** Mengirim perintah untuk mengontrol actuator (pompa, valve, dll).

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "device": "aerator",
  "action": "on"
}
```

**Field Descriptions:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `device` | string | Ya | Nama perangkat: aerator, feeder, pump_in, pump_out, mixer, sol_in, sol_ch, sol_drain |
| `action` | string | Ya | Aksi yang diinginkan: "on" atau "off" |

**Response Success (200):**
```json
{
  "status": "success",
  "message": "Perintah on untuk aerator berhasil dikirim",
  "data": {
    "device": "aerator",
    "action": "on"
  }
}
```

**Response Error (400):**
```json
{
  "status": 400,
  "error": "Action harus \"on\" atau \"off\"",
  "messages": {
    "error": "Action harus \"on\" atau \"off\""
  }
}
```

**Contoh penggunaan:**
```bash
curl -X POST http://localhost:8080/api/sensor/control \
  -H "Content-Type: application/json" \
  -d '{
    "device": "aerator",
    "action": "on"
  }'
```

---

## Database Schema

### Tabel: `sensors`
Menyimpan data sensor yang sudah diparsing:

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key |
| `ph` | FLOAT | pH air |
| `tds` | FLOAT | Total Dissolved Solids (ppm) |
| `turb` | FLOAT | Turbidity (NTU) |
| `tank` | VARCHAR(20) | Level tangki (%) |
| `chamber` | VARCHAR(20) | Level chamber (%) |
| `created_at` | DATETIME | Waktu pencatatan |

### Tabel: `water_parameters`
Menyimpan parameter air yang lebih lengkap:

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key |
| `temperature` | DECIMAL(5,2) | Suhu (°C) |
| `ph` | DECIMAL(4,2) | pH air |
| `dissolved_oxygen` | DECIMAL(5,2) | Oksigen terlarut (mg/L) |
| `turbidity` | DECIMAL(6,2) | Kekeruhan (NTU) |
| `tds` | DECIMAL(7,2) | TDS (ppm) |
| `timestamp` | DATETIME | Waktu pencatatan |

### Tabel: `actuators`
Menyimpan status aktuator:

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key |
| `sol_in` | TINYINT | Solenoid Valve Input (0/1) |
| `sol_ch` | TINYINT | Solenoid Valve Chamber (0/1) |
| `sol_drain` | TINYINT | Solenoid Valve Drain (0/1) |
| `pump_in` | TINYINT | Pompa Input (0/1) |
| `pump_out` | TINYINT | Pompa Output (0/1) |
| `mixer` | TINYINT | Mixer/Pengaduk (0/1) |
| `aerator` | TINYINT | Aerator (0/1) |
| `feeder` | TINYINT | Auto Feeder (0/1) |
| `created_at` | DATETIME | Waktu pencatatan |

### Tabel: `logs`
Menyimpan semua raw data JSON untuk debugging:

| Column | Type | Description |
|--------|------|-------------|
| `id` | INT | Primary key |
| `payload` | LONGTEXT | Raw JSON data |
| `created_at` | DATETIME | Waktu pencatatan |

---

## Error Codes

| HTTP Code | Description |
|-----------|-------------|
| 200 | OK - Request berhasil |
| 201 | Created - Data berhasil dibuat |
| 400 | Bad Request - Format request salah |
| 404 | Not Found - Resource tidak ditemukan |
| 500 | Internal Server Error - Error di server |

---

## Catatan Penting

1. **Interval Pengiriman Data**: Disarankan mengirim data setiap 5-10 detik untuk menghindari overload database.

2. **Validasi Data**: API akan otomatis memvalidasi data dan membuat notifikasi jika ada parameter yang keluar dari batas normal.

3. **Set-Point**: Batas normal parameter ditentukan berdasarkan tabel `setpoints` yang aktif.

4. **Logging**: Semua data yang masuk akan disimpan di tabel `logs` dalam bentuk JSON lengkap untuk keperluan debugging.

5. **MQTT Integration**: Untuk implementasi MQTT, gunakan library PhpMqtt di sisi server untuk subscribe ke topic sensor dan publish perintah kontrol.

---

## Roadmap API

### Planned Endpoints:
- `GET /api/sensor/history` - Mendapatkan riwayat data sensor
- `GET /api/notifications` - Mendapatkan daftar notifikasi
- `POST /api/setpoint/update` - Update set-point parameter
- `GET /api/devices/status` - Mendapatkan status semua perangkat
- `POST /api/schedule/feeding` - Mengatur jadwal pemberian pakan otomatis

---

Untuk pertanyaan atau masalah, silakan hubungi tim development.
