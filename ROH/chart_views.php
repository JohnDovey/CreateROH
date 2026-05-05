<?php
/**
 * chart_views.php - Fully Fixed & Self-Contained
 */
require_once("include/db.php");
require_once("functions.php");
$year = (int)date('Y');
$month = (int)date('n');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Page Views History</title>
    <?php require_once("include/bootstrap-head.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Page Views History</h1>

        <?php $stats = getPageViewStats(); ?>

        <!-- Summary -->
        <div class="row g-3 mb-5">
            <div class="col-md-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <h5>Total Views</h5>
                        <h2><?= number_format($stats['total']) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <h5>This Year</h5>
                        <h2><?= number_format($stats['thisYear']) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <h5>This Month</h5>
                        <h2><?= number_format($stats['thisMonth']) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Top 40 All Time -->
            <div class="col-lg-4 mb-4">
                <h5>Top 40 Pages (All Time)</h5>
                <table class="table table-dark table-striped table-hover">
                    <thead><tr><th>Page</th><th class="text-end">Views</th></tr></thead>
                    <tbody>
                    <?php
                    $topAll = db()->fetchAll("SELECT PageName, SUM(ViewCount) as Total FROM PageViews GROUP BY PageName ORDER BY Total DESC LIMIT 40");
                    foreach ($topAll as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['PageName']) ?></td>
                            <td class="text-end"><?= number_format($row['Total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Yearly Chart -->
            <div class="col-lg-8 mb-4">
                <h5>Page Views by Year</h5>
                <?php
                $yearData = db()->fetchAll("SELECT Year, SUM(ViewCount) as Views FROM PageViews GROUP BY Year ORDER BY Year");
                ?>
                <canvas id="yearChart" height="380"></canvas>
            </div>
        </div>

        <!-- Monthly Chart -->
        <h5 class="mt-5">Monthly Views (Last 36 Months)</h5>
        <?php
        $monthData = db()->fetchAll("SELECT Year || '-' || printf('%02d', Month) as MonthKey, 
                                            SUM(ViewCount) as Views 
                                     FROM PageViews 
                                     GROUP BY MonthKey 
                                     ORDER BY Year DESC, Month DESC 
                                     LIMIT 36");
        $monthData = array_reverse($monthData);
        ?>
        <canvas id="monthChart" height="420"></canvas>

    </div>

    <hr>
    <?php require_once("include/footer.php"); ?>
    <?php require_once("include/bootstrap-footer.php"); ?>

    <script>
    // Yearly Chart
    new Chart(document.getElementById('yearChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($yearData, 'Year')) ?>,
            datasets: [{
                label: 'Total Views',
                data: <?= json_encode(array_column($yearData, 'Views')) ?>,
                backgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { color: '#ddd' } },
                x: { ticks: { color: '#ddd' } }
            }
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
                tension: 0.3,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { color: '#ddd' } },
                x: { ticks: { color: '#ddd' } }
            }
        }
    });
    </script>
</body>
</html>
