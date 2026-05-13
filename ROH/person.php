<?php
/**
 * person.php - With Automatic Image Download
 */
require_once("include/db.php");
require_once("functions.php");

$PersonNumber = isset($_GET['PersonNumber']) ? (int)$_GET['PersonNumber'] : 1;
if ($PersonNumber < 1) $PersonNumber = 1;

// Fetch person data
$row = db()->fetchOne("SELECT * FROM PersonInfoRaw WHERE PersonNumber = :pn", [':pn' => $PersonNumber]);

if (!$row) {
    echo '<div class="alert alert-danger text-center">Person not found.</div>';
    exit;
}

// Get images for this person
$images = db()->fetchAll("SELECT * FROM PersonImages WHERE PersonNumber = :pn", [':pn' => $PersonNumber]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: <?= htmlspecialchars($row['LastName'] ?? '') ?></title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">
            <?= htmlspecialchars($row['LastName'] ?? '') ?>, 
            <?= htmlspecialchars($row['FirstName'] ?? '') ?>
        </h1>

        <div class="row justify-content-md-center">
            <div class="col-lg-8">

                <!-- Personal Details -->
                <div class="card bg-primary mb-4">
                    <div class="card-body">
                        <h3 class="text-center mb-3">Personal Details</h3>
                        <table class="table table-dark">
                            <tr><th>Person Number</th><td><?= $row['PersonNumber'] ?></td></tr>
                            <tr><th>Rank</th><td><?= htmlspecialchars($row['Rank'] ?? '') ?></td></tr>
                            <tr><th>Regiment</th><td><?= htmlspecialchars($row['Regiment'] ?? '') ?></td></tr>
                            <tr><th>Date of Death</th><td><?= htmlspecialchars($row['DateDeath'] ?? '') ?></td></tr>
                            <!-- Add more fields as needed -->
                        </table>
                    </div>
                </div>

                <!-- Images Section -->
                <?php if (!empty($images)): ?>
                    <h3 class="text-center mb-3">Images</h3>
                    <div class="row g-3">
                        <?php foreach ($images as $img): 
                            $remoteUrl = $img['ImgUrlComplete'] ?? '';
                            $pathPart = parse_url((string) $remoteUrl, PHP_URL_PATH);
                            $baseName = $pathPart ? basename($pathPart) : '';
                            if ($baseName === '' || strpos($baseName, '..') !== false) {
                                $baseName = 'person_' . $PersonNumber . '_' . substr(md5($remoteUrl), 0, 12) . '.img';
                            }
                            $localPath = 'DownLoadImage/' . $baseName;
                            $fullLocalPath = __DIR__ . '/' . $localPath;

                            if (!file_exists($fullLocalPath) && $remoteUrl !== '') {
                                roh_download_remote_image_safe($remoteUrl, $fullLocalPath);
                            }

                            $displaySrc = file_exists($fullLocalPath) ? $localPath : $remoteUrl;
                        ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card bg-dark">
                                    <a href="<?= htmlspecialchars($displaySrc) ?>" target="_blank">
                                        <img src="<?= htmlspecialchars($displaySrc) ?>" 
                                             class="card-img-top img-fluid" 
                                             alt="Photo of <?= htmlspecialchars($row['LastName']) ?>"
                                             style="max-height: 280px; object-fit: contain;">
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
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