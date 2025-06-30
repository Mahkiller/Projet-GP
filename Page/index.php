<?php
require_once 'Connection.php';
$conn = dbconnect();

$sql = "
    SELECT d.dept_no, d.dept_name,
           CONCAT(e.first_name, ' ', e.last_name) AS manager_name
    FROM departments d
    LEFT JOIN dept_manager dm
        ON d.dept_no = dm.dept_no
        AND dm.to_date = (SELECT MAX(to_date) FROM dept_manager WHERE dept_no = d.dept_no)
    LEFT JOIN employees e
        ON dm.emp_no = e.emp_no
";
$result = mysqli_query($conn, $sql);

$departments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $departments[] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Départements</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Style/Style.css">
</head>
<body>
    <h2 class="text-center my-4">Liste des Départements</h2>
    <div class="container">
        <table class="table table-hover table-bordered align-middle shadow">
            <thead class="table-primary">
                <tr>
                    <th>Nom du département</th>
                    <th>Manager en cours</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($departments as $dept): ?>
                    <tr class="zoom-hover">
                        <td>
                            <a href="employe.php?dept_no=<?= urlencode($dept['dept_no']) ?>">
                                <?= htmlspecialchars($dept['dept_name']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($dept['manager_name'] ?? 'Aucun') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>
