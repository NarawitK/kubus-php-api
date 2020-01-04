<?php
class DBConnect
{
  public static function makeCon($config){
    try {
      return new PDO($config['connection'].';dbname='.$config['databaseName'],
      $config['username'],
      $config['password'],
      $config['options']
    );
  }
    catch (PDOException $e) {
      die($e->getMessage());
    }
  }
}

 ?>
