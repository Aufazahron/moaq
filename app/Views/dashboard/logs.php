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

        /* Log Card */
        .log-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            transition: all 0.3s;
        }

        .log-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }

        .log-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .log-id {
            font-weight: 600;
            color: var(--primary);
            font-size: 0.875rem;
        }

        .log-time {
            font-size: 0.875rem;
            color: var(--gray);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .log-status {
            margin-bottom: 1rem;
        }

        .badge-status {
            background: #DCFCE7;
            color: #16A34A;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
        }

        /* Expand Button */
        .expand-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.875rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .expand-btn:hover {
            background: var(--primary-dark);
        }

        /* Detail Content */
        .detail-content {
            display: none;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .detail-content.show {
            display: block;
        }

        .detail-section {
            margin-bottom: 1.5rem;
        }

        .detail-section:last-child {
            margin-bottom: 0;
        }

        .detail-section h4 {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            background: #f9fafb;
            border-radius: 8px;
            overflow: hidden;
        }

        .detail-table thead {
            background: #F3F4F6;
        }

        .detail-table th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            color: var(--dark);
            border-bottom: 2px solid #E5E7EB;
        }

        .detail-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #F3F4F6;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
            display: inline-block;
        }

        .badge-on {
            background: #DCFCE7;
            color: #16A34A;
        }

        .badge-off {
            background: #FEE2E2;
            color: #DC2626;
        }

        /* Pagination */
        .pagination-container {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .pagination-btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            color: var(--dark);
            background: white;
            border: 1px solid var(--border);
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .pagination-btn:hover:not(.disabled) {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .pagination-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-search {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--gray);
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--gray);
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

            .main-container {
                padding: 1rem;
            }

            .log-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .detail-table {
                font-size: 0.875rem;
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
            <a href="<?= base_url('dashboard') ?>" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= base_url('dashboard/logs') ?>" class="nav-item active">
                <i class="fas fa-list-alt"></i>
                <span>Logs</span>
            </a>
            <a href="<?= base_url('dashboard/history') ?>" class="nav-item">
                <i class="fas fa-history"></i>
                <span>Histori Data</span>
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
        <h1 class="page-title">
            <i class="fas fa-list-alt"></i> System Logs
        </h1>

        <?php if (empty($logs)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Belum Ada Log</h3>
                <p>Log sistem akan muncul di sini ketika ada data yang masuk</p>
            </div>
        <?php else: ?>
            <?php foreach ($logs as $log): ?>
                <?php 
                    $payload = json_decode($log['payload'], true);
                    $sensors = $payload['sensors'] ?? ($payload['data']['sensors'] ?? []);
                    $actuators = $payload['actuators'] ?? ($payload['data']['actuators'] ?? []);
                    $status = $payload['status'] ?? ($payload['data']['status'] ?? 'N/A');
                ?>
                <div class="log-card">
                    <div class="log-header">
                        <div class="log-id">
                            <i class="fas fa-hashtag"></i> Log ID: <?= esc($log['id']) ?>
                        </div>
                        <div class="log-time">
                            <i class="fas fa-clock"></i>
                            <?= date('d M Y, H:i:s', strtotime($log['created_at'])) ?>
                        </div>
                    </div>
                    
                    <?php if ($status !== 'N/A'): ?>
                    <div class="log-status">
                        <strong style="color: var(--dark);">Status Sistem:</strong>
                        <span class="badge-status">
                            <?= esc($status) ?>
                        </span>
                    </div>
                    <?php endif; ?>

                    <div>
                        <button class="expand-btn" onclick="toggleDetail(<?= $log['id'] ?>)">
                            <i class="fas fa-chevron-down" id="icon-<?= $log['id'] ?>"></i>
                            <span id="text-<?= $log['id'] ?>">Lihat Detail</span>
                        </button>
                    </div>

                    <div class="detail-content" id="detail-<?= $log['id'] ?>">
                        <!-- Sensors Table -->
                        <?php if (!empty($sensors)): ?>
                        <div class="detail-section">
                            <h4>
                                <i class="fas fa-microchip" style="color: var(--primary);"></i>
                                Data Sensor
                            </h4>
                            <div style="overflow-x: auto;">
                                <table class="detail-table">
                                    <thead>
                                        <tr>
                                            <th>Parameter</th>
                                            <th>Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sensors as $key => $value): ?>
                                        <?php
                                            // Clean value from JSON formatting
                                            $cleanValue = $value;
                                            if (is_string($cleanValue)) {
                                                $cleanValue = trim($cleanValue, '"');
                                            } elseif (is_array($cleanValue)) {
                                                $cleanValue = json_encode($cleanValue);
                                            }
                                        ?>
                                        <tr>
                                            <td style="font-weight: 500; color: var(--gray); text-transform: uppercase; font-size: 0.875rem;">
                                                <?= esc($key) ?>
                                            </td>
                                            <td style="color: var(--dark); font-weight: 500;">
                                                <?= $cleanValue ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Actuators Table -->
                        <?php if (!empty($actuators)): ?>
                        <div class="detail-section">
                            <h4>
                                <i class="fas fa-cogs" style="color: var(--secondary);"></i>
                                Status Aktuator
                            </h4>
                            <div style="overflow-x: auto;">
                                <table class="detail-table">
                                    <thead>
                                        <tr>
                                            <th>Perangkat</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($actuators as $key => $value): ?>
                                        <tr>
                                            <td style="font-weight: 500; color: var(--gray); text-transform: uppercase; font-size: 0.875rem;">
                                                <?= esc($key) ?>
                                            </td>
                                            <td>
                                                <?php if ($value == 1 || $value === 'on'): ?>
                                                    <span class="badge badge-on">
                                                        <i class="fas fa-check-circle"></i> ON
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-off">
                                                        <i class="fas fa-times-circle"></i> OFF
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Pagination -->
            <?php if ($pager->getPageCount() > 1): ?>
            <?php 
                $currentPage = $pager->getCurrentPage();
                $totalPages = $pager->getPageCount();
                $nextPage = $currentPage + 1;
                $prevPage = $currentPage - 1;
            ?>
            <div class="pagination-container">
                <a href="<?= base_url('dashboard/logs') ?>?page=1" class="pagination-btn" title="Data Terbaru">
                    <i class="fas fa-angle-double-left"></i>
                </a>
                
                <?php if ($currentPage > 1): ?>
                <a href="<?= base_url('dashboard/logs') ?>?page=<?= $currentPage - 1 ?>" class="pagination-btn" title="Halaman Sebelumnya">
                    </i> <?= $prevPage    ?>
                </a>
                <?php else: ?>
                <span class="pagination-btn disabled">
                    <i class="fas fa-angle-left"></i>
                </span>
                <?php endif; ?>
                
                <div class="pagination-search">
                    <input type="number" id="pageInput" min="1" max="<?= $totalPages ?>" 
                           value="<?= $currentPage ?>" 
                           placeholder="Hal" style="width: 60px; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 6px; text-align: center;">
                    <span style="margin: 0 0.5rem; color: var(--gray);">dari <?= $totalPages ?></span>
                    <button onclick="goToPage()" class="pagination-btn" style="background: var(--primary); color: white; border: none;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                
                <?php if ($currentPage < $totalPages): ?>
                <a href="<?= base_url('dashboard/logs') ?>?page=<?= $currentPage + 1 ?>" class="pagination-btn" title="Halaman Selanjutnya">
                    <?= $nextPage    ?> 
                </a>
                <?php else: ?>
                <span class="pagination-btn disabled">
                     <i class="fas fa-angle-right"></i>
                </span>
                <?php endif; ?>
                
                <a href="<?= base_url('dashboard/logs') ?>?page=<?= $totalPages ?>" class="pagination-btn" title="Data Terlama">
                    <i class="fas fa-angle-double-right"></i>
                </a>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <script>
        function goToPage() {
            const pageInput = document.getElementById('pageInput');
            const page = parseInt(pageInput.value);
            const maxPage = <?= $pager->getPageCount() ?? 1 ?>;
            
            if (page >= 1 && page <= maxPage) {
                window.location.href = '<?= base_url('dashboard/logs') ?>?page=' + page;
            } else {
                alert('Halaman tidak valid. Masukkan halaman 1 sampai ' + maxPage);
            }
        }
        
        document.getElementById('pageInput')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                goToPage();
            }
        });

        function toggleMobileMenu() {
            const nav = document.getElementById('navContainer');
            nav.classList.toggle('mobile-open');
        }

        function toggleDetail(id) {
            const detailContent = document.getElementById('detail-' + id);
            const icon = document.getElementById('icon-' + id);
            const text = document.getElementById('text-' + id);
            
            if (detailContent.classList.contains('show')) {
                detailContent.classList.remove('show');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
                text.textContent = 'Lihat Detail';
            } else {
                detailContent.classList.add('show');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
                text.textContent = 'Sembunyikan Detail';
            }
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
