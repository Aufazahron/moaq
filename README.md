# Sistem Monitoring Air Kolam

Aplikasi web untuk monitoring kualitas air kolam secara real-time menggunakan CodeIgniter 4.

## 🚀 Quick Start

### Requirements
- PHP 8.1+
- MySQL/MariaDB
- Composer

### Installation

1. **Clone repository**
   ```bash
   git clone https://github.com/Aufazahron/moaq.git
   cd moaq
   git checkout web
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Setup database**
   ```bash
   php create_database.php
   ```

4. **Run server**
   ```bash
   php spark serve
   ```

5. **Access**
   ```
   http://localhost:8080
   ```

## 📊 Features

- ✅ Real-time monitoring parameter air
- ✅ Dashboard monitoring
- ✅ Grafik historis data
- ✅ Set-point configuration
- ✅ Manual control
- ✅ Notifikasi alert
- ✅ RESTful API

## 🔧 Configuration

Edit `.env` file:

```env
database.default.database = monitoring_kolam
database.default.username = root
database.default.password = 
```

## 📡 API Endpoints

### Sensor Data
```http
POST /api/sensor/receive
Content-Type: application/json

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

### Get Latest Data
```http
GET /api/sensor/latest
```

### Control Device
```http
POST /api/sensor/control
Content-Type: application/json

{
  "device": "aerator",
  "action": "on"
}
```

## 📁 Structure

```
├── app/
│   ├── Controllers/
│   │   ├── Dashboard.php
│   │   └── Api/Sensor.php
│   ├── Models/
│   │   ├── SensorModel.php
│   │   └── WaterParameterModel.php
│   └── Views/
│       └── dashboard/
├── database/
│   └── monitoring_kolam_complete.sql
├── public/
└── .env
```

## 📖 Documentation

Lihat folder `docs/` untuk dokumentasi lengkap:
- API Documentation
- Software Documentation
- MQTT Integration Guide

## 🤝 Contributing

Pull requests are welcome!

## 📄 License

MIT License
