<?php
/**
 * migrate_foreign_keys.php
 * Fixed migration with proper orphan check + FKs + Indexes
 */
require_once("include/db.php");

$pageTitle = "Database Migration - Foreign Keys & Indexes";
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
            <div class="col-lg-10">

                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_migration'])): ?>
                    <?php
                    $db = db()->getConnection();

                    // === 1. ORPHAN CHECK ===
                    echo '<h4>Step 1: Checking for Orphaned Records...</h4>';
                    $orphanChecks = [
                        ['field' => 'RankID',     'refTable' => 'Rank',     'refField' => 'RankID'],
                        ['field' => 'RegimentID', 'refTable' => 'Regiment', 'refField' => 'RegimentID'],
                        ['field' => 'UnitID',     'refTable' => 'Unit',     'refField' => 'UnitID'],
                        ['field' => 'LocalityID', 'refTable' => 'Locality', 'refField' => 'LocalityID'],
                        ['field' => 'CountryID',  'refTable' => 'Country',  'refField' => 'CountryID'],
                        ['field' => 'CemeteryID', 'refTable' => 'Cemetery', 'refField' => 'CemeteryID'],
                    ];

                    $hasOrphans = false;
                    foreach ($orphanChecks as $check) {
                        $sql = "SELECT COUNT(*) as orphans 
                                FROM PersonInfoRaw 
                                LEFT JOIN {$check['refTable']} ON PersonInfoRaw.{$check['field']} = {$check['refTable']}.{$check['refField']}
                                WHERE {$check['refTable']}.{$check['refField']} IS NULL 
                                  AND PersonInfoRaw.{$check['field']} IS NOT NULL";
                        $result = db()->fetchOne($sql);
                        $count = $result['orphans'] ?? 0;

                        if ($count > 0) {
                            $hasOrphans = true;
                            echo "<div class='alert alert-warning'>⚠️ {$count} orphaned records for {$check['field']}</div>";
                        } else {
                            echo "<div class='alert alert-success'>✅ No orphans for {$check['field']}</div>";
                        }
                    }

                    if ($hasOrphans) {
                        echo '<div class="alert alert-danger">Fix orphaned records before continuing.</div>';
                    } else {
                        // === 2. ADD FOREIGN KEYS ===
                        echo '<h4>Step 2: Adding Foreign Keys...</h4>';
                        $fkList = [
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_rank FOREIGN KEY (RankID) REFERENCES Rank(RankID)" => "Rank",
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_regiment FOREIGN KEY (RegimentID) REFERENCES Regiment(RegimentID)" => "Regiment",
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_unit FOREIGN KEY (UnitID) REFERENCES Unit(UnitID)" => "Unit",
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_locality FOREIGN KEY (LocalityID) REFERENCES Locality(LocalityID)" => "Locality",
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_country FOREIGN KEY (CountryID) REFERENCES Country(CountryID)" => "Country",
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_cemetery FOREIGN KEY (CemeteryID) REFERENCES Cemetery(CemeteryID)" => "Cemetery",
                            "ALTER TABLE PersonImages ADD CONSTRAINT fk_images_person FOREIGN KEY (PersonNumber) REFERENCES PersonInfoRaw(PersonNumber)" => "PersonImages",
                            "ALTER TABLE rawweb ADD CONSTRAINT fk_rawweb_person FOREIGN KEY (PersonNumber) REFERENCES PersonInfoRaw(PersonNumber)" => "rawweb"
                        ];

                        foreach ($fkList as $sql => $name) {
                            try {
                                $db->exec($sql);
                                echo "✅ Foreign key added for <strong>$name</strong><br>";
                            } catch (Exception $e) {
                                if (strpos($e->getMessage(), 'already exists') !== false) {
                                    echo "⚠️ Foreign key for <strong>$name</strong> already exists<br>";
                                } else {
                                    echo "❌ Error on $name: " . htmlspecialchars($e->getMessage()) . "<br>";
                                }
                            }
                        }

                        // === 3. INDEXES ===
                        echo '<h4>Step 3: Creating Indexes...</h4>';
                        $indexes = [
                            "CREATE INDEX IF NOT EXISTS idx_person_rank ON PersonInfoRaw(RankID)",
                            "CREATE INDEX IF NOT EXISTS idx_person_regiment ON PersonInfoRaw(RegimentID)",
                            "CREATE INDEX IF NOT EXISTS idx_person_unit ON PersonInfoRaw(UnitID)",
                            "CREATE INDEX IF NOT EXISTS idx_person_locality ON PersonInfoRaw(LocalityID)",
                            "CREATE INDEX IF NOT EXISTS idx_person_country ON PersonInfoRaw(CountryID)",
                            "CREATE INDEX IF NOT EXISTS idx_person_cemetery ON PersonInfoRaw(CemeteryID)",
                            "CREATE INDEX IF NOT EXISTS idx_images_person ON PersonImages(PersonNumber)"
                        ];

                        foreach ($indexes as $idx) {
                            try {
                                $db->exec($idx);
                                echo "✅ Index created<br>";
                            } catch (Exception $e) {
                                echo "⚠️ Index already exists or error: " . htmlspecialchars($e->getMessage()) . "<br>";
                            }
                        }

                        echo '<div class="alert alert-success mt-4">✅ Migration completed successfully!</div>';
                    }
                    ?>

                <?php else: ?>

                    <div class="card">
                        <div class="card-body">
                            <h5>Full Database Migration</h5>
                            <p>This will check for orphaned records, add foreign keys, and create indexes.</p>
                            <form method="post">
                                <button type="submit" name="run_migration" class="btn btn-danger btn-lg"
                                        onclick="return confirm('Run full migration now?')">
                                    🚀 Run Migration
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
