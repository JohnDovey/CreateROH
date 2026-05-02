<?php
/**
 * chartAge.php
 * Secure and modern deaths by age chart
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Deaths by Age</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>

<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Deaths by Age Group</h1>

        <div class="row justify-content-md-center">
            <!-- Main Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card bg-primary">
                    <div class="card-header">
                        <h5 class="mb-0">Age Distribution at Time of Death</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Get chart data securely (grouped by age)
                        $sql = "SELECT 
                                    CASE 
                                        WHEN Age < 18 THEN 'Under 18'
                                        WHEN Age BETWEEN 18 AND 24 THEN '18-24'
                                        WHEN Age BETWEEN 25 AND 29 THEN '25-29'
                                        WHEN Age BETWEEN 30 AND 34 THEN '30-34'
                                        WHEN Age BETWEEN 35 AND 39 THEN '35-39'
                                        WHEN Age >= 40 THEN '40+'
                                        ELSE 'Unknown'
                                    END as AgeGroup,
                                    COUNT(*) as CountAge
                                FROM PersonInfoRaw 
                                GROUP BY AgeGroup 
                                ORDER BY 
                                    CASE AgeGroup
                                        WHEN 'Under 18' THEN 1
                                        WHEN '18-24' THEN 2
                                        WHEN '25-29' THEN 3
                                        WHEN '30-34' THEN 4
                                        WHEN '35-39' THEN 5
                                        WHEN '40+' THEN 6
                                        ELSE 7
                                    END";
                        
                        $data = db()->fetchAll($sql);

                        $LabelNames = json_encode(array_column($data, 'AgeGroup'));
                        $DataPoints = json_encode(array_column($data, 'CountAge'));
                        ?>

                        <canvas id="StatsGraph" style="height: 520px; width: 100%;"></canvas>

                        <?php 
                        $MyChartTitle = "Deaths by Age Group";
                        $MyChartType = $_GET['chart'] ?? 'bar';
                        if (!in_array($MyChartType, ['bar', 'line', 'pie', 'doughnut'])) {
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
                        <h5 class="mb-0">Age Breakdown</h5>
                    </div>
                    <div class="card-body" style="max-height: 560px; overflow-y: auto;">
                        <?php
                        $totalDeaths = CountTotalDeaths();
                        $noAge       = CountNoAge();
                        $withAge     = $totalDeaths - $noAge;
                        ?>
                        <table class="table table-dark table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Age Group</th>
                                    <th class="text-end">Deaths</th>
                                    <th class="text-end">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data as $row): 
                                $percent = $withAge > 0 ? ($row['CountAge'] / $withAge) * 100 : 0;
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['AgeGroup']) ?></td>
                                    <td class="text-end"><?= number_format($row['CountAge']) ?></td>
                                    <td class="text-end"><?= round($percent, 1) ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer small text-muted">
                        Total with Known Age: <?= number_format($withAge) ?> | Unknown Age: <?= number_format($noAge) ?>
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
