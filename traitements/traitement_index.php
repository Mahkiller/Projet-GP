<?php
require_once '../Page/Connection.php';

function getDepartments() {
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
    return mysqli_query($conn, $sql);
}

function searchEmployes($where, $offset, $limit) {
    $conn = dbconnect();
    $sql = "SELECT e.emp_no, e.first_name, e.last_name, e.hire_date, d.dept_name
        FROM employees e
        JOIN dept_emp de ON e.emp_no = de.emp_no
        JOIN departments d ON de.dept_no = d.dept_no
        ".(count($where) ? "WHERE ".implode(' AND ', $where) : "")."
        ORDER BY e.last_name, e.first_name
        LIMIT $offset, $limit";
    return mysqli_query($conn, $sql);
}

function countSearchEmployes($where) {
    $conn = dbconnect();
    $sql = "SELECT COUNT(*) as total
        FROM employees e
        JOIN dept_emp de ON e.emp_no = de.emp_no
        JOIN departments d ON de.dept_no = d.dept_no
        ".(count($where) ? "WHERE ".implode(' AND ', $where) : "");
    $res = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($res)['total'];
}
?>