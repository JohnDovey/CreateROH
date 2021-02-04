<?php
require_once("functions.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Regiment Death Stats (Top 60)</title>
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
                <h2 class="border rounded-circle text-center">Regiment Death Stats (Top 60)</h2>
                <?php
               // Get Chart Data
                $sql = "Select RegimentID,Regiment,Unit, Unit2, COUNT(Regiment) as CountRegiment from PersonInfoRaw where RegimentID > 0 GROUP BY Regiment order by CountRegiment DESC, Rank LIMIT 60;";
                $ret = $db->query($sql);
                $LabelNames="[";
                $DataPoints="[";

                while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                    // ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                    $LabelNames= $LabelNames . "'" . $row['Regiment'] . ":" . $row['Unit2'] . "',";
                    $DataPoints= $DataPoints . "'" . $row['CountRegiment'] . "',";
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
                $MyChartTitle = "Death by Regiment Stats";
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
                $NoRegiment = CountNoRegiment($db);
                $TotalWithRegiment=$TotalDeaths - $NoRegiment;
                
                $sql = "Select RegimentID,Regiment,Unit, Unit2, COUNT(Regiment) as CountRegiment from PersonInfoRaw where RegimentID > 0 GROUP BY Regiment order by CountRegiment DESC, Rank LIMIT 60;";
                $ret = $db->query($sql);
                    ?>
                <div class="card">
                    <div class="card-body">
                        <table class="table table-dark table-fluid">
                            <thead>
                                <caption>Regiment<br>Total Deaths by Regiment (Top 60): <?=$TotalDeaths?><br>Deaths with Regiment: <?=$TotalWithRegiment?><br>No Regiment: <?=$NoRegiment?></caption>
                                <tr>
                                    <th>Regiment</th>
                                    <th>Count</th>
                                    <th>% of <?=$TotalWithRegiment?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                    ?>
                                <tr>
                                    <td><?=$row['Regiment']?> <small class="text-muted"><?=$row['Unit']?>: <?=$row['Unit2']?></small></td>
                                    <td><?=$row['CountRegiment']?></td>
                                <td><?=percent($row['CountRegiment'] / $TotalWithRegiment)?></td>
                                   
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