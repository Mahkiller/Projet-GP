<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../traitements/traitement_stats_titre.php';
$stats = getStatsTitre();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques par emploi</title>
    <link rel="stylesheet" href="/Php/EmployeV2/Projet-GP/Style/bootstrap.min.css">
    <link rel="stylesheet" href="/Php/EmployeV2/Projet-GP/Style/Style.css">
</head>
<body>
    <?php include_once '../inc/navbar.php'; ?>
    <div class="container my-4">
        <h2 class="text-center mb-4">Statistiques par emploi</h2>
        <table class="table table-bordered table-hover align-middle shadow">
            <thead class="table-primary">
                <tr>
                    <th>Emploi</th>
                    <th>Nombre d'hommes</th>
                    <th>Nombre de femmes</th>
                    <th>Salaire moyen (€)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($stats as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['nb_hommes']) ?></td>
                        <td><?= htmlspecialchars($row['nb_femmes']) ?></td>
                        <td><?= htmlspecialchars($row['salaire_moyen']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>