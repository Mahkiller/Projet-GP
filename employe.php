<?php
require_once 'Connection.php';

$conn = dbconnect();

if (!isset($_GET['dept_no'])) {
    die('Aucun département sélectionné.');
}
$dept_no = mysqli_real_escape_string($conn, $_GET['dept_no']);

$sql = "SELECT e.emp_no, e.first_name, e.last_name, e.hire_date
FROM employees e
JOIN dept_emp de ON e.emp_no = de.emp_no
WHERE de.dept_no = '$dept_no'
ORDER BY e.last_name, e.first_name";

$result = mysqli_query($conn, $sql);
if (!$result) {
    die('Erreur SQL : ' . mysqli_error($conn));
}

$dept_sql = "SELECT dept_name FROM departments WHERE dept_no = '$dept_no'";
$dept_result = mysqli_query($conn, $dept_sql);
if (!$dept_result) {
    die('Erreur SQL : ' . mysqli_error($conn));
}
$dept = mysqli_fetch_assoc($dept_result);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employés du département</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <h2 class="text-center my-4">
        Employés du département <?= htmlspecialchars($dept['dept_name'] ?? $dept_no) ?>
    </h2>
    <div class="container">
        <table class="table table-hover table-bordered align-middle shadow">
            <thead class="table-primary">
                <tr>
                    <th>Numéro</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Date d'embauche</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="zoom-hover">
                        <td><?= htmlspecialchars($row['emp_no']) ?></td>
                        <td><?= htmlspecialchars($row['first_name']) ?></td>
                        <td><?= htmlspecialchars($row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['hire_date']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="text-center mb-4">
            <a href="index.php" class="btn btn-primary">Retour à la liste des départements</a>
        </div>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>