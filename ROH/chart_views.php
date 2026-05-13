<?php
/**
 * chart_views.php - Optimized & Lightweight
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Page Views History</title>
    <?php require_once("include/bootstrap-head.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        canvas { max-height: 380px; }
    </style>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Page Views History</h1>

        <?php $stats = getPageViewStats(); ?>

        <div class="row g-3 mb-5 text-center">
            <div class="col-4"><div class="card bg-primary text-white"><div class="card-body"><h6>Total</h6><h4><?= number_format($stats['total']) ?></h4></div></div></div>
            <div class="col-4"><div class="card bg-primary text-white"><div class="card-body"><h6>This Year</h6><h4><?= number_format($stats['thisYear']) ?></h4></div></div></div>
            <div class="col-4"><div class="card bg-primary text-white"><div class="card-body"><h6>This Month</h6><h4><?= number_format($stats['thisMonth']) ?></h4></div></div></div>
        </div>

        <h5>Page Views by Year</h5>
        <?php
        $yearData = db()->fetchAll("SELECT Year, SUM(ViewCount) as Views FROM PageViews GROUP BY Year ORDER BY Year");
        ?>
        <canvas id="yearChart"></canvas>

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
                    label: 'Views',
                    data: <?= json_encode(array_column($yearData, 'Views')) ?>,
                    backgroundColor: '#0d6efd'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: { beginAtZero: true },
                    x: { }
                }
            }
        });

        // Monthly Chart
        new Chart(document.getElementById('monthChart'), {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($monthData, 'MonthKey')) ?>,
                datasets: [{
                    label: 'Monthly',
                    data: <?= json_encode(array_column($monthData, 'Views')) ?>,
                    borderColor: '#0dcaf0',
                    tension: 0.2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true
            }
        });
    });
    </script>
</body>
</html>