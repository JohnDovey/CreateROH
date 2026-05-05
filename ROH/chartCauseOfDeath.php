<?php
/**
 * chartCauseOfDeath.php - Optimized Lightweight Version
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Cause of Death</title>
    <?php require_once("include/bootstrap-head.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        canvas { max-height: 420px; }
    </style>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Cause of Death Statistics</h1>

        <?php
        $data = db()->fetchAll("SELECT CauseDeath, COUNT(*) as CountCause 
                                FROM PersonInfoRaw 
                                WHERE CauseDeath IS NOT NULL 
                                  AND CauseDeath != '' 
                                  AND CauseDeath != 'Unknown'
                                GROUP BY CauseDeath 
                                ORDER BY CountCause DESC");
        ?>

        <div class="row justify-content-md-center">
            <div class="col-lg-8 mb-4">
                <div class="card bg-primary">
                    <div class="card-body">
                        <canvas id="causeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Summary Table -->
            <div class="col-lg-4">
                <div class="card bg-primary h-100">
                    <div class="card-header">
                        <h5>Detailed Breakdown</h5>
                    </div>
                    <div class="card-body" style="max-height: 560px; overflow-y: auto;">
                        <?php
                        $total = CountTotalDeaths();
                        $noCause = CountNoCause();
                        $withCause = $total - $noCause;
                        ?>
                        <table class="table table-dark table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Cause</th>
                                    <th class="text-end">Deaths</th>
                                    <th class="text-end">% of Known</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data as $row): 
                                $percent = $withCause > 0 ? round(($row['CountCause'] / $withCause) * 100, 1) : 0;
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['CauseDeath']) ?></td>
                                    <td class="text-end"><?= number_format($row['CountCause']) ?></td>
                                    <td class="text-end"><?= $percent ?>%</td>
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
        new Chart(document.getElementById('causeChart'), {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_column($data, 'CauseDeath')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($data, 'CountCause')) ?>,
                    backgroundColor: [
                        '#0d6efd', '#0dcaf0', '#ffc107', '#198754', 
                        '#dc3545', '#6f42c1', '#fd7e14', '#20c997'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#ddd', padding: 15 }
                    }
                }
            }
        });
    });
    </script>
</body>
</html>
