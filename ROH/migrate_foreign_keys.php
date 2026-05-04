<?php
/**
 * migrate_foreign_keys.php - SQLite Compatible Version
 */
require_once("include/db.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ROH - Add Foreign Keys</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Add Foreign Keys (SQLite Version)</h1>

        <div class="row justify-content-md-center">
            <div class="col-lg-10">

                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_migration'])): ?>
                    <?php
                    $db = db()->getConnection();
                    echo '<h4>Adding Foreign Keys...</h4>';

                    $statements = [
                        "ALTER TABLE PersonInfoRaw ADD FOREIGN KEY (RankID) REFERENCES Rank(RankID)",
                        "ALTER TABLE PersonInfoRaw ADD FOREIGN KEY (RegimentID) REFERENCES Regiment(RegimentID)",
                        "ALTER TABLE PersonInfoRaw ADD FOREIGN KEY (UnitID) REFERENCES Unit(UnitID)",
                        "ALTER TABLE PersonInfoRaw ADD FOREIGN KEY (LocalityID) REFERENCES Locality(LocalityID)",
                        "ALTER TABLE PersonInfoRaw ADD FOREIGN KEY (CountryID) REFERENCES Country(CountryID)",
                        "ALTER TABLE PersonInfoRaw ADD FOREIGN KEY (CemeteryID) REFERENCES Cemetery(CemeteryID)",
                        "ALTER TABLE PersonImages ADD FOREIGN KEY (PersonNumber) REFERENCES PersonInfoRaw(PersonNumber)",
                        "ALTER TABLE rawweb ADD FOREIGN KEY (PersonNumber) REFERENCES PersonInfoRaw(PersonNumber)"
                    ];

                    foreach ($statements as $sql) {
                        try {
                            $db->exec($sql);
                            echo "✅ Added: " . htmlspecialchars($sql) . "<br>";
                        } catch (Exception $e) {
                            if (strpos($e->getMessage(), 'already exists') !== false || strpos($e->getMessage(), 'constraint') !== false) {
                                echo "⚠️ Already exists: " . htmlspecialchars($sql) . "<br>";
                            } else {
                                echo "❌ Failed: " . htmlspecialchars($e->getMessage()) . "<br>";
                            }
                        }
                    }

                    echo '<div class="alert alert-success mt-4">Migration finished.</div>';
                    ?>

                <?php else: ?>

                    <div class="card">
                        <div class="card-body">
                            <h5>SQLite Foreign Key Migration</h5>
                            <p>This version uses SQLite-compatible syntax.</p>
                            <form method="post">
                                <button type="submit" name="run_migration" class="btn btn-danger btn-lg"
                                        onclick="return confirm('Run foreign key migration?')">
                                    🚀 Add Foreign Keys
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
