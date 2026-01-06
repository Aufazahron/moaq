<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .chart-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border: 1px solid #e5e7eb;
        }

        .chart-container {
            position: relative;
            height: 500px;
            width: 100%;
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }

            .chart-card {
                padding: 1.5rem;
            }

            .chart-container {
                height: 400px;
            }
        }

        .range-btn {
            background: transparent;
            color: var(--gray);
        }

        .range-btn.active {
            background: white;
            color: var(--primary);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
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
                    <div style="font-size: 0.75rem; opacity: 0.8;">Grafik Parameter Air</div>
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
            <a href="<?= base_url('dashboard/logs') ?>" class="nav-item">
                <i class="fas fa-list-alt"></i>
                <span>Logs</span>
            </a>
            <a href="<?= base_url('dashboard/history') ?>" class="nav-item">
                <i class="fas fa-history"></i>
                <span>Histori Data</span>
            </a>
            <a href="<?= base_url('dashboard/grafik') ?>" class="nav-item active">
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
        <h1 class="page-title">Grafik Parameter Air</h1>

        <div class="chart-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--dark);">
                    <i class="fas fa-chart-line" style="color: var(--primary); margin-right: 0.5rem;"></i>
                    Data Real-time Parameter Air
                </h2>
                <div style="display: flex; align-items: center; gap: 0.5rem; background: #f3f4f6; padding: 0.25rem; border-radius: 8px;">
                    <button onclick="changeRange('latest')" class="range-btn <?= $currentRange === 'latest' ? 'active' : '' ?>" id="range-latest" style="padding: 0.5rem 1rem; border-radius: 6px; border: none; cursor: pointer; font-size: 0.875rem; font-weight: 500; transition: all 0.3s;">Real-time</button>
                    <button onclick="changeRange('1h')" class="range-btn <?= $currentRange === '1h' ? 'active' : '' ?>" id="range-1h" style="padding: 0.5rem 1rem; border-radius: 6px; border: none; cursor: pointer; font-size: 0.875rem; font-weight: 500; transition: all 0.3s;">1 Jam Terakhir</button>
                    <button onclick="changeRange('24h')" class="range-btn <?= $currentRange === '24h' ? 'active' : '' ?>" id="range-24h" style="padding: 0.5rem 1rem; border-radius: 6px; border: none; cursor: pointer; font-size: 0.875rem; font-weight: 500; transition: all 0.3s;">24 Jam Terakhir</button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="parameterChart"></canvas>
            </div>
        </div>
    </main>

    <script>
        const chartData = <?= json_encode($chartData) ?>;
        
        const ctx = document.getElementById('parameterChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'pH Air',
                        data: chartData.ph,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 2,
                        pointHoverRadius: 6,
                        yAxisID: 'y',
                        spanGaps: true
                    },
                    {
                        label: 'Kekeruhan (NTU)',
                        data: chartData.turb,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 2,
                        pointHoverRadius: 6,
                        yAxisID: 'y',
                        spanGaps: true
                    },
                    {
                        label: 'TDS (ppm)',
                        data: chartData.tds,
                        borderColor: '#db2777',
                        backgroundColor: 'rgba(219, 39, 119, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 2,
                        pointHoverRadius: 6,
                        yAxisID: 'y1',
                        spanGaps: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 13,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: '600'
                        },
                        bodyFont: {
                            size: 13
                        },
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45,
                            font: {
                                size: 11
                            },
                            color: '#6b7280'
                        },
                        border: {
                            color: '#e5e7eb'
                        }
                    },
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: '#f3f4f6',
                            lineWidth: 1
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#6b7280'
                        },
                        title: {
                            display: true,
                            text: 'pH / NTU'
                        },
                        border: {
                            color: '#e5e7eb'
                        }
                    },
                    y1: {
                        beginAtZero: false,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#6b7280'
                        },
                        title: {
                            display: true,
                            text: 'TDS (ppm)'
                        },
                        border: {
                            color: '#e5e7eb'
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });

        // Realtime Update Polling
        let currentRange = '<?= $currentRange ?>';

        function changeRange(range) {
            currentRange = range;
            
            // Update UI buttons
            document.querySelectorAll('.range-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById('range-' + range).classList.add('active');
            
            // Refresh with current range immediately, skip animation for smooth range switching
            updateRealtimeChart(true);
        }

        function updateRealtimeChart(isRangeSwitch = false) {
            fetch('<?= base_url('dashboard/realtime') ?>?range=' + currentRange)
                .then(response => response.json())
                .then(res => {
                    if (res.chart) {
                        const newChartData = res.chart;
                        
                        // Update Data
                        chart.data.labels = newChartData.labels;
                        chart.data.datasets[0].data = newChartData.ph;
                        chart.data.datasets[1].data = newChartData.turb;
                        chart.data.datasets[2].data = newChartData.tds;
                        
                        // Jika ganti range, matikan animasi biar nggak "narik" (stretching)
                        // Data terbaru akan tetap di kanan, data lama akan melebar ke kiri
                        if (isRangeSwitch) {
                            chart.update('none'); 
                        } else {
                            // Update normal (Real-time polling)
                            chart.update(currentRange === 'latest' ? 'none' : 'default'); 
                        }
                    }
                })
                .catch(err => console.error('Grafik update failed:', err));
        }

        // Run Every 15 seconds
        setInterval(updateRealtimeChart, 15000);
    </script>
</body>
</html>

