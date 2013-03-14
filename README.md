mysql_slice
=

create selective slice of database and tables from mysql select

-
**i.e. you have this:**

select * from employees.employees e1 left join employees.employees e2 on 1=1 limit 1;


**you got this:**


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
