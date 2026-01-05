# ✅ MQTT INTEGRATION COMPLETE!

## 🎉 Status: MQTT Ready untuk Intel NUC / MQTT Broker

**Waktu:** 2026-01-05 17:42  
**Status:** PRODUCTION READY ✓

---

## ✅ Yang Sudah Selesai

### 1. Library MQTT ✓
- ✅ php-mqtt/client v2.3.1 installed
- ✅ Mendukung MQTT protocol
- ✅ Auto-reconnect
- ✅ QoS support

### 2. MQTT Handler ✓
**File:** `app/Libraries/MqttHandler.php`

Fitur:
- ✅ Subscribe ke topic sensor
- ✅ Auto-parse JSON data
- ✅ Auto-save ke database
- ✅ Auto-alert jika parameter abnormal
- ✅ Logging lengkap

### 3. CLI Command ✓
**Command:** `php spark mqtt:subscribe`

Fitur:
- ✅ Connect ke MQTT broker
- ✅ Subscribe topic `kolam/sensors/data`
- ✅ Real-time processing
- ✅ Error handling
- ✅ Ctrl+C to stop

### 4. Konfigurasi ✓
**File:** `.env`

```env
MQTT_BROKER = localhost          # ← Ganti dengan IP Intel NUC!
MQTT_PORT = 1883
MQTT_USERNAME = 
MQTT_PASSWORD = 
MQTT_CLIENT_ID = monitoring_kolam
```

---

## 🚀 Cara Menggunakan

### Step 1: Konfigurasi Broker

Edit file `.env`:
```env
# Ganti localhost dengan IP Intel NUC
MQTT_BROKER = 192.168.1.100  # Contoh IP
MQTT_PORT = 1883
```

### Step 2: Jalankan Web Server

**Terminal 1:**
```bash
cd c:\xampp\htdocs\tormonitor
php spark serve
```

Server running di: http://localhost:8080

### Step 3: Jalankan MQTT Subscriber

**Terminal 2:**
```bash
cd c:\xampp\htdocs\tormonitor
php spark mqtt:subscribe
```

**Output:**
```
========================================
  MQTT Subscriber - Monitoring Kolam   
========================================

Connecting to MQTT broker...
✓ Connected to MQTT Broker: 192.168.1.100:1883

📡 Subscribing to topic: kolam/sensors/data
✓ Subscribed to: kolam/sensors/data
⏳ Waiting for messages...
```

### Step 4: Kirim Data dari ESP32/Sensor

**Topic:** `kolam/sensors/data`

**Payload:**
```json
{
  "sensors": {
    "ph": 7.2,
    "tds": 450,
    "turb": 12.1,
    "tank": "80%",
    "chamber": "20%"
  }
}
```

---

## 📊 Alur Data MQTT

```
┌─────────────┐
│   ESP32     │───┐
│  Sensor 1   │   │
└─────────────┘   │
                  │ MQTT Publish
┌─────────────┐   │ Topic: kolam/sensors/data
│   ESP32     │───┤
│  Sensor 2   │   │
└─────────────┘   │
                  ↓
           ┌──────────────┐
           │ MQTT Broker  │
           │ (Intel NUC)  │
           └──────────────┘
                  │ Subscribe
                  ↓
      ┌───────────────────────┐
      │  php spark             │
      │  mqtt:subscribe        │
      └───────────────────────┘
                  │
                  ↓ Parse JSON & Save
      ┌───────────────────────┐
      │   MySQL Database      │
      │   monitoring_kolam    │
      └───────────────────────┘
                  │
                  ↓ Read Data
      ┌───────────────────────┐
      │   Dashboard Web UI    │
      │   localhost:8080      │
      └───────────────────────┘
```

---

## 🧪 Testing

### Test 1: Menggunakan mosquitto_pub

```bash
# Install mosquitto-clients dulu jika belum ada

# Test kirim data (ganti IP broker)
mosquitto_pub -h localhost -t kolam/sensors/data -m '{"sensors":{"ph":7.2,"tds":450,"turb":12.1}}'
```

### Test 2: Menggunakan MQTTX (GUI)

1. Download: https://mqttx.app/
2. Connect ke broker: `mqtt://localhost:1883`
3. Publish ke topic: `kolam/sensors/data`
4. Payload:
   ```json
   {"sensors":{"ph":7.2,"tds":450,"turb":12.1,"tank":"80%"}}
   ```

### Output di Subscriber

Ketika data diterima:
```
========================================
📥 Message received at 2026-01-05 17:42:00
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

## 🔌 ESP32 Integration

### Kode ESP32 Minimal

```cpp
#include <WiFi.h>
#include <PubSubClient.h>

const char* mqtt_server = "192.168.1.100"; // IP Intel NUC
const char* mqtt_topic = "kolam/sensors/data";

WiFiClient espClient;
PubSubClient client(espClient);

void kirimData() {
  String json = "{\"sensors\":{";
  json += "\"ph\":" + String(bacaPH()) + ",";
  json += "\"tds\":" + String(bacaTDS()) + ",";
  json += "\"turb\":" + String(bacaTurb());
  json += "}}";
  
  client.publish(mqtt_topic, json.c_str());
}
```

**Lihat:** `MQTT_GUIDE.md` untuk kode lengkap

---

## 📁 File-File MQTT

| File | Fungsi |
|------|--------|
| `app/Libraries/MqttHandler.php` | MQTT Handler class |
| `app/Commands/MqttSubscribe.php` | CLI Command |
| `.env` | Konfigurasi MQTT broker |
| `MQTT_GUIDE.md` | Panduan lengkap MQTT |

---

## 🎯 MQTT Topics

| Topic | Publisher | Subscriber | Fungsi |
|-------|-----------|------------|--------|
| `kolam/sensors/data` | ESP32 | PHP Server | Data sensor |
| `kolam/control` | PHP Server | ESP32 | Perintah kontrol |
| `kolam/status` | ESP32 | PHP Server | Status sistem |

---

## ⚙️ Konfigurasi Intel NUC

### Install Mosquitto Broker (di Intel NUC)

```bash
# Ubuntu/Debian
sudo apt-get update
sudo apt-get install mosquitto mosquitto-clients

# Start service
sudo systemctl start mosquitto
sudo systemctl enable mosquitto

# Check status
sudo systemctl status mosquitto
```

### Konfigurasi Mosquitto

Edit `/etc/mosquitto/mosquitto.conf`:
```
listener 1883
allow_anonymous true
```

Restart:
```bash
sudo systemctl restart mosquitto
```

### Test dari Intel NUC

```bash
# Subscribe test
mosquitto_sub -h localhost -t kolam/sensors/data

# Publish test (terminal lain)
mosquitto_pub -h localhost -t kolam/sensors/data -m '{"sensors":{"ph":7.0}}'
```

---

## 🔧 Troubleshooting

### Error: Connection refused

**Solusi:**
1. Cek broker running: `sudo systemctl status mosquitto`
2. Cek firewall: `sudo ufw allow 1883`
3. Test ping ke IP broker

### Data tidak masuk database

**Solusi:**
1. Pastikan subscriber running
2. Cek format JSON benar
3. Lihat log: `writable/logs/`

### Subscriber berhenti

**Solusi:**
- Restart command: `php spark mqtt:subscribe`
- Cek error di terminal
- Setup auto-restart (lihat MQTT_GUIDE.md)

---

## ✨ Fitur Auto-Processing

Saat data diterima via MQTT:

1. ✅ **Auto-save** ke table `logs` (raw JSON)
2. ✅ **Auto-save** ke table `sensors` (parsed data)
3. ✅ **Auto-save** ke table `water_parameters` (jika ada temp/DO)
4. ✅ **Auto-check** parameter vs setpoint
5. ✅ **Auto-alert** jika ada parameter abnormal → table `notifications`
6. ✅ **Auto-update** device status (jika ada actuator data)

---

## 📊 Monitor Data

### Via Command Line (SQL)

```sql
USE monitoring_kolam;

-- Data terbaru
SELECT * FROM sensors ORDER BY created_at DESC LIMIT 10;

-- Alert yang belum dibaca
SELECT * FROM notifications WHERE is_read = 0;

-- Status device
SELECT * FROM device_status;
```

### Via Dashboard Web

```
http://localhost:8080/dashboard              → Real-time data
http://localhost:8080/dashboard/grafik       → Grafik
http://localhost:8080/dashboard/notifikasi   → Alert
```

---

## 🎉 STATUS FINAL

✅ **MQTT Library** - Installed  
✅ **MQTT Handler** - Ready  
✅ **CLI Command** - Available  
✅ **Database** - Integrated  
✅ **Auto-Alert** - Active  
✅ **Documentation** - Complete  

---

## 🚀 Next Steps

### 1. Update .env dengan IP Intel NUC
```env
MQTT_BROKER = 192.168.1.XXX  # IP Intel NUC Anda
```

### 2. Jalankan Subscriber
```bash
php spark mqtt:subscribe
```

### 3. Setup ESP32
- Install library PubSubClient
- Upload kode dari MQTT_GUIDE.md
- Test publish data

### 4. Monitor
- Dashboard: http://localhost:8080
- Lihat data real-time
- Cek alert otomatis

---

**Sistem siap menerima data dari sensor via MQTT!** 📡🚀

**Dokumentasi:**
- `MQTT_GUIDE.md` - Panduan lengkap
- `API_DOCUMENTATION.md` - API reference
- `SETUP_COMPLETE.md` - Setup summary

---

*Setup completed: 2026-01-05 17:42*  
*MQTT Integration: READY ✓*
