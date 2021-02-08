<?php
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="Description" content="ROH: South African Military Deaths" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/darkly/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link rel="stylesheet" href="styles.min.css">
    <title>ROH: Person Information</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <ul class="nav ROH">
            <li class="nav-item">
                <a class="nav-link active" href="#"><i class="fa fa-active"></i>Active</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fa fa-navigation"></i>Link</a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#"><i class="fa fa-disabled"></i>Disabled</a>
            </li>
        </ul>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg col-sm col-md">
                <h1>Details</h1>
                <!-- Begin Card -->
                <div class="card-deck">
                    <?php
					$PersonNumber= $_GET['PersonNumber'];
					if ($PersonNumber < 1){
					  $PersonNumber=1;
					}
$sql="select * from PersonInfoRaw where PersonNumber=  " . $PersonNumber . ";";
$ret = $db->query($sql);
while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
	?>
                    <div class="card">

                        <div class="card-body">
                            <h3 class="card-title alert alert-success">Person Details</h3>
                            <p class="card-text">
                            <table class="table table-striped table-inverse table-responsive">
                                <tr>
                                    <td scope="row">Service No:</td>
                                    <td scope="head"> <?=$row['ServiceNo']?></td>
                                <tr>
                                <tr>
                                    <td scope="row">Rank:</td>
                                    <td><a href="list.php?mod=rank&rankid=<?=$row['Rank']?>"><?=$row['Rank']?></a></td>
                                </tr>
                                <tr>
                                    <td class="t">Name:</td>
                                    <td> <?=$row['Name']?></td>
                                <tr>
                                <tr>
                                    <td scope="row">Last Name:</td>
                                    <td class=""><a
                                            href="list.php?mod=LastName&LastName='<?=$row['LastName']?>'"><?=$row['LastName']?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td scope="row">First Name:</td>
                                    <td class=""><?=$row['FirstName']?></td>
                                </tr>
                                <tr>
                                    <td scope="row">Initials:</td>
                                    <td class=""><?=$row['Initials']?></td>
                                </tr>
                            </table>
                            </p>
                        </div>
                        <div class="card-footer">
                            <p>Person Number: <a
                                    href="person.php?mod=person&PersonNumber=<?=$row['PersonNumber']?>"><?=$row['PersonNumber']?></a>
                            </p>
                        </div>
                    </div>
                    <div class="card">

                        <div class="card-body">
                            <h3 class="card-title alert alert-success">Death Details</h3>
                            <p class="card-text">
                            <table class="table table-striped table-inverse table-responsive">
                                <tr>
                                    <td scope="row">Date of Death:</td>
                                    <td><a href='list.php?mod=DoD&DateDeath=<?=$row['DateDeath']?>'><?=$row['DateDeath']?>
                                    </td>
                                </tr>
                                <tr>
                                    <td scope="row">Age:</td>
                                    <td><?=$row['Age']?></td>
                                </tr>
                                <tr>
                                    <td scope="row">Cause of Death:</td>
                                    <td><?=$row['CauseDeath']?></td>
                                </tr>
                            </table>
                            </p>
                        </div>
                        <div class="card-footer">
                            <p>Regiment/Unit: <a
                                    href="list.php?mod=regiment&regimentID=<?=$row['RegimentID']?>"><?=$row['Regiment']?></a>
                                / <a href="list.php?mod=unit&unitID=<?=$row['UnitID']?>"><?=$row['Unit']?></a> </p>
                        </div>
                    </div>
                    <div class="card">

                        <div class="card-body">
                            <h3 class="card-title alert alert-success">Commemoration</h3>
                            <p class="card-text">
                            <table class="table table-striped table-inverse table-responsive">
                                <tr>
                                    <td scope="row">Country:</td>
                                    <td><a
                                            href='list.php?mod=country&countryID=<?=$row['CountryID']?>'><?=$row['Country']?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td scope="row">Cemetery:</td>
                                    <td><a
                                            href='list.php?mod=cemetery&cemeteryID=<?=$row['CemeteryID']?>'><?=$row['Cemetery']?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td scope="row">Grave Reference:</td>
                                    <td><?=$row['GraveRef']?></td>
                                </tr>
                            </table>
                            </p>
                        </div>
                        <div class="card-footer">
                            <p><i class="fa fa-map-marker fa-1x" aria-hidden="true"></i><a
                                    href="https://www.google.com/maps/place/<?=$row['CemeteryLat']?>+<?=$row['CemeteryLong']?>">Map</a>
                            </p>
                        </div>
                    </div>
                    <div class="card text-center ">
                        <div class="card-header">
                            <i class="fa fa-map-marker fa-3x" aria-hidden="true"></i>
                            <h5 class="card-title alert alert-success"><a
                                    href="list.php?mod=cemetery&cememteryID=<?=$row['CemeteryID']?>"><?=$row['Cemetery']?></a>
                            </h5>
                        </div>
                        <div class="card-body card-body-cascade text-center">
                            <div id="map-container-google-8" class="z-depth-1-half map-container-5"
                                style="height: 300px">
                                <iframe
                                    src="https://www.google.com/maps/place/<?=$row['CemeteryLat']?>+<?=$row['CemeteryLong']?>"
                                    frameborder="0" style="border:0" allowfullscreen></iframe>
                            </div>
                            <p>Lat: <?=$row['CemeteryLat']?></p>
                            <p>Long:<?=$row['CemeteryLong']?></p>
                        </div>
                        <div class="card-footer">

                        </div>
                    </div>
                    <div class="card text-center ">
                        <div class="card-header">
                            <h5 class="card-title">
                                <div class="alert alert-success" role="alert">
                                    <h4 class="alert-heading">More Info</h4>
                                    <p></p>
                                    <p class="mb-0"></p>
                                </div>
                            </h5>
                        </div>
                        <div class="card-body card-body-cascade text-center">
                            <p class="alert alert-success">Additional Info</p>
                            <p><?=$row['AddInfo']; ?></p>
                            <p class="alert alert-success">Citation</p>
                            <p><?=$row['Citation']; ?></p>
                        </div>
                        <div class="card-footer">

                        </div>
                    </div>
                    <?php
}
?>

                    <?php 
$NextNo = $PersonNumber + 1;
$PrevNo = $PersonNumber - 1 ;
If ($PrevNo < 1){
	$PrevNo =1;
}
?>
                    <ul class="nav">
                        <li>
                        <li class="nav-item"><button type="button" class="btn btn-link"><a class="nav-link"
                                    href="person.php?PersonNumber=<?=$PrevNo ?>">Prev</a></button></li>
                        <li class="nav-item"><button type="button" class="btn btn-link"><a class="nav-link"
                                    href="person.php?PersonNumber=<?=$NextNo ?>">Next</a></button></li>
                    </ul>

                    <!-- End Card -->
                </div> <!-- Card Desck End -->
				<div class="card-deck">
                <?php
				$sql="select * from PersonImages where PersonNumber=  " . $PersonNumber . ";";
				$ret = $db->query($sql);
				while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
	?>
               
                    <div class="card">
                        <img src="<?=$row['ImgUrlComplete']?>" class="card-img-top" alt=" <?=$row['PersonNumber']?>">
                        <div class="card-body">                            
                        </div>
                        <div class="card-footer">
                            <small class="text-muted"><h5 class="card-title"><?=$row['ImgUrl']?></h5></small>
                        </div>
                    </div>
					<?php
				}
				?>
                </div>
 



        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
        <script src="https://kit.fontawesome.com/79aa4f4cb7.js" crossorigin="anonymous"></script>
</body>

</html>