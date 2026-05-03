<?php
/**
 * include/menu.php
 * Modern Bootstrap 5 Responsive Navigation
 */
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fa fa-cross"></i> Roll of Honour
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler" type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" 
                aria-controls="navbarNav" 
                aria-expanded="false" 
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu Items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">

                <!-- Home -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fa fa-home"></i> Home
                    </a>
                </li>

                <!-- Browse -->
                <li class="nav-item">
                    <a class="nav-link" href="mainListPeople.php">
                        <i class="fa fa-list"></i> Browse All
                    </a>
                </li>

                <!-- Lists Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" 
                       id="listsDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-table"></i> Lists
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="listsDropdown">
                        <li><a class="dropdown-item" href="listPeopleRegiment.php">By Regiment</a></li>
                        <li><a class="dropdown-item" href="listPeopleYear.php">By Year of Death</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="person.php">Single Person Lookup</a></li>
                    </ul>
                </li>

                <!-- Statistics Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" 
                       id="statsDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-chart-bar"></i> Statistics
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="statsDropdown">
                        <li><a class="dropdown-item" href="chartYear.php">By Year</a></li>
                        <li><a class="dropdown-item" href="chartAge.php">By Age</a></li>
                        <li><a class="dropdown-item" href="chartCauseOfDeath.php">Cause of Death</a></li>
                        <li><a class="dropdown-item" href="chartCauseOfDeathYear.php">Cause by Year</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="chartRegiment.php">By Regiment</a></li>
                        <li><a class="dropdown-item" href="chartRank.php">By Rank</a></li>
                        <li><a class="dropdown-item" href="chartCountry.php">By Country</a></li>
                        <li><a class="dropdown-item" href="chartCemetery.php">By Cemetery</a></li>
                        <li><a class="dropdown-item" href="chartUnit.php">By Unit</a></li>
                        <li><a class="dropdown-item" href="chartLocality.php">By Locality</a></li>
                    </ul>
                </li>

                <!-- Utilities -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" 
                       id="utilsDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-tools"></i> Utilities
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="utilsDropdown">
                        <li><a class="dropdown-item" href="DataInfo.php">Database Info</a></li>
                        <li><a class="dropdown-item" href="pageviews.php">Page Views Statistics</a></li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>
