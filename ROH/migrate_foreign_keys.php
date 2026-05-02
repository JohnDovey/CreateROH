<?php
/**
 * migrate_foreign_keys.php
 * Full migration: Check orphans + Add Foreign Keys + Create Indexes
 */
require_once("include/db.php");

$pageTitle = "Database Migration - Foreign Keys & Indexes";
$errors = [];
$success = [];
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
                        ['table' => 'PersonInfoRaw', 'field' => 'RankID',     'ref' => 'Rank(RankID)'],
                        ['table' => 'PersonInfoRaw', 'field' => 'RegimentID', 'ref' => 'Regiment(RegimentID)'],
                        ['table' => 'PersonInfoRaw', 'field' => 'UnitID',     'ref' => 'Unit(UnitID)'],
                        ['table' => 'PersonInfoRaw', 'field' => 'LocalityID', 'ref' => 'Locality(LocalityID)'],
                        ['table' => 'PersonInfoRaw', 'field' => 'CountryID',  'ref' => 'Country(CountryID)'],
                        ['table' => 'PersonInfoRaw', 'field' => 'CemeteryID', 'ref' => 'Cemetery(CemeteryID)'],
                    ];

                    $hasOrphans = false;
                    foreach ($orphanChecks as $check) {
                        $sql = "SELECT COUNT(*) as orphans FROM {$check['table']} 
                                LEFT JOIN {$check['ref']} 
                                ON {$check['table']}.{$check['field']} = {$check['ref']} 
                                WHERE {$check['ref']} IS NULL AND {$check['table']}.{$check['field']} IS NOT NULL";
                        $result = db()->fetchOne($sql);
                        $count = $result['orphans'] ?? 0;

                        if ($count > 0) {
                            $hasOrphans = true;
                            echo "<div class='alert alert-warning'>⚠️ {$count} orphaned records in {$check['table']}.{$check['field']}</div>";
                        } else {
                            echo "<div class='alert alert-success'>✅ No orphans in {$check['table']}.{$check['field']}</div>";
                        }
                    }

                    if ($hasOrphans) {
                        echo '<div class="alert alert-danger">Migration stopped due to orphaned records. Fix data first.</div>';
                    } else {
                        // === 2. ADD FOREIGN KEYS ===
                        echo '<h4>Step 2: Adding Foreign Keys...</h4>';
                        $fkMigrations = [
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_rank FOREIGN KEY (RankID) REFERENCES Rank(RankID)" => "Rank",
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_regiment FOREIGN KEY (RegimentID) REFERENCES Regiment(RegimentID)" => "Regiment",
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_unit FOREIGN KEY (UnitID) REFERENCES Unit(UnitID)" => "Unit",
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_locality FOREIGN KEY (LocalityID) REFERENCES Locality(LocalityID)" => "Locality",
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_country FOREIGN KEY (CountryID) REFERENCES Country(CountryID)" => "Country",
                            "ALTER TABLE PersonInfoRaw ADD CONSTRAINT fk_person_cemetery FOREIGN KEY (CemeteryID) REFERENCES Cemetery(CemeteryID)" => "Cemetery",
                            "ALTER TABLE PersonImages ADD CONSTRAINT fk_images_person FOREIGN KEY (PersonNumber) REFERENCES PersonInfoRaw(PersonNumber)" => "PersonImages",
                            "ALTER TABLE rawweb ADD CONSTRAINT fk_rawweb_person FOREIGN KEY (PersonNumber) REFERENCES PersonInfoRaw(PersonNumber)" => "rawweb"
                        ];

                        foreach ($fkMigrations as $sql => $name) {
                            try {
                                $db->exec($sql);
                                $success[] = "Added FK for $name";
                            } catch (Exception $e) {
                                if (strpos($e->getMessage(), 'already exists') !== false) {
                                    $success[] = "FK for $name already exists";
                                } else {
                                    $errors[] = "Error on $name: " . $e->getMessage();
                                }
                            }
                        }

                        // === 3. CREATE INDEXES ===
                        echo '<h4>Step 3: Creating Recommended Indexes...</h4>';
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
                                $success[] = "Created index";
                            } catch (Exception $e) {
                                $errors[] = $e->getMessage();
                            }
                        }

                        echo '<div class="alert alert-success">Migration completed successfully!</div>';
                    }
                    ?>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?= implode('<br>', $success) ?></div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
                    <?php endif; ?>

                <?php else: ?>

                    <div class="card">
                        <div class="card-body">
                            <h5>Database Integrity Migration</h5>
                            <p>This script will:</p>
                            <ol>
                                <li>Check for orphaned records</li>
                                <li>Add recommended foreign keys</li>
                                <li>Create performance indexes</li>
                            </ol>
                            <form method="post">
                                <button type="submit" name="run_migration" class="btn btn-danger btn-lg"
                                        onclick="return confirm('Run full migration? This may take a moment.')">
                                    🚀 Run Full Migration
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
