<?php
function CompareDistances($start, $endPoint){
  $closest;
  $closest_distance;
  $temp;
  try{
    if(is_object($endPoint)){
      $closest = $endPoint;
      $closest_distance = CalculateDistance($start->latitude,$start->longitude,$endPoint->latitude,$endPoint->longitude,"M");
    }
    else{
      $closest = $endPoint[0];
      $closest_distance = CalculateDistance($start->latitude,$start->longitude,$endPoint[0]->latitude,$endPoint[0]->longitude,"M");
      for ($i = 1; $i < count($endPoint); $i++) {
        if ((CalculateDistance($start->latitude,$start->longitude,$endPoint[$i]->latitude,$endPoint[$i]->longitude,"M") < $closest_distance)) {
          $closest_distance = CalculateDistance($start->latitude,$start->longitude,$endPoint[$i]->latitude,$endPoint[$i]->longitude,"M");
          $closest = $endPoint[$i];
        }
    }
  }
    return (object)array("closest"=> $closest, "distance"=> $closest_distance);
  }
  catch(Exception $e){
    throw $e;
  } 
}
//Contain Duplicate
function CompareDistancesWithStep($start, $endPoint, $step){
  $closest;
  $closest_distance;
  $temp;
  try{
      $closest = $endPoint[0];
      $closest_distance = CalculateDistance($start->latitude,$start->longitude,$endPoint[0]->latitude,$endPoint[0]->longitude,"M");
      for ($i = 1; $i < count($endPoint); $i++) {
        if ((CalculateDistance($start->latitude,$start->longitude,$endPoint[$i]->latitude,$endPoint[$i]->longitude,"M") <= $closest_distance) && $endPoint[$i]->step >= $step) {
          $closest_distance = CalculateDistance($start->latitude,$start->longitude,$endPoint[$i]->latitude,$endPoint[$i]->longitude,"M");
          $closest = $endPoint[$i];
        }
  }
    return (object)array("closest"=> $closest, "distance"=> $closest_distance);
  }
  catch(Exception $e){
    throw $e;
  } 
}

function CalculateDistance($lat1, $lon1, $lat2, $lon2, $unit = "K") {
  if (($lat1 == $lat2) && ($lon1 == $lon2)) {
    return 0;
  }
  else {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
      return ($miles * 1.609344);
    } else if ($unit == "N") {
      return ($miles * 0.8684);
    }
    else if ($unit =="M"){
      return ($miles * 1609.344); 
    }
    else {
      return $miles;
    }
  }
}
?>