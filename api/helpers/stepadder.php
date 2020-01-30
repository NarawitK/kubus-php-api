<?php
require_once '../helpers/calcdist.php';

/*
* Most of comments in this section  = debugging.
* Summary: Need to test Double Step station. / [PASS] Nearby step / [PASS] Std. Case.
*/

function AddStepToArduinoPOST($arduino_json, $queryWaypoints, $queryBusLocationInDB){
    $next_step = null;
    $currentStep = null;
    $busDataFromArduino = $arduino_json;
    $waypoints = $queryWaypoints;
    $currentStep = $queryBusLocationInDB->step;
    $next_step = AssignNextStep($currentStep, $waypoints);
    /*
    echo 'Current: '.$currentStep;
    echo 'Next: '.$next_step;
    */
    $checkerResult = InitCheckStepOutOfOrder($waypoints,$currentStep,$next_step,$busDataFromArduino->latitude,$busDataFromArduino->longitude);
    if(!$checkerResult){
      return FindNextStep($busDataFromArduino, $waypoints, $currentStep, $next_step);
    }
    else{
      return $checkerResult;
    }
  }

  function InitCheckStepOutOfOrder($filteredWaypoint, $currentStep, $nextStep, $deviceLat, $deviceLng){
    $coordinate =  (object)array( "latitude" => $deviceLat, "longitude" => $deviceLng);
    $closestStation = CompareDistancesWithDuplication($coordinate, $filteredWaypoint, $currentStep, $nextStep);
    $closestStep = $closestStation->closest->step;
    if($closestStep !== $currentStep && $closestStep !== $nextStep){
      //echo 'OUT';
      return $closestStep;
    }
    else{
      //echo 'IN';
      return false;
    }
  }

  function FindNextStep($dataFromDevice, $filteredWaypoint, $currentStep, $nextStep){
    $next_ClosestStation = CompareDistances($dataFromDevice, $filteredWaypoint[$nextStep-1]);
    if(CheckNextStepProximity($next_ClosestStation)){
      return $nextStep;
    }
    else{
      return $currentStep;
    }
  } 

  //Assign comparable next step
  function AssignNextStep($currentStepInDB, $filteredWaypoints){
    $next_step = null;
    $maxWaypointsCount = count($filteredWaypoints);
    if($currentStepInDB == $filteredWaypoints[$maxWaypointsCount-1]->step){
      $next_step = $filteredWaypoints[0]->step;
    }
    else{
      $next_step = $filteredWaypoints[$currentStepInDB]->step;
    }
    return $next_step;
  }

  function CheckNextStepProximity($NextStepDistanceData){
    if($NextStepDistanceData->distance <= 50){
      //echo 'In Prox';
      return true;
    }
    else{
      //echo 'No Prox';
      return false;
    }
  }
?>