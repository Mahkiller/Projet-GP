<?php
  include('../Page/Connection.php');  
?>
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
?>