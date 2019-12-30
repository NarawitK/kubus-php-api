<?php

$post_data->postmode = "p1";
$post_data->bus_id = 98;
$post_data->latitude = 14.00667;
$post_data->longitude = 99.97719;
$post_data->speed = 200.01;
$post_data->course = 5822;
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