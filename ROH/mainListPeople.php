<?php
/**
 * mainListPeople.php
 * Secure paginated people list
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: List People</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>

<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Roll of Honour — All Personnel</h1>

        <?php
        $totalDeaths = CountTotalDeaths();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $sortField = in_array($_GET['sort'] ?? 'LastName', ['PersonNumber','LastName','FirstName','RankID','DateDeath']) 
                     ? $_GET['sort'] 
                     : 'LastName';
        
        $recordsPerPage = 50;
        $fromRecordNum = ($recordsPerPage * $page) - $recordsPerPage;
        ?>

        <div class="row justify-content-md-center">
            <div class="col-md-auto bg-primary p-4 rounded shadow-sm" style="min-width: 1100px;">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Total Records: <strong><?= number_format($totalDeaths) ?></strong></h4>
                    <div>
                        Sorted by: <strong><?= htmlspecialchars($sortField) ?></strong>
                    </div>
                </div>

                <table class="table table-dark table-striped table-hover">
                    <thead>
                        <tr>
                            <th><a href="?page=<?= $page ?>&sort=PersonNumber" class="text-white">Person #</a></th>
                            <th><a href="?page=<?= $page ?>&sort=LastName" class="text-white">Last Name</a></th>
                            <th><a href="?page=<?= $page ?>&sort=FirstName" class="text-white">First Name</a></th>
                            <th>Initials</th>
                            <th>Rank</th>
                            <th><a href="?page=<?= $page ?>&sort=DateDeath" class="text-white">Year</a></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT *, strftime('%Y', DateDeath) as Year 
                            FROM PersonInfoRaw 
                            ORDER BY {$sortField}, FirstName 
                            LIMIT :offset, :limit";
                    
                    $params = [
                        ':offset' => $fromRecordNum,
                        ':limit'  => $recordsPerPage
                    ];

                    $rows = db()->fetchAll($sql, $params);

                    foreach ($rows as $row):
                    ?>
                        <tr>
                            <td>
                                <a href="person.php?PersonNumber=<?= $row['PersonNumber'] ?>" 
                                   class="text-white">
                                    <?= $row['PersonNumber'] ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars(ucwords(strtolower($row['LastName'] ?? ''))) ?></td>
                            <td><?= htmlspecialchars(ucwords(strtolower($row['FirstName'] ?? ''))) ?></td>
                            <td><?= htmlspecialchars($row['Initials'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['Rank'] ?? '') ?></td>
                            <td><?= $row['Year'] ?? '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php
                $total_pages = ceil($totalDeaths / $recordsPerPage);
                ?>
                <nav aria-label="People pagination">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=1&sort=<?= $sortField ?>">« First</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page-1 ?>&sort=<?= $sortField ?>">‹ Previous</a>
                            </li>
                        <?php endif; ?>

                        <?php
                        $range = 2;
                        $start = max(1, $page - $range);
                        $end = min($total_pages, $page + $range);
                        for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&sort=<?= $sortField ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page+1 ?>&sort=<?= $sortField ?>">Next ›</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $total_pages ?>&sort=<?= $sortField ?>">Last »</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <p class="text-center text-muted">
                    Page <?= $page ?> of <?= $total_pages ?> • <?= number_format($totalDeaths) ?> total records
                </p>

            </div>
        </div>
    </div>

    <hr>
    <?php require_once("include/footer.php"); ?>
    <?php require_once("include/bootstrap-footer.php"); ?>
</body>
</html>
