<?php
/**
 * chartYear.php - Self-contained with Yellow Axis Labels
 */
require_once("include/db.php");
require_once("functions.php");

$selectedYear = isset($_GET['Year']) ? (int)$_GET['Year'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Deaths by Year</title>
    <?php require_once("include/bootstrap-head.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Deaths by Year</h1>

        <!-- Year Filter -->
        <div class="text-center mb-4">
            <form method="get" class="d-inline">
                <label for="Year" class="me-2 fw-bold">Select Year:</label>
                <select name="Year" id="Year" class="form-select d-inline w-auto" onchange="this.form.submit()">
                    <option value="">All Years</option>
                    <?php
                    $years = db()->fetchAll("SELECT DISTINCT substr(DateDeath,1,4) as Year 
                                             FROM PersonInfoRaw 
                                             WHERE DateDeath IS NOT NULL 
                                             ORDER BY Year DESC");
                    foreach ($years as $y): ?>
                        <option value="<?= $y['Year'] ?>" <?= $y['Year'] == $selectedYear ? 'selected' : '' ?>>
                            <?= $y['Year'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <div class="row justify-content-md-center">
            <div class="col-lg-10">
                <div class="card bg-primary">
                    <div class="card-body">
                        <?php
                        $where = "";
                        $params = [];
                        if ($selectedYear) {
                            $where = "WHERE DateDeath LIKE :y1 OR substr(DateDeath,1,4) = :y2";
                            $params = [':y1' => $selectedYear.'%', ':y2' => (string)$selectedYear];
                        }

                        $data = db()->fetchAll("SELECT substr(DateDeath,1,4) as Year, COUNT(*) as Count 
                                                FROM PersonInfoRaw 
                                                $where 
                                                GROUP BY Year 
                                                ORDER BY Year", $params);
                        ?>

                        <canvas id="yearChart" height="500"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    new Chart(document.getElementById('yearChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($data, 'Year')) ?>,
            datasets: [{
                label: 'Number of Deaths',
                data: <?= json_encode(array_column($data, 'Count')) ?>,
                backgroundColor: '#0d6efd',
                borderColor: '#0d6efd',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: <?= json_encode($selectedYear ? "Deaths in " . $selectedYear : "Deaths by Year") ?>,
                    color: '#fff',
                    font: { size: 18 }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: 'rgba(255,255,255,0.1)' },
                    ticks: { color: '#ffdd57' }   // Yellow
                },
                x: { 
                    grid: { color: 'rgba(255,255,255,0.1)' },
                    ticks: { color: '#ffdd57' }   // Yellow
                }
            }
        }
    });
    </script>

    <hr>
    <?php require_once("include/footer.php"); ?>
    <?php require_once("include/bootstrap-footer.php"); ?>
</body>
</html>
