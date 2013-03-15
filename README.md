mysql_slice
=

Create selective slice of database and tables from mysql

- Select subsets of data to transfer from production to testing.

- Filter data that goes into develeopment/testing database by your own criteria,
so there are no real passwords or customer logins in dev/test environment

- Can be useful for quick data joins between servers: get data from different
servers and manipulate them on local machine


-
**i.e. you have this:**
```sql
select * from employees.employees e1 left join employees.employees e2 on 1=1 limit 1;
```

**you got this:**

```sql
DROP DATABASE `employees`;

CREATE DATABASE  `employees`;

CREATE TABLE `employees` (
  `emp_no` int(11) NOT NULL,
  `birth_date` date NOT NULL,
  `first_name` varchar(14) NOT NULL,
  `last_name` varchar(16) NOT NULL,
  `gender` enum('M','F') NOT NULL,
  `hire_date` date NOT NULL,
  PRIMARY KEY (`emp_no`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT IGNORE INTO `employees`.`employees` VALUES
("10001","1953-09-02","Georgi","Facello","M","1986-06-26");

INSERT IGNORE INTO `employees`.`employees` VALUES
("10001","1953-09-02","Georgi","Facello","M","1986-06-26");
```
-

see example.php for filtering sensitive data examples
