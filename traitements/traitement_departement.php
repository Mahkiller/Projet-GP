<?php
require_once '../Page/Connection.php';

function getDepartments() {
    $conn = dbconnect();
    $departments = [];
    $sql = "SELECT d.dept_no, d.dept_name,
                   (SELECT CONCAT(m.first_name, ' ', m.last_name)
                    FROM v_manager_departement m
                    WHERE m.dept_no = d.dept_no
                    LIMIT 1) AS manager_name
            FROM departments d";
    $res = mysqli_query($conn, $sql);
    if (!$res) {
        die('Erreur SQL : ' . mysqli_error($conn));
    }
    while ($row = mysqli_fetch_assoc($res)) {
        $departments[] = $row;
    }
    mysqli_close($conn);
    return $departments;
}
?>