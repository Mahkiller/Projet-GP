<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <link rel="stylesheet" href="/Php/EmployeV2/Projet-GP/Style/bootstrap.min.css">
    <link rel="stylesheet" href="/Php/EmployeV2/Projet-GP/Style/Style.css">
</head>
<body>
    <?php include_once '../inc/navbar.php'; ?>
    <div class="container my-4">
        <h2 class="text-center my-4">Bienvenue dans notre société</h2>
        <div class="text-center mb-4">
            <a href="Departement.php" class="btn btn-primary btn-lg mx-2">Voir les Départements</a>
            <a href="recherche.php" class="btn btn-success btn-lg mx-2">Recherche Employé</a>
        </div>
    </div>
</body>
</html>