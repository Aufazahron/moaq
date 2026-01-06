<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #06b6d4;
            --dark: #1f2937;
            --light: #f9fafb;
            --gray: #6b7280;
            --border: #e5e7eb;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #e5e7eb;
            color: #1f2937;
            line-height: 1.6;
        }

        /* Header */
        .top-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-section img {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            object-fit: cover;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Navigation */
        .nav-container {
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            position: sticky;
            top: 73px;
            z-index: 99;
        }

        .nav-menu {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
        }

        .nav-item {
            padding: 1rem 1.5rem;
            text-decoration: none;
            color: var(--gray);
            font-weight: 500;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-item:hover, .nav-item.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        /* Main Content */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--dark);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            transition: all 0.3s;
            border: 1px solid #e5e7eb;
        }

        .stat-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
            transform: translateY(-1px);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--gray);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-icon.temp { background: #fef3c7; color: #d97706; }
        .stat-icon.ph { background: #dbeafe; color: #2563eb; }
        .stat-icon.do { background: #d1fae5; color: #059669; }
        .stat-icon.ntu { background: #e0e7ff; color: #6366f1; }
        .stat-icon.tds { background: #fce7f3; color: #db2777; }
        .stat-icon.feed { background: #fef3c7; color: #d97706; }
        .stat-icon.status { background: #d1fae5; color: #059669; }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .stat-unit {
            font-size: 1rem;
            color: var(--gray);
            font-weight: 400;
            margin-left: 0.25rem;
        }

        .stat-progress {
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 1rem;
        }

        .stat-progress-bar {
            height: 100%;
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        .stat-progress-bar.temp { background: #f59e0b; }
        .stat-progress-bar.ph { background: #2563eb; }
        .stat-progress-bar.do { background: #059669; }
        .stat-progress-bar.ntu { background: #6366f1; }
        .stat-progress-bar.tds { background: #db2777; }

        /* Hero Card */
        .hero-card {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border-radius: 20px;
            padding: 3rem;
            color: white;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .hero-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .hero-subtitle {
            font-size: 1.125rem;
            opacity: 0.9;
        }

        .hero-image {
            position: absolute;
            right: 2rem;
            bottom: 0;
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 16px;
            opacity: 0.3;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            .nav-container {
                display: none;
            }

            .nav-container.mobile-open {
                display: block;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .main-container {
                padding: 1rem;
            }

            .hero-card {
                padding: 2rem 1.5rem;
            }

            .hero-title {
                font-size: 1.75rem;
            }
        }

        /* Pulse Red Animation for Issue Highlight */
        @keyframes pulse-red {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4);
            }
            50% {
                box-shadow: 0 0 0 4px rgba(220, 38, 38, 0);
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="top-header">
        <div class="header-content">
            <div class="logo-section">
                <img src="<?= base_url('assets/img/kolam-logo.png') ?>" alt="Monitoring Air Kolam">
                <div>
                    <div class="logo-text">Monitoring Air Kolam</div>
                    <div style="font-size: 0.75rem; opacity: 0.8;">Monitoring Kualitas Air Real-time</div>
                </div>
            </div>
            <div class="status-badge">
                <span class="status-dot"></span>
                <span>Sistem Aktif</span>
            </div>
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="nav-container" id="navContainer">
        <div class="nav-menu">
            <a href="<?= base_url('dashboard') ?>" class="nav-item active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= base_url('dashboard/logs') ?>" class="nav-item">
                <i class="fas fa-list-alt"></i>
                <span>Logs</span>
            </a>
            <a href="<?= base_url('dashboard/history') ?>" class="nav-item">
                <i class="fas fa-history"></i>
                <span>Histori Data Sensor </span>
            </a>
            <a href="<?= base_url('dashboard/grafik') ?>" class="nav-item">
                <i class="fas fa-chart-line"></i>
                <span>Grafik</span>
            </a>
            <a href="<?= base_url('dashboard/notifikasi') ?>" class="nav-item">
                <i class="fas fa-bell"></i>
                <span>Notifikasi</span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-container">
        <h1 class="page-title">Dashboard Monitoring</h1>

        <!-- Hero Card -->
        <div class="hero-card">
            <div class="hero-content">
                <h2 class="hero-title">Selamat Datang di Sistem Monitoring Air Kolam</h2>
                <p class="hero-subtitle">Sistem monitoring kualitas air kolam berbasis IoT untuk budidaya ikan yang optimal</p>
            </div>
            <img src="<?= base_url('assets/img/kolam-bg.jpg') ?>" alt="Budidaya Ikan" class="hero-image">
        </div>

        <!-- Water Quality Status Card -->
        <?php if (isset($latestSensor) && $latestSensor): ?>
        <div style="background: white; border-radius: 20px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 2rem;">
                <div style="flex: 1; min-width: 250px;">
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem;">
                        <i class="fas fa-water"></i> Status Kualitas Air
                    </h3>
                    <p style="color: #6b7280; font-size: 0.875rem;">
                        Berdasarkan parameter pH, TDS, dan Kekeruhan
                    </p>
                </div>
                <?php
                // Generate water quality if not set
                $waterQuality = $latestSensor['water_quality'] ?? null;
                if ($waterQuality === null) {
                    $ph = floatval(trim($latestSensor['ph'] ?? '0', '"'));
                    $tds = floatval(trim($latestSensor['tds'] ?? '0', '"'));
                    $turb = floatval(trim($latestSensor['turb'] ?? '0', '"'));
                    
                    if ($ph > 0 && $tds > 0 && $turb > 0) {
                        $waterQuality = 'Bagus';
                        if ($ph < 6.5 || $ph > 8.0) {
                            $waterQuality = 'Kurang Bagus';
                        } elseif ($tds > 1000) {
                            $waterQuality = 'Kurang Bagus';
                        } elseif ($turb < 5 || $turb > 50) {
                            $waterQuality = 'Kurang Bagus';
                        }
                    }
                }
                ?>
                <div style="text-align: center;" id="water-quality-status-container">
                    <?php if ($waterQuality === 'Bagus'): ?>
                        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1.5rem 3rem; border-radius: 16px; box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);">
                            <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem;">
                                BAGUS
                            </div>
                            <div style="font-size: 0.875rem; opacity: 0.9;">
                                Kualitas Air Optimal
                            </div>
                        </div>
                    <?php elseif ($waterQuality === 'Kurang Bagus'): ?>
                        <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 1.5rem 3rem; border-radius: 16px; box-shadow: 0 8px 24px rgba(239, 68, 68, 0.3);">
                            <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem;">
                                KURANG BAGUS
                            </div>
                            <div style="font-size: 0.875rem; opacity: 0.9;">
                                Perlu Perhatian
                            </div>
                        </div>
                    <?php else: ?>
                        <div style="background: #F3F4F6; color: #6b7280; padding: 1.5rem 3rem; border-radius: 16px;">
                            <div style="font-size: 3rem; margin-bottom: 0.5rem;">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem;">
                                N/A
                            </div>
                            <div style="font-size: 0.875rem; opacity: 0.9;">
                                Data Belum Tersedia
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div style="flex: 1; min-width: 250px;">
                    <div style="background: #F9FAFB; padding: 1rem; border-radius: 12px;">
                        <div style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Data Terbaru:</div>
                        <?php
                        // Check for issues in latest sensor data
                        $phVal = floatval(trim($latestSensor['ph'] ?? '0', '"'));
                        $tdsVal = floatval(trim($latestSensor['tds'] ?? '0', '"'));
                        $turbVal = floatval(trim($latestSensor['turb'] ?? '0', '"'));
                        
                        $phIssue = '';
                        $tdsIssue = '';
                        $turbIssue = '';
                        
                        if ($phVal < 6.5) {
                            $phIssue = 'pH terlalu rendah (< 6.5). Standar: 6.5 - 8.0';
                        } elseif ($phVal > 8.0) {
                            $phIssue = 'pH terlalu tinggi (> 8.0). Standar: 6.5 - 8.0';
                        }
                        
                        if ($tdsVal > 1000) {
                            $tdsIssue = 'TDS terlalu tinggi (> 1000 ppm). Standar: ≤ 1000 ppm';
                        }
                        
                        if ($turbVal < 5) {
                            $turbIssue = 'Kekeruhan terlalu rendah (< 5 NTU). Standar: 5 - 50 NTU';
                        } elseif ($turbVal > 50) {
                            $turbIssue = 'Kekeruhan terlalu tinggi (> 50 NTU). Standar: 5 - 50 NTU';
                        }
                        ?>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.875rem;" id="latest-data-container">
                            <div id="latest-ph-container" <?= $phIssue ? 'onclick="showIssueDetail(\'pH\', \'' . esc($phIssue) . '\', \'' . esc($latestSensor['ph']) . '\')" style="background: #FEE2E2; color: #DC2626; padding: 0.5rem; border-radius: 6px; cursor: pointer; font-weight: 600; animation: pulse-red 2s infinite;"' : '' ?>>
                                <strong>pH:</strong> <span id="latest-ph-val"><?= $latestSensor['ph'] ?? 'N/A' ?></span>
                                <?php if ($phIssue): ?>
                                    <i class="fas fa-exclamation-circle" style="margin-left: 0.25rem;"></i>
                                <?php endif; ?>
                            </div>
                            <div id="latest-tds-container" <?= $tdsIssue ? 'onclick="showIssueDetail(\'TDS\', \'' . esc($tdsIssue) . '\', \'' . esc($latestSensor['tds']) . '\')" style="background: #FEE2E2; color: #DC2626; padding: 0.5rem; border-radius: 6px; cursor: pointer; font-weight: 600; animation: pulse-red 2s infinite;"' : '' ?>>
                                <strong>TDS:</strong> <span id="latest-tds-val"><?= $latestSensor['tds'] ?? 'N/A' ?></span> ppm
                                <?php if ($tdsIssue): ?>
                                    <i class="fas fa-exclamation-circle" style="margin-left: 0.25rem;"></i>
                                <?php endif; ?>
                            </div>
                            <div id="latest-turb-container" <?= $turbIssue ? 'onclick="showIssueDetail(\'Kekeruhan\', \'' . esc($turbIssue) . '\', \'' . esc($latestSensor['turb']) . '\')" style="background: #FEE2E2; color: #DC2626; padding: 0.5rem; border-radius: 6px; cursor: pointer; font-weight: 600; animation: pulse-red 2s infinite;"' : '' ?>>
                                <strong>Kekeruhan:</strong> <span id="latest-turb-val"><?= $latestSensor['turb'] ?? 'N/A' ?></span> NTU
                                <?php if ($turbIssue): ?>
                                    <i class="fas fa-exclamation-circle" style="margin-left: 0.25rem;"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <strong>Waktu:</strong> <span id="latest-time-val"><?= date('H:i', strtotime($latestSensor['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <!-- Temperature -->
            <!-- <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Suhu Air</div>
                    <div class="stat-icon temp">
                        <i class="fas fa-thermometer-half"></i>
                    </div>
                </div>
                <div class="stat-value">
                    <?= number_format($parameters['temperature'], 1) ?><span class="stat-unit">°C</span>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar temp" style="width: <?= min(100, (($parameters['temperature'] - 20) / (35 - 20)) * 100) ?>%"></div>
                </div>
            </div> -->

            <!-- pH -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">pH Air</div>
                    <div class="stat-icon ph">
                        <i class="fas fa-flask"></i>
                    </div>
                </div>
                <div class="stat-value" id="stat-val-ph">
                    <?= number_format($parameters['ph'], 1) ?><span class="stat-unit">pH</span>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar ph" id="stat-progress-ph" style="width: <?= min(100, (($parameters['ph'] - 4) / (9 - 4)) * 100) ?>%"></div>
                </div>
            </div>

            <!-- Turbidity -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Kekeruhan</div>
                    <div class="stat-icon ntu">
                        <i class="fas fa-tint"></i>
                    </div>
                </div>
                <div class="stat-value" id="stat-val-turb">
                    <?= number_format($parameters['turbidity'], 1) ?><span class="stat-unit">NTU</span>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar ntu" id="stat-progress-turb" style="width: <?= min(100, ($parameters['turbidity'] / 50) * 100) ?>%"></div>
                </div>
            </div>

            <!-- TDS -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Total Dissolved Solids</div>
                    <div class="stat-icon tds">
                        <i class="fas fa-water"></i>
                    </div>
                </div>
                <div class="stat-value" id="stat-val-tds">
                    <?= number_format($parameters['tds'], 0) ?><span class="stat-unit">ppm</span>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar tds" style="width: <?= min(100, ($parameters['tds'] / 2000) * 100) ?>%"></div>
                </div>
            </div>

            <!-- Tank Level -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Level Tangki</div>
                    <div class="stat-icon" style="background: #E0F2FE; color: #0284C7;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
                <div class="stat-value" id="stat-val-tank">
                    <?= esc($parameters['tank']) ?>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar" id="stat-progress-tank" style="width: <?= str_replace('%', '', $parameters['tank']) ?>%; background: #0284C7;"></div>
                </div>
            </div>

            <!-- Chamber Level -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Level Chamber</div>
                    <div class="stat-icon" style="background: #F3E8FF; color: #9333EA;">
                        <i class="fas fa-box-open"></i>
                    </div>
                </div>
                <div class="stat-value" id="stat-val-chamber">
                    <?= esc($parameters['chamber']) ?>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar" id="stat-progress-chamber" style="width: <?= str_replace('%', '', $parameters['chamber']) ?>%; background: #9333EA;"></div>
                </div>
            </div>

            <!-- Mixer Status -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Status Mixer</div>
                    <div class="stat-icon" style="background: <?= $parameters['mixer'] ? '#DCFCE7' : '#FEE2E2' ?>; color: <?= $parameters['mixer'] ? '#16A34A' : '#DC2626' ?>;">
                        <i class="fas fa-blender"></i>
                    </div>
                </div>
                <div class="stat-value" style="color: <?= $parameters['mixer'] ? '#16A34A' : '#DC2626' ?>;">
                    <?= $parameters['mixer'] ? 'ON' : 'OFF' ?>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar" style="width: 100%; background: <?= $parameters['mixer'] ? '#16A34A' : '#DC2626' ?>;"></div>
                </div>
            </div>

            <!-- System Status -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Status Sistem</div>
                    <div class="stat-icon status">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-value" id="stat-val-system" style="color: #059669;">
                    <?= esc($parameters['system_status']) ?>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar" style="width: 100%; background: #10b981;"></div>
                </div>
                <div style="font-size: 0.75rem; color: #9CA3AF; margin-top: 0.5rem; text-align: right;">
                    Update: <span id="stat-last-updated"><?= esc($parameters['last_updated']) ?></span>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal untuk Issue Detail -->
    <div id="issueModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; border-radius: 16px; padding: 2rem; max-width: 500px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: slideIn 0.3s;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.5rem; font-weight: 700; color: #DC2626; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span id="modalTitle">Parameter Bermasalah</span>
                </h3>
                <button onclick="closeIssueModal()" style="background: none; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer; padding: 0.5rem;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div style="background: #FEE2E2; border-left: 4px solid #DC2626; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <div style="font-size: 0.875rem; color: #991B1B; font-weight: 600; margin-bottom: 0.5rem;">
                    Nilai Saat Ini:
                </div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #DC2626;" id="modalValue">
                    -
                </div>
            </div>
            
            <div style="background: #F9FAFB; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <div style="font-size: 0.875rem; color: #6b7280; font-weight: 600; margin-bottom: 0.5rem;">
                    <i class="fas fa-info-circle"></i> Penjelasan:
                </div>
                <div style="color: #1f2937; line-height: 1.6;" id="modalReason">
                    -
                </div>
            </div>
            
            <div style="background: #DBEAFE; border-left: 4px solid #2563EB; padding: 1rem; border-radius: 8px;">
                <div style="font-size: 0.875rem; color: #1E40AF; font-weight: 600; margin-bottom: 0.5rem;">
                    <i class="fas fa-lightbulb"></i> Rekomendasi:
                </div>
                <div style="color: #1E3A8A; font-size: 0.875rem;" id="modalRecommendation">
                    Segera lakukan penyesuaian parameter untuk menjaga kualitas air optimal.
                </div>
            </div>
            
            <button onclick="closeIssueModal()" style="width: 100%; margin-top: 1.5rem; background: #DC2626; color: white; padding: 0.75rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.3s;">
                Tutup
            </button>
        </div>
    </div>

    <style>
        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>

    <script>
        function showIssueDetail(paramName, reason, value) {
            const modal = document.getElementById('issueModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalValue = document.getElementById('modalValue');
            const modalReason = document.getElementById('modalReason');
            const modalRecommendation = document.getElementById('modalRecommendation');
            
            // Set content
            modalTitle.textContent = paramName;
            modalValue.textContent = value + (paramName === 'pH' ? '' : (paramName === 'TDS' ? ' ppm' : ' NTU'));
            modalReason.textContent = reason;
            
            // Set recommendation based on parameter
            let recommendation = '';
            if (paramName === 'pH') {
                if (parseFloat(value) < 6.5) {
                    recommendation = 'Tambahkan bahan alkali (seperti kapur) untuk menaikkan pH air. Lakukan secara bertahap dan monitor perubahan pH.';
                } else {
                    recommendation = 'Tambahkan bahan asam (seperti cuka atau asam sitrat) untuk menurunkan pH air. Lakukan secara bertahap.';
                }
            } else if (paramName === 'TDS') {
                recommendation = 'Lakukan pergantian air sebagian (water change) untuk menurunkan TDS. Gunakan air bersih dengan TDS rendah.';
            } else if (paramName === 'Kekeruhan') {
                if (parseFloat(value) < 5) {
                    recommendation = 'Air terlalu jernih mungkin kurang nutrisi. Pertimbangkan menambah pakan atau pupuk organik.';
                } else {
                    recommendation = 'Tingkatkan filtrasi air dan kurangi pemberian pakan. Pertimbangkan menambah aerasi.';
                }
            }
            
            modalRecommendation.textContent = recommendation;
            
            // Show modal
            modal.style.display = 'flex';
        }
        
        function closeIssueModal() {
            const modal = document.getElementById('issueModal');
            modal.style.display = 'none';
        }
        
        // Close modal when clicking outside
        document.getElementById('issueModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeIssueModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeIssueModal();
            }
        });

        function toggleMobileMenu() {
            const nav = document.getElementById('navContainer');
            nav.classList.toggle('mobile-open');
        }

        // Set active nav item based on current page
        document.querySelectorAll('.nav-item').forEach(item => {
            if (item.href === window.location.href) {
                item.classList.add('active');
            }
        });

        // Realtime Update Polling
        function updateRealtimeData() {
            fetch('<?= base_url('dashboard/realtime') ?>')
                .then(response => response.json())
                .then(res => {
                    const latest = res.latest;
                    const params = res.parameters;
                    
                    if (latest) {
                        // Update Latest Data List
                        document.getElementById('latest-ph-val').textContent = latest.ph || 'N/A';
                        document.getElementById('latest-tds-val').textContent = latest.tds || 'N/A';
                        document.getElementById('latest-turb-val').textContent = latest.turb || 'N/A';
                        
                        const time = new Date(latest.created_at);
                        document.getElementById('latest-time-val').textContent = time.getHours().toString().padStart(2, '0') + ':' + time.getMinutes().toString().padStart(2, '0');
                        
                        // Calculate quality for status card update
                        const phVal = parseFloat(latest.ph || 0);
                        const tdsVal = parseFloat(latest.tds || 0);
                        const turbVal = parseFloat(latest.turb || 0);
                        
                        let waterQuality = 'Bagus';
                        let phIssue = '';
                        let tdsIssue = '';
                        let turbIssue = '';
                        
                        if (phVal < 6.5) { phIssue = 'pH terlalu rendah (< 6.5). Standar: 6.5 - 8.0'; waterQuality = 'Kurang Bagus'; }
                        else if (phVal > 8.0) { phIssue = 'pH terlalu tinggi (> 8.0). Standar: 6.5 - 8.0'; waterQuality = 'Kurang Bagus'; }
                        
                        if (tdsVal > 1000) { tdsIssue = 'TDS terlalu tinggi (> 1000 ppm). Standar: ≤ 1000 ppm'; waterQuality = 'Kurang Bagus'; }
                        if (turbVal < 5) { turbIssue = 'Kekeruhan terlalu rendah (< 5 NTU). Standar: 5 - 50 NTU'; waterQuality = 'Kurang Bagus'; }
                        else if (turbVal > 50) { turbIssue = 'Kekeruhan terlalu tinggi (> 50 NTU). Standar: 5 - 50 NTU'; waterQuality = 'Kurang Bagus'; }
                        
                        // Update container visibility & styles
                        updateLatestContainer('ph', phIssue, latest.ph);
                        updateLatestContainer('tds', tdsIssue, latest.tds);
                        updateLatestContainer('turb', turbIssue, latest.turb);
                        
                        // Update Status Card
                        const statusContainer = document.getElementById('water-quality-status-container');
                        if (waterQuality === 'Bagus') {
                            statusContainer.innerHTML = `
                                <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1.5rem 3rem; border-radius: 16px; box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);">
                                    <div style="font-size: 3rem; margin-bottom: 0.5rem;"><i class="fas fa-check-circle"></i></div>
                                    <div style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem;">BAGUS</div>
                                    <div style="font-size: 0.875rem; opacity: 0.9;">Kualitas Air Optimal</div>
                                </div>`;
                        } else {
                            statusContainer.innerHTML = `
                                <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 1.5rem 3rem; border-radius: 16px; box-shadow: 0 8px 24px rgba(239, 68, 68, 0.3);">
                                    <div style="font-size: 3rem; margin-bottom: 0.5rem;"><i class="fas fa-exclamation-triangle"></i></div>
                                    <div style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem;">KURANG BAGUS</div>
                                    <div style="font-size: 0.875rem; opacity: 0.9;">Perlu Perhatian</div>
                                </div>`;
                        }
                    }
                    
                    if (params) {
                        // Update Grid Stats
                        document.getElementById('stat-val-ph').innerHTML = parseFloat(params.ph).toFixed(1) + '<span class="stat-unit">pH</span>';
                        document.getElementById('stat-progress-ph').style.width = Math.min(100, ((params.ph - 4) / 5) * 100) + '%';
                        
                        document.getElementById('stat-val-turb').innerHTML = parseFloat(params.turbidity).toFixed(1) + '<span class="stat-unit">NTU</span>';
                        document.getElementById('stat-progress-turb').style.width = Math.min(100, (params.turbidity / 50) * 100) + '%';
                        
                        document.getElementById('stat-val-tds').innerHTML = parseFloat(params.tds).toFixed(0) + '<span class="stat-unit">ppm</span>';
                        
                        document.getElementById('stat-val-tank').textContent = params.tank;
                        document.getElementById('stat-progress-tank').style.width = params.tank.replace('%', '') + '%';
                        
                        document.getElementById('stat-val-chamber').textContent = params.chamber;
                        document.getElementById('stat-progress-chamber').style.width = params.chamber.replace('%', '') + '%';
                        
                        document.getElementById('stat-val-system').textContent = params.system_status;
                        document.getElementById('stat-last-updated').textContent = params.last_updated;
                    }
                })
                .catch(err => console.error('Realtime update failed:', err));
        }

        function updateLatestContainer(type, issue, val) {
            const container = document.getElementById('latest-' + type + '-container');
            const label = type === 'ph' ? 'pH' : (type === 'tds' ? 'TDS' : 'Kekeruhan');
            const unit = type === 'ph' ? '' : (type === 'tds' ? ' ppm' : ' NTU');
            
            if (issue) {
                container.setAttribute('onclick', `showIssueDetail('${label}', '${issue}', '${val}')`);
                container.style.cssText = "background: #FEE2E2; color: #DC2626; padding: 0.5rem; border-radius: 6px; cursor: pointer; font-weight: 600; animation: pulse-red 2s infinite;";
                container.innerHTML = `<strong>${label}:</strong> <span id="latest-${type}-val">${val}</span>${unit} <i class="fas fa-exclamation-circle" style="margin-left: 0.25rem;"></i>`;
            } else {
                container.removeAttribute('onclick');
                container.style.cssText = "";
                container.innerHTML = `<strong>${label}:</strong> <span id="latest-${type}-val">${val}</span>${unit}`;
            }
        }

        // Run Every 15 seconds
        setInterval(updateRealtimeData, 15000);
        // Also run once on load
        updateRealtimeData();
    </script>
</body>
</html>

