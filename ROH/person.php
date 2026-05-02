<?php
/**
 * ROH - person.php
 * Secure version using the new db() helper
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Person Info</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>

<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-3 text-center">Roll of Honour: Person Info</h1>

        <?php
        // Secure input handling
        $PersonNumber = isset($_GET['PersonNumber']) ? (int)$_GET['PersonNumber'] : 1;
        if ($PersonNumber < 1) {
            $PersonNumber = 1;
        }

        // Fetch person data securely
        $sql = "SELECT *, strftime('%Y', DateDeath) as Year 
                FROM PersonInfoRaw 
                WHERE PersonNumber = :pn";
        $row = db()->fetchOne($sql, [':pn' => $PersonNumber]);

        if (!$row) {
            echo '<div class="alert alert-danger text-center">Person not found.</div>';
        } else {
        ?>

        <div class="row justify-content-md-center">
            <div class="col col-lg-2"></div>

            <!-- Main Content -->
            <div class="col-md-auto bg-primary">

                <h2 class="border rounded-circle text-center">Person Info</h2>

                <!-- Person Details Card -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h3 class="card-title alert alert-success">Personal Details</h3>
                        <table class="table table-dark table-striped table-responsive">
                            <tr>
                                <td>Service No:</td>
                                <td><?= htmlspecialchars($row['ServiceNo'] ?? 'Unknown') ?></td>
                            </tr>
                            <tr>
                                <td>Rank:</td>
                                <td><?= htmlspecialchars($row['Rank'] ?? 'Unknown') ?></td>
                            </tr>
                            <tr>
                                <td>Last Name:</td>
                                <td><?= htmlspecialchars($row['LastName'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td>First Name:</td>
                                <td><?= htmlspecialchars($row['FirstName'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <td>Initials:</td>
                                <td><?= htmlspecialchars($row['Initials'] ?? '') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer">
                        <p>Person Number: 
                            <strong><?= $row['PersonNumber'] ?></strong>
                        </p>
                    </div>
                </div>

                <!-- Death Details Card -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h3 class="card-title alert alert-success">Death Details</h3>
                        <table class="table table-dark table-striped table-responsive">
                            <tr>
                                <td>Date of Death:</td>
                                <td>
                                    <?= htmlspecialchars($row['DateDeath'] ?? 'Unknown') ?>
                                    <a href="listPeopleYear.php?Year=<?= $row['Year'] ?? '' ?>" 
                                       class="btn btn-sm btn-primary">View Year</a>
                                </td>
                            </tr>
                            <tr>
                                <td>Age:</td>
                                <td><?= htmlspecialchars($row['Age'] ?? 'Unknown') ?></td>
                            </tr>
                            <tr>
                                <td>Cause of Death:</td>
                                <td><?= htmlspecialchars($row['CauseDeath'] ?? 'Unknown') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer">
                        <p>
                            Regiment/Unit: 
                            <?= htmlspecialchars($row['Regiment'] ?? '') ?> / 
                            <?= htmlspecialchars($row['Unit'] ?? '') ?>
                        </p>
                    </div>
                </div>

                <!-- Commemoration Card -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h3 class="card-title alert alert-success">Commemoration</h3>
                        <table class="table table-dark table-striped table-responsive">
                            <tr>
                                <td>Country:</td>
                                <td><?= htmlspecialchars($row['Country'] ?? 'Unknown') ?></td>
                            </tr>
                            <tr>
                                <td>Cemetery:</td>
                                <td><?= htmlspecialchars($row['Cemetery'] ?? 'Unknown') ?></td>
                            </tr>
                            <tr>
                                <td>Grave Reference:</td>
                                <td><?= htmlspecialchars($row['GraveRef'] ?? 'Unknown') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer">
                        <?php if (!empty($row['CemeteryLat']) && !empty($row['CemeteryLong'])): ?>
                            <a href="https://www.google.com/maps/place/<?= urlencode($row['CemeteryLat'] . ' ' . $row['CemeteryLong']) ?>" 
                               class="btn btn-primary" target="_blank">
                                <i class="fa fa-map-marker"></i> View on Map
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Embedded Map -->
                <?php if (!empty($row['CemeteryLat']) && !empty($row['CemeteryLong'])): ?>
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <iframe 
                            src="https://www.google.com/maps?q=<?= urlencode($row['CemeteryLat'] . ',' . $row['CemeteryLong']) ?>" 
                            width="100%" height="300" style="border:0" allowfullscreen>
                        </iframe>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Additional Info -->
                <div class="card mb-3">
                    <div class="card-body">
                        <h4 class="alert alert-success">Additional Information</h4>
                        <p><?= nl2br(htmlspecialchars($row['AddInfo'] ?? '')) ?></p>
                        
                        <h4 class="alert alert-success">Citation</h4>
                        <p><?= nl2br(htmlspecialchars($row['Citation'] ?? '')) ?></p>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <?php
                $prev = max(1, $PersonNumber - 1);
                $next = $PersonNumber + 1;
                ?>
                <div class="text-center mb-4">
                    <a href="person.php?PersonNumber=<?= $prev ?>" class="btn btn-secondary">← Previous</a>
                    <a href="person.php?PersonNumber=<?= $next ?>" class="btn btn-secondary">Next →</a>
                </div>

                <!-- Images Section -->
                <h3 class="alert alert-success text-center">Images</h3>
                <?php
                $imgSql = "SELECT * FROM PersonImages WHERE PersonNumber = :pn";
                $images = db()->fetchAll($imgSql, [':pn' => $PersonNumber]);

                if (empty($images)) {
                    echo '<p class="text-muted">No images available for this person.</p>';
                } else {
                    foreach ($images as $img) {
                        $imgSrc = GetImgSrc($img['id']);
                ?>
                    <div class="card mb-3">
                        <img src="<?= htmlspecialchars($imgSrc) ?>" 
                             class="card-img-top" 
                             alt="Photo of <?= htmlspecialchars($row['Name'] ?? 'person') ?>">
                        <div class="card-footer">
                            <small class="text-muted"><?= htmlspecialchars($img['ImgUrl'] ?? '') ?></small>
                        </div>
                    </div>
                <?php
                    }
                }
                ?>
            </div>

            <div class="col col-lg-2"></div>
        </div>

        <?php } // end if $row ?>

    </div> <!-- End Container -->

    <hr>
    <?php require_once("include/footer.php"); ?>
    <?php require_once("include/bootstrap-footer.php"); ?>
</body>
</html>
