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
            <a href="<?= base_url('dashboard/setpoint') ?>" class="nav-item">
                <i class="fas fa-sliders-h"></i>
                <span>Set-Point</span>
            </a>
            <a href="<?= base_url('dashboard/manual-control') ?>" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Kontrol Manual</span>
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

        <!-- Stats Grid -->
        <div class="stats-grid">
            <!-- Temperature -->
            <div class="stat-card">
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
            </div>

            <!-- pH -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">pH Air</div>
                    <div class="stat-icon ph">
                        <i class="fas fa-flask"></i>
                    </div>
                </div>
                <div class="stat-value">
                    <?= number_format($parameters['ph'], 1) ?><span class="stat-unit">pH</span>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar ph" style="width: <?= min(100, (($parameters['ph'] - 4) / (9 - 4)) * 100) ?>%"></div>
                </div>
            </div>

            <!-- Dissolved Oxygen -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Oksigen Terlarut</div>
                    <div class="stat-icon do">
                        <i class="fas fa-wind"></i>
                    </div>
                </div>
                <div class="stat-value">
                    <?= number_format($parameters['dissolved_oxygen'], 1) ?><span class="stat-unit">mg/L</span>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar do" style="width: <?= min(100, ($parameters['dissolved_oxygen'] / 10) * 100) ?>%"></div>
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
                <div class="stat-value">
                    <?= number_format($parameters['turbidity'], 1) ?><span class="stat-unit">NTU</span>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar ntu" style="width: <?= min(100, ($parameters['turbidity'] / 50) * 100) ?>%"></div>
                </div>
            </div>

            <!-- TDS -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Total Dissolved Solids</div>
                    <div class="stat-icon tds">
                        <i class="fas fa-tint"></i>
                    </div>
                </div>
                <div class="stat-value">
                    <?= number_format($parameters['tds'], 0) ?><span class="stat-unit">ppm</span>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar tds" style="width: <?= min(100, ($parameters['tds'] / 2000) * 100) ?>%"></div>
                </div>
            </div>

            <!-- Feed Count -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-label">Pakan Hari Ini</div>
                    <div class="stat-icon feed">
                        <i class="fas fa-utensils"></i>
                    </div>
                </div>
                <div class="stat-value">
                    <?= $parameters['feed_count'] ?><span class="stat-unit">x</span>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar" style="width: 0%; background: #e5e7eb;"></div>
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
                <div class="stat-value" style="color: #059669;">
                    <?= esc($parameters['system_status']) ?>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar" style="width: 100%; background: #10b981;"></div>
                </div>
            </div>
        </div>
    </main>

    <script>
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
    </script>
</body>
</html>

