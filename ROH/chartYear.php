<?php
require_once("functions.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Year Stats</title>
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
                <h2 class="border rounded-circle text-center">Year Stats</h2>
                <?php
               // Get Chart Data
                $sql = "select  strftime('%Y',DateDeath) as Year, count(strftime('%Y',DateDeath)) as CountYearDeath from PersonInfoRaw where strftime('%Y',DateDeath) > 0 group by strftime('%Y',DateDeath) order by strftime('%Y',DateDeath);";
                $ret = $db->query($sql);
                $LabelNames="[";
                $DataPoints="[";

                while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                    // ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                    $LabelNames= $LabelNames . "'" . $row['Year'] . "',";
                    $DataPoints= $DataPoints . "'" . $row['CountYearDeath'] . "',";
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
                $MyChartTitle = "Death by Year Stats";
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
                $NoYear = CountNoYear($db);
                $TotalWithYear=$TotalDeaths - $NoYear;
 $sql = "select  strftime('%Y',DateDeath) as Year, count(strftime('%Y',DateDeath)) as CountYearDeath from PersonInfoRaw where strftime('%Y',DateDeath) > 0 group by strftime('%Y',DateDeath) order by strftime('%Y',DateDeath);";
 $ret = $db->query($sql);
 ?>
                <div class="card">
                    <div class="card-body">
                        <table class="table table-dark table-fluid">
                            <thead>
                            <caption>Year Deaths.<br>Total Deaths: <?=$TotalWithYear?><br>Deaths with Year: <?=$TotalWithYear?><br>No Year: <?=$NoYear?></caption>
                                <tr>
                                <th></th>
                                    <th class="text-center">Year</th>
                                    <th class="text-right">Count</th>
                                    <th class="text-right">% of <?=$TotalWithYear?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                    ?>
                                <tr>
                                <td><a href="listPeopleYear.php?Year=<?=$row['Year']?>" class="btn btn-primary"
                                            role="button"><i class="fa fa-microscope"></i></a></td>
                                    <td class="text-center"><?=$row['Year']?></td>
                                    <td class="text-right"><?=$row['CountYearDeath']?></td>
                                    <td class="text-right"><?=percent($row['CountYearDeath'] / $TotalDeaths)?></td>

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