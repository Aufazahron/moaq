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

        .content-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
            margin-bottom: 2rem;
        }

        .fish-tabs {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .fish-tab {
            padding: 0.75rem 1.5rem;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            color: var(--gray);
        }

        .fish-tab:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }

        .fish-tab.active {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        }

        .slider-group {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
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
            font-size: 1.125rem;
            font-weight: 700;
            color: #2563eb;
        }

        .slider-container {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .range-slider {
            flex: 1;
            height: 10px;
            border-radius: 5px;
            background: #e5e7eb;
            outline: none;
            -webkit-appearance: none;
            border: 1px solid #d1d5db;
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

        .range-slider:focus {
            background: #d1d5db;
        }

        .btn-submit {
            background: #2563eb;
            color: white;
            padding: 0.875rem 2rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        }

        .btn-submit:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }

        .btn-container {
            text-align: center;
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }

            .content-card {
                padding: 1.5rem;
            }

            .fish-tabs {
                overflow-x: auto;
                flex-wrap: nowrap;
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
                    <div style="font-size: 0.75rem; opacity: 0.8;">Pengaturan Set-Point</div>
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
            <a href="<?= base_url('dashboard/setpoint') ?>" class="nav-item active">
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

    <main class="main-container">
        <h1 class="page-title">Pengaturan Set-Point Parameter</h1>

        <div class="content-card">
            <div class="fish-tabs">
                <?php foreach ($fishTypes as $key => $fish): ?>
                <div class="fish-tab <?= $key === 'lele' ? 'active' : '' ?>" onclick="selectFishTab('<?= $key ?>')">
                    <i class="fas fa-fish"></i>
                    <span><?= esc($fish['name']) ?></span>
                </div>
                <?php endforeach; ?>
                <div class="fish-tab" onclick="selectFishTab('custom')">
                    <i class="fas fa-cog"></i>
                    <span>Custom</span>
                </div>
            </div>

            <div class="slider-group">
                <div class="slider-label">
                    <span class="slider-label-text">Suhu Air (°C)</span>
                    <span class="slider-value" id="temp-value"><?= $setpoints['temp']['min'] ?> - <?= $setpoints['temp']['max'] ?></span>
                </div>
                <div class="slider-container">
                    <input type="range" min="20" max="35" value="<?= $setpoints['temp']['min'] ?>" class="range-slider" id="temp-min" oninput="updateTempRange()">
                    <input type="range" min="20" max="35" value="<?= $setpoints['temp']['max'] ?>" class="range-slider" id="temp-max" oninput="updateTempRange()">
                </div>
            </div>

            <div class="slider-group">
                <div class="slider-label">
                    <span class="slider-label-text">pH Air</span>
                    <span class="slider-value" id="ph-value"><?= $setpoints['ph']['min'] ?> - <?= $setpoints['ph']['max'] ?></span>
                </div>
                <div class="slider-container">
                    <input type="range" min="4" max="9" step="0.1" value="<?= $setpoints['ph']['min'] ?>" class="range-slider" id="ph-min" oninput="updatePhRange()">
                    <input type="range" min="4" max="9" step="0.1" value="<?= $setpoints['ph']['max'] ?>" class="range-slider" id="ph-max" oninput="updatePhRange()">
                </div>
            </div>

            <div class="slider-group">
                <div class="slider-label">
                    <span class="slider-label-text">Oksigen Terlarut (mg/L)</span>
                    <span class="slider-value" id="do-value"><?= $setpoints['do'] ?></span>
                </div>
                <input type="range" min="1" max="10" step="0.1" value="<?= $setpoints['do'] ?>" class="range-slider" id="do-min" oninput="updateDoValue()">
            </div>

            <div class="slider-group">
                <div class="slider-label">
                    <span class="slider-label-text">Kekeruhan Maksimum (NTU)</span>
                    <span class="slider-value" id="ntu-value"><?= $setpoints['ntu'] ?></span>
                </div>
                <input type="range" min="0" max="50" step="1" value="<?= $setpoints['ntu'] ?>" class="range-slider" id="ntu-max" oninput="updateNtuValue()">
            </div>

            <div class="btn-container">
                <button class="btn-submit" onclick="submitFishType()">
                    <i class="fas fa-save"></i>
                    <span>Simpan Set-Point</span>
                </button>
            </div>
        </div>
    </main>

    <script>
        const fishSetpoints = <?= json_encode($fishTypes) ?>;
        let currentFishType = 'lele';

        function selectFishTab(fishType) {
            document.querySelectorAll('.fish-tab').forEach(t => t.classList.remove('active'));
            event.target.closest('.fish-tab').classList.add('active');
            currentFishType = fishType;
            
            if (fishType !== 'custom' && fishSetpoints[fishType]) {
                const setpoint = fishSetpoints[fishType];
                document.getElementById('temp-min').value = setpoint.temp[0];
                document.getElementById('temp-max').value = setpoint.temp[1];
                document.getElementById('ph-min').value = setpoint.ph[0];
                document.getElementById('ph-max').value = setpoint.ph[1];
                document.getElementById('do-min').value = setpoint.do;
                document.getElementById('ntu-max').value = setpoint.ntu;
                
                updateTempRange();
                updatePhRange();
                updateDoValue();
                updateNtuValue();
            }
        }

        function updateTempRange() {
            const min = document.getElementById('temp-min').value;
            const max = document.getElementById('temp-max').value;
            document.getElementById('temp-value').textContent = `${min} - ${max}`;
        }

        function updatePhRange() {
            const min = document.getElementById('ph-min').value;
            const max = document.getElementById('ph-max').value;
            document.getElementById('ph-value').textContent = `${min} - ${max}`;
        }

        function updateDoValue() {
            const value = document.getElementById('do-min').value;
            document.getElementById('do-value').textContent = value;
        }

        function updateNtuValue() {
            const value = document.getElementById('ntu-max').value;
            document.getElementById('ntu-value').textContent = value;
        }

        function submitFishType() {
            alert('Set-point berhasil disimpan! (Data dummy)');
        }
    </script>
</body>
</html>

