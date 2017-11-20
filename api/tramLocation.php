<?php

include '../core/database/bootstrap.php';

//Take GET
if(!isset($_GET["id"])){
  return 0;
}
else{
  $getCarID = $_GET["id"];
}

$table = 'location';

$res = $app['database']->sendLocByID($table,$getCarID);
header('Content-type:application/json');
$json_str = json_encode($res);
echo $json_str;

?>
