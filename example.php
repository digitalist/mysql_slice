<?php
include ('queryDumper.php');
include ('../local_db_login.php');

/* uses  mysqli::fetch_all(MYSQLI_NUM) 
 * array(
 * 0=>array(field1, field2), 
 * ... 
 * n=>array(field1,field2)
 * )
 * 
 * we need mysqli metadata
 * mysqli->fetch_fields() //  array(db, table, origtable)
 * there are some hacks for getting meta with mysql_ extension on php.net
 * but mysqli works great
 * */

//self joins test
$exampleQuery="select * from employees.employees e1 left join employees.employees e2 on 1=1 limit 1";
$exampleQuery="select *  from employees.employees e1 left join employees.salaries s on s.emp_no=e1.emp_no  group by last_name limit 5;";
//$exampleQuery="select * from information_schema.columns c1 left join information_schema.columns c2 on 1=1 limit 1";



$exampleMysqli = new mysqli($host, $user, $password, $database);
$exampleResult=$exampleMysqli->query($exampleQuery);

//example
//if no mysqlnd (native driver installed)
if (!method_exists('mysqli_result', 'fetch_all') ){
    $exampleData=fetchAll($exampleResult);
}else{
    $exampleData=$exampleResult->fetch_all(MYSQLI_NUM);
}    
$exampleMeta=$exampleResult->fetch_fields();
   



/*
 * field content removal options
 * column name => function name in queryDumper.php, namespace QueryDumperHelpers
 * 
 * */
 
$forbiddenFields=array(
'password'=>'replacePassword', //change password -> md5("password")
'login'=>'replaceLogin', //change login vasya@mail.ru -> vasya@example.com
'comment'=>'sanitizeComment' //lorem ipsum or 
);




//get tables dump
$dump=(\queryDumper\dump($exampleData, $exampleMeta, $forbiddenFields));



$dropDatabase=true; //default false
$dropTable=true; //default false

$dbAndTablesCreationDump=\QueryDumperDatabaseAndTables\dump($exampleMysqli,$exampleMeta, $dropDatabase, $dropTable);

$databases=$dbAndTablesCreationDump['databases'];
$tables=$dbAndTablesCreationDump['tables'];

echo implode("\n", $databases)."\n";
echo implode("\n", $tables).";\n";
echo "\n";
echo implode("\n", $dump);
echo "\n";


//mysqlnd not installed
function fetchAll($result)
{
    $rows = array();
    while($row = $result->fetch_array(MYSQLI_NUM))
    {
        $rows[] = $row;
    }
    return $rows;
}


?>
