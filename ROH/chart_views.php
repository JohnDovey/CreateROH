<?php
/**
 * chart_views.php - Enhanced Page Views Dashboard
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
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Page Views History</h1>

        <!-- Summary Cards -->
        <?php $stats = getPageViewStats(); ?>
        <div class="row g-3 mb-5">
            <div class="col-md-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <h5>Total Site Views</h5>
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
            <div class="col-md-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body text-center">
                        <h5>Pages Tracked</h5>
                        <h2><?= number_format(db()->fetchOne("SELECT COUNT(DISTINCT PageName) as c FROM PageViews")['c'] ?? 0) ?></h2>
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
                    $topAll = db()->fetchAll("SELECT PageName, SUM(ViewCount) as Total 
                                              FROM PageViews GROUP BY PageName 
                                              ORDER BY Total DESC LIMIT 40");
                    foreach ($topAll as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['PageName']) ?></td>
                            <td class="text-end"><?= number_format($row['Total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Top 40 This Year -->
            <div class="col-lg-4 mb-4">
                <h5>Top 40 Pages (<?= $year ?>)</h5>
                <table class="table table-dark table-striped table-hover">
                    <thead><tr><th>Page</th><th class="text-end">Views</th></tr></thead>
                    <tbody>
                    <?php
                    $topYear = db()->fetchAll("SELECT PageName, SUM(ViewCount) as Total 
                                               FROM PageViews 
                                               WHERE Year = :y 
                                               GROUP BY PageName 
                                               ORDER BY Total DESC LIMIT 40", [':y' => $year]);
                    foreach ($topYear as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['PageName']) ?></td>
                            <td class="text-end"><?= number_format($row['Total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Top 40 This Month -->
            <div class="col-lg-4 mb-4">
                <h5>Top 40 Pages (<?= date('F Y') ?>)</h5>
                <table class="table table-dark table-striped table-hover">
                    <thead><tr><th>Page</th><th class="text-end">Views</th></tr></thead>
                    <tbody>
                    <?php
                    $topMonth = db()->fetchAll("SELECT PageName, SUM(ViewCount) as Total 
                                                FROM PageViews 
                                                WHERE Year = :y AND Month = :m 
                                                GROUP BY PageName 
                                                ORDER BY Total DESC LIMIT 40", [':y' => $year, ':m' => $month]);
                    foreach ($topMonth as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['PageName']) ?></td>
                            <td class="text-end"><?= number_format($row['Total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Monthly Views Chart -->
        <h4 class="mt-5 mb-3">Monthly Page Views (Last 36 Months)</h4>
        <?php
        $monthData = db()->fetchAll("SELECT Year || '-' || printf('%02d', Month) as MonthKey, 
                                            SUM(ViewCount) as Views 
                                     FROM PageViews 
                                     GROUP BY MonthKey 
                                     ORDER BY Year DESC, Month DESC 
                                     LIMIT 36");
        $monthData = array_reverse($monthData);
        $LabelNames = json_encode(array_column($monthData, 'MonthKey'));
        $DataPoints = json_encode(array_column($monthData, 'Views'));
        $MyChartTitle = "Monthly Page Views";
        $MyChartType = 'line';
        ?>
        <canvas id="StatsGraph" style="height: 420px;"></canvas>
        <?php include_once('js/chartGeneric.php'); ?>

    </div>

    <hr>
    <?php require_once("include/footer.php"); ?>
    <?php require_once("include/bootstrap-footer.php"); ?>
</body>
</html>
