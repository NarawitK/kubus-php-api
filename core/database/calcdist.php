<?php
function CompareDistances($busData, $stationData){
  $closest = $stationData[0];
  $closest_distance;
  $temp;
  try{
    $closest_distance = CalculateDistance($busData->latitude,$busData->longitude,$stationData[0]->latitude,$stationData[0]->longitude);
    for ($i = 1; $i < count($stationData); $i++) {
      if ((CalculateDistance($busData->latitude,$busData->longitude,$stationData[$i]->latitude,$stationData[$i]->longitude) < $closest_distance)) {
        $closest_distance = CalculateDistance($busData->latitude,$busData->longitude,$stationData[$i]->latitude,$stationData[$i]->longitude);
        $closest = $stationData[$i];
      }
    }
    return array("closest"=>$closest, "distance"=> $closest_distance);
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