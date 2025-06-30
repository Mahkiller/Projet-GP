<?php
require_once 'Connection.php';
$conn = dbconnect();

if (!isset($_GET['emp_no'])) {
    die('Aucun employé sélectionné.');
}
$emp_no = mysqli_real_escape_string($conn, $_GET['emp_no']);

$sql = "SELECT e.*, t.title, s.salary
        FROM employees e
        LEFT JOIN titles t ON e.emp_no = t.emp_no AND (t.to_date IS NULL OR t.to_date = (SELECT MAX(to_date) FROM titles WHERE emp_no = e.emp_no))
        LEFT JOIN salaries s ON e.emp_no = s.emp_no AND s.to_date = (SELECT MAX(to_date) FROM salaries WHERE emp_no = e.emp_no)
        WHERE e.emp_no = '$emp_no'
        LIMIT 1";
$result = mysqli_query($conn, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    die('Employé introuvable.');
}
$emp = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fiche de l'employé</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Style/Style.css">
</head>
<body>
    <h2 class="text-center my-4">Fiche de l'employé</h2>
    <div class="container" style="max-width:600px;">
        <table class="table table-bordered">
            <tr><th>Numéro</th><td><?= htmlspecialchars($emp['emp_no']) ?></td></tr>
            <tr><th>Prénom</th><td><?= htmlspecialchars($emp['first_name']) ?></td></tr>
            <tr><th>Nom</th><td><?= htmlspecialchars($emp['last_name']) ?></td></tr>
            <tr><th>Date de naissance</th><td><?= htmlspecialchars($emp['birth_date']) ?></td></tr>
            <tr><th>Sexe</th><td><?= htmlspecialchars($emp['gender']) ?></td></tr>
            <tr><th>Date d'embauche</th><td><?= htmlspecialchars($emp['hire_date']) ?></td></tr>
            <tr><th>Poste</th><td><?= htmlspecialchars($emp['title'] ?? 'Non renseigné') ?></td></tr>
            <tr><th>Salaire</th><td><?= htmlspecialchars($emp['salary'] ?? 'Non renseigné') ?></td></tr>
        </table>
        <div class="text-center mb-4">
            <a href="javascript:history.back()" class="btn btn-secondary">Retour</a>
        </div>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>