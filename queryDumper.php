<?php
namespace QueryDumper;

function dump($result, $metadata, $forbiddenFields, $mysqli_link=false){
    $insertValues=queryParse($result, $metadata, $forbiddenFields);
    
    $dump=createDump($insertValues);
    return $dump;
}

function queryParse($result, $metadata, $forbiddenFields, $mysqli_link=false){
 
    $tableAliases=array();
    foreach ($result as $row=>$values){
        foreach ($metadata as $fc=>$meta){
            $db=$meta->db;
            $table=$meta->orgtable;
            $alias=$meta->table;
            if (array_key_exists($meta->orgname,$forbiddenFields)){
                $helperName='\QueryDumperHelpers\\'.$forbiddenFields[$meta->orgname];
                $values[$fc]=$helperName($values[$fc]);
            }
            if ($mysqli_link){
                $insertValues[$db][$alias][$table][$row][$fc]='"'.$mysqli->real_escape_string($values[$fc]).'"'; 
            }else{
                $insertValues[$db][$alias][$table][$row][$fc]='"'.addslashes($values[$fc]).'"'; 
            }    
        }
      
    }
    return $insertValues;
}

function createDump($insertValues){
    
    
    foreach ($insertValues as $db=>$aliases){
        foreach ($aliases as $alias=>$tables){
            foreach ($tables as $table=>$rows){
                
                $inserts=array();
                foreach ($rows as $row){
                    $inserts[]="(".implode(",", $row).")";  
                }
                $query="INSERT IGNORE INTO `{$db}`.`{$table}` VALUES\n".implode(",\n",$inserts).";";
                
                $queries[]=$query;

            }
        }
    }
    return $queries;
}


namespace QueryDumperHelpers;
/*
 * field conversions functions
 * 
 * 
 */
function replacePassword($input){
    return md5("password");
}
function replaceLogin($input){
    return preg_replace("/@.*/", "@example.com", $input);
} 

function sanitizeComment($input){
    //$generator = new LoremIpsumGenerator;
    //return $generator->getContent(100, 'plain');
    return "test comment\n\n ...ok\n\n";
}

namespace QueryDumperDatabaseAndTables;
//uses mysqli
//todo: use callback functions


function dump($mysqli, $metadata, $dropDatabase=false, $dropTable=false){
    
    $dump=DatabasesAndTables($mysqli, $metadata);
    $createDatabases=array();
    $createTables=array();
    
    $dbExists=" IF NOT EXISTS ";
    $dropDb="";
    
    foreach ($dump['databases'] as $db=>$empty){

        if ($dropDatabase){
            $dropDb="DROP DATABASE `{$db}`;\n";
            $dbExists="";
        }
        $createDatabases[$db]="{$dropDb}CREATE DATABASE {$dbExists} `{$db}`;";
    }
    foreach ($dump['tables'] as $db=>$table){
        foreach($table as $tableName=>$tableCreationCode){
            $createTables[$tableName]=$tableCreationCode;
        }    
    }
    
    $dump=array(
        'databases'=>$createDatabases,
        'tables'=>$createTables
    );

    return $dump;
    
}    
/*
 * database structure dump functions
 * 
 */
 
function DatabasesAndTables($mysqli, $metadata){
    
    $databases=array();
    $tables=array();
    foreach ($metadata as $fc=>$meta){
        $db=$meta->db;
        $table=$meta->orgtable;
        if (!array_key_exists($db, $databases)){
            $databases[$db]='';
        }
        $key=$db.'.'.$table;
        if (!array_key_exists($key, $tables)){
            $result=$mysqli->query("SHOW CREATE TABLE `{$db}`.`{$table}`;");
            
            $row=$result->fetch_array(MYSQLI_NUM);
            var_dump($row);
            $tableName=array_keys($row);

            $tables[$db][$table]=$row[1];
            
        }
        
    }
    return array('databases'=>$databases, 'tables'=>$tables);
}



?>
