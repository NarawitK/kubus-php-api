<?php

class QueryBuilder{
  protected $pdo;
  //Table Name
  private const BUS_TABLE_NAME = "bus";
  private const BUSLOCATION_TABLE_NAME = "bus_location";
  private const ROUTE_TABLE_NAME = "route";
  private const STATION_TABLE_NAME = "station";
  private const WAYPOINT_TABLE_NAME = "waypoint";

  //Constructor
  public function __construct($pdo){
    $this->pdo = $pdo;
  }

  private function Query($querystring){
    try{
      $statement = $this->pdo->prepare($querystring);
      $result = $statement->execute();
      $statement = $statement->fetchAll(PDO::FETCH_OBJ);
    }
    catch(Exception $e){
      return 0;
    }
    return $statement;
  }
  //Define new Database jobs here
  //Below is use for KUBUS Query.

  //Bus Section
  //Mode 1
  public function GetAllBus(){
    $querystring = "SELECT * FROM ".self::BUS_TABLE_NAME;
    $result = $this->Query($querystring);
    return $result; //Query PASS
  }

  //Mode 2
  public function GetBusInRoute($route_id){
    $querystring = "SELECT * FROM ".self::BUS_TABLE_NAME." WHERE route_id = {$route_id} ";
    $result = $this->Query($querystring);
    return $result; //Query PASS
  }

  //Bus Location
  //Mode 3
  public function GetAllRecentBusLocation(){
    //$querystring = "SELECT DISTINCT Bus_id,lat,lon,MAX(timestamp) as timestamp, color FROM ".self::BUSLOCATION_TABLE_NAME." GROUP BY Bus_id";
    $querystring = "SELECT bus_id, step, b.plate, r.id as route_id, r.name as route_name, r.description, latitude, longitude, speed, MAX(timestamp) as timestamp, color FROM ".self::BUSLOCATION_TABLE_NAME." as bl 
                    INNER JOIN ".self::BUS_TABLE_NAME." as b on bl.bus_id = b.id
                    INNER JOIN ".self::ROUTE_TABLE_NAME." as r on b.route_id = r.id
                    GROUP BY bl.bus_id";
    $result = $this->Query($querystring);
    return $result; //Query PASS
  }
  //Mode 4
  public function GetRecentBusLocationInRoute($route_id){
    $querystring = "SELECT bus_id, step, b.plate, r.name as route_name, r.description, latitude, longitude, speed, MAX(timestamp) as timestamp, color FROM ".self::BUSLOCATION_TABLE_NAME." as bl 
                    INNER JOIN ".self::BUS_TABLE_NAME." as b on bl.bus_id = b.id
                    INNER JOIN ".self::ROUTE_TABLE_NAME." as r on b.route_id = r.id
                    WHERE r.id = {$route_id} GROUP BY bl.bus_id";
    $result = $this->Query($querystring);
    return $result; //Query Pass
  }
  //Mode 5
  public function GetSpecificBusLocation($bus_id){
    $querystring = "SELECT DISTINCT bus_id,latitude,longitude,MAX(timestamp) as timestamp FROM ".self::BUSLOCATION_TABLE_NAME." WHERE bus_id = {$bus_id}";
    $result = $this->Query($querystring);
    return $result; //Query PASS
  }

  //Station
  //Mode 6
  public function GetAllStation(){
    $querystring = "SELECT * FROM ".self::STATION_TABLE_NAME;
    $result = $this->Query($querystring);
    return $result; //Query Pass (Mod Route Later)
  }

  //Mode 7
  public function GetStationInRoute($route_id){ //Mode
    $querystring = "SELECT wp.station_id, wp.step, wp.route_id, r.name as route_name, r.description as route_description, s.name as station_name, s.latitude, s.longitude FROM ".self::WAYPOINT_TABLE_NAME." as wp
                    INNER JOIN ".self::ROUTE_TABLE_NAME." as r ON wp.route_id = r.id
                    INNER JOIN ".self::STATION_TABLE_NAME." as s ON wp.station_id = s.id
                    WHERE route_id = {$route_id} ORDER BY wp.step";
    $result = $this->Query($querystring);
    return $result; //Query Pass
  }

  //Route
  //Mode 8
  public function GetAllRouteInfo(){
    $querystring = "SELECT * FROM ".self::ROUTE_TABLE_NAME;
    $result = $this->Query($querystring);
    return $result; //Query Pass
  }

  //Mode 9
  public function GetRouteColor($route_id){
    $querystring = "SELECT color FROM ".self::ROUTE_TABLE_NAME." WHERE id = {$route_id}";
    $result = $this->Query($querystring);
    return $result; //Query Pass. Maybe not needed.
  }

  //Waypoint
  //Mode 10
  public function GetWaypointInRoute($route_id){
  $querystring = "SELECT step,station_id,route_id,r.name as route_name,r.description as route_description,s.name as station_name,s.latitude,s.longitude FROM ".self::WAYPOINT_TABLE_NAME." as wp
                  INNER JOIN ".self::ROUTE_TABLE_NAME." as r ON wp.route_id = r.id
                  INNER JOIN ".self::STATION_TABLE_NAME." as s ON wp.station_id = s.id
                  WHERE route_id = {$route_id} ORDER BY step";
    $result = $this->Query($querystring);
    return $result; //PASS
  }
  //Mode 11
  public function GetWaypointAll(){
    $querystring = "SELECT step,station_id,route_id,r.name as route_name,r.description as route_description,s.name as station_name,s.latitude,s.longitude FROM ".self::WAYPOINT_TABLE_NAME." as wp
                    INNER JOIN ".self::ROUTE_TABLE_NAME." as r ON wp.route_id = r.id
                    INNER JOIN ".self::STATION_TABLE_NAME." as s ON wp.station_id = s.id
                    ORDER BY route_id";
      $result = $this->Query($querystring);
      return $result; //PASS
    }
}
 ?>
