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
      $statement = $statement->FetchAll(PDO::FETCH_ASSOC);
    }
    catch(Exception $e){
      return $e->getMessage();
    }
    return $statement;
  }
  //Define new Database jobs here
  //Below is use for KUBUS Query.

  //Bus Section
  public function GetAllBus(){
    $querystring = "SELECT * FROM ".self::BUS_TABLE_NAME;
    $result = $this->Query($querystring);
    return $result; //Query PASS
  }

  public function GetBusInRoute($route_id){
    $querystring = "SELECT * FROM ".self::BUS_TABLE_NAME." WHERE Route_id = {$route_id} ";
    $result = $this->Query($querystring);
    return $result; //Query PASS
  }

  //Bus Location
  public function GetAllRecentBusLocation(){
    $querystring = "SELECT DISTINCT Bus_id,lat,lon,MAX(timestamp) as timestamp FROM ".self::BUSLOCATION_TABLE_NAME." GROUP BY Bus_id";
    $result = $this->Query($querystring);
    return $result; //Query PASS
  }

  public function GetRecentBusLocationInRoute($route_id){
    $querystring = "SELECT Bus_id,lat,lon,MAX(timestamp) as timestamp FROM ".self::BUSLOCATION_TABLE_NAME." as bl 
                    INNER JOIN ".self::BUS_TABLE_NAME." as b on bl.bus_id = b.id_bus
                    INNER JOIN ".self::ROUTE_TABLE_NAME." as r on b.Route_id = r.id_route
                    WHERE r.id_route = {$route_id} GROUP BY bl.Bus_id";
    $result = $this->Query($querystring);
    return $result; //Query Pass
  }

  public function GetSpecificBusLocation($bus_id){
    $querystring = "SELECT DISTINCT Bus_id,lat,lon,MAX(timestamp) as timestamp FROM ".self::BUSLOCATION_TABLE_NAME." WHERE Bus_id = {$bus_id}";
    $result = $this->Query($querystring);
    return $result; //Query PASS
  }

  //Station
  public function GetAllStation(){
    $querystring = "SELECT * FROM ".self::STATION_TABLE_NAME;
    $result = $this->Query($querystring);
    return $result; //Query Pass (Mod Route Later)
  }

  public function GetStationInRoute($route_id){
    $querystring = "SELECT wp.Station_id,wp.Route_id,r.name as RouteName,r.desc,s.name as StationName,s.lat,s.lon FROM ".self::WAYPOINT_TABLE_NAME." as wp
                    INNER JOIN ".self::ROUTE_TABLE_NAME." as r ON wp.Route_id = r.id_route
                    INNER JOIN ".self::STATION_TABLE_NAME." as s ON wp.Station_id = s.id
                    WHERE Route_id = {$route_id}";
    $result = $this->Query($querystring);
    return $result; //Query Pass
  }

  //Route
  public function GetAllRouteInfo(){
    $querystring = "SELECT * FROM ".self::ROUTE_TABLE_NAME;
    $result = $this->Query($querystring);
    return $result; //Query Pass
  }

  public function GetRouteColor($id_route){
    $querystring = "SELECT color FROM ".self::ROUTE_TABLE_NAME." WHERE id_route = {$id_route}";
    $result = $this->Query($querystring);
    return $result; //Query Pass
  }

  //Waypoint
  public function GetWaypointInRoute($route_id){
  $querystring = "SELECT step,Route_id,Station_id,r.name,r.desc,s.name,s.lat,s.lon FROM ".self::WAYPOINT_TABLE_NAME." as wp
                  INNER JOIN ".self::ROUTE_TABLE_NAME." as r ON wp.Route_id = r.id_route
                  INNER JOIN ".self::STATION_TABLE_NAME." as s ON wp.Station_id = s.id
                  WHERE Route_id = {$route_id}";
    $result = $this->Query($querystring);
    return $result; //QP
  }
}
 ?>
