<?php
/**
 * listPeopleOnThisDay.php
 * Secure list of people by date of death
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: People on This Day</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>

<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <?php
        $DateDeath = isset($_GET['DateDeath']) ? $_GET['DateDeath'] : date('Y-m-d'); // default to today if none
        $page = max(1, (int)($_GET['page'] ?? 1));
        $sortField = in_array($_GET['sort'] ?? 'LastName', ['PersonNumber','LastName','FirstName','Rank']) 
                     ? $_GET['sort'] 
                     : 'LastName';

        $recordsPerPage = 50;
        $fromRecordNum = ($recordsPerPage * $page) - $recordsPerPage;

        // Count people on this date
        $sqlCount = "SELECT COUNT(*) as total FROM PersonInfoRaw WHERE DateDeath = :date";
        $totalOnDay = db()->fetchOne($sqlCount, [':date' => $DateDeath])['total'] ?? 0;
        ?>

        <h1 class="display-4 text-center my-5">
            Roll of Honour — <?= htmlspecialchars($DateDeath) ?> 
            <small class="text-muted">(<?= number_format($totalOnDay) ?> records)</small>
        </h1>

        <div class="row justify-content-md-center">
            <div class="col-md-auto bg-primary p-4 rounded shadow-sm" style="min-width: 1100px;">

                <!-- Date Selector -->
                <div class="mb-4">
                    <form method="get" class="d-inline">
                        <label for="DateDeath" class="me-2">Select Date:</label>
                        <select name="DateDeath" id="DateDeath" class="form-select d-inline w-auto" onchange="this.form.submit()">
                            <?php
                            $dateSql = "SELECT DISTINCT DateDeath 
                                        FROM PersonInfoRaw 
                                        WHERE DateDeath IS NOT NULL 
                                        ORDER BY DateDeath";
                            $dates = db()->fetchAll($dateSql);
                            foreach ($dates as $d): ?>
                                <option value="<?= htmlspecialchars($d['DateDeath']) ?>" 
                                        <?= $d['DateDeath'] == $DateDeath ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($d['DateDeath']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <table class="table table-dark table-striped table-hover">
                    <thead>
                        <tr>
                            <th><a href="?DateDeath=<?= urlencode($DateDeath) ?>&sort=PersonNumber" class="text-white">Person #</a></th>
                            <th><a href="?DateDeath=<?= urlencode($DateDeath) ?>&sort=LastName" class="text-white">Last Name</a></th>
                            <th><a href="?DateDeath=<?= urlencode($DateDeath) ?>&sort=FirstName" class="text-white">First Name</a></th>
                            <th>Initials</th>
                            <th>Rank</th>
                            <th>Regiment</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT *, strftime('%Y', DateDeath) as Year 
                            FROM PersonInfoRaw 
                            WHERE DateDeath = :date 
                            ORDER BY {$sortField}, FirstName 
                            LIMIT :offset, :limit";

                    $params = [
                        ':date'   => $DateDeath,
                        ':offset' => $fromRecordNum,
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
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php
                $total_pages = ceil($totalOnDay / $recordsPerPage);
                ?>
                <nav aria-label="Pagination">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item"><a class="page-link" href="?DateDeath=<?= urlencode($DateDeath) ?>&page=1&sort=<?= $sortField ?>">« First</a></li>
                            <li class="page-item"><a class="page-link" href="?DateDeath=<?= urlencode($DateDeath) ?>&page=<?= $page-1 ?>&sort=<?= $sortField ?>">‹ Prev</a></li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?DateDeath=<?= urlencode($DateDeath) ?>&page=<?= $i ?>&sort=<?= $sortField ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item"><a class="page-link" href="?DateDeath=<?= urlencode($DateDeath) ?>&page=<?= $page+1 ?>&sort=<?= $sortField ?>">Next ›</a></li>
                            <li class="page-item"><a class="page-link" href="?DateDeath=<?= urlencode($DateDeath) ?>&page=<?= $total_pages ?>&sort=<?= $sortField ?>">Last »</a></li>
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
