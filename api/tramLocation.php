<?php

include '../core/database/bootstrap.php';
$res = $app['database']->selectLocRecentTS("location");
var_dump($res);
$res = null;
echo "<br/>";
$res = $app['database']->selectLocSpecific("location",3);
var_dump($res);
echo $res;
?>
