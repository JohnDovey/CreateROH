<?php
/**
 * pageviews.php
 * Page Views Dashboard
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
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Page Views Statistics</h1>

        <div class="row justify-content-md-center">
            <div class="col-lg-10">

                <!-- Summary Cards -->
                <?php $stats = getPageViewStats(); ?>
                <div class="row g-3 mb-5">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body text-center">
                                <h3>Total Page Views</h3>
                                <h2><?= number_format($stats['total']) ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body text-center">
                                <h3>This Year</h3>
                                <h2><?= number_format($stats['thisYear']) ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body text-center">
                                <h3>This Month</h3>
                                <h2><?= number_format($stats['thisMonth']) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Per-Page Table -->
                <h4 class="mb-3">Views by Page</h4>
                <table class="table table-dark table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Page</th>
                            <th class="text-end">Total Views</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $pages = db()->fetchAll("SELECT PageName, SUM(ViewCount) as Total 
                                             FROM PageViews 
                                             GROUP BY PageName 
                                             ORDER BY Total DESC");
                    foreach ($pages as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['PageName']) ?></td>
                            <td class="text-end"><?= number_format($p['Total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Yearly Chart -->
                <h4 class="mt-5 mb-3">Yearly Views</h4>
                <?php
                $yearData = db()->fetchAll("SELECT Year, SUM(ViewCount) as Views 
                                            FROM PageViews GROUP BY Year ORDER BY Year");
                $LabelNames = json_encode(array_column($yearData, 'Year'));
                $DataPoints = json_encode(array_column($yearData, 'Views'));
                $MyChartTitle = "Views per Year";
                $MyChartType = 'bar';
                ?>
                <canvas id="StatsGraph" style="height: 400px;"></canvas>
                <?php include_once('js/chartGeneric.php'); ?>

                <!-- Monthly Chart (last 24 months) -->
                <h4 class="mt-5 mb-3">Monthly Views (last 24 months)</h4>
                <?php
                $monthData = db()->fetchAll("SELECT Year || '-' || printf('%02d', Month) as MonthKey, 
                                                    SUM(ViewCount) as Views 
                                             FROM PageViews 
                                             GROUP BY MonthKey 
                                             ORDER BY Year DESC, Month DESC 
                                             LIMIT 24");
                $monthData = array_reverse($monthData); // oldest first
                $LabelNames = json_encode(array_column($monthData, 'MonthKey'));
                $DataPoints = json_encode(array_column($monthData, 'Views'));
                $MyChartTitle = "Monthly Views";
                $MyChartType = 'line';
                ?>
                <canvas id="StatsGraph2" style="height: 400px;"></canvas>
                <?php include_once('js/chartGeneric.php'); ?>

            </div>
        </div>
    </div>

    <hr>
    <?php require_once("include/footer.php"); ?>
    <?php require_once("include/bootstrap-footer.php"); ?>
</body>
</html>
