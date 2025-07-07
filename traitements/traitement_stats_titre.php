<?php
require_once '../Page/Connection.php';

function getStatsTitre() {
    $conn = dbconnect();
    $sql = "SELECT * FROM v_stats_titre ORDER BY title";
    $res = mysqli_query($conn, $sql);
    $stats = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $stats[] = $row;
    }
    return $stats;
}
?>