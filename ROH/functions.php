<?php
/**
 * ROH - functions.php
 * Secure, cleaned, and modernised version
 * John Dovey - Updated for security (prepared statements)
 */

require_once("include/db.php");

/* ================================================================
   Image Functions
   ================================================================ */

function GetImgSrc($id) {
    $sql = "SELECT * FROM PersonImages WHERE id = :id";
    $row = db()->fetchOne($sql, [':id' => (int)$id]);

    if (!$row) {
        return 'DownLoadImage/no_image.jpg';
    }

    // Handle missing / placeholder images
    if ($row['ImgUrl'] === '/search/photos/no_image.jpg') {
        return 'DownLoadImage/no_image.jpg';
    }

    if (empty($row['ImgName']) || strlen(trim($row['ImgName'])) < 2) {
        return $row['ImgUrlComplete'] ?? 'DownLoadImage/no_image.jpg';
    }

    return $row['ImgPath'] . '/' . $row['ImgName'];
}

/**
 * Legacy wrapper - downloads image if missing (use with caution)
 */
function SaveRemoteImage($url, $id) {
    $filename = basename($url);
    $targetDir = "DownLoadImage";
    $targetPath = $targetDir . '/' . $filename;

    if (file_exists($targetPath)) {
        return true;
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $ch = curl_init($url);
    $fp = fopen($targetPath, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $success = curl_exec($ch);
    curl_close($ch);
    fclose($fp);

    if ($success) {
        $sql = "UPDATE PersonImages 
                SET ImgName = :name, ImgPath = :path 
                WHERE id = :id";
        db()->execute($sql, [
            ':name' => $filename,
            ':path' => $targetDir,
            ':id'   => (int)$id
        ]);
        return true;
    }
    return false;
}

/* ================================================================
   Count / Statistics Functions (now secure)
   ================================================================ */

function CountRecords($table) {
    $sql = "SELECT COUNT(*) FROM " . preg_replace('/[^a-zA-Z0-9_]/', '', $table);
    $result = db()->fetchOne($sql);
    return $result ? (int)array_values($result)[0] : 0;
}

function CountTotalDeaths() {
    return CountRecords('PersonInfoRaw');
}

function CountNoAge() {
    $sql = "SELECT COUNT(*) FROM PersonInfoRaw WHERE Age < 1 OR Age IS NULL OR Age = ''";
    $result = db()->fetchOne($sql);
    return $result ? (int)array_values($result)[0] : 0;
}

function CountNoCause() {
    $sql = "SELECT COUNT(*) FROM PersonInfoRaw WHERE CauseDeath IS NULL OR CauseDeath = '' OR CauseDeath = 'Unknown'";
    $result = db()->fetchOne($sql);
    return $result ? (int)array_values($result)[0] : 0;
}

function CountNoCountry() {
    $sql = "SELECT COUNT(*) FROM PersonInfoRaw WHERE CountryID < 1 OR CountryID IS NULL";
    $result = db()->fetchOne($sql);
    return $result ? (int)array_values($result)[0] : 0;
}

function CountNoLocality() {
    $sql = "SELECT COUNT(*) FROM PersonInfoRaw WHERE LocalityID < 1 OR LocalityID IS NULL";
    $result = db()->fetchOne($sql);
    return $result ? (int)array_values($result)[0] : 0;
}

function CountNoUnit() {
    $sql = "SELECT COUNT(*) FROM PersonInfoRaw WHERE UnitID < 1 OR UnitID IS NULL";
    $result = db()->fetchOne($sql);
    return $result ? (int)array_values($result)[0] : 0;
}

function CountNoRegiment() {
    $sql = "SELECT COUNT(*) FROM PersonInfoRaw WHERE RegimentID < 1 OR RegimentID IS NULL";
    $result = db()->fetchOne($sql);
    return $result ? (int)array_values($result)[0] : 0;
}

function CountNoCemetery() {
    $sql = "SELECT COUNT(*) FROM PersonInfoRaw WHERE CemeteryID < 1 OR CemeteryID IS NULL";
    $result = db()->fetchOne($sql);
    return $result ? (int)array_values($result)[0] : 0;
}

function CountNoRank() {
    $sql = "SELECT COUNT(*) FROM PersonInfoRaw WHERE RankID < 1 OR RankID IS NULL";
    $result = db()->fetchOne($sql);
    return $result ? (int)array_values($result)[0] : 0;
}

function CountNoYear() {
    $sql = "SELECT COUNT(*) FROM PersonInfoRaw WHERE DateDeath IS NULL OR DateDeath = '' OR strftime('%Y', DateDeath) < 1";
    $result = db()->fetchOne($sql);
    return $result ? (int)array_values($result)[0] : 0;
}

function CountDistinct($field) {
    $safeField = preg_replace('/[^a-zA-Z0-9_]/', '', $field);
    $sql = "SELECT COUNT(DISTINCT {$safeField}) FROM PersonInfoRaw";
    $result = db()->fetchOne($sql);
    return $result ? (int)array_values($result)[0] : 0;
}

/* ================================================================
   Lookup & Helper Functions
   ================================================================ */

function GetRegimentName($regimentID) {
    $sql = "SELECT RegimentName FROM Regiment WHERE RegimentID = :id";
    $row = db()->fetchOne($sql, [':id' => (int)$regimentID]);
    return $row ? $row['RegimentName'] : 'Unknown';
}

function percent($number) {
    return round((float)$number * 100, 2) . ' %';
}

/* ================================================================
   Date / Age Calculation Functions (improved)
   ================================================================ */

function CalcActualAge($dob) {
    if (empty($dob)) return 0;
    try {
        $birth = new DateTime($dob);
        $now = new DateTime();
        return $birth->diff($now)->y;
    } catch (Exception $e) {
        return 0;
    }
}

function CalcDeathAge($dob, $dod) {
    if (empty($dob) || empty($dod)) return 0;
    try {
        $birth = new DateTime($dob);
        $death = new DateTime($dod);
        return $birth->diff($death)->y;
    } catch (Exception $e) {
        return 0;
    }
}

/* ================================================================
   General Utility Functions
   ================================================================ */

function getFileExtension($str) {
    $i = strrpos($str, ".");
    if ($i === false) return "";
    return substr($str, $i + 1);
}

function MakeNotNull($field) {
    return $field !== null ? $field : "";
}

function GetYesNo($bool) {
    return $bool ? "Yes" : "No";
}

function hideemail($email) {
    if (empty($email)) return "";
    $parts = explode("@", $email);
    return $parts[0] . "@xxx";
}

function dirList($directory) {
    if (!is_dir($directory)) return [];
    $results = [];
    $handler = opendir($directory);
    while ($file = readdir($handler)) {
        if ($file != "." && $file != "..") {
            $results[] = $file;
        }
    }
    closedir($handler);
    return $results;
}

/* ================================================================
   Legacy / Deprecated - Keep for compatibility if needed
   ================================================================ */

// Old global $db is replaced by db() helper
// Remove or deprecate the old class if still present elsewhere

?>
