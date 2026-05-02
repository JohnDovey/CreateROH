<?php
/**
 * include/template.php
 * Standard page template - Copy and customise for new pages
 */

require_once("include/db.php");
require_once("functions.php");

// ====================== PAGE SPECIFIC LOGIC ======================
// Example: Get a parameter safely
// $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ====================== PAGE TITLE ======================
$pageTitle = "Roll of Honour - New Page";
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
            <div class="col col-lg-2"></div>

            <!-- ====================== MAIN CONTENT ====================== -->
            <div class="col-md-auto bg-primary p-4 rounded shadow-sm" style="min-width: 800px; max-width: 1100px;">

                <!-- Your page content goes here -->
                <div class="alert alert-info">
                    <strong>This is a template.</strong> Replace this area with your page content.
                </div>

                <!-- Example card layout -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Page Section Title</h5>
                    </div>
                    <div class="card-body">
                        <p>Your content here...</p>
                    </div>
                </div>

            </div>

            <div class="col col-lg-2"></div>
        </div>
    </div>

    <hr>

    <?php require_once("include/footer.php"); ?>
    <?php require_once("include/bootstrap-footer.php"); ?>
</body>
</html>
