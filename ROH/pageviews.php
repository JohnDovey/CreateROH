<?php
/**
 * pageviews.php - Optimized Lightweight Version
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Page Views Statistics</title>
    <?php require_once("include/bootstrap-head.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        canvas { max-height: 380px; }
    </style>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Page Views Statistics</h1>

        <?php
        $stats = getPageViewStats();
        $dailyChart = getDailyPageViewsThreeMonthChartData();
        ?>

        <!-- Summary Cards -->
        <div class="row g-3 mb-5 text-center">
            <div class="col-md-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <h5>Total Site Views</h5>
                        <h2><?= number_format($stats['total']) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <h5>This Year</h5>
                        <h2><?= number_format($stats['thisYear']) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <h5>This Month</h5>
                        <h2><?= number_format($stats['thisMonth']) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 40 Pages -->
        <h5>Top 40 Pages (All Time)</h5>
        <table class="table table-dark table-striped table-hover mb-5">
            <thead><tr><th>Page title</th><th class="text-end">Views</th></tr></thead>
            <tbody>
            <?php
            $topPages = db()->fetchAll("SELECT PageName, SUM(ViewCount) as Total 
                                        FROM PageViews GROUP BY PageName 
                                        ORDER BY Total DESC LIMIT 40");
            foreach ($topPages as $row): ?>
                <tr>
                    <td><?= htmlspecialchars(roh_page_display_title($row['PageName'])) ?></td>
                    <td class="text-end"><?= number_format($row['Total']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Yearly Chart -->
        <h5>Page Views by Year</h5>
        <?php
        $yearData = db()->fetchAll("SELECT Year, SUM(ViewCount) as Views FROM PageViews GROUP BY Year ORDER BY Year");
        ?>
        <canvas id="yearChart"></canvas>

        <!-- Monthly Chart -->
        <h5 class="mt-5">Monthly Views (Last 24 Months)</h5>
        <?php
        $monthData = db()->fetchAll("SELECT Year || '-' || printf('%02d', Month) as MonthKey, 
                                            SUM(ViewCount) as Views 
                                     FROM PageViews 
                                     GROUP BY MonthKey 
                                     ORDER BY Year DESC, Month DESC LIMIT 24");
        $monthData = array_reverse($monthData);
        ?>
        <canvas id="monthChart"></canvas>

        <!-- Daily views: this month vs prior two months -->
        <div class="card bg-primary mt-5 mb-5">
            <div class="card-body">
                <h5 class="card-title">Daily page views (this month and prior two months)</h5>
                <p class="small text-muted mb-3">
                    Lines show total site views per calendar day. The x-axis is the day of the month (1–31) so you can compare the same calendar day across months.
                    Daily totals are recorded automatically on each page load; older than about four months are removed to limit database size.
                </p>
                <canvas id="dailyViewsChart"></canvas>
            </div>
        </div>

    </div>

    <hr>
    <?php require_once("include/footer.php"); ?>
    <?php require_once("include/bootstrap-footer.php"); ?>

    <script>
    window.addEventListener('load', function() {

        // Yearly Chart
        new Chart(document.getElementById('yearChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($yearData, 'Year')) ?>,
                datasets: [{
                    label: 'Views per Year',
                    data: <?= json_encode(array_column($yearData, 'Views')) ?>,
                    backgroundColor: '#0d6efd'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true
            }
        });

        // Monthly Chart
        new Chart(document.getElementById('monthChart'), {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($monthData, 'MonthKey')) ?>,
                datasets: [{
                    label: 'Monthly Views',
                    data: <?= json_encode(array_column($monthData, 'Views')) ?>,
                    borderColor: '#0dcaf0',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true
            }
        });

        // Daily views (3 months, day-of-month x-axis)
        new Chart(document.getElementById('dailyViewsChart'), {
            type: 'line',
            data: {
                labels: <?= json_encode($dailyChart['labels']) ?>,
                datasets: <?= json_encode($dailyChart['datasets'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { color: '#e0e0e0', font: { size: 13 } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#ddd'
                    }
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Day of month', color: '#aaa' },
                        ticks: { color: '#aaa' },
                        grid: { color: 'rgba(255,255,255,0.08)' }
                    },
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Page views (all pages)', color: '#aaa' },
                        ticks: { color: '#aaa' },
                        grid: { color: 'rgba(255,255,255,0.08)' }
                    }
                }
            }
        });
    });
    </script>
</body>
</html>