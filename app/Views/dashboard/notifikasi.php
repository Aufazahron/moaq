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

        .notification-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
        }

        .notification-item {
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: 12px;
            border-left: 4px solid;
            background: #f9fafb;
            transition: all 0.3s;
        }

        .notification-item:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .notification-item.safe {
            border-left-color: #10b981;
            background: #f9fafb;
        }

        .notification-item.warning {
            border-left-color: #f59e0b;
            background: #f9fafb;
        }

        .notification-item.critical {
            border-left-color: #ef4444;
            background: #f9fafb;
        }

        .notification-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.75rem;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .notification-item.safe .notification-icon {
            background: #d1fae5;
            color: #059669;
        }

        .notification-item.warning .notification-icon {
            background: #fef3c7;
            color: #d97706;
        }

        .notification-item.critical .notification-icon {
            background: #fee2e2;
            color: #dc2626;
        }

        .notification-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark);
            flex: 1;
        }

        .notification-time {
            font-size: 0.875rem;
            color: var(--gray);
        }

        .notification-message {
            color: var(--gray);
            font-size: 0.9375rem;
            margin-left: 3.5rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gray);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }

            .notification-card {
                padding: 1.5rem;
            }

            .notification-message {
                margin-left: 0;
                margin-top: 0.5rem;
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
                    <div style="font-size: 0.75rem; opacity: 0.8;">Notifikasi Sistem</div>
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
            <a href="<?= base_url('dashboard/manual-control') ?>" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Kontrol Manual</span>
            </a>
            <a href="<?= base_url('dashboard/grafik') ?>" class="nav-item">
                <i class="fas fa-chart-line"></i>
                <span>Grafik</span>
            </a>
            <a href="<?= base_url('dashboard/notifikasi') ?>" class="nav-item active">
                <i class="fas fa-bell"></i>
                <span>Notifikasi</span>
            </a>
        </div>
    </nav>

    <main class="main-container">
        <h1 class="page-title">Notifikasi Sistem</h1>

        <div class="notification-card">
            <?php if (empty($notifications)): ?>
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--dark);">Tidak Ada Notifikasi</h3>
                    <p>Sistem berjalan normal, tidak ada notifikasi saat ini.</p>
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?= esc($notification['type']) ?>">
                        <div class="notification-header">
                            <div class="notification-icon">
                                <?php if ($notification['type'] === 'critical'): ?>
                                    <i class="fas fa-exclamation-triangle"></i>
                                <?php elseif ($notification['type'] === 'warning'): ?>
                                    <i class="fas fa-exclamation-circle"></i>
                                <?php else: ?>
                                    <i class="fas fa-check-circle"></i>
                                <?php endif; ?>
                            </div>
                            <div class="notification-title"><?= esc($notification['title']) ?></div>
                            <div class="notification-time">
                                <?= date('d M Y, H:i', strtotime($notification['time'])) ?>
                            </div>
                        </div>
                        <div class="notification-message">
                            <?= esc($notification['message']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

