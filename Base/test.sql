-- nom des manager de departement
SELECT employees.first_name, employees.last_name, departments.dept_no, dept_name
from
    departments
    JOIN dept_manager ON departments.dept_no = dept_manager.dept_no
    JOIN employees ON dept_manager.emp_no = employees.emp_no
where
    to_date = '9999-01-01';

-- creation de v_manager_departement:

CREATE or REPLACE VIEW v_manager_departement AS
SELECT d.dept_no, d.dept_name, e.first_name, e.last_name
FROM departments d
JOIN dept_manager dm ON d.dept_no = dm.dept_no
JOIN employees e ON dm.emp_no = e.emp_no
WHERE dm.to_date = '9999-01-01';

-- liste des employees dans un departement

-- employees
CREATE OR REPLACE VIEW v_employees_dept AS
SELECT e.emp_no, e.first_name, e.last_name, e.hire_date, de.dept_no, d.dept_name, e.birth_date
FROM employees e
JOIN dept_emp de ON e.emp_no = de.emp_no
JOIN departments d ON de.dept_no = d.dept_no
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
        CREATE OR REPLACE VIEW v_nb_emp_dept AS
SELECT COUNT(e.emp_no) AS nb_emp, de.dept_no
FROM employees e
JOIN dept_emp de ON e.emp_no = de.emp_no
GROUP BY de.dept_no;
    
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
        CREATE OR REPLACE VIEW v_salaire_ttl_emp AS
SELECT s.emp_no, AVG(s.salary) AS avg_salary, t.title, de.dept_no
FROM salaries s
JOIN dept_emp de ON de.emp_no = s.emp_no
JOIN titles t ON t.emp_no = s.emp_no
GROUP BY s.emp_no, t.title, de.dept_no;

CREATE OR REPLACE VIEW v_fiche_employe AS
SELECT e.emp_no, e.first_name, e.last_name, e.birth_date, e.gender, e.hire_date,
       t.title, s.salary
FROM employees e
LEFT JOIN (
    SELECT emp_no, title
    FROM titles
    WHERE to_date = (SELECT MAX(to_date) FROM titles WHERE emp_no = titles.emp_no)
) t ON e.emp_no = t.emp_no
LEFT JOIN (
    SELECT emp_no, salary
    FROM salaries
    WHERE to_date = (SELECT MAX(to_date) FROM salaries WHERE emp_no = salaries.emp_no)
) s ON e.emp_no = s.emp_no;


-- Liste des managers de département
-- SELECT * FROM v_manager_departement;

-- -> utiliser le view ici
-- SELECT dept_no, dept_name, CONCAT(first_name,' ',last_name) as manager_name from v_manager_departement;

-- Liste des employés d'un département
-- SELECT * FROM v_employees_dept WHERE dept_no = 'd001';

-- Nombre d'employés par département
-- SELECT * FROM v_nb_emp_dept;

-- Salaire moyen, titre et département
-- SELECT * FROM v_salaire_ttl_emp LIMIT 5;

-- Fiche d'un employé
-- SELECT * FROM v_fiche_employe WHERE emp_no = 10001;