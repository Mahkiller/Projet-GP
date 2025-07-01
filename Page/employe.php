<?php 
include('../inc/fonction.php');
?>

<?php
require_once 'Connection.php';

$conn = dbconnect();

if (!isset($_GET['dept_no'])) {
    die('Aucun département sélectionné.');
}
$dept_no = mysqli_real_escape_string($conn, $_GET['dept_no']);

$limit = 20;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$sqlCount = "SELECT COUNT(*) as total FROM employees e JOIN dept_emp de ON e.emp_no = de.emp_no WHERE de.dept_no = '$dept_no'";
$resCount = mysqli_query($conn, $sqlCount);
$totalRows = mysqli_fetch_assoc($resCount)['total'];
$hasNext = $offset + $limit < $totalRows;

$sql = "SELECT e.emp_no, e.first_name, e.last_name, e.hire_date
FROM employees e
JOIN dept_emp de ON e.emp_no = de.emp_no
WHERE de.dept_no = '$dept_no'
ORDER BY e.last_name, e.first_name
LIMIT $offset, $limit";

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
    <link rel="stylesheet" href="../Style/Style.css">
</head>
<body>
    <h2 class="text-center my-4">
        Employés du département <?= htmlspecialchars($dept['dept_name'] ?? $dept_no) ?>
    </h2>

    <div class="container">
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <form action="employe.php" class="d-flex" role="search" method="get">
            <input class="form-control me-2" type="search" name="nom" placeholder="Search" aria-label="Search">
            <input type="hidden" value=<?php echo $dept_no; ?> name="dept_no">
            <button class="btn" type="submit">Search</button>
            </form>
        </div>
    </nav>
    </div>
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
         <?php if(!isset($_GET['nom'])) { ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="zoom-hover">
                       <td>
                            <a href="Fiche.php?emp_no=<?= urlencode($row['emp_no']) ?>">
                                <?= htmlspecialchars($row['emp_no']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($row['first_name']) ?></td>
                        <td><?= htmlspecialchars($row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['hire_date']) ?></td>
                    </tr>
                <?php endwhile; ?>
        <?php } ?>
        <?php if(isset($_GET['nom'])){ ?>
            <?php
                $search=$_GET['nom'];
                $request=verifieSearch2($dept_no,$search);
            ?>
             <?php while($row = mysqli_fetch_assoc($request)): ?>
                <tr class="zoom-hover">
                    <td>
                        <a href="Fiche.php?emp_no=<?= urlencode($row['emp_no']) ?>">
                            <?= htmlspecialchars($row['emp_no']) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($row['first_name']) ?></td>
                    <td><?= htmlspecialchars($row['last_name']) ?></td>
                    <td><?= htmlspecialchars($row['hire_date']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php } ?>
        <!---->
        
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
        <div class="text-center mb-4">
            <a href="index.php" class="btn btn-primary">Retour à la liste des départements</a>
        </div>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>