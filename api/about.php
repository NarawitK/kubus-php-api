<?php

include '../core/database/bootstrap.php';
$res = $app['database']->selectAllAsObj("tram");
json_encode($res);
var_dump($res);
?>
