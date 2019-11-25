<?php
require_once "calcdist.php";

class QueryBuilder{
  protected $pdo;
  //Table Name
  /*
  PHP 7.1+ we can specify the visibility of class constants.
  private const BUS_TABLE_NAME = "bus";
  private const BUSLOCATION_TABLE_NAME = "bus_location";
  private const ROUTE_TABLE_NAME = "route";
  private const STATION_TABLE_NAME = "station";
  private const WAYPOINT_TABLE_NAME = "waypoint";
  */
  const BUS_TABLE_NAME = "bus";
  const BUSLOCATION_TABLE_NAME = "bus_location";
  const ROUTE_TABLE_NAME = "route";
  const STATION_TABLE_NAME = "station";
  const WAYPOINT_TABLE_NAME = "waypoint";

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
    catch(PDOException $e){
      return $e;
    }
    return $statement;
  }

  private function NonFetchQuery($querystring){
    try{
      $statement = $this->pdo->prepare($querystring);
      $result = $statement->execute();
    }
    catch(PDOException $e){
      return $e;
    }
    return $result;
  }

  private function SingleQuery($querystring){
    try{
      $statement = $this->pdo->prepare($querystring);
      $result = $statement->execute();
      $statement = $statement->fetch(PDO::FETCH_OBJ);
    }
    catch(PDOException $e){
      return $e;
    }
    return $statement;
  }

  private function GetRowCount($querystring){
    try{
      $statement = $this->pdo->prepare($querystring);
      $statement->execute();
      $rowCount = $statement->rowCount();
    }
    catch(Exception $e){
      return $e;
    }
    return $rowCount;
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
    $querystring = "SELECT bus_id, step, b.plate, r.id as route_id, r.name as route_name, r.description, is_active, latitude, longitude, speed, timestamp, color FROM ".self::BUSLOCATION_TABLE_NAME." bl
                      INNER JOIN ".self::BUS_TABLE_NAME." b on bl.bus_id = b.id
                    INNER JOIN ".self::ROUTE_TABLE_NAME." r on b.route_id = r.id
                    WHERE status = 1 AND timestamp = (SELECT MAX(timestamp) FROM ".self::BUSLOCATION_TABLE_NAME." bl2 WHERE bl.bus_id = bl2.bus_id)
                    GROUP BY bus_id,step,b.plate,is_active,latitude,longitude,speed,timestamp";
    $result = $this->Query($querystring);
    return $result; //Query PASS
  }

  protected function routeIterator($collection){
    $routes = "";
    for($i=0; $i < count($collection); $i++){
      if($i === count($collection)-1){
        $routes .= (string)$collection[$i];
      }
      else{
        $routes .= ",".(string)$collection[$i];
      }
    }
    return $routes;
  }
  //Mode 4
  public function GetRecentBusLocationInRoute($route_id){
    $routes = null;
    if(is_array($route_id)){
      $IteratedRoute = self::routeIterator($route_id);
      $routes = $IteratedRoute;
    }
    else{
      $routes = $route_id;
    }
    $querystring = "SELECT bus_id, step, b.plate, r.id as route_id, r.name as route_name, r.description, is_active,latitude, longitude, speed, MAX(timestamp) as timestamp, color FROM ".self::BUSLOCATION_TABLE_NAME." bl 
                    INNER JOIN ".self::BUS_TABLE_NAME."  b on bl.bus_id = b.id
                    INNER JOIN ".self::ROUTE_TABLE_NAME." r on b.route_id = r.id
                    WHERE r.id IN ({$routes}) AND status = 1 AND timestamp = (SELECT MAX(timestamp) FROM ".self::BUSLOCATION_TABLE_NAME." bl2 WHERE bl.bus_id = bl2.bus_id)
                    GROUP BY bus_id,step,b.plate,is_active,latitude,longitude,speed,timestamp
                    ";
    $result = $this->Query($querystring);
    return $result; //Query Pass
  }
  //Mode 5
  public function GetSpecificBusLocation($bus_id){
    $querystring = "SELECT DISTINCT bus_id,latitude,longitude,MAX(timestamp) as timestamp FROM ".self::BUSLOCATION_TABLE_NAME." 
                    WHERE bus_id = {$bus_id}
                    GROUP BY bus_id,latitude,longitude LIMIT 1";
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
  public function GetStationInRoute($route_id){
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
  public function GetSomeRouteInfoByID($route_id){
    $querystring = "SELECT id, name, description FROM ".self::ROUTE_TABLE_NAME." WHERE id = {$route_id}";
    $result = $this->SingleQuery($querystring);
    return $result; //Query Pass.
  }
  
  //Waypoint
  //Mode 10
  public function GetWaypointInRoute($route_id){
    $querystring = "SELECT step,station_id,wp.route_id,r.name as route_name,r.description as route_description,s.name as station_name,s.latitude,s.longitude FROM ".self::WAYPOINT_TABLE_NAME." as wp
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
    ORDER BY route_id, step";
    $result = $this->Query($querystring);
    return $result; //PASS
  }
  
  //Mode 12
  public function GetRouteInStation($station_id){
      $querystring = "select distinct r.id as routeID, r.name as routeName, r.description as routeDescription from ".self::WAYPOINT_TABLE_NAME." as wp
      inner join ".self::ROUTE_TABLE_NAME." as r on wp.route_id = r.id
      inner join ".self::STATION_TABLE_NAME." as s on wp.station_id = s.id
      where s.id = {$station_id}
      order by r.id";
      $result = $this->Query($querystring);
      return $result; //
    }
    
    //Mode 13
    public function GetRouteAndStationDataForQRCode($station_id){
      $querystring = "select distinct r.id as routeID, r.name as routeName, r.description as routeDescription, s.id as stationID, s.name as stationName, s.latitude, s.longitude 
      from ".self::WAYPOINT_TABLE_NAME." as wp
      inner join ".self::ROUTE_TABLE_NAME." as r on wp.route_id = r.id
      inner join ".self::STATION_TABLE_NAME." as s on wp.station_id = s.id
      where s.id = {$station_id}
      order by r.id";
      $result = $this->Query($querystring);
      return $result;
    }
    
    //POST: Insert/Update Car Function
    public function UpdateBusData($data){
      $isBusExist = $this->CheckBusExist($data->bus_id);
      echo("isBusExist".$isBusExist);
      if($isBusExist){
        $isBusLocationExist = $this->CheckBusLocationExist($data->bus_id);
        echo("isBusLocationExist".$isBusLocationExist);
        if($isBusLocationExist){
          echo('Go Update');
          $result = $this->UpdateBusDataQuery($data); 
        }
        else{
          echo('Go Insert');
          $result = $this->InsertBusDataQuery($data);
        }
        return $result;
      }
      else{
        throw new Error("Bus not exist in system.");
      }
    }
    
    private function InsertBusDataQuery($data){
      $querystring = "INSERT INTO ".self::BUSLOCATION_TABLE_NAME." (bus_id,latitude,longitude,speed,course,is_active)
      VALUES ({$data->bus_id},{$data->latitude},{$data->longitude},{$data->speed},{$data->course},1)";
      $result = $this->NonFetchQuery($querystring);
      return $result;
    }
    private function UpdateBusDataQuery($data){
      $querystring = "UPDATE ".self::BUSLOCATION_TABLE_NAME." 
                      SET latitude = {$data->latitude}, longitude = {$data->longitude}, is_active = 1, speed = {$data->speed}, course ={$data->course}
                      WHERE bus_id = {$data->bus_id}";
      $result = $this->NonFetchQuery($querystring);
      return $result;
      
    }
    //Mode FnTest
    private function CheckBusExist($bus_id){
      $querystring = "SELECT id FROM ".self::BUS_TABLE_NAME." 
      WHERE id = {$bus_id} LIMIT 1";
      $result = $this->GetRowCount($querystring);
      if($result){
        return true;
      }
      else{
        return false;
      }
    }
  
    private function CheckBusLocationExist($bus_id){
      $querystring = "SELECT bus_id FROM ".self::BUSLOCATION_TABLE_NAME."
                      WHERE bus_id = {$bus_id} LIMIT 1";
      $result = $this->GetRowCount($querystring);
      if($result){
        return true;
      }
      else{
        return false;
      }
    }
  }
  
  ?>
