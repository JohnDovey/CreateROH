<?php
/**
 * chartCauseOfDeath.php - Updated with Year Filter
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
    <title>Roll of Honour: Cause of Death</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Cause of Death Statistics</h1>

        <!-- Year Filter -->
        <div class="text-center mb-4">
            <form method="get" class="d-inline">
                <label for="Year" class="me-2 fw-bold">Filter by Year:</label>
                <select name="Year" class="form-select d-inline w-auto" onchange="this.form.submit()">
                    <option value="">All Years</option>
                    <?php
                    $years = db()->fetchAll("SELECT DISTINCT substr(DateDeath,1,4) as Year FROM PersonInfoRaw WHERE DateDeath IS NOT NULL ORDER BY Year DESC");
                    foreach ($years as $y): ?>
                        <option value="<?= $y['Year'] ?>" <?= $y['Year'] == $selectedYear ? 'selected' : '' ?>>
                            <?= $y['Year'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <div class="row justify-content-md-center">
            <div class="col-lg-8 mb-4">
                <div class="card bg-primary">
                    <div class="card-header">
                        <h5>Cause of Death Distribution <?= $selectedYear ? "in $selectedYear" : "" ?></h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $where = $selectedYear ? "AND (DateDeath LIKE :y1 OR substr(DateDeath,1,4) = :y2)" : "";
                        $params = $selectedYear ? [':y1' => $selectedYear.'%', ':y2' => (string)$selectedYear] : [];

                        $sql = "SELECT CauseDeath, COUNT(*) as CountCause 
                                FROM PersonInfoRaw 
                                WHERE CauseDeath IS NOT NULL AND CauseDeath != '' AND CauseDeath != 'Unknown' 
                                $where 
                                GROUP BY CauseDeath 
                                ORDER BY CountCause DESC";
                        
                        $data = db()->fetchAll($sql, $params);

                        $LabelNames = json_encode(array_column($data, 'CauseDeath'));
                        $DataPoints = json_encode(array_column($data, 'CountCause'));
                        ?>

                        <canvas id="StatsGraph" style="height: 520px;"></canvas>
                        <?php 
                        $MyChartTitle = $selectedYear ? "Causes in $selectedYear" : "Causes of Death";
                        $MyChartType = 'pie';
                        include_once('js/chartGeneric.php'); 
                        ?>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="col-lg-4">
                <div class="card bg-primary h-100">
                    <div class="card-body" style="max-height: 560px; overflow-y:auto;">
                        <table class="table table-dark table-striped">
                            <thead><tr><th>Cause</th><th class="text-end">Count</th></tr></thead>
                            <tbody>
                            <?php foreach ($data as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['CauseDeath']) ?></td>
                                    <td class="text-end"><?= number_format($row['CountCause']) ?></td>
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
</body>
</html>
