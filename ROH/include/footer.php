<?php
/**
 * include/footer.php
 * Modern, respectful, and clean footer
 */
?>
<?php
require_once(__DIR__ . '/../functions.php');   // Ensure functions are loaded

// Increment page view on every page load
$pageFile = $_SERVER['PHP_SELF'] ?? 'unknown.php';
incrementPageView($pageFile);

// Get current stats
$stats = getPageViewStats();
?>
<!-- Footer -->
<footer class="bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="row">

            <!-- Left Column - Memorial Message -->
            <div class="col-md-5 mb-4">
                <h5 class="text-white">Roll of Honour</h5>
                <p class="small">
                    Remembering South African service personnel who made the ultimate sacrifice.<br>
                    Lest We Forget.
                </p>
                <p class="small text-muted">
                    Originally compiled in the 1990s from official documents.<br>
                    This is a restoration and modernisation project.
                </p>
            </div>

            <!-- Contact -->
            <div class="col-md-4 mb-4">
                <h6 class="text-white">Contact</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2">
                        <i class="fa fa-envelope me-2"></i>
                        <a href="mailto:dovey.john@gmail.com" class="text-light text-decoration-none">dovey.john@gmail.com</a>
                    </li>
                    <li class="mb-2">
                        <i class="fa fa-map-marker me-2"></i>
                        Pedasí, Los Santos, Panamá
                    </li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div class="col-md-3 mb-4">
                <h6 class="text-white">Quick Links</h6>
                <ul class="list-unstyled small">
                    <li><a href="index.php" class="text-light text-decoration-none">Home</a></li>
                    <li><a href="mainListPeople.php" class="text-light text-decoration-none">Browse Names</a></li>
                    <li><a href="chartYear.php" class="text-light text-decoration-none">Statistics</a></li>
                    <li><a href="DataInfo.php" class="text-light text-decoration-none">Database Info</a></li>
                </ul>
            </div>

        </div>

        <!-- Bottom Bar -->
        <hr class="my-4">
        <div class="row align-items-center">
            <div class="col-md-6 small text-muted">
                &copy; <?= date("Y") ?> Roll of Honour Project — All Rights Reserved
            </div>
            <div class="col-md-6 text-md-end small text-muted">
                A personal restoration project • 
                <a href="https://github.com/JohnDovey/CreateROH" class="text-light text-decoration-none" target="_blank">
                    GitHub
                </a>
            </div>
        </div>
    </div>
    <div class="text-center mt-4 small text-muted">
    Total Page Views: <strong><?= number_format($stats['total']) ?></strong> | 
    Views this Year: <strong><?= number_format($stats['thisYear']) ?></strong> | 
    Views this Month: <strong><?= number_format($stats['thisMonth']) ?></strong>
</div>
</footer>
