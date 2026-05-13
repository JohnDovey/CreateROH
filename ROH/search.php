<?php
/**
 * search.php - Fixed (No ServiceNumber column)
 */
require_once("include/db.php");
require_once("functions.php");

$searchTerm = trim($_GET['q'] ?? '');
$results = [];
$error = '';

if (!empty($searchTerm)) {
    try {
        $sql = "SELECT PersonNumber, LastName, FirstName, Initials, Rank, Regiment, DateDeath
                FROM PersonInfoRaw 
                WHERE LastName LIKE :term1 
                   OR FirstName LIKE :term2
                   OR PersonNumber = :exact 
                ORDER BY LastName, FirstName 
                LIMIT 100";

        $params = [
            ':term1' => '%' . $searchTerm . '%',
            ':term2' => '%' . $searchTerm . '%',
            ':exact' => is_numeric($searchTerm) ? (int)$searchTerm : -1
        ];

        $results = db()->fetchAll($sql, $params);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll of Honour: Search</title>
    <?php require_once("include/bootstrap-head.php"); ?>
</head>
<body>
    <div class="container-fluid clearfix">
        <?php require_once("include/menu.php"); ?>

        <h1 class="display-4 text-center my-5">Search Roll of Honour</h1>

        <div class="row justify-content-center mb-5">
            <div class="col-lg-6">
                <form method="get" class="input-group input-group-lg">
                    <input type="text" 
                           name="q" 
                           class="form-control" 
                           placeholder="Enter Surname or Person Number"
                           value="<?= htmlspecialchars($searchTerm) ?>" 
                           autofocus>
                    <button class="btn btn-primary" type="submit">Search</button>
                </form>
                <small class="text-muted">Examples: Smith, 12345, or partial surname</small>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($searchTerm)): ?>
            <h5 class="text-center mb-4">
                <?= count($results) ?> result(s) for "<strong><?= htmlspecialchars($searchTerm) ?></strong>"
            </h5>

            <?php if (empty($results)): ?>
                <div class="alert alert-warning text-center">
                    No records found. Try a different spelling or number.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-dark table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Person #</th>
                                <th>Name</th>
                                <th>Rank</th>
                                <th>Regiment</th>
                                <th>Date of Death</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($results as $row): ?>
                            <tr>
                                <td>
                                    <a href="person.php?PersonNumber=<?= $row['PersonNumber'] ?>" 
                                       class="btn btn-sm btn-primary">
                                        <?= $row['PersonNumber'] ?>
                                    </a>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars(ucwords(strtolower($row['LastName'] ?? ''))) ?></strong>, 
                                    <?= htmlspecialchars(ucwords(strtolower($row['FirstName'] ?? ''))) ?>
                                </td>
                                <td><?= htmlspecialchars($row['Rank'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['Regiment'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['DateDeath'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>

    <hr>
    <?php require_once("include/footer.php"); ?>
    <?php require_once("include/bootstrap-footer.php"); ?>
</body>
</html>