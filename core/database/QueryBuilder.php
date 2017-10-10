<?php

class QueryBuilder{
  protected $pdo;

  public function __construct($pdo){
    $this->pdo = $pdo;
  }
  public function selectAllAsObj($table){
    $statement = $this->pdo->prepare("SELECT * FROM {$table}");
    $result = $statement->execute();
    $statement = $statement->FetchAll(PDO::FETCH_ASSOC);
    return $statement;
  }
  public function sendLocAll($table){
    $statement = $this->pdo->prepare("SELECT DISTINCT * FROM {$table} ORDER BY timestamp DESC");
    $result = $statement->execute();
    $statement = $statement->FetchAll(PDO::FETCH_ASSOC);
    return $statement;

  }
  public function sendLocByID($table,$tramID){
    $statement = $this->pdo->prepare("SELECT DISTINCT * FROM {$table} WHERE tramID = {$tramID} ORDER BY timestamp DESC LIMIT 3");
    $result = $statement->execute();
    $statement = $statement->FetchAll(PDO::FETCH_ASSOC);
    return $statement;
  }
  //Define new Database jobs here
  /*public function (){

  }*/

}
 ?>
