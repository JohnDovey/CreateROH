<?php
/**
 * listOnThisDay.php
 * Lists people who died on this day (same month and day)
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: On This Day</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <?php
        $currentMonth = (int)date('m');
        $currentDay   = (int)date('d');

        $page = max(1, (int)($_GET['page'] ?? 1));
        $recordsPerPage = 50;
        $offset = ($recordsPerPage * $page) - $recordsPerPage;

        // Count people who died on this day
        $sqlCount = "SELECT COUNT(*) as total 
                     FROM PersonInfoRaw 
                     WHERE strftime('%m', DateDeath) = :month 
                       AND strftime('%d', DateDeath) = :day";
        $totalOnThisDay = db()->fetchOne($sqlCount, [
            ':month' => sprintf('%02d', $currentMonth),
            ':day'   => sprintf('%02d', $currentDay)
        ])['total'] ?? 0;
        ?>

        <h1 class="display-4 text-center my-5">
            On This Day — <?= date('d F') ?> 
            <small class="text-muted">(<?= number_format($totalOnThisDay) ?> records)</small>
        </h1>

        <div class="row justify-content-md-center">
            <div class="col-md-auto bg-primary p-4 rounded shadow-sm" style="min-width: 1100px;">

                <table class="table table-dark table-striped table-hover">
                    <thead>
                        <tr>
                            <th><a href="?sort=PersonNumber">Person #</a></th>
                            <th><a href="?sort=LastName">Last Name</a></th>
                            <th><a href="?sort=FirstName">First Name</a></th>
                            <th>Initials</th>
                            <th>Rank</th>
                            <th>Regiment</th>
                            <th>Date of Death</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT *, strftime('%Y', DateDeath) as Year 
                            FROM PersonInfoRaw 
                            WHERE strftime('%m', DateDeath) = :month 
                              AND strftime('%d', DateDeath) = :day 
                            ORDER BY LastName, FirstName 
                            LIMIT :offset, :limit";

                    $params = [
                        ':month'  => sprintf('%02d', $currentMonth),
                        ':day'    => sprintf('%02d', $currentDay),
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
                            <td><?= htmlspecialchars($row['Regiment'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['DateDeath'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php
                $total_pages = ceil($totalOnThisDay / $recordsPerPage);
                ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item"><a class="page-link" href="?page=1">« First</a></li>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>">‹ Prev</a></li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>">Next ›</a></li>
                            <li class="page-item"><a class="page-link" href="?page=<?= $total_pages ?>">Last »</a></li>
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
