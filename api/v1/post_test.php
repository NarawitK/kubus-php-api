<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//$post_data->postmode = "p1";
$post_data->bus_id = 999;
$post_data->latitude = 0;
$post_data->longitude = 0;
$post_data->speed = 0.1456;
$post_data->course = 84.39;
$encode_postdata = json_encode($post_data);

$data = json_decode($encode_postdata);

$context = stream_context_create(array(
    'http' => array(
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => $encode_postdata
    )
));

// Send the request
$response = file_get_contents('http://localhost/kubus/api/v1/bus_post.php', FALSE, $context);

if($response === FALSE){
    die('Error');
}
//$responseData = json_decode($response, TRUE);
echo($response);
?>