<?php
require_once("functions.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour</title>
    <?php
        require_once("bootstrap-head.php");
    ?>
</head>

<body>
    <div class="container-fluid clearfix">
        <?php
require_once("menu.php");
?>

        <h1 class="display-3 text-center">Roll of Honour</h1>

        <div class="row justify-content-md-center">
            <div class="col col-lg-2">
            </div> <!-- End left col -->
            <div class="col-md-auto bg-primary">
                <h2 class="border rounded-circle text-center">Info</h2>
                
            </div> <!-- end Center Col -->
            <div class="col col-lg-2">
                
            </div> <!-- End Right Col -->
        </div>
    </div> <!-- End Container -->
    <hr>
    <?php
require_once("footer.php");
?>

<?php
        require_once("bootstrap-footer.php");
    ?>
</body>

</html>