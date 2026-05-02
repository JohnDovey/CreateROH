<?php
/**
 * chartCauseOfDeath.php
 * Secure and modern deaths by cause of death chart
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Cause of Death Statistics</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>

<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Cause of Death Statistics</h1>

        <div class="row justify-content-md-center">
            <!-- Main Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card bg-primary">
                    <div class="card-header">
                        <h5 class="mb-0">Distribution by Cause of Death</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Get chart data securely
                        $sql = "SELECT CauseDeath, COUNT(*) as CountCause 
                                FROM PersonInfoRaw 
                                WHERE CauseDeath IS NOT NULL AND CauseDeath != '' AND CauseDeath != 'Unknown'
                                GROUP BY CauseDeath 
                                ORDER BY CountCause DESC";
                        
                        $data = db()->fetchAll($sql);

                        $LabelNames = json_encode(array_column($data, 'CauseDeath'));
                        $DataPoints = json_encode(array_column($data, 'CountCause'));
                        ?>

                        <canvas id="StatsGraph" style="height: 520px; width: 100%;"></canvas>

                        <?php 
                        $MyChartTitle = "Deaths by Cause of Death";
                        $MyChartType = $_GET['chart'] ?? 'bar';
                        if (!in_array($MyChartType, ['bar', 'line', 'radar', 'pie', 'doughnut'])) {
                            $MyChartType = 'bar';
                        }
                        include_once('js/chartGeneric.php'); 
                        ?>
                    </div>
                    <div class="card-footer text-center">
                        <a href="?chart=bar" class="btn btn-sm btn-light <?= $MyChartType === 'bar' ? 'active' : '' ?>">Bar</a>
                        <a href="?chart=pie" class="btn btn-sm btn-light <?= $MyChartType === 'pie' ? 'active' : '' ?>">Pie</a>
                        <a href="?chart=doughnut" class="btn btn-sm btn-light <?= $MyChartType === 'doughnut' ? 'active' : '' ?>">Doughnut</a>
                    </div>
                </div>
            </div>

            <!-- Side Table -->
            <div class="col-lg-4">
                <div class="card bg-primary h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Detailed Breakdown</h5>
                    </div>
                    <div class="card-body" style="max-height: 560px; overflow-y: auto;">
                        <?php
                        $totalDeaths = CountTotalDeaths();
                        $noCause     = CountNoCause();
                        $withCause   = $totalDeaths - $noCause;
                        ?>
                        <table class="table table-dark table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Cause of Death</th>
                                    <th class="text-end">Deaths</th>
                                    <th class="text-end">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data as $row): 
                                $percent = $withCause > 0 ? ($row['CountCause'] / $withCause) * 100 : 0;
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['CauseDeath']) ?></td>
                                    <td class="text-end"><?= number_format($row['CountCause']) ?></td>
                                    <td class="text-end"><?= round($percent, 1) ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer small text-muted">
                        Total with Known Cause: <?= number_format($withCause) ?> | Unknown Cause: <?= number_format($noCause) ?>
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
