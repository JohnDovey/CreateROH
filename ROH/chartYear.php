<?php
/**
 * chartYear.php - Improved with clean year dropdown
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Deaths by Year</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <?php
        $selectedYear = isset($_GET['Year']) ? (int)$_GET['Year'] : null;
        ?>

        <h1 class="display-4 text-center my-5">Deaths by Year</h1>

        <!-- Year Selector -->
        <div class="text-center mb-4">
            <form method="get" class="d-inline">
                <label for="Year" class="me-2 fw-bold">Select Year:</label>
                <select name="Year" id="Year" class="form-select d-inline w-auto" onchange="this.form.submit()">
                    <option value="">All Years</option>
                    <?php
                    $yearsSql = "SELECT DISTINCT substr(DateDeath,1,4) as Year 
                                 FROM PersonInfoRaw 
                                 WHERE DateDeath IS NOT NULL 
                                 ORDER BY Year DESC";
                    $years = db()->fetchAll($yearsSql);
                    foreach ($years as $y): 
                        $yValue = (int)$y['Year'];
                    ?>
                        <option value="<?= $yValue ?>" <?= $yValue == $selectedYear ? 'selected' : '' ?>>
                            <?= $yValue ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <div class="row justify-content-md-center">
            <div class="col-lg-8 mb-4">
                <div class="card bg-primary">
                    <div class="card-header">
                        <h5>Annual Death Toll</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $where = "";
                        $params = [];
                        if ($selectedYear) {
                            $where = "WHERE DateDeath LIKE :y1 OR substr(DateDeath,1,4) = :y2";
                            $params = [':y1' => $selectedYear.'%', ':y2' => (string)$selectedYear];
                        }

                        $sql = "SELECT substr(DateDeath,1,4) as Year, COUNT(*) as CountYearDeath 
                                FROM PersonInfoRaw 
                                $where 
                                GROUP BY Year 
                                ORDER BY Year";
                        
                        $data = db()->fetchAll($sql, $params);

                        $LabelNames = json_encode(array_column($data, 'Year'));
                        $DataPoints = json_encode(array_column($data, 'CountYearDeath'));
                        ?>

                        <canvas id="StatsGraph" style="height: 480px; width: 100%;"></canvas>

                        <?php 
                        $MyChartTitle = $selectedYear ? "Deaths in $selectedYear" : "Deaths by Year";
                        $MyChartType = 'bar';
                        include_once('js/chartGeneric.php'); 
                        ?>
                    </div>
                </div>
            </div>

            <!-- Side Table -->
            <div class="col-lg-4">
                <div class="card bg-primary h-100">
                    <div class="card-header">
                        <h5>Year Breakdown</h5>
                    </div>
                    <div class="card-body" style="max-height: 520px; overflow-y: auto;">
                        <table class="table table-dark table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Year</th>
                                    <th class="text-end">Deaths</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data as $row): ?>
                                <tr>
                                    <td>
                                        <a href="listPeopleYear.php?Year=<?= $row['Year'] ?>" class="btn btn-sm btn-outline-light">
                                            <?= $row['Year'] ?>
                                        </a>
                                    </td>
                                    <td class="text-end"><?= number_format($row['CountYearDeath']) ?></td>
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
