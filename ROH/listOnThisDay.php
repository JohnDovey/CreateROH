<?php
/**
 * listOnThisDay.php - Enhanced with Monthly Death Trends
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: On This Day</title>
    <?php require_once("include/bootstrap-head.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        canvas { max-height: 380px; }
    </style>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <?php
        $selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
        $selectedDay   = isset($_GET['day']) ? (int)$_GET['day'] : (int)date('d');
        $page = max(1, (int)($_GET['page'] ?? 1));

        $recordsPerPage = 50;
        $offset = ($recordsPerPage * $page) - $recordsPerPage;

        // Count
        $sqlCount = "SELECT COUNT(*) as total 
                     FROM PersonInfoRaw 
                     WHERE strftime('%m', DateDeath) = :month 
                       AND strftime('%d', DateDeath) = :day";
        $totalOnThisDay = db()->fetchOne($sqlCount, [
            ':month' => sprintf('%02d', $selectedMonth),
            ':day'   => sprintf('%02d', $selectedDay)
        ])['total'] ?? 0;
        ?>

        <h1 class="display-4 text-center my-5">
            On This Day — <?= date('d F', mktime(0,0,0,$selectedMonth,$selectedDay)) ?> 
            <small class="text-muted">(<?= number_format($totalOnThisDay) ?> records)</small>
        </h1>

        <!-- Date Selector -->
        <div class="text-center mb-4">
            <form method="get" class="d-inline">
                <select name="month" class="form-select d-inline w-auto" onchange="this.form.submit()">
                    <?php for ($m=1; $m<=12; $m++): ?>
                        <option value="<?= $m ?>" <?= $m == $selectedMonth ? 'selected' : '' ?>>
                            <?= date('F', mktime(0,0,0,$m,1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <select name="day" class="form-select d-inline w-auto" onchange="this.form.submit()">
                    <?php for ($d=1; $d<=31; $d++): ?>
                        <option value="<?= $d ?>" <?= $d == $selectedDay ? 'selected' : '' ?>><?= $d ?></option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>

        <div class="row justify-content-md-center">
            <div class="col-md-auto bg-primary p-4 rounded shadow-sm" style="min-width: 1100px;">

                <table class="table table-dark table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Person #</th>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Rank</th>
                            <th>Regiment</th>
                            <th>Date of Death</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT *, strftime('%Y', DateDeath) as Year 
                            FROM PersonInfoRaw 
                            WHERE strftime('%m', DateDeath) = :month 
                              AND strftime('%d', DateDeath) = :day 
                            ORDER BY LastName, FirstName 
                            LIMIT :offset, :limit";

                    $params = [
                        ':month'  => sprintf('%02d', $selectedMonth),
                        ':day'    => sprintf('%02d', $selectedDay),
                        ':offset' => $offset,
                        ':limit'  => $recordsPerPage
                    ];

                    $rows = db()->fetchAll($sql, $params);

                    foreach ($rows as $row):
                    ?>
                        <tr>
                            <td><a href="person.php?PersonNumber=<?= $row['PersonNumber'] ?>" class="btn btn-sm btn-primary"><?= $row['PersonNumber'] ?></a></td>
                            <td><?= htmlspecialchars(ucwords(strtolower($row['LastName'] ?? ''))) ?></td>
                            <td><?= htmlspecialchars(ucwords(strtolower($row['FirstName'] ?? ''))) ?></td>
                            <td><?= htmlspecialchars($row['Rank'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['Regiment'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['DateDeath'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php $total_pages = ceil($totalOnThisDay / $recordsPerPage); ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item"><a class="page-link" href="?month=<?= $selectedMonth ?>&day=<?= $selectedDay ?>&page=1">« First</a></li>
                            <li class="page-item"><a class="page-link" href="?month=<?= $selectedMonth ?>&day=<?= $selectedDay ?>&page=<?= $page-1 ?>">‹ Prev</a></li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?month=<?= $selectedMonth ?>&day=<?= $selectedDay ?>&page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item"><a class="page-link" href="?month=<?= $selectedMonth ?>&day=<?= $selectedDay ?>&page=<?= $page+1 ?>">Next ›</a></li>
                            <li class="page-item"><a class="page-link" href="?month=<?= $selectedMonth ?>&day=<?= $selectedDay ?>&page=<?= $total_pages ?>">Last »</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Yearly Trend Graph -->
        <h4 class="mt-5 text-center">Deaths on <?= date('d F', mktime(0,0,0,$selectedMonth,$selectedDay)) ?> by Year</h4>
        <?php
        $graphData = db()->fetchAll("SELECT strftime('%Y', DateDeath) as Year, COUNT(*) as Count 
                                     FROM PersonInfoRaw 
                                     WHERE strftime('%m', DateDeath) = :month 
                                       AND strftime('%d', DateDeath) = :day 
                                     GROUP BY Year 
                                     ORDER BY Year", [
            ':month' => sprintf('%02d', $selectedMonth),
            ':day'   => sprintf('%02d', $selectedDay)
        ]);
        ?>
        <canvas id="trendChart" height="380"></canvas>

    </div>

    <hr>
    <?php require_once("include/footer.php"); ?>
    <?php require_once("include/bootstrap-footer.php"); ?>

    <script>
    window.addEventListener('load', function() {
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($graphData, 'Year')) ?>,
                datasets: [{
                    label: 'Deaths on this Day',
                    data: <?= json_encode(array_column($graphData, 'Count')) ?>,
                    borderColor: '#0dcaf0',
                    tension: 0.3,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: { beginAtZero: true },
                    x: { }
                }
            }
        });
    });
    </script>
</body>
</html>
