<?php
/**
 * listPeopleYear.php
 * Secure list of people by year of death
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: List People by Year</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>

<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <?php
        $Year = isset($_GET['Year']) ? (int)$_GET['Year'] : 1916;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $sortField = in_array($_GET['sort'] ?? 'LastName', ['PersonNumber','LastName','FirstName','Rank','Regiment']) 
                     ? $_GET['sort'] 
                     : 'LastName';

        $recordsPerPage = 50;
        $fromRecordNum = ($recordsPerPage * $page) - $recordsPerPage;

        // Count people in this year
        $sqlCount = "SELECT COUNT(*) as total FROM PersonInfoRaw WHERE strftime('%Y', DateDeath) = :year";
        $totalInYear = db()->fetchOne($sqlCount, [':year' => $Year])['total'] ?? 0;
        ?>

        <h1 class="display-4 text-center my-5">
            Roll of Honour — <?= $Year ?> <small class="text-muted">(<?= number_format($totalInYear) ?> records)</small>
        </h1>

        <div class="row justify-content-md-center">
            <div class="col-md-auto bg-primary p-4 rounded shadow-sm" style="min-width: 1100px;">

                <!-- Year Selector -->
                <div class="mb-4">
                    <form method="get" class="d-inline">
                        <label for="Year" class="me-2">Select Year:</label>
                        <select name="Year" id="Year" class="form-select d-inline w-auto" onchange="this.form.submit()">
                            <?php
                            $yearsSql = "SELECT DISTINCT strftime('%Y', DateDeath) as Year 
                                         FROM PersonInfoRaw 
                                         WHERE DateDeath IS NOT NULL 
                                         ORDER BY Year DESC";
                            $years = db()->fetchAll($yearsSql);
                            foreach ($years as $y): ?>
                                <option value="<?= $y['Year'] ?>" <?= $y['Year'] == $Year ? 'selected' : '' ?>>
                                    <?= $y['Year'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <table class="table table-dark table-striped table-hover">
                    <thead>
                        <tr>
                            <th><a href="?Year=<?= $Year ?>&sort=PersonNumber" class="text-white">Person #</a></th>
                            <th><a href="?Year=<?= $Year ?>&sort=LastName" class="text-white">Last Name</a></th>
                            <th><a href="?Year=<?= $Year ?>&sort=FirstName" class="text-white">First Name</a></th>
                            <th>Initials</th>
                            <th>Rank</th>
                            <th><a href="?Year=<?= $Year ?>&sort=Regiment" class="text-white">Regiment</a></th>
                            <th>Date of Death</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT *, strftime('%Y', DateDeath) as Year 
                            FROM PersonInfoRaw 
                            WHERE strftime('%Y', DateDeath) = :year 
                            ORDER BY {$sortField}, FirstName 
                            LIMIT :offset, :limit";

                    $params = [
                        ':year'   => $Year,
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
                            <td><?= htmlspecialchars($row['DateDeath'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php
                $total_pages = ceil($totalInYear / $recordsPerPage);
                ?>
                <nav aria-label="Pagination">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item"><a class="page-link" href="?Year=<?= $Year ?>&page=1&sort=<?= $sortField ?>">« First</a></li>
                            <li class="page-item"><a class="page-link" href="?Year=<?= $Year ?>&page=<?= $page-1 ?>&sort=<?= $sortField ?>">‹ Prev</a></li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?Year=<?= $Year ?>&page=<?= $i ?>&sort=<?= $sortField ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item"><a class="page-link" href="?Year=<?= $Year ?>&page=<?= $page+1 ?>&sort=<?= $sortField ?>">Next ›</a></li>
                            <li class="page-item"><a class="page-link" href="?Year=<?= $Year ?>&page=<?= $total_pages ?>&sort=<?= $sortField ?>">Last »</a></li>
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
