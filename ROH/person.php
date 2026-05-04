<?php
/**
 * person.php - Updated with DateBirth field + Lightbox
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

        <h1 class="display-3 text-center my-5">Roll of Honour: Person Info</h1>

        <?php
        $PersonNumber = isset($_GET['PersonNumber']) ? (int)$_GET['PersonNumber'] : 1;
        if ($PersonNumber < 1) $PersonNumber = 1;

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

            <div class="col-md-auto bg-primary p-4 rounded">

                <!-- Personal Details -->
                <h3 class="text-center mb-3">Personal Details</h3>
                <div class="card mb-4">
                    <div class="card-body">
                        <table class="table table-dark table-striped">
                            <tr><td>Service No:</td><td><?= htmlspecialchars($row['ServiceNo'] ?? 'Unknown') ?></td></tr>
                            <tr><td>Rank:</td><td><?= htmlspecialchars($row['Rank'] ?? 'Unknown') ?></td></tr>
                            <tr><td>Last Name:</td><td><?= htmlspecialchars($row['LastName'] ?? '') ?></td></tr>
                            <tr><td>First Name:</td><td><?= htmlspecialchars($row['FirstName'] ?? '') ?></td></tr>
                            <tr><td>Initials:</td><td><?= htmlspecialchars($row['Initials'] ?? '') ?></td></tr>
                            <tr><td>Born:</td><td><?= htmlspecialchars($row['DateBirth']) ?></td></tr>
                        </table>
                    </div>
                </div>

                <!-- Death Details -->
                <h3 class="text-center mb-3">Death Details</h3>
                <div class="card mb-4">
                    <div class="card-body">
                        <table class="table table-dark table-striped">
                            <tr><td>Date of Death:</td><td><?= htmlspecialchars($row['DateDeath'] ?? 'Unknown') ?></td></tr>
                            <tr><td>Age at Death:</td><td><?= htmlspecialchars($row['Age'] ?? 'Unknown') ?></td></tr>
                            <tr><td>Cause of Death:</td><td><?= htmlspecialchars($row['CauseDeath'] ?? 'Unknown') ?></td></tr>
                        </table>
                    </div>
                </div>

                <!-- Images -->
                <h3 class="text-center mb-3">Images</h3>

                <?php
                $imgSql = "SELECT * FROM PersonImages WHERE PersonNumber = :pn";
                $images = db()->fetchAll($imgSql, [':pn' => $PersonNumber]);

                if (empty($images)) {
                    echo '<p class="text-muted text-center">No images available for this person.</p>';
                } else {
                    foreach ($images as $img):
                        $imgSrc = GetImgSrc($img['id']);
                        $altText = $img['ImgUrl'] ? 'Photo from ' . $img['ImgUrl'] : 'Photo of ' . ($row['Name'] ?? 'person');
                ?>
                    <div class="card mb-4 shadow-sm">
                        <img src="<?= htmlspecialchars($imgSrc) ?>" 
                             class="card-img-top img-fluid" 
                             alt="<?= htmlspecialchars($altText) ?>"
                             style="cursor: pointer; max-height: 420px; object-fit: contain;"
                             onclick="showImageModal('<?= htmlspecialchars($imgSrc) ?>')">
                    </div>
                <?php endforeach; } ?>

                <!-- Navigation -->
                <?php
                $prev = max(1, $PersonNumber - 1);
                $next = $PersonNumber + 1;
                ?>
                <div class="text-center my-4">
                    <a href="person.php?PersonNumber=<?= $prev ?>" class="btn btn-secondary">← Previous</a>
                    <a href="person.php?PersonNumber=<?= $next ?>" class="btn btn-secondary">Next →</a>
                </div>

            </div>
            <div class="col col-lg-2"></div>
        </div>

        <?php } // end if $row ?>
    </div>

    <!-- Image Lightbox Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-2">
                    <img id="modalImage" src="" class="img-fluid" style="max-height: 85vh;">
                </div>
            </div>
        </div>
    </div>

    <hr>
    <?php require_once("include/footer.php"); ?>
    <?php require_once("include/bootstrap-footer.php"); ?>

    <script>
    function showImageModal(src) {
        document.getElementById('modalImage').src = src;
        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        modal.show();
    }
    </script>
</body>
</html>
