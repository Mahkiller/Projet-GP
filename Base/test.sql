-- nom des manager de departement
SELECT employees.first_name, employees.last_name, departments.dept_no, dept_name
from
    departments
    JOIN dept_manager ON departments.dept_no = dept_manager.dept_no
    JOIN employees ON dept_manager.emp_no = employees.emp_no
where
    to_date = '9999-01-01';

-- creation de v_manager_departement:

CREATE or REPLACE VIEW v_manager_departement as
SELECT employees.first_name, employees.last_name, departments.dept_no, dept_name
from
    departments
    JOIN dept_manager ON departments.dept_no = dept_manager.dept_no
    JOIN employees ON dept_manager.emp_no = employees.emp_no
where
    to_date = '9999-01-01';

-- liste des employees dans un departement

-- employees
create or replace VIEW v_employees_dept as
SELECT e.emp_no, e.first_name, e.last_name, e.hire_date, de.dept_no
FROM employees e
    JOIN dept_emp de ON e.emp_no = de.emp_no
ORDER BY e.last_name, e.first_name;

-- version 3:
    -- 1- view departement avec nombre employees du departement
        --list des employeem
        SELECT e.emp_no, e.first_name, e.last_name, e.hire_date, de.dept_no
        FROM employees e
            JOIN dept_emp de ON e.emp_no = de.emp_no;

    -- 2. dpt nb _emp
    SELECT count(e.emp_no) as nb_emp, de.dept_no
    FROM
        employees e
        JOIN dept_emp dn ON e.emp_no = dn.emp_no
        JOIN departments de ON de.dept_no = dn.dept_no
    group by
        de.dept_no;

        -- creation du view
        Create or replace view v_nb_emp_dept AS
        SELECT count(e.emp_no) as nb_emp, de.dept_no
        FROM
            employees e
            JOIN dept_emp dn ON e.emp_no = dn.emp_no
            JOIN departments de ON de.dept_no = dn.dept_no
        group by
            de.dept_no;
    
    -- salaire moyenne;
    select s.emp_no, avg(s.salary) from salaries as s;

    -- salaire moyenne - dept - emp_no - title
    select s.emp_no, avg(s.salary), ti.title, dept_emp.dept_no
    FROM
        salaries s
        JOIN dept_emp ON dept_emp.emp_no = s.emp_no
        JOIN titles ti ON ti.emp_no = s.emp_no
    GROUP BY
        s.emp_no
    LIMIT 5;

        -- creation de view
        CREATE or REPLACE VIEW v_salaire_ttl_emp as
        select s.emp_no, avg(s.salary), ti.title, dept_emp.dept_no
        FROM
            salaries s
            JOIN dept_emp ON dept_emp.emp_no = s.emp_no
            JOIN titles ti ON ti.emp_no = s.emp_no
        GROUP BY
            s.emp_no;