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
        require_once("include/bootstrap-head.php");
    ?>
</head>

<body>
    <div class="container-fluid clearfix">
        <?php
require_once("include/menu.php");
?>
        <div class="row justify-content-md-center">
            <div class="col-md-auto bg-primary">
                <h2 class="border rounded-circle text-center">Age Stats</h2>
                <?php
               // Get Chart Data
                $sql = "select  substr('000'||Age,-3) as NewAge, count(Age) as CountAge from PersonInfoRaw where Age > 0 group by NewAge order by NewAge Asc;";
                $ret = $db->query($sql);
                $LabelNames="[";
                $DataPoints="[";

                while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                    // ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                    $Age = $row['NewAge'];
                    $LabelNames= $LabelNames . "'" . $Age . "',";
                    $DataPoints= $DataPoints . "'" . $row['CountAge'] . "',";
                 }
                 $LabelNames= $LabelNames . "]";
                 $DataPoints= $DataPoints . "]";

                 
               ?>
                <div class="container">
                    <div class="row py-2">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <canvas id="StatsGraph" width="900"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                $MyChartTitle = "Death by Age Stats";
                $MyChartType = "line";
                if (isset($_GET['chart'])){
                    $MyChartType = $_GET['chart'];
                }
                
                include_once('js/chartGeneric.php'); ?>
<p><a href="<?=$_SERVER['PHP_SELF']?>?chart=bar" class="btn btn-primary" role="button">Bar Graph</a>|<a href="<?=$_SERVER['PHP_SELF']?>?chart=line" class="btn btn-primary" role="button">Line Graph</a>|<a href="<?=$_SERVER['PHP_SELF']?>?chart=radar" class="btn btn-primary" role="button">Radar Graph</a></p>

            </div> <!-- end Center Col -->
            <div class="col col-lg-2">
            <?php
                $TotalDeaths = CountTotalDeaths($db);
                $NoAge = CountNoAge($db);
                $TotalWithAge=$TotalDeaths - $NoAge;
 $sql = "select  substr('000'||Age,-3) as NewAge, count(Age) as CountAge from PersonInfoRaw where Age > 0 group by NewAge order by NewAge Asc;";
 $ret = $db->query($sql);
 ?>
                <div class="card">
                    <div class="card-body">
                        <table class="table table-dark table-fluid">
                            <thead>
                                <caption>Age Deaths.<br>Total Deaths: <?=$TotalDeaths?><br>Deaths with Age: <?=$TotalWithAge?><br>No Age: <?=$NoAge?></caption>
                                <tr>
                                    <th class="text-center">Age</th>
                                    <th class="text-right">Count</th>
                                    <th class="text-right">% of <?=$TotalWithAge?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                    ?>
                                <tr>
                                    <td class="text-center"><?=$row['NewAge']?></td>
                                    <td class="text-right"><?=$row['CountAge']?></td>
                                    <td class="text-right"><?=percent($row['CountAge'] / $TotalWithAge)?></td>

                                </tr>
                                <?php }  ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- End Right Col -->
        </div>
    </div> <!-- End Container -->
    <hr>
    <?php
require_once("include/footer.php");
?>

    <?php
        require_once("include/bootstrap-footer.php");
    ?>
</body>

</html>