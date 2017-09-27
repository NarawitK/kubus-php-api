<?php

class QueryBuilder{
  protected $pdo;

  public function __construct($pdo){
    $this->pdo = $pdo;
  }
  public function selectAllAsObj($table){
    $statement = $this->pdo->prepare("SELECT * FROM {$table}");
    $result = $statement->execute();
    return $statement->FetchAll(PDO::FETCH_OBJ);
  }
  public function selectLocRecentTS($table){
    $statement = $this->pdo->prepare("SELECT DISTINCT * FROM {$table} ORDER BY timestamp DESC");
    $result = $statement->execute();
    return $statement->FetchAll(PDO::FETCH_OBJ);
  }
  public function selectLocSpecific($table,$tramID){
    $statement = $this->pdo->prepare("SELECT DISTINCT * FROM {$table} WHERE tramID = {$tramID} ORDER BY timestamp DESC");
    $result = $statement->execute();
    return $statement->FetchAll(PDO::FETCH_OBJ);
  }
}
 ?>
