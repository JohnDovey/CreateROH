<?php
/**
 * migrate_foreign_keys.php - SQLite Compatible (Indexes + PRAGMA)
 */
require_once("include/db.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ROH - Database Optimization</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Database Optimization</h1>

        <div class="row justify-content-md-center">
            <div class="col-lg-10">

                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_migration'])): ?>
                    <?php
                    $db = db()->getConnection();

                    echo "<h4>Enabling Foreign Key Support...</h4>";
                    $db->exec("PRAGMA foreign_keys = ON;");

                    echo "<h4>Creating Indexes...</h4>";

                    $indexes = [
                        "CREATE INDEX IF NOT EXISTS idx_person_rank ON PersonInfoRaw(RankID)",
                        "CREATE INDEX IF NOT EXISTS idx_person_regiment ON PersonInfoRaw(RegimentID)",
                        "CREATE INDEX IF NOT EXISTS idx_person_unit ON PersonInfoRaw(UnitID)",
                        "CREATE INDEX IF NOT EXISTS idx_person_locality ON PersonInfoRaw(LocalityID)",
                        "CREATE INDEX IF NOT EXISTS idx_person_country ON PersonInfoRaw(CountryID)",
                        "CREATE INDEX IF NOT EXISTS idx_person_cemetery ON PersonInfoRaw(CemeteryID)",
                        "CREATE INDEX IF NOT EXISTS idx_images_person ON PersonImages(PersonNumber)",
                        "CREATE INDEX IF NOT EXISTS idx_person_datedeath ON PersonInfoRaw(DateDeath)"
                    ];

                    foreach ($indexes as $sql) {
                        try {
                            $db->exec($sql);
                            echo "✅ Index created: " . htmlspecialchars($sql) . "<br>";
                        } catch (Exception $e) {
                            echo "⚠️ " . htmlspecialchars($e->getMessage()) . "<br>";
                        }
                    }

                    echo '<div class="alert alert-success mt-4">
                            <strong>Optimization completed!</strong><br>
                            Indexes have been added and foreign key support enabled.
                          </div>';
                    ?>

                <?php else: ?>

                    <div class="card">
                        <div class="card-body">
                            <h5>Database Optimization</h5>
                            <p>This will create useful indexes and enable foreign key checking.</p>
                            <form method="post">
                                <button type="submit" name="run_migration" class="btn btn-success btn-lg">
                                    🚀 Optimize Database Now
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