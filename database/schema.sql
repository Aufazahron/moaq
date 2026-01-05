-- ============================================
-- SISTEM MONITORING AIR KOLAM
-- Database Schema MySQL
-- ============================================

-- Buat database baru
CREATE DATABASE IF NOT EXISTS monitoring_kolam
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE monitoring_kolam;

-- ============================================
-- Tabel 1: water_parameters
-- Menyimpan data sensor parameter air
-- ============================================
CREATE TABLE IF NOT EXISTS water_parameters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    temperature DECIMAL(5,2) NOT NULL COMMENT 'Suhu air (°C)',
    ph DECIMAL(4,2) NOT NULL COMMENT 'Tingkat keasaman air (pH)',
    dissolved_oxygen DECIMAL(5,2) NOT NULL COMMENT 'Kadar oksigen terlarut (mg/L)',
    turbidity DECIMAL(6,2) NOT NULL COMMENT 'Kekeruhan air (NTU)',
    tds DECIMAL(7,2) NOT NULL COMMENT 'Total padatan terlarut (ppm)',
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_timestamp (timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Data sensor parameter air kolam';

-- ============================================
-- Tabel 2: setpoints
-- Menyimpan konfigurasi threshold/batas parameter
-- ============================================
CREATE TABLE IF NOT EXISTS setpoints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parameter_name VARCHAR(50) NOT NULL COMMENT 'Nama parameter (temp, ph, do, dll)',
    min_value DECIMAL(10,2) COMMENT 'Nilai minimum (batas bawah)',
    max_value DECIMAL(10,2) COMMENT 'Nilai maximum (batas atas)',
    target_value DECIMAL(10,2) COMMENT 'Nilai target ideal',
    fish_type VARCHAR(50) DEFAULT 'general' COMMENT 'Jenis ikan (lele, nila, dll)',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_param_fish (parameter_name, fish_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Konfigurasi threshold parameter air';

-- ============================================
-- Tabel 3: notifications
-- Menyimpan riwayat alert dan notifikasi
-- ============================================
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('info', 'warning', 'danger', 'safe') DEFAULT 'info' COMMENT 'Jenis notifikasi',
    title VARCHAR(255) NOT NULL COMMENT 'Judul notifikasi',
    message TEXT NOT NULL COMMENT 'Isi pesan notifikasi',
    is_read BOOLEAN DEFAULT FALSE COMMENT 'Status dibaca atau belum',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at),
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Notifikasi dan alert sistem';

-- ============================================
-- Tabel 4: device_status
-- Menyimpan status aktuator/perangkat
-- ============================================
CREATE TABLE IF NOT EXISTS device_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_name VARCHAR(100) NOT NULL COMMENT 'Nama perangkat (aerator, feeder, dll)',
    status ENUM('on', 'off', 'error') DEFAULT 'off' COMMENT 'Status perangkat',
    last_command TEXT COMMENT 'Command terakhir yang dikirim',
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_device (device_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Status perangkat aktuator';

-- ============================================
-- Tabel 5: control_logs
-- Log semua perintah kontrol yang dikirim
-- ============================================
CREATE TABLE IF NOT EXISTS control_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_name VARCHAR(100) NOT NULL COMMENT 'Nama perangkat',
    command VARCHAR(50) NOT NULL COMMENT 'Command yang dikirim (on/off)',
    user_id INT COMMENT 'ID user yang mengirim command (opsional)',
    executed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    execution_status ENUM('success', 'failed', 'pending') DEFAULT 'pending' COMMENT 'Status eksekusi',
    notes TEXT COMMENT 'Catatan tambahan',
    INDEX idx_executed_at (executed_at),
    INDEX idx_device (device_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Log riwayat kontrol perangkat';

-- ============================================
-- Insert Data Default Setpoints
-- ============================================
INSERT INTO setpoints (parameter_name, min_value, max_value, target_value, fish_type) VALUES
-- Setpoint untuk Lele
('temperature', 26, 30, 28, 'lele'),
('ph', 6.5, 7.5, 7.0, 'lele'),
('dissolved_oxygen', 5.0, NULL, 6.0, 'lele'),
('turbidity', NULL, 150, 100, 'lele'),

-- Setpoint untuk Nila
('temperature', 25, 30, 27, 'nila'),
('ph', 6.5, 8.5, 7.5, 'nila'),
('dissolved_oxygen', 5.0, NULL, 6.0, 'nila'),
('turbidity', NULL, 50, 30, 'nila'),

-- Setpoint untuk Gurame
('temperature', 25, 30, 27, 'gurame'),
('ph', 6.5, 8.0, 7.2, 'gurame'),
('dissolved_oxygen', 5.0, NULL, 6.0, 'gurame'),
('turbidity', NULL, 25, 15, 'gurame'),

-- Setpoint General (default)
('temperature', 26, 30, 28, 'general'),
('ph', 6.5, 7.5, 7.0, 'general'),
('dissolved_oxygen', 5.0, NULL, 6.0, 'general'),
('turbidity', NULL, 50, 30, 'general'),
('tds', NULL, 1000, 500, 'general');

-- ============================================
-- Insert Data Default Device Status
-- ============================================
INSERT INTO device_status (device_name, status) VALUES
('aerator', 'off'),
('feeder', 'off'),
('water_pump', 'off'),
('heater', 'off');

-- ============================================
-- Insert Sample Notification
-- ============================================
INSERT INTO notifications (type, title, message, is_read) VALUES
('safe', 'Sistem Online', 'Sistem Monitoring Air Kolam berhasil diaktifkan', FALSE),
('info', 'Selamat Datang', 'Selamat menggunakan Sistem Monitoring Air Kolam berbasis IoT', FALSE);

-- ============================================
-- View untuk data terbaru
-- ============================================
CREATE OR REPLACE VIEW latest_parameters AS
SELECT * FROM water_parameters
ORDER BY timestamp DESC
LIMIT 1;

-- ============================================
-- Stored Procedure untuk cek threshold
-- ============================================
DELIMITER //

CREATE PROCEDURE check_water_parameters()
BEGIN
    DECLARE v_temp DECIMAL(5,2);
    DECLARE v_ph DECIMAL(4,2);
    DECLARE v_do DECIMAL(5,2);
    
    -- Ambil data terbaru
    SELECT temperature, ph, dissolved_oxygen
    INTO v_temp, v_ph, v_do
    FROM water_parameters
    ORDER BY timestamp DESC
    LIMIT 1;
    
    -- Cek suhu
    IF v_temp < 26 OR v_temp > 30 THEN
        INSERT INTO notifications (type, title, message)
        VALUES ('warning', 'Peringatan Suhu', CONCAT('Suhu air ', v_temp, '°C di luar batas normal (26-30°C)'));
    END IF;
    
    -- Cek pH
    IF v_ph < 6.5 OR v_ph > 7.5 THEN
        INSERT INTO notifications (type, title, message)
        VALUES ('warning', 'Peringatan pH', CONCAT('pH air ', v_ph, ' di luar batas normal (6.5-7.5)'));
    END IF;
    
    -- Cek DO
    IF v_do < 5.0 THEN
        INSERT INTO notifications (type, title, message)
        VALUES ('danger', 'Peringatan Oksigen', CONCAT('Kadar oksigen terlarut ', v_do, ' mg/L terlalu rendah (minimal 5.0 mg/L)'));
    END IF;
END //

DELIMITER ;

-- ============================================
-- Trigger untuk auto-check threshold
-- ============================================
DELIMITER //

CREATE TRIGGER after_insert_water_params
AFTER INSERT ON water_parameters
FOR EACH ROW
BEGIN
    CALL check_water_parameters();
END //

DELIMITER ;

-- ============================================
-- Selesai
-- ============================================
SELECT 'Database Sistem Monitoring Air Kolam berhasil dibuat!' AS status;
