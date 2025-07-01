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

if (!empty($_GET['nom']) || !empty($_GET['dept_no']) || !empty($_GET['age_min']) || !empty($_GET['age_max'])) {
    $where = [];
    if (!empty($_GET['dept_no'])) {
        $dept_no = mysqli_real_escape_string($conn, $_GET['dept_no']);
        $where[] = "de.dept_no = '$dept_no'";
    }
    if (!empty($_GET['nom'])) {
        $nom = mysqli_real_escape_string($conn, $_GET['nom']);
        $where[] = "(e.first_name LIKE '%$nom%' OR e.last_name LIKE '%$nom%')";
    }
    if (!empty($_GET['age_min'])) {
        $age_min = (int)$_GET['age_min'];
        $where[] = "TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) >= $age_min";
    }
    if (!empty($_GET['age_max'])) {
        $age_max = (int)$_GET['age_max'];
        $where[] = "TIMESTAMPDIFF(YEAR, e.birth_date, CURDATE()) <= $age_max";
    }

    $limit = 20;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($page - 1) * $limit;

    $sqlCount = "SELECT COUNT(*) as total
        FROM employees e
        JOIN dept_emp de ON e.emp_no = de.emp_no
        JOIN departments d ON de.dept_no = d.dept_no
        ".(count($where) ? "WHERE ".implode(' AND ', $where) : "");
    $resCount = mysqli_query($conn, $sqlCount);
    $totalRows = mysqli_fetch_assoc($resCount)['total'];
    $hasNext = $offset + $limit < $totalRows;

    $sqlEmp = "SELECT e.emp_no, e.first_name, e.last_name, e.hire_date, d.dept_name
        FROM employees e
        JOIN dept_emp de ON e.emp_no = de.emp_no
        JOIN departments d ON de.dept_no = d.dept_no
        ".(count($where) ? "WHERE ".implode(' AND ', $where) : "")."
        ORDER BY e.last_name, e.first_name
        LIMIT $offset, $limit";
    $resEmp = mysqli_query($conn, $sqlEmp);
    ?>
    <div class="container">
        <h4 class="my-3">Résultats de la recherche</h4>
        <table class="table table-hover table-bordered align-middle shadow">
            <thead class="table-primary">
                <tr>
                    <th>Numéro</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Département</th>
                    <th>Date d'embauche</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($resEmp)): ?>
                    <tr class="zoom-hover">
                        <td><a href="Fiche.php?emp_no=<?= urlencode($row['emp_no']) ?>"><?= htmlspecialchars($row['emp_no']) ?></a></td>
                        <td><?= htmlspecialchars($row['first_name']) ?></td>
                        <td><?= htmlspecialchars($row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['dept_name']) ?></td>
                        <td><?= htmlspecialchars($row['hire_date']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="d-flex justify-content-between my-3">
            <?php
            $params = $_GET;
            if ($page > 1) {
                $params['page'] = $page - 1;
                echo '<a class="btn btn-outline-primary" href="?' . http_build_query($params) . '">Précédent</a>';
            } else {
                echo '<span></span>';
            }
            if ($hasNext) {
                $params['page'] = $page + 1;
                echo '<a class="btn btn-outline-primary" href="?' . http_build_query($params) . '">Suivant</a>';
            }
            ?>
        </div>
    </div>
    <?php
    return;
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
    <div class="container my-4">
        <form class="row g-3 align-items-end" id="searchForm" method="get" action="index.php" autocomplete="off">
            <div class="col-md-4">
                <label for="dept_no" class="form-label">Département</label>
                <select class="form-select" id="dept_no" name="dept_no">
                    <option value="">Tous</option>
                    <?php
                    $deptRes = mysqli_query($conn, "SELECT dept_no, dept_name FROM departments");
                    while ($d = mysqli_fetch_assoc($deptRes)) {
                        $selected = (isset($_GET['dept_no']) && $_GET['dept_no'] == $d['dept_no']) ? 'selected' : '';
                        echo "<option value=\"{$d['dept_no']}\" $selected>".htmlspecialchars($d['dept_name'])."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-4 position-relative">
                <label for="nom" class="form-label">Nom employé</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($_GET['nom'] ?? '') ?>" autocomplete="off">
                <div id="suggestions" class="list-group position-absolute w-100" style="z-index:10;"></div>
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
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </div>
        </form>
    </div>
    <script>
    document.getElementById('nom').addEventListener('input', function() {
        let nom = this.value;
        let dept = document.getElementById('dept_no').value;
        if (nom.length < 2) {
            document.getElementById('suggestions').innerHTML = '';
            return;
        }
        fetch('../traitements/tbarDrecherche.php?nom=' + encodeURIComponent(nom) + '&dept_no=' + encodeURIComponent(dept))
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
