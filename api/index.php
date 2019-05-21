<?php
require '../core/database/bootstrap.php';
//require '../core/JSONFunc.php';

//Check GET Request
///Param Retrieve section
try{
  if(!isset($_GET["param"]) ){
    $param = null;
  }
  else{
    $param = $_GET["param"];
  }

///Check querymode request in $_GET[]
  if(!isset($_GET["mode"])){
    return 0;
  }
  else{
    $queryMode = $_GET["mode"];
  }
}//end try
catch(Exception $e){
  return 0;
}//end catch

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
      $res = $app['database']->GetRouteColor($param);
      break;
      case 10:
      $res = $app['database']->GetWaypointInRoute($param);
      break;
      default:
      $res = 0;
      break;
  } 
  //header('Content-type:application/json; charset=utf-8');
  $res = json_encode($res);
  echo $res;
}
catch(Exception $e){
  return 0;
}
?>
