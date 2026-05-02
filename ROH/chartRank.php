<?php
/**
 * chartRank.php
 * Secure and modern deaths by rank chart
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Deaths by Rank</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>

<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Deaths by Rank (Top Ranks)</h1>

        <div class="row justify-content-md-center">
            <!-- Main Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card bg-primary">
                    <div class="card-header">
                        <h5 class="mb-0">Rank Death Statistics</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Get chart data securely
                        $sql = "SELECT Rank, COUNT(*) as CountRank 
                                FROM PersonInfoRaw 
                                WHERE RankID > 0 
                                GROUP BY Rank 
                                ORDER BY CountRank DESC 
                                LIMIT 40";
                        
                        $data = db()->fetchAll($sql);

                        $LabelNames = json_encode(array_column($data, 'Rank'));
                        $DataPoints = json_encode(array_column($data, 'CountRank'));
                        ?>

                        <canvas id="StatsGraph" style="height: 520px; width: 100%;"></canvas>

                        <?php 
                        $MyChartTitle = "Deaths by Rank";
                        $MyChartType = $_GET['chart'] ?? 'bar';
                        if (!in_array($MyChartType, ['bar', 'line', 'radar'])) {
                            $MyChartType = 'bar';
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
                        <h5 class="mb-0">Detailed Breakdown</h5>
                    </div>
                    <div class="card-body" style="max-height: 560px; overflow-y: auto;">
                        <?php
                        $totalDeaths = CountTotalDeaths();
                        $noRank      = CountNoRank();
                        $withRank    = $totalDeaths - $noRank;
                        ?>
                        <table class="table table-dark table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th class="text-end">Deaths</th>
                                    <th class="text-end">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data as $row): 
                                $percent = $withRank > 0 ? ($row['CountRank'] / $withRank) * 100 : 0;
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['Rank']) ?></td>
                                    <td class="text-end"><?= number_format($row['CountRank']) ?></td>
                                    <td class="text-end"><?= round($percent, 1) ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer small text-muted">
                        Total with Rank: <?= number_format($withRank) ?> | No Rank: <?= number_format($noRank) ?>
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
