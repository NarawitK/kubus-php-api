<?php
include '../core/bootstrap.php';
$test_query_result = $app['database']->selectAllAsObj("TestTable");

require('views/index.view.php');
 ?>
