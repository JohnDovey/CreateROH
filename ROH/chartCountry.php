<?php
/**
 * chartCountry.php
 * Secure and modern deaths by country chart
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Deaths by Country</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>

<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Deaths by Country</h1>

        <div class="row justify-content-md-center">
            <!-- Main Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card bg-primary">
                    <div class="card-header">
                        <h5 class="mb-0">Deaths by Country of Commemoration</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Get chart data securely
                        $sql = "SELECT Country, COUNT(*) as CountCountry 
                                FROM PersonInfoRaw 
                                WHERE CountryID > 0 
                                GROUP BY Country 
                                ORDER BY CountCountry DESC";
                        
                        $data = db()->fetchAll($sql);

                        $LabelNames = json_encode(array_column($data, 'Country'));
                        $DataPoints = json_encode(array_column($data, 'CountCountry'));
                        ?>

                        <canvas id="StatsGraph" style="height: 520px; width: 100%;"></canvas>

                        <?php 
                        $MyChartTitle = "Deaths by Country";
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
                        $noCountry   = CountNoCountry();
                        $withCountry = $totalDeaths - $noCountry;
                        ?>
                        <table class="table table-dark table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th class="text-end">Deaths</th>
                                    <th class="text-end">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data as $row): 
                                $percent = $withCountry > 0 ? ($row['CountCountry'] / $withCountry) * 100 : 0;
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['Country']) ?></td>
                                    <td class="text-end"><?= number_format($row['CountCountry']) ?></td>
                                    <td class="text-end"><?= round($percent, 1) ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer small text-muted">
                        Total with Country: <?= number_format($withCountry) ?> | No Country: <?= number_format($noCountry) ?>
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
