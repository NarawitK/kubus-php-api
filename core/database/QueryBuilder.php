<?php

class QueryBuilder{
  protected $pdo;

  //Constructor
  public function __construct($pdo){
    $this->pdo = $pdo;
  }

  private function Query($querystring){
    try{
      $statement = $this->pdo->prepare($querystring);
      $result = $statement->execute();
      $statement = $statement->FetchAll(PDO::FETCH_ASSOC);
    }
    catch(Exception $e){
      return $e->getMessage();
    }
    return $statement;
  }
  
  //Prototype Methods (Unusable)
  public function selectAllAsObj($table){
    $statement = $this->pdo->prepare("SELECT * FROM {$table}");
    $result = $statement->execute();
    $statement = $statement->FetchAll(PDO::FETCH_ASSOC);
    return $statement;
  }
  public function sendLocAll($table){
    $querystring = "SELECT DISTINCT * FROM {$table} ORDER BY timestamp DESC";
    $result = Query($querystring);
    return $result;
  }
  public function sendLocByID($table,$tramID){
    $statement = $this->pdo->prepare("SELECT DISTINCT * FROM {$table} WHERE tramID = {$tramID} ORDER BY timestamp DESC LIMIT 3");
    $result = $statement->execute();
    $statement = $statement->FetchAll(PDO::FETCH_ASSOC);
    return $statement;
  }
  
  //Define new Database jobs here
  //Below is use for KUBUS Query.

  //Bus Section
  public function GetAllBus($table){
    $querystring = "SELECT * FROM {$table}";
    $result = $this->Query($querystring);
    return $result; //Query PASS
  }

  public function GetBusInRoute($table,$route_id){
    $querystring = "SELECT * FROM {$table} WHERE Route_id = {$route_id} ";
    $result = $this->Query($querystring);
    return $result; //Query PASS
  }

  //Bus Location
  public function GetAllRecentBusLocation($table){
    $querystring = "SELECT Bus_id,lat,lon,MAX(timestamp) as timestamp FROM {$table} GROUP BY Bus_id";
    $result = $this->Query($querystring);
    return $result; //Query PASS
  }

  public function GetRecentBusLocationInRoute($table,$route_id){
    $querystring = "";
    $result = $this->Query($querystring);
    return $result; //Need JOIN with bus table
  }

  public function GetSpecificBusLocation($table,$bus_id){
    $querystring = "SELECT DISTINCT Bus_id,lat,lon,MAX(timestamp) as timestamp FROM {$table} WHERE Bus_id = {$bus_id}";
    $result = $this->Query($querystring);
    return $result; //Query PASS
  }

  //Station
  public function GetAllStation($table){
    $querystring = "SELECT * FROM {$table}";
    $result = $this->Query($querystring);
    return $result; //NOT PASS
  }

  public function GetStationInRoute($table, $route_id){
    $querystring = "SELECT * FROM {$table} ORDER BY timestamp DESC";
    $result = $this->Query($querystring);
    return $result; //Need JOIN with waypoint table.
  }

  //Route
  public function GetAllRouteInfo($table){
    $querystring = "";
    $result = $this->Query($querystring);
    return $result; //Undefined
  }

  public function GetRouteColor($table,$route_id){
    $querystring = "SELECT color FROM {$table} WHERE id_route = {$route_id}";
    $result = $this->Query($querystring);
    return $result; //Undefined
  }

  //Waypoint
  public function GetAllWaypointInRoute($table,$route_id){
    $querystring = "SELECT DISTINCT * FROM {$table} ORDER BY timestamp DESC";
    $result = $this->Query($querystring);
    return $result; //Undefined
  }
}
 ?>
