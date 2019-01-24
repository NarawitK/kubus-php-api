<?php
include '../core/database/bootstrap.php';

//Take GET
/*if(!isset($_GET["id"])){
  return 0;
}
else{
  $getCarID = $_GET["id"];
}*/

$res = $app['database']->GetStationInRoute(1);
header('Content-type:application/json');
$json_str = json_encode($res);
echo $json_str;
?>
