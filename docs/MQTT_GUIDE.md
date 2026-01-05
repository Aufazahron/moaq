# 📡 MQTT Integration - Sistem Monitor Air Kolam

## ✅ Setup Selesai!

MQTT subscriber sudah siap untuk menerima data dari sensor melalui MQTT broker.

---

## 🔧 Konfigurasi MQTT

### File `.env` sudah dikonfigurasi:

```env
MQTT_BROKER = localhost          # Ganti dengan IP Intel NUC
MQTT_PORT = 1883                 # Port MQTT broker
MQTT_USERNAME =                  # Isi jika ada auth
MQTT_PASSWORD =                  # Isi jika ada auth
MQTT_CLIENT_ID = monitoring_kolam

# Topics
MQTT_TOPIC_SENSOR = kolam/sensors/data
MQTT_TOPIC_CONTROL = kolam/control
MQTT_TOPIC_STATUS = kolam/status
```

### Cara Update Konfigurasi:

1. **Edit file `.env`**
2. **Ganti `MQTT_BROKER`** dengan IP Intel NUC atau broker Anda
   ```env
   MQTT_BROKER = 192.168.1.100  # Contoh IP Intel NUC
   ```

---

## 🚀 Cara Menjalankan

### Terminal 1: Web Server
```bash
cd c:\xampp\htdocs\tormonitor
php spark serve
```

### Terminal 2: MQTT Subscriber
```bash
cd c:\xampp\htdocs\tormonitor
php spark mqtt:subscribe
```

**Output yang diharapkan:**
```
========================================
  MQTT Subscriber - Monitoring Kolam   
========================================

Starting MQTT subscriber...
Press Ctrl+C to stop

Connecting to MQTT broker...
✓ Connected to MQTT Broker: localhost:1883

📡 Subscribing to topic: kolam/sensors/data
✓ Subscribed to: kolam/sensors/data
⏳ Waiting for messages...
```

---

## 📊 Format Data MQTT

### Topic: `kolam/sensors/data`

**Payload JSON (Minimal):**
```json
{
  "sensors": {
    "ph": 7.2,
    "tds": 450,
    "turb": 12.1
  }
}
```

**Payload JSON (Lengkap):**
```json
{
  "sensors": {
    "ph": 7.2,
    "tds": 450,
    "turb": 12.1,
    "temp": 28.5,
    "do": 6.8,
    "tank": "80%",
    "chamber": "20%"
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

---

## 🧪 Testing MQTT

### 1. Install MQTT Client (untuk testing)

**Windows:**
```bash
# Download MQTT.fx atau MQTTX
# https://mqttx.app/
```

**Linux/Mac:**
```bash
# Install mosquitto-clients
sudo apt-get install mosquitto-clients  # Ubuntu/Debian
brew install mosquitto                  # macOS
```

### 2. Test Publish Data

**Menggunakan mosquitto_pub:**
```bash
# Kirim data sensor
mosquitto_pub -h localhost -t kolam/sensors/data -m '{"sensors":{"ph":7.2,"tds":450,"turb":12.1}}'

# Dengan IP broker Intel NUC
mosquitto_pub -h 192.168.1.100 -t kolam/sensors/data -m '{"sensors":{"ph":7.5,"tds":500,"turb":15.0}}'
```

**Menggunakan MQTTX (GUI):**
1. Buka MQTTX
2. Connect ke broker: `mqtt://localhost:1883`
3. Publish ke topic: `kolam/sensors/data`
4. Payload: 
   ```json
   {"sensors":{"ph":7.2,"tds":450,"turb":12.1}}
   ```

### 3. Monitor Output

Saat data diterima, subscriber akan menampilkan:

```
========================================
📥 Message received at 2026-01-05 17:40:00
Topic: kolam/sensors/data
Message:
{"sensors":{"ph":7.2,"tds":450,"turb":12.1}}
========================================
✓ Saved to logs table
✓ Saved sensor data: pH=7.2, TDS=450, Turb=12.1
✓ All parameters normal
✅ Processing complete!
```

---

## 🔌 Integrasi dengan ESP32/Arduino

### Kode ESP32 dengan MQTT

```cpp
#include <WiFi.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>

// WiFi credentials
const char* ssid = "WIFI_SSID";
const char* password = "WIFI_PASSWORD";

// MQTT Broker (Intel NUC atau broker lain)
const char* mqtt_server = "192.168.1.100";
const int mqtt_port = 1883;
const char* mqtt_topic = "kolam/sensors/data";

WiFiClient espClient;
PubSubClient client(espClient);

void setup() {
  Serial.begin(115200);
  
  // Connect WiFi
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi Connected!");
  
  // Setup MQTT
  client.setServer(mqtt_server, mqtt_port);
  reconnect();
}

void loop() {
  if (!client.connected()) {
    reconnect();
  }
  client.loop();
  
  // Kirim data setiap 10 detik
  static unsigned long lastSend = 0;
  if (millis() - lastSend > 10000) {
    lastSend = millis();
    kirimDataSensor();
  }
}

void reconnect() {
  while (!client.connected()) {
    Serial.print("Connecting to MQTT...");
    if (client.connect("ESP32_Sensor")) {
      Serial.println("connected");
    } else {
      Serial.print("failed, rc=");
      Serial.print(client.state());
      Serial.println(" retry in 5 seconds");
      delay(5000);
    }
  }
}

void kirimDataSensor() {
  // Baca sensor
  float ph = bacaSensorPH();
  float tds = bacaSensorTDS();
  float turb = bacaSensorTurbidity();
  
  // Buat JSON
  StaticJsonDocument<256> doc;
  JsonObject sensors = doc.createNestedObject("sensors");
  sensors["ph"] = ph;
  sensors["tds"] = tds;
  sensors["turb"] = turb;
  sensors["tank"] = "80%";
  sensors["chamber"] = "20%";
  
  // Serialize dan kirim
  char jsonBuffer[256];
  serializeJson(doc, jsonBuffer);
  
  if (client.publish(mqtt_topic, jsonBuffer)) {
    Serial.println("✓ Data terkirim via MQTT");
    Serial.println(jsonBuffer);
  } else {
    Serial.println("✗ Gagal mengirim data");
  }
}

float bacaSensorPH() {
  // Implement sensor reading
  return 7.2;
}

float bacaSensorTDS() {
  return 450.0;
}

float bacaSensorTurbidity() {
  return 12.1;
}
```

**Library yang diperlukan:**
- PubSubClient (by Nick O'Leary)
- ArduinoJson (by Benoit Blanchon)

Install via Arduino Library Manager.

---

## 📡 Arsitektur Sistem

```
┌──────────────┐         ┌──────────────┐         ┌──────────────┐
│  ESP32 IoT   │  MQTT   │ MQTT Broker  │  MQTT   │  PHP Server  │
│   Sensor     │ ──────→ │ (Mosquitto)  │ ──────→ │ (Subscriber) │
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
                                                    │  Dashboard   │
                                                    │   Web UI     │
                                                    └──────────────┘
```

### Alur Data:

1. **ESP32** membaca sensor → Kirim ke MQTT Broker
2. **MQTT Broker** menerima → Forward ke Subscriber
3. **PHP Subscriber** (`php spark mqtt:subscribe`) → Parse JSON
4. **Data disimpan** ke MySQL database
5. **Dashboard** mengambil data dari database → Display real-time
6. **Alert** otomatis jika parameter abnormal

---

## 🎯 MQTT Topics

| Topic | Direction | Fungsi |
|-------|-----------|--------|
| `kolam/sensors/data` | ESP32 → Server | Data sensor (pH, TDS, dll) |
| `kolam/control` | Server → ESP32 | Perintah kontrol actuator |
| `kolam/status` | ESP32 → Server | Status sistem |

---

## ⚠️ Troubleshooting

### Error: Connection refused

**Penyebab:** MQTT Broker tidak running atau salah IP

**Solusi:**
1. Pastikan Mosquitto broker running di Intel NUC
   ```bash
   # Di Intel NUC
   sudo systemctl status mosquitto
   sudo systemctl start mosquitto
   ```
2. Cek firewall
3. Ping IP broker dari komputer server

### Error: Topic not subscribed

**Penyebab:** Topic name tidak cocok

**Solusi:**
- Pastikan topic sama persis: `kolam/sensors/data`
- Case-sensitive!

### Data tidak masuk ke database

**Penyebab:** JSON format salah atau subscriber tidak running

**Solusi:**
1. Cek subscriber masih running
2. Test dengan mosquitto_pub
3. Lihat log error: `writable/logs/`

---

## 📊 Monitoring

### Cek Data di Database

```sql
USE monitoring_kolam;

-- Data sensor terbaru
SELECT * FROM sensors ORDER BY created_at DESC LIMIT 10;

-- Log raw JSON
SELECT * FROM logs ORDER BY created_at DESC LIMIT 5;

-- Notifikasi alert
SELECT * FROM notifications WHERE is_read = 0;

-- Status device
SELECT * FROM device_status;
```

### Via Dashboard

```
http://localhost:8080/dashboard         → Real-time monitoring
http://localhost:8080/dashboard/grafik  → Grafik data
http://localhost:8080/dashboard/notifikasi → Alert
```

---

## 🚀 Production Setup

### Jalankan sebagai Service (Windows)

**Gunakan NSSM (Non-Sucking Service Manager):**

```bash
# Download NSSM
# https://nssm.cc/download

# Install service
nssm install MqttSubscriber "C:\php\php.exe" "C:\xampp\htdocs\tormonitor\spark" "mqtt:subscribe"

# Start service
nssm start MqttSubscriber
```

### Auto-restart jika crash

Script `restart_mqtt.bat`:
```batch
@echo off
:start
cd c:\xampp\htdocs\tormonitor
php spark mqtt:subscribe
timeout /t 5
goto start
```

---

## ✅ Status

- ✅ MQTT Library installed
- ✅ MqttHandler.php created
- ✅ Command mqtt:subscribe ready
- ✅ .env configured
- ✅ Auto-save to database
- ✅ Auto-alert pada parameter abnormal

**Sistem siap menerima data via MQTT!** 📡

---

**Next:** Jalankan `php spark mqtt:subscribe` dan test publish data!
