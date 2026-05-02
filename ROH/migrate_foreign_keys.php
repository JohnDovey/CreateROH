<?php
/**
 * migrate_foreign_keys.php
 * One-time migration script to add proper foreign keys
 */
require_once("include/db.php");

$pageTitle = "Database Migration - Add Foreign Keys";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5"><?= htmlspecialchars($pageTitle) ?></h1>

        <div class="row justify-content-md-center">
            <div class="col-md-8">

                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_migration'])) {
                    try {
                        $db = db()->getConnection();
                        $db->exec('PRAGMA foreign_keys = ON;');

                        echo '<div class="alert alert-success">';

                        // Add Foreign Keys
                        $migrations = [
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_rank FOREIGN KEY (RankID) REFERENCES Rank(RankID)" => "Rank",
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_regiment FOREIGN KEY (RegimentID) REFERENCES Regiment(RegimentID)" => "Regiment",
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_unit FOREIGN KEY (UnitID) REFERENCES Unit(UnitID)" => "Unit",
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_locality FOREIGN KEY (LocalityID) REFERENCES Locality(LocalityID)" => "Locality",
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_country FOREIGN KEY (CountryID) REFERENCES Country(CountryID)" => "Country",
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_cemetery FOREIGN KEY (CemeteryID) REFERENCES Cemetery(CemeteryID)" => "Cemetery",
                            "ALTER TABLE PersonImages ADD CONSTRAINT fk_images_person FOREIGN KEY (PersonNumber) REFERENCES PersonInfoRaw(PersonNumber)" => "PersonImages",
                            "ALTER TABLE rawweb ADD CONSTRAINT fk_rawweb_person FOREIGN KEY (PersonNumber) REFERENCES PersonInfoRaw(PersonNumber)" => "rawweb"
                        ];

                        foreach ($migrations as $sql => $name) {
                            try {
                                $db->exec($sql);
                                echo "✅ Added foreign key for <strong>$name</strong><br>";
                            } catch (Exception $e) {
                                if (strpos($e->getMessage(), 'already exists') !== false) {
                                    echo "⚠️ Foreign key for <strong>$name</strong> already exists<br>";
                                } else {
                                    echo "❌ Error for $name: " . htmlspecialchars($e->getMessage()) . "<br>";
                                }
                            }
                        }

                        echo '</div><div class="alert alert-info">Migration completed. Foreign keys are now active.</div>';

                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger">Migration failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    }
                }
                ?>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Foreign Key Migration</h5>
                        <p class="card-text">
                            This script adds proper foreign key constraints to maintain data integrity.<br>
                            <strong>Recommended to run only once.</strong>
                        </p>

                        <form method="post">
                            <button type="submit" name="run_migration" class="btn btn-danger btn-lg" 
                                    onclick="return confirm('This will add foreign keys. Continue?')">
                                🚀 Run Foreign Key Migration
                            </button>
                        </form>

                        <hr>
                        <p class="small text-muted">
                            After running, you can enable foreign key enforcement permanently by adding 
                            <code>PRAGMA foreign_keys = ON;</code> in your <code>db.php</code> (already done in the updated version).
                        </p>
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
