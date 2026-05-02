<?php
/**
 * ROH - index.php
 * Secure and modernised home page
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour - South Africa</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>

<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-3 text-center my-5">Roll of Honour</h1>

        <div class="row justify-content-md-center">
            <div class="col col-lg-2"></div>

            <!-- Main Content Column -->
            <div class="col-md-auto bg-primary p-4 rounded">

                <div class="text-center mb-4">
                    <h2 class="display-5">South African Roll of Honour</h2>
                    <p class="lead">Remembering those who made the ultimate sacrifice</p>
                </div>

                <?php
                // Secure statistics
                $totalDeaths = CountTotalDeaths();
                $noAge       = CountNoAge();
                $noCause     = CountNoCause();
                $noCemetery  = CountNoCemetery();
                ?>

                <div class="row text-center g-3">
                    <div class="col-md-3 col-6">
                        <div class="card bg-dark text-white h-100">
                            <div class="card-body">
                                <h3 class="display-6"><?= number_format($totalDeaths) ?></h3>
                                <p class="card-text">Total Records</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card bg-dark text-white h-100">
                            <div class="card-body">
                                <h3 class="display-6"><?= number_format($noAge) ?></h3>
                                <p class="card-text">Unknown Age</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card bg-dark text-white h-100">
                            <div class="card-body">
                                <h3 class="display-6"><?= number_format($noCause) ?></h3>
                                <p class="card-text">Unknown Cause</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card bg-dark text-white h-100">
                            <div class="card-body">
                                <h3 class="display-6"><?= number_format($noCemetery) ?></h3>
                                <p class="card-text">Unknown Cemetery</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="text-center">
                    <a href="mainListPeople.php" class="btn btn-lg btn-light mx-2">
                        Browse All Names
                    </a>
                    <a href="listPeopleYear.php" class="btn btn-lg btn-light mx-2">
                        By Year of Death
                    </a>
                </div>

                <div class="mt-5 text-center text-light">
                    <p>This site honours South African service personnel who paid the ultimate price.</p>
                    <p class="small">
                        Data originally compiled in the 1990s • Restored and modernised
                    </p>
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
