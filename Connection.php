<?php
function dbconnect()
{
    $connect = mysqli_connect('localhost', 'root', '', 'employees');
    mysqli_set_charset($connect, 'utf8mb4');
    if (!$connect) {
        die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
    }
    return $connect;
}
?>