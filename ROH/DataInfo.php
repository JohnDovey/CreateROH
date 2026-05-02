<?php
/**
 * DataInfo.php
 * Secure database information page
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour - Database Information</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>

<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Database Information</h1>

        <div class="row justify-content-md-center">
            <div class="col col-lg-2"></div>

            <div class="col-md-auto bg-primary p-4 rounded shadow-sm" style="min-width: 900px;">

                <h3 class="alert alert-success text-center">Tables &amp; Record Counts</h3>

                <table class="table table-dark table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Table Name</th>
                            <th class="text-end">Records</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;";
                    $tables = db()->fetchAll($sql);

                    foreach ($tables as $t) {
                        $tableName = $t['name'];
                        $count = CountRecords($tableName);
                    ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($tableName) ?></strong></td>
                            <td class="text-end"><?= number_format($count) ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>

                <hr>

                <h4 class="alert alert-success">Key Statistics</h4>
                <div class="row g-3">
                    <?php
                    $stats = [
                        'Total Deaths'          => CountTotalDeaths(),
                        'Unknown Age'           => CountNoAge(),
                        'Unknown Cause'         => CountNoCause(),
                        'Unknown Country'       => CountNoCountry(),
                        'Unknown Cemetery'      => CountNoCemetery(),
                        'Distinct Regiments'    => CountDistinct('RegimentID'),
                        'Distinct Ranks'        => CountDistinct('RankID'),
                        'Distinct Units'        => CountDistinct('UnitID'),
                        'Distinct Countries'    => CountDistinct('CountryID'),
                    ];
                    ?>

                    <?php foreach ($stats as $label => $value): ?>
                    <div class="col-md-4 col-6">
                        <div class="card bg-dark text-white h-100">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= htmlspecialchars($label) ?></h5>
                                <h3 class="text-info"><?= number_format($value) ?></h3>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>

            <div class="col col-lg-2"></div>
        </div>
    </div>

    <hr>
    <?php require_once("include/footer.php"); ?>
    <?php require_once("include/bootstrap-footer.php"); ?>
</body>
</html>
