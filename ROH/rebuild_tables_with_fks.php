<?php
/**
 * rebuild_tables_with_fks.php
 * Advanced migration - Rebuilds tables with proper Foreign Keys
 * (SQLite does not allow easy ALTER TABLE for FKs)
 */
require_once("include/db.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ROH - Rebuild Tables with Foreign Keys</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Rebuild Tables with Foreign Keys</h1>

        <div class="row justify-content-md-center">
            <div class="col-lg-10">

                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_rebuild'])): ?>
                    <?php
                    $db = db()->getConnection();
                    echo "<h4>Starting Table Rebuild...</h4>";

                    // 1. Enable foreign keys
                    $db->exec("PRAGMA foreign_keys = ON;");

                    // 2. Rebuild main tables
                    $rebuilds = [
                        'PersonInfoRaw' => [
                            "CREATE TABLE PersonInfoRaw_new (
                                id INTEGER PRIMARY KEY,
                                PersonNumber INTEGER UNIQUE,
                                Name TEXT,
                                FirstName TEXT,
                                LastName TEXT,
                                Initials TEXT,
                                ServiceNo TEXT,
                                Rank TEXT,
                                RankID INTEGER REFERENCES Rank(RankID),
                                Regiment TEXT,
                                RegimentID INTEGER REFERENCES Regiment(RegimentID),
                                Unit TEXT,
                                UnitID INTEGER REFERENCES Unit(UnitID),
                                Locality TEXT,
                                LocalityID INTEGER REFERENCES Locality(LocalityID),
                                DateDeath TEXT,
                                Age TEXT,
                                CauseDeath TEXT,
                                AddInfo TEXT,
                                Citation TEXT,
                                Country TEXT,
                                CountryID INTEGER REFERENCES Country(CountryID),
                                Cemetery TEXT,
                                CemeteryID INTEGER REFERENCES Cemetery(CemeteryID),
                                CemeteryLat TEXT,
                                CemeteryLong TEXT,
                                GraveRef TEXT,
                                DateChecked TEXT
                            )",
                            "INSERT INTO PersonInfoRaw_new SELECT * FROM PersonInfoRaw",
                            "DROP TABLE PersonInfoRaw",
                            "ALTER TABLE PersonInfoRaw_new RENAME TO PersonInfoRaw"
                        ],

                        'PersonImages' => [
                            "CREATE TABLE PersonImages_new (
                                id INTEGER PRIMARY KEY,
                                PersonNumber INTEGER REFERENCES PersonInfoRaw(PersonNumber),
                                ImgName TEXT,
                                ImgPath TEXT,
                                ImgUrl TEXT,
                                ImgUrlComplete TEXT
                            )",
                            "INSERT INTO PersonImages_new SELECT * FROM PersonImages",
                            "DROP TABLE PersonImages",
                            "ALTER TABLE PersonImages_new RENAME TO PersonImages"
                        ],

                        'rawweb' => [
                            "CREATE TABLE rawweb_new (
                                id INTEGER PRIMARY KEY AUTOINCREMENT,
                                StartTime TEXT,
                                EndTime TEXT,
                                PageSize NUMERIC,
                                PersonNumber INTEGER REFERENCES PersonInfoRaw(PersonNumber),
                                WebAddress TEXT,
                                WebPage TEXT
                            )",
                            "INSERT INTO rawweb_new SELECT * FROM rawweb",
                            "DROP TABLE rawweb",
                            "ALTER TABLE rawweb_new RENAME TO rawweb"
                        ]
                    ];

                    foreach ($rebuilds as $table => $steps) {
                        echo "<h5>Rebuilding <strong>{$table}</strong>...</h5>";
                        foreach ($steps as $sql) {
                            try {
                                $db->exec($sql);
                                echo "✅ {$sql}<br>";
                            } catch (Exception $e) {
                                echo "❌ Error: " . htmlspecialchars($e->getMessage()) . "<br>";
                            }
                        }
                    }

                    // 3. Create indexes
                    echo "<h4>Creating Indexes...</h4>";
                    $indexes = [
                        "CREATE INDEX IF NOT EXISTS idx_person_rank ON PersonInfoRaw(RankID)",
                        "CREATE INDEX IF NOT EXISTS idx_person_regiment ON PersonInfoRaw(RegimentID)",
                        "CREATE INDEX IF NOT EXISTS idx_person_unit ON PersonInfoRaw(UnitID)",
                        "CREATE INDEX IF NOT EXISTS idx_person_locality ON PersonInfoRaw(LocalityID)",
                        "CREATE INDEX IF NOT EXISTS idx_person_country ON PersonInfoRaw(CountryID)",
                        "CREATE INDEX IF NOT EXISTS idx_person_cemetery ON PersonInfoRaw(CemeteryID)",
                        "CREATE INDEX IF NOT EXISTS idx_images_person ON PersonImages(PersonNumber)"
                    ];

                    foreach ($indexes as $sql) {
                        $db->exec($sql);
                        echo "✅ Index: " . htmlspecialchars($sql) . "<br>";
                    }

                    echo '<div class="alert alert-success mt-4"><strong>All tables rebuilt with proper Foreign Keys!</strong></div>';
                    ?>

                <?php else: ?>

                    <div class="card border-danger">
                        <div class="card-body">
                            <h5 class="text-danger">⚠️ Advanced Rebuild Operation</h5>
                            <p>This will <strong>rebuild</strong> the main tables with proper Foreign Key constraints.</p>
                            <p><strong>Backup your database first!</strong> (Make a copy of RohData.sql3)</p>
                            <form method="post">
                                <button type="submit" name="run_rebuild" class="btn btn-danger btn-lg"
                                        onclick="return confirm('This will rebuild tables. Continue?')">
                                    🚀 Rebuild Tables with Foreign Keys
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