<?php
/**
 * add_dateofbirth.php
 * Adds DateBirth column + calculates approximate birth year
 */
require_once("include/db.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ROH - Add & Calculate Date of Birth</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Add & Calculate Date of Birth</h1>

        <div class="row justify-content-md-center">
            <div class="col-lg-8">

                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_calculation'])): ?>
                    <?php
                    $db = db()->getConnection();

                    echo "<h5>Step 1: Adding DateBirth column...</h5>";

                    try {
                        $db->exec("ALTER TABLE PersonInfoRaw ADD COLUMN DateBirth TEXT");
                        echo '<div class="alert alert-success">DateBirth column added.</div>';
                    } catch (Exception $e) {
                        if (strpos($e->getMessage(), 'duplicate column') !== false) {
                            echo '<div class="alert alert-info">DateBirth column already exists.</div>';
                        } else {
                            echo '<div class="alert alert-danger">' . htmlspecialchars($e->getMessage()) . '</div>';
                        }
                    }

                    echo "<h5>Step 2: Calculating approximate birth years...</h5>";

                    $sql = "UPDATE PersonInfoRaw 
                            SET DateBirth = 
                                CASE 
                                    WHEN DateDeath IS NOT NULL 
                                     AND Age IS NOT NULL 
                                     AND Age > 0 
                                     AND Age < 120 
                                    THEN 
                                        substr(DateDeath,1,4) - Age
                                    ELSE NULL
                                END
                            WHERE DateBirth IS NULL";

                    try {
                        $affected = $db->exec($sql);
                        echo "<div class='alert alert-success'>
                                <strong>Success!</strong><br>
                                Updated <strong>$affected</strong> records with approximate birth year.
                              </div>";
                    } catch (Exception $e) {
                        echo "<div class='alert alert-danger'>Update failed: " . htmlspecialchars($e->getMessage()) . "</div>";
                    }
                    ?>

                <?php else: ?>

                    <div class="card">
                        <div class="card-body">
                            <h5>Add Date of Birth + Calculate from existing data</h5>
                            <p>This will:</p>
                            <ol>
                                <li>Add <strong>DateBirth</strong> column (if missing)</li>
                                <li>Calculate approximate birth year using <code>DateDeath - Age</code></li>
                            </ol>
                            <p><strong>Note:</strong> This is an approximation only.</p>

                            <form method="post">
                                <button type="submit" name="run_calculation" class="btn btn-primary btn-lg"
                                        onclick="return confirm('Add DateBirth and calculate birth years?')">
                                    🚀 Add & Calculate Date of Birth
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
