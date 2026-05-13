<?php
/**
 * ROH - functions.php
 * Secure, cleaned, and modernised version
 * John Dovey - Updated for security (prepared statements)
 */

require_once("include/db.php");

/** Max bytes stored when caching a remote portrait (5 MiB). */
define('ROH_REMOTE_IMAGE_MAX_BYTES', 5 * 1024 * 1024);

/**
 * Cached list of user tables in the open SQLite database.
 *
 * @return string[]
 */
function roh_sqlite_user_table_names() {
    static $tables = null;
    if ($tables !== null) {
        return $tables;
    }
    $rows = db()->fetchAll(
        "SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name"
    );
    $tables = [];
    foreach ($rows as $r) {
        if (!empty($r['name'])) {
            $tables[] = $r['name'];
        }
    }
    return $tables;
}

/**
 * Column names on PersonInfoRaw (for safe dynamic COUNT DISTINCT).
 *
 * @return array<string, true>
 */
function roh_person_info_raw_columns() {
    static $cols = null;
    if ($cols !== null) {
        return $cols;
    }
    $cols = [];
    $rows = db()->fetchAll('PRAGMA table_info(PersonInfoRaw)');
    foreach ($rows as $r) {
        if (!empty($r['name'])) {
            $cols[$r['name']] = true;
        }
    }
    return $cols;
}

/**
 * Resolve host to IPv4/IPv6 addresses for SSRF checks.
 *
 * @return string[]
 */
function roh_dns_resolve_ips($host) {
    $host = (string) $host;
    if ($host === '') {
        return [];
    }
    if (filter_var($host, FILTER_VALIDATE_IP)) {
        return [$host];
    }
    $ips = [];
    if (function_exists('dns_get_record')) {
        $records = @dns_get_record($host, DNS_A | DNS_AAAA);
        if (is_array($records)) {
            foreach ($records as $rec) {
                if (!empty($rec['ip'])) {
                    $ips[] = $rec['ip'];
                }
                if (!empty($rec['ipv6'])) {
                    $ips[] = $rec['ipv6'];
                }
            }
        }
    }
    if (empty($ips)) {
        $legacy = @gethostbynamel($host);
        if ($legacy !== false) {
            $ips[] = $legacy;
        }
    }
    return array_values(array_unique($ips));
}

/**
 * True if IP is suitable for outbound fetches (blocks loopback, private, link-local, metadata).
 */
function roh_is_public_routable_ip($ip) {
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return false;
    }
    $flags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | $flags);
    }
    return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | $flags);
}

/**
 * Validate URL before server-side fetch: HTTPS/HTTP only, no credentials, DNS must resolve to public IPs only.
 */
function roh_url_allowed_for_remote_fetch($url) {
    if (!is_string($url) || strlen($url) > 2048) {
        return false;
    }
    $parts = parse_url($url);
    if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
        return false;
    }
    $scheme = strtolower($parts['scheme']);
    if ($scheme !== 'http' && $scheme !== 'https') {
        return false;
    }
    if (!empty($parts['user']) || !empty($parts['pass'])) {
        return false;
    }
    $host = strtolower($parts['host']);
    if ($host === 'localhost' || $host === '0' || substr($host, -10) === '.localhost') {
        return false;
    }
    $ips = roh_dns_resolve_ips($parts['host']);
    if (empty($ips)) {
        return false;
    }
    foreach ($ips as $ip) {
        if (!roh_is_public_routable_ip($ip)) {
            return false;
        }
    }
    return true;
}

/**
 * Magic-byte sniff: common web image types only.
 */
function roh_binary_looks_like_image($data) {
    $len = strlen($data);
    if ($len < 12) {
        return false;
    }
    if ($data[0] === "\xff" && $data[1] === "\xd8") {
        return true;
    }
    if (strncmp($data, "\x89PNG\r\n\x1a\n", 8) === 0) {
        return true;
    }
    if (strncmp($data, 'GIF87a', 6) === 0 || strncmp($data, 'GIF89a', 6) === 0) {
        return true;
    }
    if (strncmp($data, 'RIFF', 4) === 0 && substr($data, 8, 4) === 'WEBP') {
        return true;
    }
    return false;
}

/**
 * Download remote image with SSRF guard, size limit, and image sniffing. Writes only on success.
 *
 * @return bool True if file was written
 */
function roh_download_remote_image_safe($url, $targetPath) {
    if (!roh_url_allowed_for_remote_fetch($url)) {
        return false;
    }
    $dir = dirname($targetPath);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    $tmp = $targetPath . '.part.' . bin2hex(random_bytes(4));
    $fh = fopen($tmp, 'wb');
    if (!$fh) {
        return false;
    }

    $written = 0;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_FILE => $fh,
        CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_USERAGENT => 'ROH-ImageCache/1.0',
        CURLOPT_NOPROGRESS => false,
        CURLOPT_PROGRESSFUNCTION => function ($curl, $dlTotal, $downloaded, $ulTotal, $uploaded) use (&$written) {
            $written = (int) $downloaded;
            if ($written > ROH_REMOTE_IMAGE_MAX_BYTES) {
                return 1;
            }
            return 0;
        },
    ]);

    $ok = curl_exec($ch);
    $effective = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $ctype = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    fclose($fh);

    if (!$ok || $code < 200 || $code >= 300) {
        @unlink($tmp);
        return false;
    }

    if (is_string($effective) && $effective !== '' && $effective !== $url && !roh_url_allowed_for_remote_fetch($effective)) {
        @unlink($tmp);
        return false;
    }

    $head = file_get_contents($tmp, false, null, 0, 65536);
    if ($head === false || strlen($head) < 12 || !roh_binary_looks_like_image($head)) {
        @unlink($tmp);
        return false;
    }
    $lcType = strtolower($ctype);
    if ($lcType !== ''
        && strpos($lcType, 'image/') !== 0
        && strpos($lcType, 'application/octet-stream') === false
        && strpos($lcType, 'binary/octet-stream') === false) {
        @unlink($tmp);
        return false;
    }

    if (!rename($tmp, $targetPath)) {
        @unlink($tmp);
        return false;
    }
    return true;
}

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
    $pathPart = parse_url((string) $url, PHP_URL_PATH);
    $filename = $pathPart ? basename($pathPart) : '';
    if ($filename === '' || strpos($filename, '..') !== false) {
        $filename = 'img_' . (int) $id . '.jpg';
    }

    $targetDir = 'DownLoadImage';
    $targetPath = $targetDir . '/' . $filename;

    if (file_exists($targetPath)) {
        return true;
    }

    if (!roh_download_remote_image_safe($url, __DIR__ . '/' . $targetPath)) {
        return false;
    }

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

/* ================================================================
   Count / Statistics Functions (now secure)
   ================================================================ */

function CountRecords($table) {
    $table = (string) $table;
    $allowed = array_flip(roh_sqlite_user_table_names());
    if (!isset($allowed[$table])) {
        return 0;
    }
    $sql = 'SELECT COUNT(*) FROM "' . str_replace('"', '""', $table) . '"';
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
    $field = (string) $field;
    $cols = roh_person_info_raw_columns();
    if (!isset($cols[$field])) {
        return 0;
    }
    $quoted = '"' . str_replace('"', '""', $field) . '"';
    $sql = "SELECT COUNT(DISTINCT {$quoted}) FROM PersonInfoRaw";
    $result = db()->fetchOne($sql);
    return $result ? (int)array_values($result)[0] : 0;
}

/* ================================================================
   Lookup & Helper Functions
   ================================================================ */
function GetRegimentName($regimentID) {
    if (empty($regimentID)) return 'Unknown';
    
    $sql = "SELECT Regiment FROM Regiment WHERE RegimentID = :id";
    $row = db()->fetchOne($sql, [':id' => (int)$regimentID]);
    return $row ? $row['Regiment'] : 'Unknown';
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
   PAGE VIEW COUNTER FUNCTIONS
   ================================================================ */

/**
 * Ensure PageViewsDaily exists before SELECT/INSERT (chart runs before footer on some pages).
 */
function roh_ensure_page_views_daily_table() {
    static $done = false;
    if ($done) {
        return;
    }
    db()->getConnection()->exec("CREATE TABLE IF NOT EXISTS PageViewsDaily (
        ViewDate  TEXT NOT NULL,
        PageName  TEXT NOT NULL,
        ViewCount INTEGER NOT NULL DEFAULT 1,
        PRIMARY KEY (ViewDate, PageName)
    )");
    $done = true;
}

function incrementPageView($pageName) {
    $pageName = basename($pageName);           // Security: only filename
    $year     = (int)date('Y');
    $month    = (int)date('n');

    $db = db()->getConnection();

    // Create table if it doesn't exist
    $db->exec("CREATE TABLE IF NOT EXISTS PageViews (
        PageName TEXT NOT NULL,
        Year     INTEGER NOT NULL,
        Month    INTEGER NOT NULL,
        ViewCount INTEGER DEFAULT 1,
        PRIMARY KEY (PageName, Year, Month)
    )");

    // Try to insert new record, if duplicate → increment
    try {
        $sql = "INSERT INTO PageViews (PageName, Year, Month, ViewCount) 
                VALUES (:page, :year, :month, 1)";
        db()->execute($sql, [':page' => $pageName, ':year' => $year, ':month' => $month]);
    } catch (Exception $e) {
        // Duplicate key → increment existing count
        if (strpos($e->getMessage(), 'UNIQUE') !== false || strpos($e->getMessage(), 'constraint') !== false) {
            $sql = "UPDATE PageViews 
                    SET ViewCount = ViewCount + 1 
                    WHERE PageName = :page 
                      AND Year = :year 
                      AND Month = :month";
            db()->execute($sql, [':page' => $pageName, ':year' => $year, ':month' => $month]);
        }
    }

    // Daily granularity (retained ~4 months for rolling charts)
    roh_ensure_page_views_daily_table();

    $viewDate = date('Y-m-d');
    $sqlDaily = "INSERT INTO PageViewsDaily (ViewDate, PageName, ViewCount)
                 VALUES (:d, :p, 1)
                 ON CONFLICT(ViewDate, PageName) DO UPDATE SET
                     ViewCount = PageViewsDaily.ViewCount + 1";
    db()->execute($sqlDaily, [':d' => $viewDate, ':p' => $pageName]);

    $cutoff = (new DateTimeImmutable('today'))->modify('-120 days')->format('Y-m-d');
    db()->execute('DELETE FROM PageViewsDaily WHERE ViewDate < :c', [':c' => $cutoff]);
}

/**
 * Data for the "views per day" chart: current month and the two prior months,
 * aligned by calendar day-of-month (1-31). Values are total site views that day.
 *
 * @return array{labels: int[], datasets: array<int, array{label: string, data: (int|null)[], borderColor: string, backgroundColor: string, borderWidth: int, tension: float, fill: bool, spanGaps: bool}>}
 */
function getDailyPageViewsThreeMonthChartData() {
    roh_ensure_page_views_daily_table();

    $today = new DateTimeImmutable('today');
    $months = [];
    for ($i = 2; $i >= 0; $i--) {
        $first = $today->modify("first day of this month")->modify("-{$i} months");
        $months[] = [
            'year'  => (int) $first->format('Y'),
            'month' => (int) $first->format('n'),
            'label' => $first->format('F Y'),
        ];
    }

    $rangeStart = sprintf('%04d-%02d-01', $months[0]['year'], $months[0]['month']);
    $lastCurrent = $today->format('Y-m-t');
    $totalsByDate = [];
    $rows = db()->fetchAll(
        "SELECT ViewDate, SUM(ViewCount) AS Total
         FROM PageViewsDaily
         WHERE ViewDate >= :s AND ViewDate <= :e
         GROUP BY ViewDate",
        [':s' => $rangeStart, ':e' => $lastCurrent]
    );
    foreach ($rows as $r) {
        if (!empty($r['ViewDate'])) {
            $totalsByDate[$r['ViewDate']] = (int) ($r['Total'] ?? 0);
        }
    }

    $labels = range(1, 31);
    $datasets = [];
    $colors = [
        ['border' => 'rgba(255, 205, 86, 1)', 'fill' => 'rgba(255, 205, 86, 0.08)'],
        ['border' => 'rgba(13, 202, 240, 1)', 'fill' => 'rgba(13, 202, 240, 0.08)'],
        ['border' => 'rgba(13, 110, 253, 1)', 'fill' => 'rgba(13, 110, 253, 0.12)'],
    ];

    foreach ($months as $idx => $m) {
        $y = $m['year'];
        $mon = $m['month'];
        $daysInMonth = (int) (new DateTimeImmutable(sprintf('%04d-%02d-01', $y, $mon)))->format('t');
        $data = [];
        for ($d = 1; $d <= 31; $d++) {
            if ($d > $daysInMonth) {
                $data[] = null;
                continue;
            }
            $key = sprintf('%04d-%02d-%02d', $y, $mon, $d);
            if ($y === (int) $today->format('Y') && $mon === (int) $today->format('n') && $d > (int) $today->format('j')) {
                $data[] = null;
                continue;
            }
            $data[] = $totalsByDate[$key] ?? 0;
        }
        $c = $colors[$idx] ?? $colors[0];
        $datasets[] = [
            'label'            => $m['label'],
            'data'             => $data,
            'borderColor'      => $c['border'],
            'backgroundColor'  => $c['fill'],
            'borderWidth'      => $idx === 2 ? 3 : 2,
            'tension'          => 0.25,
            'fill'             => false,
            'spanGaps'         => false,
        ];
    }

    return ['labels' => $labels, 'datasets' => $datasets];
}

function getPageViewStats() {
    $year  = (int)date('Y');
    $month = (int)date('n');

    return [
        'total'       => db()->fetchOne("SELECT COALESCE(SUM(ViewCount), 0) as total FROM PageViews")['total'] ?? 0,
        'thisYear'    => db()->fetchOne("SELECT COALESCE(SUM(ViewCount), 0) as total FROM PageViews WHERE Year = :y", [':y' => $year])['total'] ?? 0,
        'thisMonth'   => db()->fetchOne("SELECT COALESCE(SUM(ViewCount), 0) as total FROM PageViews WHERE Year = :y AND Month = :m", [':y' => $year, ':m' => $month])['total'] ?? 0,
    ];
}

/**
 * Get view count for the current page
 */
function getCurrentPageViews() {
    $pageName = basename($_SERVER['PHP_SELF'] ?? 'unknown.php');
    $year  = (int)date('Y');
    $month = (int)date('n');

    $sql = "SELECT COALESCE(SUM(ViewCount), 0) as views 
            FROM PageViews 
            WHERE PageName = :page AND Year = :year AND Month = :month";
    
    $result = db()->fetchOne($sql, [':page' => $pageName, ':year' => $year, ':month' => $month]);
    return $result['views'] ?? 0;
}
/**
 * Get ALL-TIME view count for the current page
 */
function getCurrentPageViewsAllTime() {
    $pageName = basename($_SERVER['PHP_SELF'] ?? 'unknown.php');

    $sql = "SELECT COALESCE(SUM(ViewCount), 0) as total 
            FROM PageViews 
            WHERE PageName = :page";
    
    $result = db()->fetchOne($sql, [':page' => $pageName]);
    return $result['total'] ?? 0;
}

/**
 * Browser tab title for each script (aligned with each page's HTML title element).
 * Used for PageViews reporting; keys are basenames stored in PageViews.PageName.
 *
 * @param string $storedPageName PageViews.PageName or path ending in .php
 */
function roh_page_display_title($storedPageName) {
    $base = basename((string) $storedPageName);

    static $titles = [
        'index.php' => 'Roll of Honour - South Africa',
        'search.php' => 'Roll of Honour: Search',
        'mainListPeople.php' => 'Roll of Honour: All Personnel',
        'person.php' => 'Roll of Honour: Personnel record',
        'pageviews.php' => 'Roll of Honour: Page Views Statistics',
        'DataInfo.php' => 'Roll of Honour - Database Information',
        'chart_views.php' => 'Roll of Honour: Page Views History',
        'chartYear.php' => 'Roll of Honour: Deaths by Year',
        'chartUnit.php' => 'Roll of Honour: Deaths by Unit (Top 60)',
        'chartRegiment.php' => 'Roll of Honour: Deaths by Regiment',
        'chartRank.php' => 'Roll of Honour: Deaths by Rank',
        'chartLocality.php' => 'Roll of Honour: Deaths by Locality',
        'chartCountry.php' => 'Roll of Honour: Deaths by Country',
        'chartCemetery.php' => 'Roll of Honour: Deaths by Cemetery',
        'chartCauseOfDeathYear.php' => 'Roll of Honour: Cause of Death by Year',
        'chartCauseOfDeath.php' => 'Roll of Honour: Cause of Death',
        'chartAge.php' => 'Roll of Honour: Deaths by Age',
        'listRohData.php' => 'Roll of Honour: RohData Table',
        'listPeopleYear.php' => 'Roll of Honour: List People by Year',
        'listPeopleRegiment.php' => 'Roll of Honour: List People by Regiment',
        'listPeopleOnThisDay.php' => 'Roll of Honour: People on This Day',
        'listOnThisDay.php' => 'Roll of Honour: On This Day',
        'add_dateofbirth.php' => 'ROH - Add & Calculate Date of Birth',
        'migrate_foreign_keys.php' => 'ROH - Database Optimization',
        'normalize_lookup_tables.php' => 'ROH - Normalize Lookup Tables',
        'rebuild_tables_with_fks.php' => 'ROH - Rebuild Tables with Foreign Keys',
    ];

    return $titles[$base] ?? $base;
}

/**
 * Returns emoji flag for a country name
 */
function getCountryFlag($country) {
    $flags = [
        'South Africa'                  => '🇿🇦',
        'France'                        => '🇫🇷',
        'Egypt'                         => '🇪🇬',
        'Zimbabwe'                      => '🇿🇼',
        'United Kingdom'                => '🇬🇧',
        'Tanzania'                      => '🇹🇿',
        'Italy'                         => '🇮🇹',
        'Belgium'                       => '🇧🇪',
        'Kenya'                         => '🇰🇪',
        'Namibia'                       => '🇳🇦',
        'Libya'                         => '🇱🇾',
        'Zambia'                        => '🇿🇲',
        'Israel'                        => '🇮🇱',
        'Malta'                         => '🇲🇹',
        'Germany'                       => '🇩🇪',
        'Myanmar'                       => '🇲🇲',
        'Malawi'                        => '🇲🇼',
        'Mozambique'                    => '🇲🇿',
        'Greece'                        => '🇬🇷',
        'Ethiopia'                      => '🇪🇹',
        'Tunisia'                       => '🇹🇳',
        'Somalia'                       => '🇸🇴',
        'Netherlands'                   => '🇳🇱',
        'Turkey'                        => '🇹🇷',
        'Sudan'                         => '🇸🇩',
        'Poland'                        => '🇵🇱',
        'Lesotho'                       => '🇱🇸',
        'India'                         => '🇮🇳',
        'Serbia & Montenegro'           => '🇷🇸',
        'Eritrea'                       => '🇪🇷',
        'South Korea'                   => '🇰🇷',
        'Singapore'                     => '🇸🇬',
        'Iraq'                          => '🇮🇶',
        'Madagascar'                    => '🇲🇬',
        'Algeria'                       => '🇩🇿',
        'Australia'                     => '🇦🇺',
        'Austria'                       => '🇦🇹',
        'Nigeria'                       => '🇳🇬',
        'Hungary'                       => '🇭🇺',
        'Czech Republic'                => '🇨🇿',
        'Malaysia'                      => '🇲🇾',
        'Lebanese Republic'             => '🇱🇧',
        'Ghana'                         => '🇬🇭',
        'Sri Lanka'                     => '🇱🇰',
        'Papua New Guinea'              => '🇵🇬',
        'United States of America'      => '🇺🇸',
        'Yemen'                         => '🇾🇪',
        'Thailand'                      => '🇹🇭',
        'Liberia'                       => '🇱🇷',
        'Canada'                        => '🇨🇦',
        'Bangladesh'                    => '🇧🇩',
        'Union of the Comoros'          => '🇰🇲',
        'Bulgaria'                      => '🇧🇬',
        'Sierra Leone'                  => '🇸🇱',
        'Morocco'                       => '🇲🇦',
        'Syria'                         => '🇸🇾',
        'Pakistan'                      => '🇵🇰',
        'New Zealand'                   => '🇳🇿',
        'China'                         => '🇨🇳',
        'Romania'                       => '🇷🇴',
        'Portugal'                      => '🇵🇹',
        'Norway'                        => '🇳🇴',
        'Indonesia'                     => '🇮🇩',
        'East Africa'                   => '🌍',
        'Denmark'                       => '🇩🇰',
        'Cyprus'                        => '🇨🇾',
        'Congo'                         => '🇨🇩',
        'Botswana'                      => '🇧🇼',
        'Russia'                        => '🇷🇺',
        'Switzerland'                   => '🇨🇭',
        'Mauritius'                     => '🇲🇺',
        'Japan'                         => '🇯🇵',
        'Ireland'                       => '🇮🇪',
        'Sweden'                        => '🇸🇪',
        'Iran'                          => '🇮🇷',
        'Bermuda'                       => '🇧🇲',
        'Argentina'                     => '🇦🇷',
        'United Arab Emirates'          => '🇦🇪',
        'Togo'                          => '🇹🇬',
        'Swaziland'                     => '🇸🇿',
        'Spain'                         => '🇪🇸',
        'Oman'                          => '🇴🇲',
        'Gambia'                        => '🇬🇲',
        'Bahamas'                       => '🇧🇸',
        'At Sea'                        => '🌊',
        'Ascension Island'              => '🌍',
        'Lost at Sea'                   => '🌊',
        'Unknown'                       => '🌍',
    ];

    // Try exact match first
    if (isset($flags[$country])) {
        return $flags[$country];
    }

    // Try partial match
    foreach ($flags as $key => $flag) {
        if (stripos($country, $key) !== false) {
            return $flag;
        }
    }

    return '🌍'; // Default globe for unknown countries
}
/**
 * Count records with images
 */
function CountWithImages() {
    return db()->fetchOne("SELECT COUNT(*) as total FROM PersonImages WHERE ImgPath IS NOT NULL")['total'] ?? 0;
}

/**
 * Count distinct countries
 */
function CountCountries() {
    return db()->fetchOne("SELECT COUNT(DISTINCT Country) as total FROM PersonInfoRaw WHERE Country IS NOT NULL AND Country != ''")['total'] ?? 0;
}

/* ================================================================
   Legacy / Deprecated - Keep for compatibility if needed
   ================================================================ */

// Old global $db is replaced by db() helper
// Remove or deprecate the old class if still present elsewhere

?>