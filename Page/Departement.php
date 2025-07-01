<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../traitements/traitement_departement.php';

$departments = getDepartments();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Départements</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Style/Style.css">
    <style>
        .zoom-btn:hover {
            transform: scale(1.08);
            transition: transform 0.2s;
            z-index: 2;
        }
    </style>
</head>
<body>
    <?php include_once '../inc/navbar.php'; ?>
    <h2 class="text-center my-4">Liste des Départements</h2>
    <div class="container" style="max-width:900px;">
        <table class="table table-hover table-bordered align-middle shadow">
            <thead class="table-primary">
                <tr>
                    <th>Nom du département</th>
                    <th>Manager en cours</th>
                    <th style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($departments as $dept): ?>
                    <tr>
                        <td><?= htmlspecialchars($dept['dept_name']) ?></td>
                        <td><?= htmlspecialchars($dept['manager_name'] ?? 'Aucun') ?></td>
                        <td style="text-align:center;">
                            <a href="employe.php?dept_no=<?= urlencode($dept['dept_no']) ?>" class="btn btn-primary zoom-btn" style="min-width:120px;">
                                Voir la liste
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>