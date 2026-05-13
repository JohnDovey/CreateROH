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
        /* Pie needs a definite box; max-height on canvas alone often collapses Chart.js */
        .cause-chart-wrap {
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

        <h1 class="display-4 text-center my-5">Cause of Death Statistics</h1>

        <?php
        $data = db()->fetchAll("SELECT CauseDeath, COUNT(*) as CountCause 
                                FROM PersonInfoRaw 
                                WHERE CauseDeath IS NOT NULL 
                                  AND CauseDeath != '' 
                                  AND CauseDeath != 'Unknown'
                                GROUP BY CauseDeath 
                                ORDER BY CountCause DESC");

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
                    $chartLabels[] = $row['CauseDeath'];
                    $chartValues[] = (int) $row['CountCause'];
                }
                $pieColors = array_slice($palette, 0, count($chartValues));
            } else {
                for ($i = 0; $i < $pieTop; $i++) {
                    $chartLabels[] = $data[$i]['CauseDeath'];
                    $chartValues[] = (int) $data[$i]['CountCause'];
                }
                $other = 0;
                for ($i = $pieTop, $n = count($data); $i < $n; $i++) {
                    $other += (int) $data[$i]['CountCause'];
                }
                if ($other > 0) {
                    $remainingTypes = count($data) - $pieTop;
                    $chartLabels[] = 'Other (' . $remainingTypes . ' categories)';
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
                            Chart shows the <?= (int) $pieTop ?> most common causes plus one &ldquo;Other&rdquo; slice when needed. The table lists every cause.
                        </p>
                        <div class="cause-chart-wrap">
                            <canvas id="causeChart"></canvas>
                        </div>
                        <?php else: ?>
                        <p class="text-muted mb-0">There are no known causes of death in the database to chart yet.</p>
                        <?php endif; ?>
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

    <?php if ($hasChartData): ?>
    <script>
    window.addEventListener('load', function() {
        var ctx = document.getElementById('causeChart');
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