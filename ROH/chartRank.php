<?php
/**
 * chartRank.php — Deaths by rank (pie + table, aligned with chartCauseOfDeath.php)
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        .rank-chart-wrap {
            position: relative;
            height: min(520px, 70vh);
            width: 100%;
            max-width: 720px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Deaths by Rank</h1>

        <?php
        $data = db()->fetchAll("SELECT Rank, COUNT(*) AS CountRank
                                FROM PersonInfoRaw
                                WHERE RankID > 0
                                  AND Rank IS NOT NULL
                                  AND TRIM(Rank) != ''
                                GROUP BY Rank
                                ORDER BY CountRank DESC");

        $hasChartData = !empty($data);
        $pieTop = 12;
        $chartLabels = [];
        $chartValues = [];
        $pieColors = [];
        $palette = [
            '#0d6efd', '#0dcaf0', '#ffc107', '#198754', '#dc3545', '#6f42c1', '#fd7e14', '#20c997',
            '#e83e8c', '#6610f2', '#0aa2c0', '#adb5bd', '#d63384', '#845ef7', '#51cf66', '#ff922b',
        ];
        if ($hasChartData) {
            if (count($data) <= $pieTop) {
                foreach ($data as $row) {
                    $chartLabels[] = $row['Rank'];
                    $chartValues[] = (int) $row['CountRank'];
                }
                $pieColors = array_slice($palette, 0, count($chartValues));
            } else {
                for ($i = 0; $i < $pieTop; $i++) {
                    $chartLabels[] = $data[$i]['Rank'];
                    $chartValues[] = (int) $data[$i]['CountRank'];
                }
                $other = 0;
                for ($i = $pieTop, $n = count($data); $i < $n; $i++) {
                    $other += (int) $data[$i]['CountRank'];
                }
                if ($other > 0) {
                    $remainingTypes = count($data) - $pieTop;
                    $chartLabels[] = 'Other (' . $remainingTypes . ' ranks)';
                    $chartValues[] = $other;
                }
                $pieColors = array_slice($palette, 0, count($chartValues));
            }
        }
        ?>

        <div class="row justify-content-md-center">
            <div class="col-lg-8 mb-4">
                <div class="card bg-primary">
                    <div class="card-body">
                        <?php if ($hasChartData): ?>
                        <p class="small text-muted mb-2">
                            Chart shows the <?= (int) $pieTop ?> most common ranks plus one &ldquo;Other&rdquo; slice when needed. The table lists every rank in this view.
                        </p>
                        <div class="rank-chart-wrap">
                            <canvas id="rankChart"></canvas>
                        </div>
                        <?php else: ?>
                        <p class="text-muted mb-0">There are no rank records in the database to chart yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card bg-primary h-100">
                    <div class="card-header">
                        <h5>Detailed Breakdown</h5>
                    </div>
                    <div class="card-body" style="max-height: 560px; overflow-y: auto;">
                        <?php
                        $totalDeaths = CountTotalDeaths();
                        $noRank     = CountNoRank();
                        $withRank   = $totalDeaths - $noRank;
                        ?>
                        <table class="table table-dark table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th class="text-end">Deaths</th>
                                    <th class="text-end">% of known rank</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data as $row):
                                $percent = $withRank > 0 ? round(($row['CountRank'] / $withRank) * 100, 1) : 0;
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['Rank']) ?></td>
                                    <td class="text-end"><?= number_format($row['CountRank']) ?></td>
                                    <td class="text-end"><?= $percent ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer small text-muted">
                        Total with rank: <?= number_format($withRank) ?> | No rank: <?= number_format($noRank) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <?php require_once("include/footer.php"); ?>
    <?php require_once("include/bootstrap-footer.php"); ?>

    <?php if ($hasChartData): ?>
    <script>
    window.addEventListener('load', function() {
        var ctx = document.getElementById('rankChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?= json_encode($chartLabels, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                datasets: [{
                    data: <?= json_encode($chartValues, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                    backgroundColor: <?= json_encode($pieColors, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                    borderColor: 'rgba(0, 0, 0, 0.35)',
                    borderWidth: 1,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: 8 },
                plugins: {
                    legend: {
                        position: 'right',
                        align: 'center',
                        labels: {
                            color: '#e8e8e8',
                            padding: 10,
                            boxWidth: 14,
                            font: { size: 11 },
                            maxWidth: 220,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.92)',
                        titleColor: '#fff',
                        bodyColor: '#ddd',
                        callbacks: {
                            label: function(context) {
                                var val = Number(context.raw);
                                var arr = context.dataset.data.map(function(x) { return Number(x); });
                                var sum = arr.reduce(function(a, b) { return a + b; }, 0);
                                var pct = sum ? ((val / sum) * 100).toFixed(1) : '0';
                                return ' ' + val.toLocaleString() + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    });
    </script>
    <?php endif; ?>
</body>
</html>
