-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 05 Jan 2026
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `monitoring_kolam`
--
CREATE DATABASE IF NOT EXISTS `monitoring_kolam` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `monitoring_kolam`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `logs`
-- Menyimpan semua raw data JSON dari sensor untuk keperluan debugging
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sensors`
-- Menyimpan data sensor yang sudah diparsing
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `water_parameters`
-- Tabel untuk parameter air yang lebih lengkap (untuk kompatibilitas dengan dokumentasi)
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `actuators`
-- Menyimpan status aktuator (relay, pompa, solenoid valve, dll)
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `device_status`
-- Menyimpan status terakhir dari setiap perangkat
--

CREATE TABLE IF NOT EXISTS `device_status` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `device_name` varchar(100) NOT NULL COMMENT 'Nama perangkat',
  `status` varchar(20) DEFAULT 'off' COMMENT 'Status: on, off, error',
  `last_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `device_name` (`device_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default devices
INSERT INTO `device_status` (`device_name`, `status`) VALUES
('aerator', 'off'),
('feeder', 'off'),
('pump_in', 'off'),
('pump_out', 'off'),
('mixer', 'off'),
('sol_in', 'off'),
('sol_ch', 'off'),
('sol_drain', 'off')
ON DUPLICATE KEY UPDATE `device_name`=VALUES(`device_name`);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
-- Menyimpan notifikasi dan peringatan sistem
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(20) DEFAULT 'info' COMMENT 'Jenis: info, warning, danger',
  `message` text NOT NULL COMMENT 'Isi pesan',
  `is_read` tinyint(1) DEFAULT 0 COMMENT 'Status dibaca (0=belum, 1=sudah)',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `setpoints`
-- Menyimpan set-point untuk parameter air berdasarkan jenis ikan
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default setpoints untuk berbagai jenis ikan
INSERT INTO `setpoints` (`fish_type`, `temp_min`, `temp_max`, `ph_min`, `ph_max`, `do_min`, `turbidity_max`, `tds_max`, `is_active`) VALUES
('Lele', 25.0, 32.0, 6.5, 8.5, 5.0, 50.0, 1000.0, 1),
('Nila', 25.0, 30.0, 6.5, 8.0, 5.0, 40.0, 800.0, 0),
('Gurame', 24.0, 30.0, 6.5, 8.5, 4.0, 45.0, 900.0, 0),
('Patin', 26.0, 32.0, 6.5, 8.0, 5.0, 50.0, 1000.0, 0),
('Mas', 24.0, 28.0, 6.5, 8.0, 5.0, 40.0, 800.0, 0)
ON DUPLICATE KEY UPDATE `fish_type`=VALUES(`fish_type`);

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
-- Untuk tracking migrasi database
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
