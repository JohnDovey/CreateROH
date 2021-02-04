<?php
require_once("functions.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: List People</title>
    <?php
        require_once("include/bootstrap-head.php");
    ?>
</head>

<body>
    <div class="container-fluid clearfix">
        <?php
require_once("include/menu.php");
?>

        <h1 class="display-3 text-center">Roll of Honour: List People</h1>
        <?php
                $TotalDeaths = CountTotalDeaths($db);
                
 $sql = "Select * from PersonInfoRaw order by LastName, Firstname LIMIT 60;";
 $ret = $db->query($sql);
 ?>
        <div class="row justify-content-md-center">
            <div class="col-md-auto bg-primary">
                <h2 class="border rounded-circle text-center">Info</h2>
                <div class="card">
                    <div class="card-body">
                        <table class="table table-dark table-fluid">
                            <thead>
                                <caption>List of People</caption>
                                <tr>
                                    <th class="text-right">Person Number</th>
                                    <th>Name</th>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Rank</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
                    ?>
                                <tr>
                                    <td class="text-center"><a
                                            href="person.php?PersonNumber=<?=$row['PersonNumber']?>"><?=$row['PersonNumber']?></a>
                                    </td>
                                    <td><?=$row['Name']?></td>
                                    <td><?=$row['LastName']?></td>
                                    <td><?=$row['FirstName']?></td>
                                    <td><?=$row['Rank']?></td>


                                </tr>
                                <?php }  ?>
                            </tbody>
                        </table>
                    </div>
                    <nav aria-label="People Record navigation">
                        <ul class="pagination  justify-content-center"">
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                    <span class="sr-only">Previous</span>
                                </a>
                            </li>
                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div> <!-- end Center Col -->
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