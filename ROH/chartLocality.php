<?php
/**
 * chartLocality.php
 * Secure and modern deaths by locality chart
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Deaths by Locality</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>

<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Deaths by Locality (Top Localities)</h1>

        <div class="row justify-content-md-center">
            <!-- Main Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card bg-primary">
                    <div class="card-header">
                        <h5 class="mb-0">Locality Death Statistics (Top 40)</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Get chart data securely
                        $sql = "SELECT Locality, COUNT(*) as CountLocality 
                                FROM PersonInfoRaw 
                                WHERE LocalityID > 0 
                                GROUP BY Locality 
                                ORDER BY CountLocality DESC 
                                LIMIT 40";
                        
                        $data = db()->fetchAll($sql);

                        $LabelNames = json_encode(array_column($data, 'Locality'));
                        $DataPoints = json_encode(array_column($data, 'CountLocality'));
                        ?>

                        <canvas id="StatsGraph" style="height: 520px; width: 100%;"></canvas>

                        <?php 
                        $MyChartTitle = "Deaths by Locality (Top 40)";
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
                        <h5 class="mb-0">Detailed Breakdown (Top 40)</h5>
                    </div>
                    <div class="card-body" style="max-height: 560px; overflow-y: auto;">
                        <?php
                        $totalDeaths = CountTotalDeaths();
                        $noLocality  = CountNoLocality();
                        $withLocality = $totalDeaths - $noLocality;
                        ?>
                        <table class="table table-dark table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Locality</th>
                                    <th class="text-end">Deaths</th>
                                    <th class="text-end">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data as $row): 
                                $percent = $withLocality > 0 ? ($row['CountLocality'] / $withLocality) * 100 : 0;
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['Locality']) ?></td>
                                    <td class="text-end"><?= number_format($row['CountLocality']) ?></td>
                                    <td class="text-end"><?= round($percent, 1) ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer small text-muted">
                        Total with Locality: <?= number_format($withLocality) ?> | No Locality: <?= number_format($noLocality) ?>
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
