<?php
require './core/database/bootstrap.php';
require '../helpers/stepadder.php';

  $json = file_get_contents('php://input'); 
  $dataset = json_decode($json);
  
   try{
      $waypoints = $app['database']->_FindWaypoint($dataset->bus_id);
      $currentBusLocationInDB = $app['database']->GetSpecificBusLocation($dataset->bus_id);
      $dataset->step = AddStepToArduinoPOST($dataset,$waypoints,$currentBusLocationInDB);
      /* Temporary Disable for debugging. */
      $result = $app['database']->UpdateBusData($dataset);
      if($result){
        header("HTTP/1.1 200 OK");
      } 

   }
   catch(Exception $e){
     echo($e);
   }

   /*
      $postmode = $dataset->postmode;
         switch($postmode){
      case "p1":
        $waypoints = $app['database']->_FindWaypoint($dataset->bus_id);
        $currentBusLocationInDB = $app['database']->GetSpecificBusLocation($dataset->bus_id);
        $dataset->step = AddStepToArduinoPOST($dataset,$waypoints,$currentBusLocationInDB);
        $result = $app['database']->UpdateBusData($dataset);
        echo $result;
      break;
      case "auto":
        $waypoints = $app['database']->_FindWaypoint($dataset->bus_id);
        $currentBusLocationInDB = $app['database']->GetSpecificBusLocation($dataset->bus_id);
        $dataset->step = AddStepToArduinoPOST($dataset,$waypoints,$currentBusLocationInDB);
        $result = $app['database']->UpdateBusData($dataset);
      break;
       default:
        throw new error("Update Error");
      break;
    */
?>
