<?php
/*
 ' Include this file with the command
 ' php require_once("Functions.php")
 ' John Dovey (john@justdone.co.za)
 ' November 2005 - 2010
*/
// require_once("Functions.php");
?>

<?php
// Establish Database Connection

class DB extends SQLite3
{
        function __construct( $file )
        {
            $this->open( $file );
        }
}

$db = new DB('./RohData.sql3');
if(!$db){
    echo "<h1>" . $db->lastErrorMsg() . "</h1>";
 } else {
    // echo "<h1>Opened database successfully</h1>\n";
 }
?>
<?php
function CountRecordsCode($table, $code, $codevalue, $dbase)
{
	// Params: 	$table = The table to count records
	//			$code = The Select Field to limit the count
	//			$codevalue = The value on which to select
	//			$dbase = The Database connection variable (normally $db)
	$sql = "SELECT COUNT(*) from " . $table . " where " . $code . " = " . $codevalue . ";";
	$ret = $dbase->querySingle($sql);
	$num = $ret;
	return $num;	
}
?>
<?php
function CountRecords($table, $dbase)
{
	// Count total number of records in a table
	// Params: 	$table = The table to count records
	//			$dbase = The Database connection variable (normally $db)
	$sql = "SELECT COUNT(*) from " . $table .  ";";
	$ret = $dbase->querySingle($sql);
	$num = $ret;
	return $num;	
}
?>

<?php
function GetNumRef($PlantCode, $dbase)
{
	$sql="Select COUNT(*) from Reference where PlantCode =" . $PlantCode . ";";
	$ret = $dbase->querySingle($sql);
	$num = $ret;
	return $num;
}
?>

<?php
function GetNumImage($PlantCode, $dbase)
{
	$sql="Select COUNT(*) from PlantImages where PlantCode =" . $PlantCode . ";";
	$ret = $dbase->querySingle($sql);
	$num = $ret;
	return $num;
}
?>
<?php
function GetRegimentName($myRegimentID) 
{
    $sql="select * from personrawinfo where RegimentID = " . $myRegimentID . ";";
    $ret = $db->query($sql);
while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
    return $row['RegimentName'];
    echo "<h1>Regiment = ". $row['RegimentName'] . "</h1>\n";
 }
}
?>

<?php

function CalcActualAge($DOB) {
	list($year, $month, $day) = explode("-", $DOB);
	$year_diff = date("Y") - $year;
	$month_diff = date("m") - $month;
	$day_diff = date("d") - $day;
	if ($month_diff < 0)
	$year_diff--;
	elseif (($month_diff == 0) && ($day_diff < 0))
	$year_diff--;
	return $year_diff;
} //End function
?>

<?php

function CalcDeathAge($DOB, $DOD) { // DOB=Date of Birth, DOD=Date of Death
	list($Byear, $Bmonth, $Bday) = explode("-", $DOB);
	list($Dyear, $Dmonth, $Dday) = explode("-", $DOD);
	$year_diff = $Dyear - $Byear;
	$month_diff = $Dmonth - $Bmonth;
	$day_diff = $Dday - $Bday;
	if ($month_diff < 0)
	$year_diff--;
	elseif (($month_diff == 0) && ($day_diff < 0))
	$year_diff--;
	return $year_diff;
} //End function
?>

<?php
/*== FUNCTIONS ==*/
function getFileExtension($str) {
	$i = strrpos($str, ".");
	if (!$i) {
		return "";
	}
	$l = strlen($str) - $i;
	$ext = substr($str, $i + 1, $l);
	return $ext;
}
?>

<?php
function MakeNotNull($MyField) {
	If (!is_Null($MyField)) {
		return $MyField;
	} else {
		return " ";
	} //end if
} //End function
?>

<?
function GetYesNo($InBool) {
	if ($InBool == 1) {
		return "Yes";
	} else {
		return "No";
	} // end if
} //end Function
?>
<?php
function hideemail($emailaddy){
	$pieces = explode("@", $emailaddy);
	$newemailaddy= $pieces[0] . "@xxx"; 
	return $newemailaddy;
}
?>
<?php
function dirList($directory) {
	// create an array to hold directory list
	$results = array();
	// create a handler for the directory
	$handler = opendir($directory);
	// keep going until all files in directory have been read
	while ($file = readdir($handler)) {
		// if $file isn't this directory or its parent,
		// add it to the results array
		if ($file != '.' && $file != '..')
		$results[] = $file;
	}
	// tidy up: close the handler
	closedir($handler);
	// done!
	return $results;
}
?>

