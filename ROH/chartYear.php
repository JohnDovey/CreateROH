<?php
/**
 * chartYear.php
 * Secure and modern deaths by year chart + table
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Deaths by Year</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>

<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Deaths by Year</h1>

        <div class="row justify-content-md-center">
            <!-- Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card bg-primary">
                    <div class="card-header">
                        <h5 class="mb-0">Annual Death Toll</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Get chart data securely
                        $sql = "SELECT strftime('%Y', DateDeath) as Year, 
                                       COUNT(*) as CountYearDeath 
                                FROM PersonInfoRaw 
                                WHERE DateDeath IS NOT NULL 
                                GROUP BY Year 
                                ORDER BY Year";
                        
                        $data = db()->fetchAll($sql);

                        $LabelNames = json_encode(array_column($data, 'Year'));
                        $DataPoints = json_encode(array_column($data, 'CountYearDeath'));
                        ?>

                        <canvas id="StatsGraph" style="height: 480px; width: 100%;"></canvas>

                        <?php 
                        $MyChartTitle = "Deaths by Year";
                        $MyChartType = $_GET['chart'] ?? 'line';
                        if (!in_array($MyChartType, ['line', 'bar', 'radar'])) {
                            $MyChartType = 'line';
                        }
                        include_once('js/chartGeneric.php'); 
                        ?>
                    </div>
                    <div class="card-footer text-center">
                        <a href="?chart=bar" class="btn btn-sm btn-light <?= $MyChartType === 'bar' ? 'active' : '' ?>">Bar</a>
                        <a href="?chart=line" class="btn btn-sm btn-light <?= $MyChartType === 'line' ? 'active' : '' ?>">Line</a>
                        <a href="?chart=radar" class="btn btn-sm btn-light <?= $MyChartType === 'radar' ? 'active' : '' ?>">Radar</a>
                    </div>
                </div>
            </div>

            <!-- Side Table -->
            <div class="col-lg-4">
                <div class="card bg-primary h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Year Breakdown</h5>
                    </div>
                    <div class="card-body" style="max-height: 520px; overflow-y: auto;">
                        <table class="table table-dark table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Year</th>
                                    <th class="text-end">Deaths</th>
                                    <th class="text-end">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $totalDeaths = CountTotalDeaths();
                            foreach ($data as $row):
                                $percent = $totalDeaths > 0 ? ($row['CountYearDeath'] / $totalDeaths) * 100 : 0;
                            ?>
                                <tr>
                                    <td>
                                        <a href="listPeopleYear.php?Year=<?= $row['Year'] ?>" 
                                           class="btn btn-sm btn-outline-light">
                                            <?= $row['Year'] ?>
                                        </a>
                                    </td>
                                    <td class="text-end"><?= number_format($row['CountYearDeath']) ?></td>
                                    <td class="text-end"><?= round($percent, 1) ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <?php require_once("include/footer.php"); ?>
    <?php require_once("include/bootstrap-footer.php"); ?>
</body>
</html>
