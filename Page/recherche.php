<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../traitements/traitement_recherche.php';
$departments = getDepartmentsForSearch();
$last_searches = $_SESSION['last_searches'] ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche Employé</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Style/Style.css">
    <style>
        #suggestions { position: absolute; z-index: 10; width: 100%; }
    </style>
</head>
<body>
    <?php include_once '../inc/navbar.php'; ?>
    <div class="container my-4" style="max-width:700px;">
        <h2 class="text-center mb-4">Recherche Employé</h2>
        <form class="row g-3 align-items-end position-relative" id="searchForm" method="get" action="recherche.php" autocomplete="off">
            <div class="col-md-4">
                <label for="dept_no" class="form-label">Département</label>
                <select class="form-select" id="dept_no" name="dept_no">
                    <option value="">Tous</option>
                    <?php foreach($departments as $d): ?>
                        <option value="<?= htmlspecialchars($d['dept_no']) ?>" <?= (($_GET['dept_no'] ?? '') == $d['dept_no']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['dept_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 position-relative">
                <label for="nom" class="form-label">Nom employé</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($_GET['nom'] ?? '') ?>" autocomplete="off">
                <div id="suggestions" class="list-group"></div>
            </div>
            <div class="col-md-2">
                <label for="age_min" class="form-label">Âge min</label>
                <input type="number" class="form-control" id="age_min" name="age_min" min="0" value="<?= htmlspecialchars($_GET['age_min'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label for="age_max" class="form-label">Âge max</label>
                <input type="number" class="form-control" id="age_max" name="age_max" min="0" value="<?= htmlspecialchars($_GET['age_max'] ?? '') ?>">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success">Rechercher</button>
            </div>
        </form>

        <?php if (!empty($last_searches)): ?>
            <div class="mt-4">
                <h5>Dernières recherches :</h5>
                <ul>
                    <?php foreach(array_reverse($last_searches) as $search): ?>
                        <li>
                            <a href="Fiche.php?emp_no=<?= urlencode($search['emp_no']) ?>">
                                <?= htmlspecialchars($search['first_name'].' '.$search['last_name']) ?>
                            </a>
                            (<?= htmlspecialchars($search['dept_name']) ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET) && count($results) > 0): ?>
            <div class="mt-4">
                <h5>Résultats :</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Numéro</th>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Département</th>
                            <th>Date d'embauche</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($results as $emp): ?>
                            <tr>
                                <td><a href="Fiche.php?emp_no=<?= urlencode($emp['emp_no']) ?>"><?= htmlspecialchars($emp['emp_no']) ?></a></td>
                                <td><?= htmlspecialchars($emp['first_name']) ?></td>
                                <td><?= htmlspecialchars($emp['last_name']) ?></td>
                                <td><?= htmlspecialchars($emp['dept_name']) ?></td>
                                <td><?= htmlspecialchars($emp['hire_date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif (!empty($_GET)): ?>
            <div class="mt-4 alert alert-warning">Aucun résultat trouvé.</div>
        <?php endif; ?>
    </div>
    <script>
    document.getElementById('nom').addEventListener('input', function() {
        let nom = this.value;
        let dept = document.getElementById('dept_no').value;
        if (nom.length < 2) {
            document.getElementById('suggestions').innerHTML = '';
            return;
        }
        fetch('../traitements/traitement_recherche.php?autocomplete=1&nom=' + encodeURIComponent(nom) + '&dept_no=' + encodeURIComponent(dept))
            .then(r => r.json())
            .then(data => {
                let sug = '';
                data.forEach(emp => {
                    sug += `<a href="Fiche.php?emp_no=${emp.emp_no}" class="list-group-item list-group-item-action">${emp.first_name} ${emp.last_name}</a>`;
                });
                document.getElementById('suggestions').innerHTML = sug;
            });
    });
    document.getElementById('nom').addEventListener('blur', function() {
        setTimeout(() => { document.getElementById('suggestions').innerHTML = ''; }, 200);
    });
    </script>
</body>
</html>

