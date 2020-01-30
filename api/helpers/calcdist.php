<?php
function CompareDistances($start, $endPoint)
{
    $closest = null;
    $closest_distance = null;
    $temp_dist = null;
    $result = null;
    try {
        if (is_object($endPoint)) {
            $closest = $endPoint;
            $closest_distance = CalculateDistance($start->latitude, $start->longitude, $endPoint->latitude, $endPoint->longitude, "M");
        } else {
            $closest = $endPoint[0];
            $closest_distance = CalculateDistance($start->latitude, $start->longitude, $endPoint[0]->latitude, $endPoint[0]->longitude, "M");
            for ($i = 1; $i < count($endPoint); $i++) {
                if ((CalculateDistance($start->latitude, $start->longitude, $endPoint[$i]->latitude, $endPoint[$i]->longitude, "M") < $closest_distance)) {
                    $closest_distance = CalculateDistance($start->latitude, $start->longitude, $endPoint[$i]->latitude, $endPoint[$i]->longitude, "M");
                    $closest = $endPoint[$i];
                }
            }
        }
        return (object) array("closest" => $closest, "distance" => $closest_distance);
    } catch (Exception $e) {
        throw $e;
    }
}

function CompareDistancesWithStepFallback($start, $endPoint, $currentStep)
{
    $closest = null;
    $closest_distance = null;
    $temp_dist = null;
    $result = null;
    try {
        if (is_object($endPoint)) {
            //echo '<br/>obj block<br/>';
            $result = $endPoint;
            $closest_distance = CalculateDistance($start->latitude, $start->longitude, $endPoint->latitude, $endPoint->longitude, "M");
        } else {
            //echo '<br/>else block<br/>';
            $closest[0] = $endPoint[0];
            $closest_distance = CalculateDistance($start->latitude, $start->longitude, $endPoint[0]->latitude, $endPoint[0]->longitude, "M");
            for ($i = 1; $i < count($endPoint); $i++) {
                if($temp_dist = CalculateDistance($start->latitude, $start->longitude, $endPoint[$i]->latitude, $endPoint[$i]->longitude, "M")){
                    if($temp_dist < $closest_distance){
                        $closest_distance = $temp_dist;
                        $closest[0] = $endPoint[$i];
                    }
                    else if ($temp_dist === $closest_distance) {
                        $j = count($closest);
                        $closest[$j] = $endPoint[$i];
                    }
                }
            }
            //var_dump($closest);
            $res_count = count($closest);
            if($res_count >=2){
                for ($k = 0; $k < count($closest); $k++) {
                    if ($closest[$k]->step < $currentStep) {
                        continue;
                    }
                    else{
                        $result = $closest[$k];
                    }
                }
                if($result === null){
                    $result = $closest[0];
                }
            }
            else{
                //echo '<br/>Only one result:::'.$closest[0]->step;
                $result = $closest[0];
            }
        }
        return (object) array("closest" => $result, "distance" => $closest_distance);
    } 
    catch (Exception $e) {
        throw $e;
    }
}

function CompareDistancesWithDuplication($start, $endPoint, $currentStep, $nextStep)
{
    $closest = null;
    $closest_distance = null;
    $temp_dist = null;
    $result = null;
    try {
        $closest[0] = $endPoint[0];
        $closest_distance = CalculateDistance($start->latitude, $start->longitude, $endPoint[0]->latitude, $endPoint[0]->longitude, "M");
        for ($i = 1; $i < count($endPoint); $i++) {
            if(($endPoint[$i]->step >= $currentStep && $endPoint[$i]->step <= $nextStep) && $temp_dist = CalculateDistance($start->latitude, $start->longitude, $endPoint[$i]->latitude, $endPoint[$i]->longitude, "M")){
                if ($temp_dist < $closest_distance) {
                    $closest_distance = $temp_dist;
                    $closest[0] = $endPoint[$i];
                } else if ($temp_dist === $closest_distance) {
                    $j = count($closest);
                    $closest[$j] = $endPoint[$i];
                }
            }
        }
        /*echo 'raw_data';
        echo 'dist: '.$closest_distance;
        var_dump($closest); */
        for ($k = 0; $k < count($closest); $k++) {
            if ($closest[$k]->step >= $currentStep && $closest[$k]->step <= $nextStep) {
                $result = $closest[$k];
            }
        }
        /* echo '<br/>After Looping.<br/>';
        var_dump($result);*/
        if ($result !== null && $closest_distance <= 300) {
            //echo '<br/>Return in line.<br/>';
            return (object) array("closest" => $result, "distance" => $closest_distance);
        } 
        else {
            //echo '<br/>Find new.<br/>';
            $result = CompareDistancesWithStepFallback($start,$endPoint,$currentStep);
            //var_dump($result);
            return $result;
        }
    } catch (Exception $e) {
        throw $e;
    }
}

function CalculateDistance($lat1, $lon1, $lat2, $lon2, $unit = "K")
{
    if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
    } else {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else if ($unit == "M") {
            return ($miles * 1609.344);
        } else {
            return $miles;
        }
    }
}
