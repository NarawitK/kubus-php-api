<?php

include '../core/database/bootstrap.php';

//Take GET
$dbTable = "location";
if(!isset($_GET["id"])){
  return 0;
}
else{
  $getCarID = $_GET["id"];
}

/*$res = $app['database']->sendLocAll($dbTable);
echo "ECHO JSON: ".$res."<br/>";

$res = null;

$res = $app['database']->sendLocByID("location",3);
echo $res."<br/>";

$res = null;*/

$res = $app['database']->sendLocByID("location",$getCarID);
header('Content-type:application/json');
$json_str = json_encode($res);
echo $json_str;

?>
