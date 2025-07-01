<?php
require_once '../Page/Connection.php';
$conn = dbconnect();

$nom = $_GET['nom'] ?? '';
$dept_no = $_GET['dept_no'] ?? '';

$where = [];
if ($dept_no) {
    $dept_no = mysqli_real_escape_string($conn, $dept_no);
    $where[] = "de.dept_no = '$dept_no'";
}
if ($nom) {
    $nom = mysqli_real_escape_string($conn, $nom);
    $where[] = "(e.first_name LIKE '%$nom%' OR e.last_name LIKE '%$nom%')";
}
$sql = "SELECT e.emp_no, e.first_name, e.last_name
        FROM employees e
        JOIN dept_emp de ON e.emp_no = de.emp_no
        ".(count($where) ? "WHERE ".implode(' AND ', $where) : "")."
        ORDER BY e.last_name, e.first_name
        LIMIT 10";
$res = mysqli_query($conn, $sql);
$results = [];
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = $row;
}
header('Content-Type: application/json');
echo json_encode($results);
?>