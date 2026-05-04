<?php
/**
 * normalize_lookup_tables.php
 * FINAL VERSION - Drops and recreates lookup tables with consistent schema
 */
require_once("include/db.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ROH - Normalize Lookup Tables</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Normalize Lookup Tables</h1>

        <div class="row justify-content-md-center">
            <div class="col-lg-10">

                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_normalization'])): ?>
                    <?php
                    $db = db()->getConnection();
                    echo '<h4>Starting Normalization (Clean Recreation)...</h4>';

                    $lookupTables = ['Rank', 'Regiment', 'Unit', 'Locality', 'Country', 'Cemetery'];

                    foreach ($lookupTables as $tableName) {
                        $idField   = $tableName . 'ID';
                        $nameField = $tableName;   // Consistent: Rank, Regiment, etc.

                        echo "<h5>Processing <strong>{$tableName}</strong>...</h5>";

                        // Drop existing table (safe)
                        $db->exec("DROP TABLE IF EXISTS {$tableName}");

                        // Create fresh table
                        $createSQL = "CREATE TABLE {$tableName} (
                            {$idField} INTEGER PRIMARY KEY,
                            {$nameField} TEXT UNIQUE
                        )";
                        $db->exec($createSQL);

                        // Populate from PersonInfoRaw
                        $insertSQL = "INSERT OR IGNORE INTO {$tableName} ({$nameField})
                                      SELECT DISTINCT {$nameField} 
                                      FROM PersonInfoRaw 
                                      WHERE {$nameField} IS NOT NULL AND {$nameField} != ''";
                        $db->exec($insertSQL);

                        // Update PersonInfoRaw with IDs
                        $updateSQL = "UPDATE PersonInfoRaw 
                                      SET {$idField} = (
                                          SELECT {$idField} 
                                          FROM {$tableName} 
                                          WHERE {$tableName}.{$nameField} = PersonInfoRaw.{$nameField}
                                      )
                                      WHERE PersonInfoRaw.{$nameField} IS NOT NULL";
                        $db->exec($updateSQL);

                        $count = db()->fetchOne("SELECT COUNT(*) as cnt FROM {$tableName}")['cnt'] ?? 0;
                        echo "<div class='alert alert-success'>âœ… {$tableName} recreated with {$count} entries</div>";
                    }

                    echo '<div class="alert alert-success mt-4"><strong>Normalization completed successfully!</strong><br>
                          Lookup tables have been recreated with consistent structure.</div>';
                    ?>

                <?php else: ?>

                    <div class="card">
                        <div class="card-body">
                            <h5>Database Normalization Tool</h5>
                            <p><strong>This will drop and recreate lookup tables.</strong> Existing data in lookup tables will be lost but rebuilt from PersonInfoRaw.</p>
                            <form method="post">
                                <button type="submit" name="run_normalization" class="btn btn-primary btn-lg"
                                        onclick="return confirm('This will DROP existing lookup tables. Continue?')">
                                    ðŸš€ Normalize Lookup Tables Now
                                </button>
                            </form>
                        </div>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>

    <hr>
    <?php require_once("include/footer.php"); ?>
    <?php require_once("include/bootstrap-footer.php"); ?>
</body>
</html>
