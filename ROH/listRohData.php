<?php
/**
 * listRohData.php - Safe version
 */
require_once("include/db.php");
require_once("functions.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: RohData Table</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">All Records in Table: rohdata</h1>

        <?php
        $totalRecords = CountRecords('rohdata');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $recordsPerPage = 50;
        $offset = ($recordsPerPage * $page) - $recordsPerPage;
        ?>

        <div class="row justify-content-md-center">
            <div class="col-md-auto bg-primary p-4 rounded shadow-sm" style="min-width: 1200px;">

                <p><strong>Total Records:</strong> <?= number_format($totalRecords) ?></p>

                <table class="table table-dark table-striped table-hover">
                    <thead>
                        <tr>
                            <th>PersonNumber</th>
                            <th>Name</th>
                            <th>Rank</th>
                            <th>Regiment</th>
                            <th>DateDeath</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT * FROM rohdata 
                            ORDER BY PersonNumber 
                            LIMIT :offset, :limit";

                    $rows = db()->fetchAll($sql, [
                        ':offset' => $offset,
                        ':limit'  => $recordsPerPage
                    ]);

                    foreach ($rows as $row):
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['PersonNumber'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['Name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['Rank'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['Regiment'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['DateDeath'] ?? '') ?></td>
                            <td>
                                <?php if (!empty($row['PersonNumber'])): ?>
                                    <a href="person.php?PersonNumber=<?= $row['PersonNumber'] ?>" 
                                       class="btn btn-sm btn-primary">View</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination (same as before) -->
                <?php $total_pages = ceil($totalRecords / $recordsPerPage); ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item"><a class="page-link" href="?page=1">« First</a></li>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>">‹ Prev</a></li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page-3); $i <= min($total_pages, $page+3); $i++): ?>
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