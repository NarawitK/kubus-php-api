<?php
include_once "./core/database/calcdist.php";
require './core/database/bootstrap.php';

$post_data->postmode = "p1";
$post_data->bus_id = 3;
$post_data->latitude = 14.028336;
$post_data->longitude = 99.983968;
$post_data->speed = 60.01;
$post_data->course = 0.00;
$encode_postdata = json_encode($post_data);

$data = json_decode($encode_postdata);
$stations = $app['database']->GetWaypointAll();
$compare_result = CompareDistances($data,$stations);

$context = stream_context_create(array(
    'http' => array(
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => $encode_postdata
    )
));

// Send the request
$response = file_get_contents('http://localhost/kubus/api/bus_post.php', FALSE, $context);

if($response === FALSE){
    die('Error');
}
//$responseData = json_decode($response, TRUE);
echo($response);
?>