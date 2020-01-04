<?php
require "./core/DBConnector.php";
use Core\DatabaseConnector;

$config = require "./core/config.php";
$dbConnection = (new DatabaseConnector($config['database']))->getConnection();

?>