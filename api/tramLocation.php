<?php

include '../core/database/bootstrap.php';
$res = $app['database']->sendLocAll("location");
var_dump($res);
$res = null;
echo "<br/>";
$res = $app['database']->sendLocByID("location",3);
var_dump($res);
echo $res;
?>
