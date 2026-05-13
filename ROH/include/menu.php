<?php
/**
 * include/menu.php - With Logo
 */
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <!-- Logo + Brand -->
        <a class="navbar-brand d-flex align-items-center fw-bold" href="index.php">
            <img src="images/For-the-Fallen.jpg" 
                 alt="For the Fallen" 
                 style="height: 48px; width: auto; border-radius: 6px; margin-right: 12px;">
            <span>Roll of Honour</span>
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" aria-controls="navbarNav" 
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="fa fa-home"></i> Home</a></li>
                <li class="nav-item"><a class="nav-link" href="search.php"><i class="fa fa-search"></i> Search</a></li>
                <li class="nav-item"><a class="nav-link" href="mainListPeople.php"><i class="fa fa-list"></i> Browse All</a></li>

                <!-- Lists -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Lists</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="listPeopleRegiment.php">By Regiment</a></li>
                        <li><a class="dropdown-item" href="listPeopleYear.php">By Year</a></li>
                        <li><a class="dropdown-item" href="listOnThisDay.php">On This Day</a></li>
                    </ul>
                </li>

                <!-- Statistics -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Statistics</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="chartYear.php">By Year</a></li>
                        <li><a class="dropdown-item" href="chartCauseOfDeath.php">Cause of Death</a></li>
                        <li><a class="dropdown-item" href="chartRank.php">Deaths by Rank</a></li>
                        <li><a class="dropdown-item" href="chartCountry.php">By Country</a></li>
                        <li><a class="dropdown-item" href="pageviews.php">Page Views</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>