<?php
/**
 * chartCountry.php - Self-contained with Flags
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        .flag { font-size: 1.5em; margin-left: 8px; }
    </style>
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
                        <h5>Deaths by Country of Commemoration</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $sql = "SELECT Country, COUNT(*) as CountCountry 
                                FROM PersonInfoRaw 
                                WHERE CountryID > 0 
                                GROUP BY Country 
                                ORDER BY CountCountry DESC";
                        
                        $data = db()->fetchAll($sql);

                        $LabelNames = json_encode(array_column($data, 'Country'));
                        $DataPoints = json_encode(array_column($data, 'CountCountry'));
                        ?>

                        <canvas id="countryChart" style="height: 520px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Side Table with Flags -->
            <div class="col-lg-4">
                <div class="card bg-primary h-100">
                    <div class="card-header">
                        <h5>Detailed Breakdown</h5>
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
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data as $row): 
                                $percent = $withCountry > 0 ? round(($row['CountCountry'] / $withCountry) * 100, 1) : 0;
                                $flag = getCountryFlag($row['Country']);
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['Country']) ?></td>
                                    <td class="text-end"><?= number_format($row['CountCountry']) ?></td>
                                    <td class="text-end"><?= $percent ?>%</td>
                                    <td class="text-end flag"><?= $flag ?></td>
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

    <script>
    window.addEventListener('load', function() {
        new Chart(document.getElementById('countryChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($data, 'Country')) ?>,
                datasets: [{
                    label: 'Deaths',
                    data: <?= json_encode(array_column($data, 'CountCountry')) ?>,
                    backgroundColor: '#0d6efd'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { color: '#ffdd57' }},
                    x: { ticks: { color: '#ffdd57' }}
                }
            }
        });
    });
    </script>
</body>
</html>
