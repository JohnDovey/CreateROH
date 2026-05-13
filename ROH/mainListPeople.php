<?php
/**
 * mainListPeople.php - ULTRA SAFE VERSION
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: All Personnel</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <?php
        $totalDeaths = CountTotalDeaths();
        $page = max(1, (int)($_GET['page'] ?? 1));

        // ULTRA SAFE sort handling
        $allowedSort = ['PersonNumber', 'LastName', 'FirstName', 'Rank', 'DateDeath'];
        $sortField = $_GET['sort'] ?? 'LastName';
        if (!in_array($sortField, $allowedSort)) {
            $sortField = 'LastName';
        }

        $recordsPerPage = 50;
        $offset = ($recordsPerPage * $page) - $recordsPerPage;
        ?>

        <h1 class="display-4 text-center my-5">Roll of Honour — All Personnel</h1>

        <div class="row justify-content-md-center">
            <div class="col-md-auto bg-primary p-4 rounded shadow-sm" style="min-width: 1100px;">

                <div class="d-flex justify-content-between mb-3">
                    <h4>Total Records: <strong><?= number_format($totalDeaths) ?></strong></h4>
                </div>

                <table class="table table-dark table-striped table-hover">
                    <thead>
                        <tr>
                            <th><a href="?sort=PersonNumber" class="text-white">Person #</a></th>
                            <th><a href="?sort=LastName" class="text-white">Last Name</a></th>
                            <th><a href="?sort=FirstName" class="text-white">First Name</a></th>
                            <th>Initials</th>
                            <th>Rank</th>
                            <th><a href="?sort=DateDeath" class="text-white">Year</a></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT *, strftime('%Y', DateDeath) as Year 
                            FROM PersonInfoRaw 
                            ORDER BY {$sortField}, FirstName 
                            LIMIT :offset, :limit";

                    $params = [
                        ':offset' => $offset,
                        ':limit'  => $recordsPerPage
                    ];

                    $rows = db()->fetchAll($sql, $params);

                    foreach ($rows as $row):
                    ?>
                        <tr>
                            <td>
                                <a href="person.php?PersonNumber=<?= $row['PersonNumber'] ?>" 
                                   class="btn btn-sm btn-primary">
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
                <?php $total_pages = ceil($totalDeaths / $recordsPerPage); ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item"><a class="page-link" href="?page=1&sort=<?= $sortField ?>">« First</a></li>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>&sort=<?= $sortField ?>">‹ Prev</a></li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&sort=<?= $sortField ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>&sort=<?= $sortField ?>">Next ›</a></li>
                            <li class="page-item"><a class="page-link" href="?page=<?= $total_pages ?>&sort=<?= $sortField ?>">Last »</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <hr>
    <?php require_once("include/footer.php"); ?>
    <?php require_once("include/bootstrap-footer.php"); ?>
</body>
</html>