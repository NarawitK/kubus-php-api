<?php
return [
  'database' => [
    'databaseName'=>"kubus",
    'username'=>"root",
    'password'=>"",
    'port'=>"3306",
    'connection'=>"localhost",
    'options'=>[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",PDO::ATTR_EMULATE_PREPARES => false]
  ]
];
?>
