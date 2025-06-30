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
    // nom des colonnes dans view_employees_dept;
    $colones=mysqli_fetch_fields(getView_emp_dept());

    foreach($colones as $c){
        if($c-> name !="emp_no"){
            $sql=sprintf("SELECT*FROM v_employees_dept where %s='%s' and dept_no='%s'",
            $c-> name,
            $search,
            $dept_no);
            $request=mysqli_query(dbconnect(), $sql);
            if($request){
                return $request;
            }
        }
        else if($c-> name=="emp_no"){
            $sql=sprintf("SELECT*FROM v_employees_dept where %s='%d' and dept_no='%s'",
            $c-> name,
            $search,
            $dept_no);
            $request=mysqli_query(dbconnect(), $sql);
            if($request){
                return $request;
            }
        }
    }
    
 }
?>