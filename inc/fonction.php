<!--plus besoin d'include dbconnect(), tu appelle si la fonction en a besoin-->


<?php

 function verifieSearch($dept_no,$search){
    $sql=sprintf("SELECT*FROM v_employees_dept where first_name='%s' and dept_no='%s'",
    $search,
    $dept_no);
    $request=mysqli_query(dbconnect(), $sql);
    return $request;
 }

 function getView_emp_dept(){
    $sql=sprintf("SELECT*FROM v_employees_dept ");
    $request=mysqli_query(dbconnect(), $sql);
    return $request;
 }

 function verifieSearch2($dept_no,$search){
    $conn = dbconnect(); 
    $colonnes = mysqli_fetch_fields(getView_emp_dept());

    foreach ($colonnes as $c) {
        $column = $c->name;

        // Vérifie si la colonne actuelle est celle qu'on cherche à filtrer
        if ($column != 'emp_no' && is_string($search)) {
            $sql = "SELECT * FROM v_employees_dept WHERE $column = ? AND dept_no = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ss', $search, $dept_no); 

        } elseif ($column == 'emp_no' && is_numeric($search)) {
            $sql = "SELECT * FROM v_employees_dept WHERE $column = ? AND dept_no = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'is', $search, $dept_no);

        } else {
            continue;
        }

        // Exécute et retourne le résultat
        if (mysqli_stmt_execute($stmt)) { // ??
            $result = mysqli_stmt_get_result($stmt);
            if ($result && mysqli_num_rows($result) > 0) {
                return $result; // Retourne les résultats
            }
        }

        mysqli_stmt_close($stmt); // Ferme le statement à chaque itération
    }

    return null; // Aucun résultat
 }

 // fonctions a ajouter ici:
 // de preference met tous tes fonctions ici pas besoin de les faire dans chaque traitements que tu auras besoin
 // connection.php
 function dbconnect()
{
    $connect = mysqli_connect('localhost', 'root', '', 'employees');
    mysqli_set_charset($connect, 'utf8mb4');
    if (!$connect) {
        die('Erreur de connexion à la base de données : ' . mysqli_connect_error());
    }
    return $connect;
}

 // traitement_departement.php
 // -> getDeptManager()
 // traitement_index.php - getDepartments
 // si tu veux un mysqli_result|bool et un tableau
 
 // fonction recupere donnees de mysql
function getDeptManager() {
    $conn = dbconnect();
    $sql = " SELECT dept_no, dept_name, CONCAT(first_name,' ',last_name) as manager_name 
            FROM v_manager_departement";
    return mysqli_query($conn, $sql);
}

// tu donnes un tab sql -> getDeptManager ou autre -> donne un tableau (array)
 function getRequestTab($request) {
    $conn=dbconnect();
    if (!$request) {
        die('Erreur SQL : ' . mysqli_error($conn));
    }
    while ($row = mysqli_fetch_assoc($request)) {
        $tab[] = $row;
    }
   // je ne sais pas comment utiliser mysqli_close($conn);
    return $tab;
}

// traitement_fiche.php
function getEmployeFiche($emp_no) {
    $conn = dbconnect();
    $emp_no = mysqli_real_escape_string($conn, $emp_no);
    // pas besoin de limit -> emp_no=id employee (valeur unique)
    $sql = "SELECT * FROM v_fiche_employe WHERE emp_no = '$emp_no'";
    $result = mysqli_query($conn, $sql);
    if (!$result || mysqli_num_rows($result) == 0) {
        return null;
    }
    return mysqli_fetch_assoc($result);
}

// traitement_index.php 
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

// traitement_recherche.php
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

// traitement_employe.php
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