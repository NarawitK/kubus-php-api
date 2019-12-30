<?php
require "./core/database/bootstrap.php";
require "../helpers/stepadder.php";

  $json = file_get_contents('php://input');
  $utf_json = utf8_encode($json); 
  $dataset = json_decode($utf_json);
  $postmode = $dataset->postmode;
   try{
     switch($postmode){
       case "p1":
        $waypoints = $app['database']->_FindWaypoint($dataset->bus_id);
        $currentBusLocationInDB = $app['database']->GetSpecificBusLocation($dataset->bus_id);
        $dataset->step = AddStepToArduinoPOST($dataset,$waypoints,$currentBusLocationInDB);
        $result = $app['database']->UpdateBusData($dataset);
        echo $result;
        break;
       default:
        throw new error("Update Error");
        break;
     }
   }
   catch(Exception $e){
     echo($e);
   }
?>
