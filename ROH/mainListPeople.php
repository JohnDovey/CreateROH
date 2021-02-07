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
            // page is the current page, if there's nothing set, default is page 1
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $SortField = isset($_GET['sort']) ? $_GET['sort'] : "LastName";
            // set records or rows of data per page
            $recordsPerPage = 50;

            // calculate for the query LIMIT clause
            ?>
            
            <?php
             $fromRecordNum = ($recordsPerPage * $page) - $recordsPerPage;
            $sql = "Select *, strftime('%Y',DateDeath) as Year from PersonInfoRaw order by {$SortField}, Firstname LIMIT {$fromRecordNum}, {$recordsPerPage};";
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
                                    <th class="text-right"><a href="<?=$_SERVER['PHP_SELF']?>?page=<?=$page?>&sort=PersonNumber">Person Number</a></th>
                                    
                                    <th><a href="<?=$_SERVER['PHP_SELF']?>?page=<?=$page?>&sort=LastName">Last Name</a></th>
                                    <th><a href="<?=$_SERVER['PHP_SELF']?>?page=<?=$page?>&sort=FirstName">First Name</a></th>
                                    <th>Initials</th>
                                    <th><a href="<?=$_SERVER['PHP_SELF']?>?page=<?=$page?>&sort=RankID">Rank</a></th>
                                    <th><a href="<?=$_SERVER['PHP_SELF']?>?page=<?=$page?>&sort=Year">Year</a></th>
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
                                    
                                    <td><?=ucwords(strtolower($row['LastName']))?></td>
                                    <td><?=ucwords(strtolower($row['FirstName']))?></td>
                                    <td><?=$row['Initials']?></td>
                                    <td><?=$row['Rank']?></td>
                                    <td data-toggle="tooltip" title="<?=$row['DateDeath']?>"><?=$row['Year']?></td>


                                </tr>
                                <?php }  ?>
                            </tbody>
                        </table>
                    </div>
                    <nav aria-label="People Record navigation">
                        <ul class="pagination  justify-content-center">
                            
                    <?php
                    // *************** <PAGING_SECTION> ***************
        // ***** for 'first' and 'previous' pages
        if($page>1){
            // ********** show the first page
            ?>
            <li class="page-item">
            <a class="page-link" href="<?=$_SERVER['PHP_SELF']?>?page=1&sort=<?=$SortField?>" aria-lable="First Page">
            <span aria-hidden="true"><<</span>
            <span class="sr-only">First</span></a>
            
            <?php
             
            // ********** show the previous page
            $prev_page = $page - 1;
            ?>
            <li class="page-item">
            <a class="page-link" href="<?=$_SERVER['PHP_SELF']?>?page=<?=$prev_page?>&sort=<?=$SortField?>" title="Previous page is <?=$prev_page?>" aria-label="Previous Page is <?=$prev_page?>">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Previous Page <?=$prev_page?></span>
                                </a>
                                </li>
            <?php 
        }
         
         
        // ********** show the number paging

        // find out total pages
    
        $total_rows= CountTotalDeaths($db);
        $total_pages = ceil($total_rows / $recordsPerPage);

        // range of num links to show
        $range = 2;

        // display links to 'range of pages' around 'current page'
        $initial_num = $page - $range;
        $condition_limit_num = ($page + $range)  + 1;

        for ($x=$initial_num; $x<$condition_limit_num; $x++) {
             
            // be sure '$x is greater than 0' AND 'less than or equal to the $total_pages'
            if (($x > 0) && ($x <= $total_pages)) {
             
                // current page
                if ($x == $page) {
                    ?>
                    <li class="page-item active"><a class="page-link" href="<?=$_SERVER['PHP_SELF']?>?page=<?=$x?>&sort=<?=$SortField?>"><?=$x?></a></li>
                    <?php
                }
                // not current page
                else { ?>
                    <li class="page-item"><a class="page-link" href="<?=$_SERVER['PHP_SELF']?>?page=<?=$x?>&sort=<?=$SortField?>"><?=$x?></a></li>
                <?php
                }
            }
        }
         
         
        // ***** for 'next' and 'last' pages
        if($page<$total_pages){
            // ********** show the next page
            $next_page = $page + 1;
            ?>
                    <li class="page-item">
                                <a class="page-link" href="<?=$_SERVER['PHP_SELF']?>?page=<?=$next_page?>&sort=<?=$SortField?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </li>
            <?php
             
            // ********** show the last page
            ?>
            <li class="page-item">
                                <a class="page-link" href="<?=$_SERVER['PHP_SELF']?>?page=<?=$total_pages?>&sort=<?=$SortField?>" aria-label="Next">
                                    <span aria-hidden="true">>></span>
                                    <span class="sr-only">Last page</span>
                                </a>
                            </li>
            </ul>
      <?php  }
    // *************** </PAGING_SECTION> ***************
    ?>        
                    </nav>
                    <p class="text-center">(<?=$page?>/<?=$total_pages?>) (Sorted by: <?=$SortField?>)</p>
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