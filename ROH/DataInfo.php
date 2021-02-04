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

        <h1 class="display-3 text-center">Roll of Honour</h1>

        <div class="row justify-content-md-center">
            <div class="col col-lg-2">
            </div> <!-- End left col -->
            <div class="col-md-auto bg-primary">
                <h2 class="border rounded-circle text-center">Database Info</h2>
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th>Nmae</th>
                            
                            <th>Count</th>
                        </tr>
                    <tbody>
                        <tr>
                            <?php
                $sql = "SELECT type,name FROM 'main'.sqlite_master where type='table';";
                $ret = $db->query($sql);
                while ($row = $ret->fetchArray(SQLITE3_ASSOC)){ ?>
                        <tr>
                            <td><?=$row['name']?></td>
                            
                            <td><button type="button" class="btn btn-primary">
                            <?=$row['name']?><span class="badge badge-light"> <?=CountRecords($row['name'], $db)?></span>
</button></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div> <!-- end Center Col -->
            <div class="col col-lg-2">

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