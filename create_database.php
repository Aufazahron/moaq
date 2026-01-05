<?php

/**
 * Script untuk membuat database monitoring_kolam secara otomatis
 * Jalankan dengan: php create_database.php
 */

echo "======================================\n";
echo "  DATABASE SETUP - Monitoring Kolam  \n";
echo "======================================\n\n";

// Konfigurasi database
$host = 'localhost';
$username = 'root';
$password = ''; // Sesuaikan dengan password MySQL Anda
$dbname = 'monitoring_kolam';

try {
    // Koneksi tanpa database terlebih dahulu
    echo "1. Connecting to MySQL server...\n";
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "   ✓ Connected to MySQL\n\n";

    // Drop database jika sudah ada (optional, hapus jika tidak ingin reset)
    echo "2. Checking if database exists...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `$dbname`");
    echo "   ✓ Cleaned up old database (if exists)\n\n";

    // Buat database baru
    echo "3. Creating database '$dbname'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` 
                DEFAULT CHARACTER SET utf8mb4 
                COLLATE utf8mb4_general_ci");
    echo "   ✓ Database '$dbname' created\n\n";

    // Gunakan database yang baru dibuat
    $pdo->exec("USE `$dbname`");

    // ===== TABEL 1: LOGS =====
    echo "4. Creating table 'logs'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `logs` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
            `created_at` datetime DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "   ✓ Table 'logs' created\n";

    // ===== TABEL 2: SENSORS =====
    echo "5. Creating table 'sensors'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `sensors` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `ph` float DEFAULT NULL COMMENT 'pH air (6.5-8.5)',
            `tds` float DEFAULT NULL COMMENT 'Total Dissolved Solids dalam ppm',
            `turb` float DEFAULT NULL COMMENT 'Turbidity/Kekeruhan dalam NTU',
            `tank` varchar(20) DEFAULT NULL COMMENT 'Level tangki air (%)',
            `chamber` varchar(20) DEFAULT NULL COMMENT 'Level chamber (%)',
            `created_at` datetime DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "   ✓ Table 'sensors' created\n";

    // ===== TABEL 3: WATER_PARAMETERS =====
    echo "6. Creating table 'water_parameters'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `water_parameters` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `temperature` decimal(5,2) DEFAULT NULL COMMENT 'Suhu air dalam °C',
            `ph` decimal(4,2) DEFAULT NULL COMMENT 'pH air',
            `dissolved_oxygen` decimal(5,2) DEFAULT NULL COMMENT 'Oksigen terlarut dalam mg/L',
            `turbidity` decimal(6,2) DEFAULT NULL COMMENT 'Kekeruhan dalam NTU',
            `tds` decimal(7,2) DEFAULT NULL COMMENT 'Total padatan terlarut dalam ppm',
            `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_timestamp` (`timestamp`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "   ✓ Table 'water_parameters' created\n";

    // ===== TABEL 4: ACTUATORS =====
    echo "7. Creating table 'actuators'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `actuators` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `sol_in` tinyint(1) DEFAULT 0 COMMENT 'Solenoid Valve Input (0=OFF, 1=ON)',
            `sol_ch` tinyint(1) DEFAULT 0 COMMENT 'Solenoid Valve Chamber (0=OFF, 1=ON)',
            `sol_drain` tinyint(1) DEFAULT 0 COMMENT 'Solenoid Valve Drain (0=OFF, 1=ON)',
            `pump_in` tinyint(1) DEFAULT 0 COMMENT 'Pompa Input (0=OFF, 1=ON)',
            `pump_out` tinyint(1) DEFAULT 0 COMMENT 'Pompa Output (0=OFF, 1=ON)',
            `mixer` tinyint(1) DEFAULT 0 COMMENT 'Mixer/Pengaduk (0=OFF, 1=ON)',
            `aerator` tinyint(1) DEFAULT 0 COMMENT 'Aerator (0=OFF, 1=ON)',
            `feeder` tinyint(1) DEFAULT 0 COMMENT 'Auto Feeder (0=OFF, 1=ON)',
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "   ✓ Table 'actuators' created\n";

    // ===== TABEL 5: DEVICE_STATUS =====
    echo "8. Creating table 'device_status'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `device_status` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `device_name` varchar(100) NOT NULL COMMENT 'Nama perangkat',
            `status` varchar(20) DEFAULT 'off' COMMENT 'Status: on, off, error',
            `last_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `device_name` (`device_name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "   ✓ Table 'device_status' created\n";

    // ===== TABEL 6: NOTIFICATIONS =====
    echo "9. Creating table 'notifications'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `notifications` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `type` varchar(20) DEFAULT 'info' COMMENT 'Jenis: info, warning, danger',
            `message` text NOT NULL COMMENT 'Isi pesan',
            `is_read` tinyint(1) DEFAULT 0 COMMENT 'Status dibaca (0=belum, 1=sudah)',
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_created_at` (`created_at`),
            KEY `idx_is_read` (`is_read`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "   ✓ Table 'notifications' created\n";

    // ===== TABEL 7: SETPOINTS =====
    echo "10. Creating table 'setpoints'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `setpoints` (
            `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `fish_type` varchar(100) NOT NULL COMMENT 'Jenis ikan',
            `temp_min` decimal(5,2) DEFAULT NULL COMMENT 'Suhu minimum (°C)',
            `temp_max` decimal(5,2) DEFAULT NULL COMMENT 'Suhu maximum (°C)',
            `ph_min` decimal(4,2) DEFAULT NULL COMMENT 'pH minimum',
            `ph_max` decimal(4,2) DEFAULT NULL COMMENT 'pH maximum',
            `do_min` decimal(5,2) DEFAULT NULL COMMENT 'Dissolved Oxygen minimum (mg/L)',
            `turbidity_max` decimal(6,2) DEFAULT NULL COMMENT 'Turbidity maximum (NTU)',
            `tds_max` decimal(7,2) DEFAULT NULL COMMENT 'TDS maximum (ppm)',
            `is_active` tinyint(1) DEFAULT 0 COMMENT 'Set-point yang sedang aktif',
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "   ✓ Table 'setpoints' created\n";

    // ===== TABEL 8: MIGRATIONS =====
    echo "11. Creating table 'migrations'...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `migrations` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `version` varchar(255) NOT NULL,
            `class` varchar(255) NOT NULL,
            `group` varchar(255) NOT NULL,
            `namespace` varchar(255) NOT NULL,
            `time` int(11) NOT NULL,
            `batch` int(11) UNSIGNED NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    echo "   ✓ Table 'migrations' created\n\n";

    // ===== INSERT DATA DEFAULT =====
    echo "12. Inserting default data...\n";

    // Default device status
    $pdo->exec("
        INSERT INTO `device_status` (`device_name`, `status`) VALUES
        ('aerator', 'off'),
        ('feeder', 'off'),
        ('pump_in', 'off'),
        ('pump_out', 'off'),
        ('mixer', 'off'),
        ('sol_in', 'off'),
        ('sol_ch', 'off'),
        ('sol_drain', 'off')
        ON DUPLICATE KEY UPDATE `device_name`=VALUES(`device_name`)
    ");
    echo "   ✓ Device status defaults inserted\n";

    // Default setpoints
    $pdo->exec("
        INSERT INTO `setpoints` (`fish_type`, `temp_min`, `temp_max`, `ph_min`, `ph_max`, `do_min`, `turbidity_max`, `tds_max`, `is_active`) VALUES
        ('Lele', 25.0, 32.0, 6.5, 8.5, 5.0, 50.0, 1000.0, 1),
        ('Nila', 25.0, 30.0, 6.5, 8.0, 5.0, 40.0, 800.0, 0),
        ('Gurame', 24.0, 30.0, 6.5, 8.5, 4.0, 45.0, 900.0, 0),
        ('Patin', 26.0, 32.0, 6.5, 8.0, 5.0, 50.0, 1000.0, 0),
        ('Mas', 24.0, 28.0, 6.5, 8.0, 5.0, 40.0, 800.0, 0)
        ON DUPLICATE KEY UPDATE `fish_type`=VALUES(`fish_type`)
    ");
    echo "   ✓ Setpoints for fish types inserted\n\n";

    echo "======================================\n";
    echo "  ✓ DATABASE SETUP COMPLETED!       \n";
    echo "======================================\n\n";

    echo "Database Information:\n";
    echo "  Database Name: $dbname\n";
    echo "  Total Tables: 8\n";
    echo "  - logs\n";
    echo "  - sensors\n";
    echo "  - water_parameters\n";
    echo "  - actuators\n";
    echo "  - device_status\n";
    echo "  - notifications\n";
    echo "  - setpoints\n";
    echo "  - migrations\n\n";

    echo "Default Data:\n";
    echo "  - 8 device statuses\n";
    echo "  - 5 fish type setpoints\n";
    echo "  - Active setpoint: Lele\n\n";

    echo "Next Steps:\n";
    echo "  1. Update .env file with database config\n";
    echo "  2. Run: php spark serve\n";
    echo "  3. Access: http://localhost:8080\n";
    echo "  4. Test API: http://localhost:8080/api/sensor/latest\n\n";

} catch (PDOException $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n\n";
    echo "Troubleshooting:\n";
    echo "  1. Make sure MySQL/MariaDB is running\n";
    echo "  2. Check username and password\n";
    echo "  3. Verify MySQL port (default: 3306)\n\n";
    exit(1);
}
