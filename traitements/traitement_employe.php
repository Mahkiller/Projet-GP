<?php
require_once '../Page/Connection.php';

function getEmployesByDept($dept_no, $offset = 0, $limit = 20, $conn = null) {
    if (!$conn) $conn = dbconnect();
    $dept_no = mysqli_real_escape_string($conn, $dept_no);
    $sql = "SELECT * FROM v_employees_dept WHERE dept_no = '$dept_no' ORDER BY last_name, first_name LIMIT $offset, $limit";
    return mysqli_query($conn, $sql);
}

function countEmployesByDept($dept_no, $conn = null) {
    if (!$conn) $conn = dbconnect();
    $dept_no = mysqli_real_escape_string($conn, $dept_no);
    $sql = "SELECT nb_emp FROM v_nb_emp_dept WHERE dept_no = '$dept_no'";
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($res);
    return $row ? $row['nb_emp'] : 0;
}

function getDeptName($dept_no, $conn = null) {
    if (!$conn) $conn = dbconnect();
    $dept_no = mysqli_real_escape_string($conn, $dept_no);
    $sql = "SELECT dept_name FROM departments WHERE dept_no = '$dept_no'";
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($res);
    return $row['dept_name'] ?? $dept_no;
}
?>