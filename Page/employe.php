<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../traitements/traitement_employe.php';
$conn = dbconnect();

if (!isset($_GET['dept_no'])) {
    die('Aucun département sélectionné.');
}
$dept_no = $_GET['dept_no'];

$limit = 20;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$totalRows = countEmployesByDept($dept_no, $conn);
$totalPages = ceil($totalRows / $limit);
$hasNext = $offset + $limit < $totalRows;

$result = getEmployesByDept($dept_no, $offset, $limit, $conn);
$dept_name = getDeptName($dept_no, $conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employés du département</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/Php/EmployeV2/Projet-GP/Style/bootstrap.min.css">
    <link rel="stylesheet" href="/Php/EmployeV2/Projet-GP/Style/Style.css">
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

    <h2 class="text-center my-4">
        Employés du département <?= htmlspecialchars($dept_name ?? $dept_no) ?>
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
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
         <?php if(!isset($_GET['nom'])) { ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['emp_no']) ?></td>
                        <td><?= htmlspecialchars($row['first_name']) ?></td>
                        <td><?= htmlspecialchars($row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['hire_date']) ?></td>
                        <td>
                            <a href="Fiche.php?emp_no=<?= urlencode($row['emp_no']) ?>" class="btn btn-success zoom-btn">
                                Voir la fiche
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
        <?php } ?>
        <?php if(isset($_GET['nom'])){ ?>
            <?php
                $search = mysqli_real_escape_string($conn, $_GET['nom']);
                $dept_no_sql = mysqli_real_escape_string($conn, $dept_no);
                $sql = "SELECT * FROM v_employees_dept WHERE dept_no = '$dept_no_sql' AND (first_name LIKE '%$search%' OR last_name LIKE '%$search%')";
                $request = mysqli_query($conn, $sql);
            ?>
             <?php while($row = mysqli_fetch_assoc($request)): ?>
                <tr>
                    <td>
                        <a href="Fiche.php?emp_no=<?= urlencode($row['emp_no']) ?>">
                            <?= htmlspecialchars($row['emp_no']) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($row['first_name']) ?></td>
                    <td><?= htmlspecialchars($row['last_name']) ?></td>
                    <td><?= htmlspecialchars($row['hire_date']) ?></td>
                    <td>
                        <a href="Fiche.php?emp_no=<?= urlencode($row['emp_no']) ?>" class="btn btn-success zoom-btn">
                            Voir la fiche
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php } ?>
        <!---->
        
            </tbody>
        </table>
        <div class="d-flex justify-content-center my-3">
            <nav>
                <ul class="pagination">
                    <?php
                    $params = $_GET;
                    $params['page'] = max(1, $page - 1);
                    echo '<li class="page-item'.($page == 1 ? ' disabled' : '').'"><a class="page-link" href="?'.http_build_query($params).'">Précédent</a></li>';

                    if ($totalPages <= 5) {
                        for ($i = 1; $i <= $totalPages; $i++) {
                            $params['page'] = $i;
                            $active = ($i == $page) ? ' active' : '';
                            echo '<li class="page-item'.$active.'"><a class="page-link" href="?'.http_build_query($params).'">'.$i.'</a></li>';
                        }
                    } else {
                        for ($i = 1; $i <= 2; $i++) {
                            $params['page'] = $i;
                            $active = ($i == $page) ? ' active' : '';
                            echo '<li class="page-item'.$active.'"><a class="page-link" href="?'.http_build_query($params).'">'.$i.'</a></li>';
                        }
                        if ($page > 4) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        for ($i = max(3, $page - 1); $i <= min($totalPages - 2, $page + 1); $i++) {
                            if ($i > 2 && $i < $totalPages - 1) {
                                $params['page'] = $i;
                                $active = ($i == $page) ? ' active' : '';
                                echo '<li class="page-item'.$active.'"><a class="page-link" href="?'.http_build_query($params).'">'.$i.'</a></li>';
                            }
                        }
                        if ($page < $totalPages - 3) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        for ($i = $totalPages - 1; $i <= $totalPages; $i++) {
                            if ($i > 2) {
                                $params['page'] = $i;
                                $active = ($i == $page) ? ' active' : '';
                                echo '<li class="page-item'.$active.'"><a class="page-link" href="?'.http_build_query($params).'">'.$i.'</a></li>';
                            }
                        }
                    }

                    $params['page'] = min($totalPages, $page + 1);
                    echo '<li class="page-item'.($page == $totalPages ? ' disabled' : '').'"><a class="page-link" href="?'.http_build_query($params).'">Suivant</a></li>';
                    ?>
                </ul>
            </nav>
        </div>
        <div class="text-center mb-4">
            <a href="index.php" class="btn btn-primary">Retour à la liste des départements</a>
        </div>
    </div>
</body>
</html>
