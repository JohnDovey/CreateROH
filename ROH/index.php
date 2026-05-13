<?php
/**
 * index.php - Clean Card Layout with Hero Image
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
    <style>
        .hero-card img {
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.6);
        }
        .infographic {
            background: linear-gradient(135deg, #1a1a1a, #2c2c2c);
            border: 4px solid #8B0000;
            border-radius: 12px;
            padding: 35px 20px;
            text-align: center;
            color: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.6);
        }
        .death-count {
            font-size: 4.8rem;
            font-weight: bold;
            color: #ffdd57;
            line-height: 1;
        }
    </style>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-3 text-center my-5">South African Roll of Honour</h1>

        <!-- Statistics Cards -->
        <div class="row g-4 justify-content-center mb-5">
            <div class="col-md-4 col-lg-3">
                <div class="card bg-primary text-white h-100 text-center">
                    <div class="card-body">
                        <h5>Total Recorded Deaths</h5>
                        <h2 class="display-5"><?= number_format(CountTotalDeaths()) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="card bg-primary text-white h-100 text-center">
                    <div class="card-body">
                        <h5>Personnel with Images</h5>
                        <h2 class="display-5"><?= number_format(CountWithImages()) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="card bg-primary text-white h-100 text-center">
                    <div class="card-body">
                        <h5>Countries of Commemoration</h5>
                        <h2 class="display-5"><?= number_format(CountCountries()) ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hero Image Card -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-10">
                <div class="card hero-card bg-dark">
                    <div class="card-body p-3">
                        <img src="images/For-the-Fallen.jpg" 
                             alt="For the Fallen - Lest We Forget" 
                             class="img-fluid w-100">
                    </div>
                </div>
            </div>
        </div>

        <!-- On This Day Infographic -->
        <?php
        $todayMonth = (int)date('m');
        $todayDay   = (int)date('d');
        $todayCount = db()->fetchOne("SELECT COUNT(*) as total 
                                      FROM PersonInfoRaw 
                                      WHERE strftime('%m', DateDeath) = :month 
                                        AND strftime('%d', DateDeath) = :day", [
            ':month' => sprintf('%02d', $todayMonth),
            ':day'   => sprintf('%02d', $todayDay)
        ])['total'] ?? 0;
        ?>

        <div class="row justify-content-center">
            <div class="col-lg-9">
                <a href="listOnThisDay.php" class="text-decoration-none">
                    <div class="infographic">
                        <h2 style="color:#ffdd57; margin:0;">ON THIS DAY</h2>
                        <h3 style="color:#ddd; margin:12px 0 20px 0;">
                            <?= date('d F') ?>
                        </h3>
                        <div class="death-count"><?= number_format($todayCount) ?></div>
                        <p style="margin:10px 0 0 0; font-size:1.25rem; color:#ccc;">
                            South African heroes who fell on this day
                        </p>
                        <div class="mt-4">
                            <span class="btn btn-outline-light btn-lg px-5">View Full List →</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

    </div>

    <hr>
    <?php require_once("include/footer.php"); ?>
    <?php require_once("include/bootstrap-footer.php"); ?>
</body>
</html>