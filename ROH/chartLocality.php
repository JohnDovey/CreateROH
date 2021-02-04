<?php
require_once("functions.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Locality Death Stats</title>
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
            <div class="col col-lg-2">
            </div> <!-- End left col -->
            <div class="col-md-auto bg-primary">
                <h2 class="border rounded-circle text-center">Locality Death Stats</h2>
                <?php
               // Get Chart Data
                $sql = "Select LocalityID,Locality, COUNT(Locality) as CountLocality from PersonInfoRaw where LocalityID > 0 GROUP BY Locality order by Locality ASC;";
                $ret = $db->query($sql);
                $LabelNames="[";
                $DataPoints="[";

                while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                    // ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                    $LabelNames= $LabelNames . "'" . $row['Locality'] . "',";
                    $DataPoints= $DataPoints . "'" . $row['CountLocality'] . "',";
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
                $MyChartTitle = "Death by Locality Stats";
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
                $NoLocality = CountNoLocality($db);
                $TotalWithLocality=$TotalDeaths - $NoLocality;
                
 $sql = "Select LocalityID,Locality, COUNT(Locality) as CountLocality from PersonInfoRaw where LocalityID > 0 GROUP BY Locality order by CountLocality Desc, Locality;";
 $ret = $db->query($sql);
 ?>
                <div class="card">
                    <div class="card-body">
                        <table class="table table-dark table-fluid">
                            <thead>
                                <caption>Locality<br>Total Deaths: <?=$TotalDeaths?><br>Deaths with Locality: <?=$TotalWithLocality?><br>No Locality: <?=$NoLocality?></caption>
                                <tr>
                                    <th>Locality</th>
                                    <th>Count</th>
                                    <th>% of <?=$TotalWithLocality?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                    ?>
                                <tr>
                                    <td><?=$row['Locality']?></td>
                                    <td><?=$row['CountLocality']?></td>
                                <td><?=percent($row['CountLocality'] / $TotalWithLocality)?></td>
                                   
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