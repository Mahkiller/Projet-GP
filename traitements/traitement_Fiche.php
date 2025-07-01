<?php
require_once '../Page/Connection.php';

function getEmployeFiche($emp_no) {
    $conn = dbconnect();
    $emp_no = mysqli_real_escape_string($conn, $emp_no);

    $sql = "SELECT * FROM v_fiche_employe WHERE emp_no = '$emp_no' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if (!$result || mysqli_num_rows($result) == 0) {
        return null;
    }
    return mysqli_fetch_assoc($result);
}
?>