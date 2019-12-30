<?php
require './core/database/bootstrap.php';
//Check GET Request

try{
  if(!isset($_GET["param"]) ){
    $param = null;
  }
  else{
    $param = $_GET["param"];
  }
//Check querymode request in $_GET[]
  if(!isset($_GET["mode"])){
    throw new Error("Mode not assigned");
  }
  else{
    $queryMode = $_GET["mode"];
  }
}

catch(Exception $e){
  echo $e;
}

///Switch query result by $queryMode
try{
  switch($queryMode){
    case 1:
      $res = $app['database']->GetAllBus();
      break;
      case 2:
      $res = $app['database']->GetBusInRoute($param);
      break;
      case 3:
      $res = $app['database']->GetAllRecentBusLocation();
      break;
      case 4:
      $res = $app['database']->GetRecentBusLocationInRoute($param);
      break;
      case 5:
      $res = $app['database']->GetSpecificBusLocation($param);
      break;
      case 6:
      $res = $app['database']->GetAllStation();
      break;
      case 7:
      $res = $app['database']->GetStationInRoute($param);
      break;
      case 8:
      $res = $app['database']->GetAllRouteInfo();
      break;
      case 9:
      $res = $app['database']->GetSomeRouteInfoByID($param);
      break;
      case 10:
      $res = $app['database']->GetWaypointInRoute($param);
      break;
      case 11;
      $res = $app['database']->GetWaypointAll();
      break;
      case 12:
      $res = $app['database']->GetRouteInStation($param);
      break;
      case 13:
      $res = $app['database']->GetRouteAndStationDataForQRCode($param);
      break;
      default:
      throw new Error("Requestmode or parameter is invalid");
      break;
  } 
  header('Content-type:application/json; charset=utf-8');
  $res = json_encode($res);
  echo $res;
}
catch(Exception $e){
  echo $e;
}
?>
