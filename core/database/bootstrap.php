<?php

$app = [];

//Database config
$app['config'] = require('config.php');

//Database bootstrap
require('DBconnect.php');
require('QueryBuilder.php');

try{
  $app['database'] = new QueryBuilder(DBConnect::makeCon($app['config']['database']));
}
catch(Exception $e){
  echo $e;
}

?>
