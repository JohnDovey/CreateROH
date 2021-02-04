<?php
require_once("functions.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Country Death Stats</title>
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
                <h2 class="border rounded-circle text-center">Country Death Stats</h2>
                <?php
               // Get Chart Data
                $sql = "Select CountryID,Country, COUNT(CountryID) as CountCountry from PersonInfoRaw GROUP BY CountryID order by CountryID ASC;";
                $ret = $db->query($sql);
                $LabelNames="[";
                $DataPoints="[";

                while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                    // ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                    $LabelNames= $LabelNames . "'" . $row['Country'] . "',";
                    $DataPoints= $DataPoints . "'" . $row['CountCountry'] . "',";
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
                $MyChartTitle = "Death by Country Stats";
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
                $NoCountry = CountNoCountry($db);
                $TotalWithCountry=$TotalDeaths - $NoCountry;
                
 $sql = "Select CountryID,Country, COUNT(CountryID) as CountCountry from PersonInfoRaw GROUP BY CountryID order by CountryID ASC;";
 $ret = $db->query($sql);
 ?>
                <div class="card">
                    <div class="card-body">
                        <table class="table table-dark table-fluid">
                            <thead>
                                <caption>Country<br>Total Deaths: <?=$TotalDeaths?><br>Deaths with Country: <?=$TotalWithCountry?><br>No Country: <?=$NoCountry?></caption>
                                <tr>
                                    <th>Country</th>
                                    <th>Count</th>
                                    <th>% of <?=$TotalWithCountry?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                    ?>
                                <tr>
                                    <td><?=$row['Country']?></td>
                                    <td><?=$row['CountCountry']?></td>
                                <td><?=percent($row['CountCountry'] / $TotalWithCountry)?></td>
                                   
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