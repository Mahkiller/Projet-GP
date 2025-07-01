<?php
require_once '../Page/Connection.php';

if (isset($_GET['autocomplete'])) {
    $conn = dbconnect();
    $nom = $_GET['nom'] ?? '';
    $dept_no = $_GET['dept_no'] ?? '';
    $where = [];
    if ($dept_no) {
        $dept_no = mysqli_real_escape_string($conn, $dept_no);
        $where[] = "dept_no = '$dept_no'";
    }
    if ($nom) {
        $nom = mysqli_real_escape_string($conn, $nom);
        $where[] = "(first_name LIKE '%$nom%' OR last_name LIKE '%$nom%')";
    }
    $sql = "SELECT emp_no, first_name, last_name FROM v_employees_dept "
         . (count($where) ? "WHERE ".implode(' AND ', $where) : "")
         . " ORDER BY last_name, first_name LIMIT 10";
    $res = mysqli_query($conn, $sql);
    $results = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $results[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($results);
    exit;
}
function getDepartmentsForSearch() {
    $conn = dbconnect();
    $departments = [];
    $sql = "SELECT dept_no, dept_name FROM departments ORDER BY dept_name";
    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        $departments[] = $row;
    }
    return $departments;
}

$results = [];
if (!empty($_GET['nom']) || !empty($_GET['dept_no']) || !empty($_GET['age_min']) || !empty($_GET['age_max'])) {
    $conn = dbconnect();
    $where = [];
    if (!empty($_GET['dept_no'])) {
        $dept_no = mysqli_real_escape_string($conn, $_GET['dept_no']);
        $where[] = "dept_no = '$dept_no'";
    }
    if (!empty($_GET['nom'])) {
        $nom = mysqli_real_escape_string($conn, $_GET['nom']);
        $where[] = "(first_name LIKE '%$nom%' OR last_name LIKE '%$nom%')";
    }
    if (!empty($_GET['age_min'])) {
        $age_min = (int)$_GET['age_min'];
        $where[] = "TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= $age_min";
    }
    if (!empty($_GET['age_max'])) {
        $age_max = (int)$_GET['age_max'];
        $where[] = "TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) <= $age_max";
    }
    $sql = "SELECT * FROM v_employees_dept "
         . (count($where) ? "WHERE ".implode(' AND ', $where) : "")
         . " ORDER BY last_name, first_name LIMIT 20";
    $res = mysqli_query($conn, $sql);
    $results = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $results[] = $row;
    }
    if (count($results) === 1) {
        $_SESSION['last_searches'] = $_SESSION['last_searches'] ?? [];
        array_push($_SESSION['last_searches'], $results[0]);
        $_SESSION['last_searches'] = array_slice($_SESSION['last_searches'], -5, 5, true);
        header('Location: ../Page/Fiche.php?emp_no=' . urlencode($results[0]['emp_no']));
        exit;
    }
}