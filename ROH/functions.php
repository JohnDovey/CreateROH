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

$db = new DB('./ROHData.sql3');
//$db = new DB('../bin/debug/ROHData.sql3');
if(!$db){
    echo "<h1>" . $db->lastErrorMsg() . "</h1>";
 } else {
    // echo "<h1>Opened database successfully</h1>\n";
 }
?>
<?php
function GetImgSrc($id, $db){
	$imgurl="DownLoadImage/no_image.jpg";
	$sql = "SELECT * from PersonImages where id = " . $id . ";";
	$ret = $db->query($sql);
	$row2 = $ret->fetchArray(SQLITE3_ASSOC);

	if ($row2['ImgUrl']=='/search/photos/no_image.jpg'){
		$row2['ImgPath'] = 'DownLoadImage';
		$row2['ImgName'] = 'no_image.jpg';
		$sql2 = "UPDATE PersonImages set ImgName = '" . $row2['ImgName'] . "' where id = " . $id . ";";
		$ret2 = $db->exec($sql2);
		$sql2 = "UPDATE PersonImages set ImgPath = '" . $row2['ImgPath'] . "' where id = " . $id . ";";
		$ret2 = $db->exec($sql2);
		
		
		//echo "<h1>" . $sql2 . "</h1>";
	}

	if (is_null($row2['ImgName']) ||strlen($row2['ImgName'])<2 ){
		$imgUrl = $row2['ImgUrlComplete'];
	} else {
		$imgUrl = $row2['ImgPath'] . "/" . $row2['ImgName'];
	}
	
	return $imgUrl;
}

?>
<?php
function SaveRemoteImage($url, $id, $db){
	$FileName=basename($url);
	$NewDir="DownLoadImage";
	
	if (file_exists('DownLoadImage/' . $FileName)){
	}else{
		$ch = curl_init('$url');
		$fp = fopen('DownLoadImage/' . $FileName, 'wb');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
		
		$sql="UPDATE PersonImages set 'ImgName' = '{$FileName}', 'ImgPath'='{$NewDir}/' where id =  " . $id . ";";
		$ret = $db->exec($sql);
	}
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
	return $ret;	
}
?>
<?php
function CountRecordsYear($codevalue, $dbase)
{
	// Params: 	$table = The table to count records
	//			$code = The Select Field to limit the count
	//			$codevalue = The value on which to select
	//			$dbase = The Database connection variable (normally $db)
	$sql = "SELECT COUNT(*),  strftime('%Y',DateDeath) as Year from PersonInfoRaw where Year = '" . $codevalue . "';";
	$ret = $dbase->querySingle($sql);
	return $ret;	
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
	return $ret;	
}
?>

<?php
function CountDistinctPersonInfoRaw($field, $dbase) {
	$sql="select  count(DISTINCT " . $field . ") from PersonInfoRaw;";
	$ret = $dbase->querySingle($sql);
	$num = $ret;
	return $num;
}
?>

<?php
function CountTotalDeaths($dbase){
	$sql="select  count(*) from PersonInfoRaw;";
	$ret = $dbase->querySingle($sql);
	return $ret;
}
function percent($number){
    return round($number * 100, 2) . ' %';
}
?>
<?php
function CountNoAge($dbase){
	$sql="select  count(*) from PersonInfoRaw where Age < 1;";
	$ret = $dbase->querySingle($sql);
	return $ret;
}
?>
<?php
function CountNoCause($dbase){
	$sql="select  count(*) from PersonInfoRaw where CauseDeath < 1;";
	$ret = $dbase->querySingle($sql);
	return $ret;
}
?>
<?php
function CountNoCountry($dbase){
	$sql="select  count(*) from PersonInfoRaw where CountryID < 1;";
	$ret = $dbase->querySingle($sql);
	return $ret;
}
?>
<?php
function CountNoLocality($dbase){
	$sql="select  count(*) from PersonInfoRaw where LocalityID < 1;";
	$ret = $dbase->querySingle($sql);
	return $ret;
}
?>
<?php
function CountNoUnit($dbase){
	$sql="select  count(*) from PersonInfoRaw where UnitID < 1;";
	$ret = $dbase->querySingle($sql);
	return $ret;
}
?>
<?php
function CountNoRegiment($dbase){
	$sql="select  count(*) from PersonInfoRaw where RegimentID < 1;";
	$ret = $dbase->querySingle($sql);
	return $ret;
}
?>
<?php
function CountNoCemetery($dbase){
	$sql="select  count(*) from PersonInfoRaw where CemeteryID < 1;";
	$ret = $dbase->querySingle($sql);
	return $ret;
}
?>
<?php
function CountNoRank($dbase){
	$sql="select  count(*) from PersonInfoRaw where RankID < 1;";
	$ret = $dbase->querySingle($sql);
	return $ret;
}
?>
<?php
function CountNoYear($dbase){
	$sql="select  count(*) from PersonInfoRaw where strftime('%Y',DateDeath) < 1;";
	$ret = $dbase->querySingle($sql);
	return $ret;
}
?>
<?php
function GetRegimentName($myRegimentID, $db) 
{
    $sql="select Regiment from PersonInfoRaw where RegimentID = " . $myRegimentID . ";";
    $ret = $db->querySingle($sql);
    return $ret;
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