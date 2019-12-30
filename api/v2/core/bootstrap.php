<?php
require "./core/DBConnector.php";
use Core\DatabaseConnector;

$dbConnection = (new DatabaseConnector())->getConnection();
?>