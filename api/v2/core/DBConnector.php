<?php
namespace Core;

class DatabaseConnector {

    private $dbConnection = null;

    public function __construct($config)
    {
        $host = $config['connection'];
        $port = $config["port"];
        $db   = $config["databaseName"];
        $user = $config["username"];
        $pass = $config["password"];
        $options = $config['options'];

        try {
            $this->dbConnection = new \PDO(
                "mysql:host=$host;port=$port;charset=utf8;dbname=$db",
                $user,
                $pass,
                $options
                
            );
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->dbConnection;
    }
}