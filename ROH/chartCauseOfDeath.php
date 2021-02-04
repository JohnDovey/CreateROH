<?php
require_once("functions.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Cause of Death Stats</title>
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
                <h2 class="border rounded-circle text-center">Cause of Death Stats</h2>
                <?php
               // Get Chart Data
                $sql = "SELECT CauseDeath, count(CauseDeath) as CountCauseDeath from PersonInfoRaw group by CauseDeath order by CountCauseDeath DESC limit 60;";
                $ret = $db->query($sql);
                $LabelNames="[";
                $DataPoints="[";

                while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                    // ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                    $LabelNames= $LabelNames . "'" . $row['CauseDeath'] . "',";
                    $DataPoints= $DataPoints . "'" . $row['CountCauseDeath'] . "',";
                 }
                 $LabelNames= $LabelNames . "]";
                 $DataPoints= $DataPoints . "]";

                 
               ?>
                <div class="container">
                    <div class="row py-2">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <canvas id="YearPie" width="900"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include_once('js/chartCauseOfDeath.php'); ?>

            </div> <!-- end Center Col -->
            <div class="col col-lg-2">

                <?php
                $TotalDeaths = CountTotalDeaths($db);
                $NoCause = CountNoCause($db);
                $TotalWithCause=$TotalDeaths - $NoCause;
                
 $sql = "SELECT CauseDeath, count(CauseDeath) as CountCauseDeath from PersonInfoRaw group by CauseDeath order by CountCauseDeath DESC limit 60;";
 $ret = $db->query($sql);
 ?>
                <div class="card">
                    <div class="card-body">
                        <table class="table table-dark table-fluid">
                            <thead>
                                <caption>Cause of Death (top 60)<br>Total Deaths: <?=$TotalDeaths?><br>Deaths with Cause: <?=$TotalWithCause?><br>No Cause: <?=$NoCause?></caption>
                                <tr>
                                    <th>Cause of Death</th>
                                    <th>Count</th>
                                    <th>% of <?=$TotalWithCause?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                    ?>
                                <tr>
                                    <td><?=$row['CauseDeath']?></td>
                                    <td><?=$row['CountCauseDeath']?></td>
                                <td><?=percent($row['CountCauseDeath'] / $TotalWithCause)?></td>
                                   
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