<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #e5e7eb; color: #1f2937; line-height: 1.6; }
        .top-header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; padding: 1rem 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100; }
        .header-content { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo-section { display: flex; align-items: center; gap: 1rem; }
        .logo-section img { width: 50px; height: 50px; border-radius: 12px; object-fit: cover; }
        .logo-text { font-size: 1.5rem; font-weight: 700; letter-spacing: -0.5px; }
        .status-badge { display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem; }
        .status-dot { width: 8px; height: 8px; border-radius: 50%; background: #10b981; animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .nav-container { background: white; border-bottom: 1px solid #e5e7eb; padding: 0 2rem; position: sticky; top: 73px; z-index: 99; }
        .nav-menu { max-width: 1400px; margin: 0 auto; display: flex; gap: 0.5rem; overflow-x: auto; }
        .nav-item { padding: 1rem 1.5rem; text-decoration: none; color: #6b7280; font-weight: 500; border-bottom: 3px solid transparent; transition: all 0.3s; white-space: nowrap; display: flex; align-items: center; gap: 0.5rem; }
        .nav-item:hover, .nav-item.active { color: #2563eb; border-bottom-color: #2563eb; }
        .main-container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        .page-title { font-size: 2rem; font-weight: 700; margin-bottom: 2rem; color: #1f2937; }
        .table-card { background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #F3F4F6; }
        th { padding: 1rem; text-align: left; font-weight: 600; color: #1f2937; border-bottom: 2px solid #E5E7EB; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px; }
        tbody tr { border-bottom: 1px solid #F3F4F6; transition: background 0.2s; }
        tbody tr:hover { background: #F9FAFB; }
        td { padding: 1rem; color: #1f2937; }
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

        /* Issue Cell Styling */
        .issue-cell {
            position: relative;
            animation: pulse-red 2s infinite;
        }

        .issue-cell:hover {
            background: #FCA5A5 !important;
            transform: scale(1.05);
            transition: all 0.3s;
            box-shadow: 0 0 10px rgba(220, 38, 38, 0.3);
        }

        @keyframes pulse-red {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4);
            }
            50% {
                box-shadow: 0 0 0 4px rgba(220, 38, 38, 0);
            }
        }

        .export-btn { background: #10b981; color: white; padding: 0.5rem 1.5rem; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; transition: background 0.3s; }
        .export-btn:hover { background: #059669; }
        .mobile-menu-btn { display: none; background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer; }
        @media (max-width: 768px) {
            .mobile-menu-btn { display: block; }
            .nav-container { display: none; }
            .nav-container.mobile-open { display: block; }
            .main-container { padding: 1rem; }
            .page-title { font-size: 1.5rem; }
            .table-card { padding: 1rem; }
        }
    </style>
</head>
<body>
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

    <nav class="nav-container" id="navContainer">
        <div class="nav-menu">
            <a href="<?= base_url('dashboard') ?>" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= base_url('dashboard/logs') ?>" class="nav-item">
                <i class="fas fa-list-alt"></i>
                <span>Logs</span>
            </a>
            <a href="<?= base_url('dashboard/history') ?>" class="nav-item active">
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

    <main class="main-container">
        <h1 class="page-title">
            <i class="fas fa-history"></i> Histori Data Sensor
        </h1>

        <!-- Export Form -->
        <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1rem; color: #1f2937; font-size: 1.125rem; font-weight: 600;">
                <i class="fas fa-download"></i> Export Data ke CSV
            </h3>
            <form id="exportForm" method="get" action="<?= base_url('dashboard/history/export') ?>" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; margin-bottom: 0.5rem; color: #6b7280; font-size: 0.875rem; font-weight: 500;">
                        Tanggal & Jam Mulai
                    </label>
                    <input type="datetime-local" name="start_date" id="startDate" required
                           style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 8px;">
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; margin-bottom: 0.5rem; color: #6b7280; font-size: 0.875rem; font-weight: 500;">
                        Tanggal & Jam Akhir
                    </label>
                    <input type="datetime-local" name="end_date" id="endDate" required
                           style="width: 100%; padding: 0.5rem; border: 1px solid #e5e7eb; border-radius: 8px;">
                </div>
                <div>
                    <button type="submit" class="export-btn">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </button>
                </div>
            </form>
            <p style="margin-top: 0.75rem; color: #6b7280; font-size: 0.875rem;">
                <i class="fas fa-info-circle"></i> Pilih range tanggal & jam untuk export data sensor ke file CSV
            </p>
        </div>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>pH</th>
                        <th>TDS (ppm)</th>
                        <th>Kekeruhan (NTU)</th>
                        <th>Tank</th>
                        <th>Chamber</th>
                        <th>Kualitas Air</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sensors)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 3rem; color: #6b7280;">
                            <i class="fas fa-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                            Belum ada data sensor
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($sensors as $sensor): ?>
                        <?php
                            $ph = trim($sensor['ph'] ?? 'N/A', '"');
                            $tds = trim($sensor['tds'] ?? 'N/A', '"');
                            $turb = trim($sensor['turb'] ?? 'N/A', '"');
                            $tank = trim($sensor['tank'] ?? 'N/A', '"');
                            $chamber = trim($sensor['chamber'] ?? 'N/A', '"');
                            
                            // Generate water quality if not set
                            $waterQuality = $sensor['water_quality'] ?? null;
                            $phIssue = '';
                            $tdsIssue = '';
                            $turbIssue = '';
                            
                            if ($waterQuality === null && $ph !== 'N/A' && $tds !== 'N/A' && $turb !== 'N/A') {
                                $phVal = floatval($ph);
                                $tdsVal = floatval($tds);
                                $turbVal = floatval($turb);
                                
                                $waterQuality = 'Bagus';
                                
                                // Check each parameter and record issues
                                if ($phVal < 6.5) {
                                    $waterQuality = 'Kurang Bagus';
                                    $phIssue = 'pH terlalu rendah (< 6.5). Standar: 6.5 - 8.0';
                                } elseif ($phVal > 8.0) {
                                    $waterQuality = 'Kurang Bagus';
                                    $phIssue = 'pH terlalu tinggi (> 8.0). Standar: 6.5 - 8.0';
                                }
                                
                                if ($tdsVal > 1000) {
                                    $waterQuality = 'Kurang Bagus';
                                    $tdsIssue = 'TDS terlalu tinggi (> 1000 ppm). Standar: ≤ 1000 ppm';
                                }
                                
                                if ($turbVal < 5) {
                                    $waterQuality = 'Kurang Bagus';
                                    $turbIssue = 'Kekeruhan terlalu rendah (< 5 NTU). Standar: 5 - 50 NTU';
                                } elseif ($turbVal > 50) {
                                    $waterQuality = 'Kurang Bagus';
                                    $turbIssue = 'Kekeruhan terlalu tinggi (> 50 NTU). Standar: 5 - 50 NTU';
                                }
                            } elseif ($waterQuality === 'Kurang Bagus' && $ph !== 'N/A' && $tds !== 'N/A' && $turb !== 'N/A') {
                                // If already marked as Kurang Bagus, check which parameters are problematic
                                $phVal = floatval($ph);
                                $tdsVal = floatval($tds);
                                $turbVal = floatval($turb);
                                
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
                            }
                        ?>
                        <tr>
                            <td style="font-weight: 600; color: #2563eb;">#<?= esc($sensor['id']) ?></td>
                            <td <?= $phIssue ? 'class="issue-cell" title="' . esc($phIssue) . '" onclick="showIssueDetail(\'pH\', \'' . esc($phIssue) . '\', \'' . esc($ph) . '\')" style="background: #FEE2E2; color: #DC2626; font-weight: 600; cursor: pointer;"' : '' ?>>
                                <?= $ph ?>
                                <?php if ($phIssue): ?>
                                    <i class="fas fa-exclamation-circle" style="margin-left: 0.25rem;"></i>
                                <?php endif; ?>
                            </td>
                            <td <?= $tdsIssue ? 'class="issue-cell" title="' . esc($tdsIssue) . '" onclick="showIssueDetail(\'TDS\', \'' . esc($tdsIssue) . '\', \'' . esc($tds) . '\')" style="background: #FEE2E2; color: #DC2626; font-weight: 600; cursor: pointer;"' : '' ?>>
                                <?= $tds ?>
                                <?php if ($tdsIssue): ?>
                                    <i class="fas fa-exclamation-circle" style="margin-left: 0.25rem;"></i>
                                <?php endif; ?>
                            </td>
                            <td <?= $turbIssue ? 'class="issue-cell" title="' . esc($turbIssue) . '" onclick="showIssueDetail(\'Kekeruhan\', \'' . esc($turbIssue) . '\', \'' . esc($turb) . '\')" style="background: #FEE2E2; color: #DC2626; font-weight: 600; cursor: pointer;"' : '' ?>>
                                <?= $turb ?>
                                <?php if ($turbIssue): ?>
                                    <i class="fas fa-exclamation-circle" style="margin-left: 0.25rem;"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= $tank ?></td>
                            <td><?= $chamber ?></td>
                            <td>
                                <?php if ($waterQuality === 'Bagus'): ?>
                                    <span style="background: #DCFCE7; color: #16A34A; padding: 0.25rem 0.75rem; border-radius: 6px; font-weight: 500; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        <i class="fas fa-check-circle"></i> Bagus
                                    </span>
                                <?php elseif ($waterQuality === 'Kurang Bagus'): ?>
                                    <span style="background: #FEE2E2; color: #DC2626; padding: 0.25rem 0.75rem; border-radius: 6px; font-weight: 500; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        <i class="fas fa-exclamation-circle"></i> Kurang Bagus
                                    </span>
                                <?php else: ?>
                                    <span style="background: #F3F4F6; color: #6b7280; padding: 0.25rem 0.75rem; border-radius: 6px; font-weight: 500; font-size: 0.875rem;">
                                        N/A
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="color: #6b7280; font-size: 0.875rem;">
                                <?= date('d M Y, H:i:s', strtotime($sensor['created_at'])) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (!empty($sensors) && $pager->getPageCount() > 1): ?>
        <?php 
            $currentPage = $pager->getCurrentPage();
            $totalPages = $pager->getPageCount();
         $nextPage = $currentPage + 1;
                $prevPage = $currentPage - 1;
            ?>
            <div class="pagination-container">
                <a href="<?= base_url('dashboard/history') ?>?page=1" class="pagination-btn" title="Data Terbaru">
                    <i class="fas fa-angle-double-left"></i>
                </a>
                
                <?php if ($currentPage > 1): ?>
                <a href="<?= base_url('dashboard/history') ?>?page=<?= $currentPage - 1 ?>" class="pagination-btn" title="Halaman Sebelumnya">
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
                <a href="<?= base_url('dashboard/history') ?>?page=<?= $currentPage + 1 ?>" class="pagination-btn" title="Halaman Selanjutnya">
                    <?= $nextPage    ?> 
                </a>
                <?php else: ?>
                <span class="pagination-btn disabled">
                     <i class="fas fa-angle-right"></i>
                </span>
                <?php endif; ?>
                
                <a href="<?= base_url('dashboard/history') ?>?page=<?= $totalPages ?>" class="pagination-btn" title="Data Terlama">
                    <i class="fas fa-angle-double-right"></i>
                </a>
            </div>
            <?php endif; ?>
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
                if (value < 6.5) {
                    recommendation = 'Tambahkan bahan alkali (seperti kapur) untuk menaikkan pH air. Lakukan secara bertahap dan monitor perubahan pH.';
                } else {
                    recommendation = 'Tambahkan bahan asam (seperti cuka atau asam sitrat) untuk menurunkan pH air. Lakukan secara bertahap.';
                }
            } else if (paramName === 'TDS') {
                recommendation = 'Lakukan pergantian air sebagian (water change) untuk menurunkan TDS. Gunakan air bersih dengan TDS rendah.';
            } else if (paramName === 'Kekeruhan') {
                if (value < 5) {
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

        function goToPage() {
            const pageInput = document.getElementById('pageInput');
            const page = parseInt(pageInput.value);
            const maxPage = <?= $pager->getPageCount() ?? 1 ?>;
            
            if (page >= 1 && page <= maxPage) {
                window.location.href = '<?= base_url('dashboard/history') ?>?page=' + page;
            } else {
                alert('Halaman tidak valid. Masukkan halaman 1 sampai ' + maxPage);
            }
        }
        
        document.getElementById('pageInput')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                goToPage();
            }
        });

        // Validate export form
        document.getElementById('exportForm')?.addEventListener('submit', function(e) {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            if (!startDate || !endDate) {
                e.preventDefault();
                alert('Mohon pilih tanggal mulai dan tanggal akhir');
                return false;
            }
            
            if (new Date(startDate) > new Date(endDate)) {
                e.preventDefault();
                alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
                return false;
            }
            
            return true;
        });

        function toggleMobileMenu() {
            document.getElementById('navContainer').classList.toggle('mobile-open');
        }

        document.querySelectorAll('.nav-item').forEach(item => {
            if (item.href === window.location.href) {
                item.classList.add('active');
            }
        });
    </script>
</body>
</html>
