<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../traitements/traitement_Fiche.php';

if (!isset($_GET['emp_no'])) {
    die('Aucun employé sélectionné.');
}
$emp = getEmployeFiche($_GET['emp_no']);
if (!$emp) {
    die('Employé introuvable.');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fiche de l'employé</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/Php/EmployeV2/Projet-GP/Style/bootstrap.min.css">
    <link rel="stylesheet" href="/Php/EmployeV2/Projet-GP/Style/Style.css">
</head>
<body>
    <?php include_once '../inc/navbar.php'; ?>
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