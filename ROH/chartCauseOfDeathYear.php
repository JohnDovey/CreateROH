<?php
/**
 * chartCauseOfDeathYear.php
 * Secure and modern deaths by cause and year chart
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Cause of Death by Year</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>

<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Cause of Death by Year</h1>

        <div class="row justify-content-md-center">
            <!-- Main Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card bg-primary">
                    <div class="card-header">
                        <h5 class="mb-0">Cause of Death Distribution by Year</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Get chart data securely (stacked or grouped by cause)
                        $sql = "SELECT strftime('%Y', DateDeath) as Year, 
                                       CauseDeath,
                                       COUNT(*) as CountCause 
                                FROM PersonInfoRaw 
                                WHERE CauseDeath IS NOT NULL AND CauseDeath != '' 
                                GROUP BY Year, CauseDeath 
                                ORDER BY Year, CountCause DESC";
                        
                        $data = db()->fetchAll($sql);

                        // For simplicity, we'll group by year and show top causes
                        $years = [];
                        $causes = [];
                        foreach ($data as $row) {
                            $years[$row['Year']] = true;
                            $causes[$row['CauseDeath']] = true;
                        }

                        // Prepare data for Chart.js (this is simplified)
                        $LabelNames = json_encode(array_keys($years));
                        $DataPoints = json_encode(array_map(function($y) use ($data) {
                            $sum = 0;
                            foreach ($data as $row) {
                                if ($row['Year'] == $y) $sum += $row['CountCause'];
                            }
                            return $sum;
                        }, array_keys($years)));
                        ?>

                        <canvas id="StatsGraph" style="height: 520px; width: 100%;"></canvas>

                        <?php 
                        $MyChartTitle = "Deaths by Cause of Death per Year";
                        $MyChartType = $_GET['chart'] ?? 'bar';
                        if (!in_array($MyChartType, ['bar', 'line'])) {
                            $MyChartType = 'bar';
                        }
                        include_once('js/chartGeneric.php'); 
                        ?>
                    </div>
                </div>
            </div>

            <!-- Side Summary -->
            <div class="col-lg-4">
                <div class="card bg-primary h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Summary by Cause</h5>
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
                            <?php
                            $causeSql = "SELECT CauseDeath, COUNT(*) as CountCause 
                                         FROM PersonInfoRaw 
                                         WHERE CauseDeath IS NOT NULL AND CauseDeath != '' 
                                         GROUP BY CauseDeath 
                                         ORDER BY CountCause DESC";
                            $causesData = db()->fetchAll($causeSql);
                            foreach ($causesData as $row): 
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
                        Total with Cause: <?= number_format($withCause) ?> | Unknown Cause: <?= number_format($noCause) ?>
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
