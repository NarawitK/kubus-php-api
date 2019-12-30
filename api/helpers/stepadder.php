<?php
require_once '../helpers/calcdist.php';

function AddStepToArduinoPOST($arduino_json, $queryWaypoints, $queryBusLocationInDB){
    $next_step = null;
    $currentStep = null;
    $busDataFromArduino = $arduino_json;
    $waypoints = $queryWaypoints;
    $currentBusLocationInDB = $queryBusLocationInDB;
    echo "CheckDB";
    var_dump($currentBusLocationInDB);
    $currentStep = $currentBusLocationInDB->step;
    $next_step = AssignNextStep($currentStep, $waypoints);

    $checkResult = InitCheckStepOutOfOrder($waypoints,$currentStep,$next_step,$busDataFromArduino->latitude,$busDataFromArduino->longitude);
    if($checkResult == false){
      return FindNextStep($busDataFromArduino, $waypoints, $currentStep, $next_step);
    }
    else{
      return $checkResult;
    }
  }

  function InitCheckStepOutOfOrder($filteredWaypoint, $currentStep, $nextStep, $deviceLat, $deviceLng){
    $coordinate =  (object)array( "latitude" => $deviceLat, "longitude" => $deviceLng);
    $closestStation = CompareDistancesWithStep($coordinate, $filteredWaypoint, $currentStep);
    $closestStep = $closestStation->closest->step;
    echo "<br/>closestStep: {$closestStep}<br/>";
    if($closestStep !== $currentStep && $closestStep !== $nextStep){
      return $closestStep;
    }
    else{
      echo "Still in order <br/>";
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
      return true;
    }
    else{
      return false;
    }
  }
?>