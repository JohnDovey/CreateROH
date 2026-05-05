<?php
require_once(__DIR__ . '/../functions.php');

// Increment page view
$pageFile = $_SERVER['PHP_SELF'] ?? 'unknown.php';
incrementPageView($pageFile);

// Get stats
$stats = getPageViewStats();
$currentPageAllTime   = getCurrentPageViewsAllTime();
$currentPageThisMonth = getCurrentPageViews();
?>

<!-- Footer content remains the same until the statistics section -->

<!-- Page View Statistics -->
<div class="text-center mt-4 small text-muted">
    <strong>Total Site Views:</strong> <?= number_format($stats['total']) ?> | 
    <strong>This Year:</strong> <?= number_format($stats['thisYear']) ?> | 
    <strong>This Month:</strong> <?= number_format($stats['thisMonth']) ?> <br>
    <strong>This Page (All Time):</strong> <?= number_format($currentPageAllTime) ?> | 
    <strong>This Page (This Month):</strong> <?= number_format($currentPageThisMonth) ?>
</div>
