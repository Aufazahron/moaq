<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 * Routes untuk Sistem Monitoring Air Kolam
 */

// ===== HALAMAN UTAMA → DASHBOARD =====
$routes->get('/', 'Dashboard::index');

// ===== DASHBOARD MONITORING =====
$routes->get('dashboard', 'Dashboard::index');
$routes->get('dashboard/setpoint', 'Dashboard::setpoint');
$routes->get('dashboard/manual-control', 'Dashboard::manualControl');
$routes->get('dashboard/grafik', 'Dashboard::grafik');
$routes->get('dashboard/notifikasi', 'Dashboard::notifikasi');

// ===== API ENDPOINTS =====
// API untuk sensor ESP32
$routes->post('api/sensor/receive', 'Api\Sensor::receive');
$routes->get('api/sensor/latest', 'Api\Sensor::latest');
$routes->post('api/sensor/control', 'Api\Sensor::control');
