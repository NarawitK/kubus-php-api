<?php
return [
  'database' => [
    'databaseName'=>"kubus",//'New Database' ,
    'username'=>"root",//'' ,
    'password'=>"",//'' ,
    'connection'=>"mysql:host=localhost",//'mysql:host=127.0.0.1' ,
    'options'=>[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",PDO::ATTR_EMULATE_PREPARES => false]
  ]
];
?>
