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

        .control-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
        }

        .control-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border);
        }

        .control-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .control-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #dbeafe;
            color: #2563eb;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 64px;
            height: 36px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: .4s;
            border-radius: 36px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 28px;
            width: 28px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        input:checked + .toggle-slider {
            background: #2563eb;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(28px);
        }

        .slider-group {
            margin-top: 1.5rem;
        }

        .slider-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .slider-label-text {
            font-weight: 600;
            color: var(--dark);
        }

        .slider-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2563eb;
        }

        .range-slider {
            width: 100%;
            height: 8px;
            border-radius: 4px;
            background: #e5e7eb;
            outline: none;
            -webkit-appearance: none;
        }

        .range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #2563eb;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.4);
            border: 2px solid white;
        }

        .range-slider::-moz-range-thumb {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #2563eb;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.4);
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }

            .control-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="top-header">
        <div class="header-content">
            <div class="logo-section">
                <img src="<?= base_url('assets/img/kolam-logo.png') ?>" alt="Sistem Monitoring Air Kolam">
                <div>
                    <div class="logo-text">Sistem Monitoring Air Kolam</div>
                    <div style="font-size: 0.75rem; opacity: 0.8;">Kontrol Manual Sistem</div>
                </div>
            </div>
        </div>
    </header>

    <nav class="nav-container">
        <div class="nav-menu">
            <a href="<?= base_url('dashboard') ?>" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= base_url('dashboard/setpoint') ?>" class="nav-item">
                <i class="fas fa-sliders-h"></i>
                <span>Set-Point</span>
            </a>
            <a href="<?= base_url('dashboard/manual-control') ?>" class="nav-item active">
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

    <main class="main-container">
        <h1 class="page-title">Kontrol Manual Perangkat</h1>

        <div class="control-card">
            <div class="control-header">
                <div class="control-title">
                    <div class="control-icon">
                        <i class="fas fa-fan"></i>
                    </div>
                    <span>Aerator</span>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="aerator-toggle" <?= $controls['aerator'] ? 'checked' : '' ?>>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <p style="color: var(--gray); font-size: 0.875rem;">Aktifkan atau nonaktifkan sistem aerator untuk meningkatkan kadar oksigen dalam air</p>
        </div>

        <div class="control-card">
            <div class="control-header">
                <div class="control-title">
                    <div class="control-icon">
                        <i class="fas fa-wind"></i>
                    </div>
                    <span>Kecepatan Blower</span>
                </div>
            </div>
            <div class="slider-group">
                <div class="slider-label">
                    <span class="slider-label-text">Kecepatan</span>
                    <span class="slider-value" id="blower-value"><?= $controls['blower_speed'] ?>%</span>
                </div>
                <input type="range" min="0" max="100" value="<?= $controls['blower_speed'] ?>" class="range-slider" id="blower-speed" oninput="updateBlowerValue()">
            </div>
        </div>

        <div class="control-card">
            <div class="control-header">
                <div class="control-title">
                    <div class="control-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <span>Dosing Pump</span>
                </div>
            </div>
            <div class="slider-group">
                <div class="slider-label">
                    <span class="slider-label-text">Kecepatan</span>
                    <span class="slider-value" id="dosing-value"><?= $controls['dosing_speed'] ?>%</span>
                </div>
                <input type="range" min="0" max="100" value="<?= $controls['dosing_speed'] ?>" class="range-slider" id="dosing-speed" oninput="updateDosingValue()">
            </div>
        </div>
    </main>

    <script>
        function updateBlowerValue() {
            const value = document.getElementById('blower-speed').value;
            document.getElementById('blower-value').textContent = value + '%';
            console.log('Blower speed:', value + '%');
        }

        function updateDosingValue() {
            const value = document.getElementById('dosing-speed').value;
            document.getElementById('dosing-value').textContent = value + '%';
            console.log('Dosing speed:', value + '%');
        }

        document.getElementById('aerator-toggle').addEventListener('change', function() {
            console.log('Aerator:', this.checked ? 'ON' : 'OFF');
        });
    </script>
</body>
</html>

