<?php
/**
 * extract_birth_dates.php
 * Batch OCR Birth Date Extraction with Progress + Start Point
 */
require_once("include/db.php");
require_once("functions.php");   // ← This was missing

$batchSize = 100;
$page = max(1, (int)($_GET['page'] ?? 1));
$startFrom = isset($_GET['start']) ? (int)$_GET['start'] : 1;

$offset = ($page - 1) * $batchSize;

// Get total for progress
$totalPersons = CountRecords('PersonInfoRaw');

echo "<h1>Birth Date Extraction from Gravestone Images</h1>";
echo "<p><strong>Progress:</strong> Processing from PersonNumber <strong>$startFrom</strong> onwards</p>";
echo "<hr>";

$persons = db()->fetchAll("SELECT PersonNumber, Name 
                           FROM PersonInfoRaw 
                           WHERE PersonNumber >= :start 
                           ORDER BY PersonNumber 
                           LIMIT :offset, :limit", [
    ':start'  => $startFrom,
    ':offset' => $offset,
    ':limit'  => $batchSize
]);

$updatesGenerated = 0;
$processedThisBatch = 0;

foreach ($persons as $person) {
    $pn = $person['PersonNumber'];
    $name = $person['Name'];
    $processedThisBatch++;

    $images = db()->fetchAll("SELECT * FROM PersonImages WHERE PersonNumber = :pn", [':pn' => $pn]);

    if (empty($images)) continue;

    echo "<h5>#{$pn} — {$name}</h5>";

    foreach ($images as $img) {
        $imgPath = $img['ImgPath'] . '/' . $img['ImgName'];
        $fullPath = __DIR__ . '/../' . $imgPath;

        if (!file_exists($fullPath)) continue;

        $command = "tesseract " . escapeshellarg($fullPath) . " stdout --oem 3 --psm 6 2>/dev/null";
        $text = shell_exec($command);

        if (empty($text)) continue;

        $birthYear = null;
        if (preg_match('/Born[:\s]*(\d{4})/i', $text, $m)) {
            $birthYear = $m[1];
        } elseif (preg_match('/b\.\s*(\d{4})/i', $text, $m)) {
            $birthYear = $m[1];
        } elseif (preg_match('/(\d{4})\s*[-–]\s*\d{4}/', $text, $m)) {
            $birthYear = $m[1];
        }

        if ($birthYear) {
            echo "→ Found <strong>{$birthYear}</strong> in {$img['ImgName']}<br>";
            echo "<pre>UPDATE PersonInfoRaw SET DateBirth = '{$birthYear}' WHERE PersonNumber = {$pn};</pre><br>";
            $updatesGenerated++;
        }
    }
}

echo "<hr>";
echo "<p><strong>Batch Summary:</strong> Processed $processedThisBatch persons • Found $updatesGenerated possible birth dates</p>";

// Navigation
$totalPages = ceil(($totalPersons - $startFrom + 1) / $batchSize) ?: 1;

echo "<div class='text-center mt-4'>";
if ($page > 1) {
    echo "<a href='?page=" . ($page-1) . "&start=$startFrom' class='btn btn-secondary'>← Previous Batch</a> ";
}
echo "<strong>Batch $page of $totalPages</strong> ";
if ($page < $totalPages) {
    echo " <a href='?page=" . ($page+1) . "&start=$startFrom' class='btn btn-primary'>Next Batch →</a>";
}

echo "<div class='mt-4'>";
echo "<form method='get' class='d-inline'>";
echo "<input type='hidden' name='page' value='1'>";
echo "Start from PersonNumber: <input type='number' name='start' value='$startFrom' class='form-control d-inline w-auto' style='width:140px'>";
echo " <button type='submit' class='btn btn-outline-light'>Go</button>";
echo "</form>";
echo "</div>";
echo "</div>";
?>

<p class="text-center small text-muted mt-4">
    Copy the UPDATE statements above and run them in your database.
</p>