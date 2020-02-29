<?php
namespace Core;

class QueryBuilder
{
    protected $pdo;
    /*
    PHP 7.1+ we can specify the visibility of class constants.
    private const BUS_TABLE_NAME = "bus";
    private const BUSLOCATION_TABLE_NAME = "bus_location";
    private const BUSINROUTE_TABLE_NAME = "bus_in_route";
    private const ROUTE_TABLE_NAME = "route";
    private const STATION_TABLE_NAME = "station";
    private const WAYPOINT_TABLE_NAME = "waypoint";
     */
    const BUS_TABLE_NAME = "bus";
    const BUSLOCATION_TABLE_NAME = "bus_location";
    const BUSINROUTE_TABLE_NAME = "bus_in_route";
    const ROUTE_TABLE_NAME = "route";
    const STATION_TABLE_NAME = "station";
    const WAYPOINT_TABLE_NAME = "waypoint";

    //Constructor
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    private function Query($statement)
    {
        try {
            $statement = $this->pdo->prepare($statement);
            $result = $statement->execute();
            $statement = $statement->fetchAll(\PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            return $e;
        }
        return $statement;
    }

    private function NonFetchQuery($statement)
    {
        try {
            $statement = $this->pdo->prepare($statement);
            $statement->execute();
        } catch (\PDOException $e) {
            return $e;
        }
        return $statement->rowCount();
    }

    private function SingleQuery($statement)
    {
        try {
            $statement = $this->pdo->prepare($statement);
            $result = $statement->execute();
            $statement = $statement->fetch(\PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            return $e;
        }
        return $statement;
    }

    private function GetRowCount($statement)
    {
        try {
            $statement = $this->pdo->prepare($statement);
            $statement->execute();
            $rowCount = $statement->rowCount();
        } catch (\Exception $e) {
            return $e;
        }
        return $rowCount;
    }

    private function IterateUpdateDataSet(array $input)
    {
        $set_string = "";
        $keys = array_keys($input);
        $input_length = count($input);
        for ($i = 0; $i < $input_length; $i++) {
            $set_string .= "{$keys[$i]} = :{$keys[$i]}";
            if ($i < $input_length - 1) {
                $set_string .= ",";
            }
        }
        return $set_string;
    }

    //Mode 1
    //Bus Section
    public function GetAllBus()
    {
        $statement = "SELECT * FROM " . self::BUS_TABLE_NAME;
        $result = $this->Query($statement);
        return $result; //Query PASS
    }
    public function GetBus($bus_id)
    {
        $statement = "SELECT * FROM " . self::BUS_TABLE_NAME . "
                    WHERE id = {$bus_id}";
        $result = $this->SingleQuery($statement);
        return $result; //Query PASS
    }
    public function InsertBus(array $input)
    {
        $statement = "INSERT INTO " . self::BUS_TABLE_NAME . " (plate, status, details)
    VALUES (:plate, :status, :details);";
        try {
            $statement = $this->pdo->prepare($statement);
            $statement->execute(array(
                'plate' => $input['plate'],
                'status' => $input['status'],
                'details' => $input['details'] ?? null,
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function UpdateBus($id, array $input)
    {
        $statement = "UPDATE " . self::BUS_TABLE_NAME . "
      SET plate = :plate, status = :status, details = :details
      WHERE id = :id";
      try {
        $statement = $this->pdo->prepare($statement);
        $statement->execute(array(
          'id' => (int) $id,
          'plate' => $input['plate'],
          'status' => $input['status'],
          'details' => $input['details'] ?? null,
        ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function deleteBus($id)
    {
        $statement = "DELETE FROM " . self::BUS_TABLE_NAME . " WHERE id = {$id}";
        try {
            $result = $this->GetRowCount($statement);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    //End Bus

    //Mode 2
    public function GetBusInRoute($route_id)
    {
        $statement = "SELECT b.id AS busID, plate, status, details FROM " . self::BUS_TABLE_NAME . " b
    INNER JOIN " . self::BUSINROUTE_TABLE_NAME . " bir ON b.id = bir.bus_id
    INNER JOIN " . self::ROUTE_TABLE_NAME . " r ON bir.route_id = r.id
    WHERE bir.route_id = {$route_id}
    ORDER BY b.id";
        $result = $this->Query($statement);
        return $result; //Query PASS
    }

    //Bus Location
    //Mode 3
    public function GetAllRecentBusLocation()
    {
        $statement = "SELECT bl.id,bir.bus_id, bir.route_id, step, bl.course, r.name as route_name, r.description, b.plate, bl.is_active, bl.latitude, bl.longitude, bl.speed, bl.timestamp, r.color  FROM " . self::BUSINROUTE_TABLE_NAME . " bir
  INNER JOIN " . self::BUS_TABLE_NAME . " b ON b.id = bir.bus_id
  INNER JOIN " . self::ROUTE_TABLE_NAME . " r ON r.id = bir.route_id
  INNER JOIN " . self::BUSLOCATION_TABLE_NAME . " bl ON b.id = bl.bus_id
  WHERE status = 1 AND timestamp = (SELECT MAX(timestamp) FROM " . self::BUSLOCATION_TABLE_NAME . " bl2 WHERE bl.bus_id = bl2.bus_id)
  ORDER BY id DESC";
        $result = $this->Query($statement);
        return $result; //Query PASS
    }
    public function GetRecentBusLocation($bus_id)
    {
        $statement = "SELECT bl.id,bir.bus_id, bir.route_id, step, bl.course, r.name as route_name, r.description, b.plate, bl.is_active, bl.latitude, bl.longitude, bl.speed, bl.timestamp, r.color  FROM " . self::BUSINROUTE_TABLE_NAME . " bir
  INNER JOIN " . self::BUS_TABLE_NAME . " b ON b.id = bir.bus_id
  INNER JOIN " . self::ROUTE_TABLE_NAME . " r ON r.id = bir.route_id
  INNER JOIN " . self::BUSLOCATION_TABLE_NAME . " bl ON b.id = bl.bus_id
  WHERE status = 1 AND b.id = {$bus_id} AND timestamp = (SELECT MAX(timestamp) FROM " . self::BUSLOCATION_TABLE_NAME . " bl2 WHERE bl.bus_id = bl2.bus_id)
  ORDER BY id DESC LIMIT 1";
        $result = $this->SingleQuery($statement);
        return $result; //Query PASS
    }
    public function InsertBusLocation(array $input)
    {
        $statement = "INSERT INTO " . self::BUSLOCATION_TABLE_NAME . " (bus_id, latitude, longitude, is_active, step, course, speed)
    VALUES (:id, :latitude, :longitude, :is_active, :step, :course, :speed);";
        $statement = $this->pdo->prepare($statement);
        $statement->execute(array(
            'id' => $input['id'],
            'latitude' => $input['latitude'],
            'longitude' => $input['longitude'],
            'is_active' => $input['is_active'],
            'step' => $input['step'] ?? null,
            'course' => $input['course'] ?? 0,
            'speed' => $input['speed'] ?? 0,
        ));
        return $statement->rowCount();
    }
    public function UpdateBusLocation($bus_id, array $input)
    {
        $statement = "UPDATE " . self::BUSLOCATION_TABLE_NAME . "
    SET latitude = :latitude, longitude = :longitude, is_active = :is_active, step = :step, speed = :speed, course = :course
    WHERE bus_id = {$bus_id}";
        $statement = $this->pdo->prepare($statement);
        $statement->execute(array(
            'latitude' => $input['latitude'],
            'longitude' => $input['longitude'],
            'is_active' => $input['is_active'] ?? 1,
            'step' => $input['step'],
            'course' => $input['course'],
            'speed' => $input['speed'],
        ));
        return $statement->rowCount();
    }
    public function deleteBusLocation($id)
    {
        $statement = "DELETE FROM " . self::BUSLOCATION_TABLE_NAME . " WHERE id = {$id}";
        try {
            $result = $this->GetRowCount($statement);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    //END BusLocation

    //Mode 4
    //BIR
    public function GetRecentBusLocationInRoute($route_id)
    {
        $statement = "SELECT bir.bus_id, bir.route_id, step, bl.course, r.name as route_name, r.description, b.plate, bl.is_active, bl.latitude, bl.longitude, bl.speed, bl.timestamp, r.color  FROM " . self::BUSINROUTE_TABLE_NAME . " bir
INNER JOIN " . self::BUS_TABLE_NAME . " b ON b.id = bir.bus_id
INNER JOIN " . self::ROUTE_TABLE_NAME . " r ON r.id = bir.route_id
INNER JOIN " . self::BUSLOCATION_TABLE_NAME . " bl ON b.id = bl.bus_id
WHERE r.id = {$route_id} AND b.status = 1 AND timestamp = (SELECT MAX(timestamp) FROM " . self::BUSLOCATION_TABLE_NAME . " bl2 WHERE bl.bus_id = bl2.bus_id)
ORDER BY bir.bus_id";
        $result = $this->Query($statement);
        return $result; //Query Pass
    }
    //END

    //Station
    //Mode 6
    public function GetAllStation()
    {
        $statement = "SELECT id AS station_id,name AS station_name, latitude, longitude FROM " . self::STATION_TABLE_NAME;
        $result = $this->Query($statement);
        return $result; //Query Pass (Mod Route Later)
    }
    public function GetStation($id)
    {
        $statement = "SELECT * FROM " . self::STATION_TABLE_NAME . " WHERE id = {$id}";
        $result = $this->SingleQuery($statement);
        return $result;
    }
    public function InsertStation(array $input)
    {
        $statement = "INSERT INTO " . self::STATION_TABLE_NAME . " (name, latitude, longitude)
    VALUES (:name, :latitude, :longitude);";
        $statement = $this->pdo->prepare($statement);
        $statement->execute($input);
        return $statement->rowCount();
    }
    public function UpdateStation($id, array $input)
    {
        $statement = "UPDATE " . self::STATION_TABLE_NAME . "
    SET name = :name, latitude = :latitude, longitude = :longitude
    WHERE id = {$id}";
        $statement = $this->pdo->prepare($statement);
        $statement->execute($input);
        return $statement->rowCount();
    }
    public function deleteStation($id)
    {
        $statement = "DELETE FROM " . self::STATION_TABLE_NAME . " WHERE id = {$id}";
        try {
            $result = $this->GetRowCount($statement);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    /*
    //Mode 7
    public function GetStationInRoute($route_id){
$statement = "SELECT DISTINCT wp.station_id, wp.route_id, r.name as route_name, r.description as route_description, s.name as station_name, s.latitude, s.longitude FROM ".self::WAYPOINT_TABLE_NAME." as wp
    INNER JOIN ".self::ROUTE_TABLE_NAME." as r ON wp.route_id = r.id
    INNER JOIN ".self::STATION_TABLE_NAME." as s ON wp.station_id = s.id
    WHERE route_id = {$route_id} ORDER BY wp.station_id";
    $result = $this->Query($statement);
    return $result; //Query Pass
    }
     */

    //Route
    //Mode 8
    public function GetAllRouteInfo()
    {
        $statement = "SELECT * FROM " . self::ROUTE_TABLE_NAME;
        $result = $this->Query($statement);
        return $result; //Query Pass
    }

    //Mode 9
    public function GetSomeRouteInfoByID($route_id)
    {
        $statement = "SELECT id, name, description FROM " . self::ROUTE_TABLE_NAME . " WHERE id = {$route_id}";
        $result = $this->SingleQuery($statement);
        return $result; //Query Pass.
    }

    public function InsertRoute(array $input)
    {
        $statement = "INSERT INTO " . self::ROUTE_TABLE_NAME . "(name, description, color) VALUES(:name, :description, :color)";
        $statement = $this->pdo->prepare($statement);
        $statement->execute($input);
        return $statement->rowCount();
    }

    public function UpdateRoute($id, array $input)
    {
        $set_string = $this->IterateUpdateDataSet($input);
        $statement = 'UPDATE '.self::ROUTE_TABLE_NAME." SET {$set_string} WHERE id = {$id}";
        $statement = $this->pdo->prepare($statement);
        $statement->execute($input);
        return $statement->rowCount();
    }

    public function DeleteRoute($id)
    {
        $statement = "DELETE FROM " . self::ROUTE_TABLE_NAME . " WHERE id = {$id}";
        try {
            $result = $this->GetRowCount($statement);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
    //END Route

    //Waypoint
    //Mode 10
    public function GetWaypointInRoute($route_id)
    {
        $statement = "SELECT wp.id, step, station_id, wp.route_id, r.name as route_name,r.description as route_description,s.name as station_name, s.latitude, s.longitude, r.color FROM " . self::WAYPOINT_TABLE_NAME . " as wp
    INNER JOIN " . self::ROUTE_TABLE_NAME . " as r ON wp.route_id = r.id
    INNER JOIN " . self::STATION_TABLE_NAME . " as s ON wp.station_id = s.id
    WHERE route_id = {$route_id} ORDER BY step";
        $result = $this->Query($statement);
        return $result; //PASS
    }
    //Mode 11
    public function GetAllWaypoint()
    {
        $statement = "SELECT wp.id, step, station_id, route_id, r.name as route_name,r.description as route_description,s.name as station_name,s.latitude,s.longitude,r.color FROM " . self::WAYPOINT_TABLE_NAME . " as wp
    INNER JOIN " . self::ROUTE_TABLE_NAME . " as r ON wp.route_id = r.id
    INNER JOIN " . self::STATION_TABLE_NAME . " as s ON wp.station_id = s.id
    ORDER BY route_id, step";
        $result = $this->Query($statement);
        return $result;
    }

    public function GetWaypoint($id)
    {
        $statement = "SELECT wp.id, step, station_id, wp.route_id, r.name as route_name,r.description as route_description,s.name as station_name, s.latitude, s.longitude, r.color FROM " . self::WAYPOINT_TABLE_NAME . " as wp
    INNER JOIN " . self::ROUTE_TABLE_NAME . " as r ON wp.route_id = r.id
    INNER JOIN " . self::STATION_TABLE_NAME . " as s ON wp.station_id = s.id
    WHERE wp.id = {$id} ORDER BY step";
        $result = $this->Query($statement);
        return $result; //PASS
    }

    public function InsertWaypoint(array $input)
    {
        $statement = "INSERT INTO " . self::WAYPOINT_TABLE_NAME . "(step, route_id, station_id)
    VALUES(:step, :route_id, :station_id)";
        $statement = $this->pdo->prepare($statement);
        $statement->execute($input);
        return $statement->rowCount();
    }

    public function UpdateWaypoint($id, array $input)
    {
        $set_string = $this->IterateUpdateDataSet($input);
        $statement = "UPDATE " . self::WAYPOINT_TABLE_NAME . " SET {$set_string} WHERE id = {$id}";
        $statement = $this->pdo->prepare($statement);
        $statement->execute($input);
        return $statement->rowCount();
    }

    public function DeleteWaypoint($id)
    {
        $statement = "DELETE FROM " . self::WAYPOINT_TABLE_NAME . " WHERE id = {$id}";
        try {
            $result = $this->GetRowCount($statement);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    //Extra
    //Mode 12
    public function GetRouteInStation($station_id)
    {
        $statement = "SELECT DISTINCT r.id as routeID, r.name as routeName, r.description as routeDescription FROM " . self::WAYPOINT_TABLE_NAME . " as wp
      INNER JOIN " . self::ROUTE_TABLE_NAME . " as r ON wp.route_id = r.id
      INNER JOIN " . self::STATION_TABLE_NAME . " as s ON wp.station_id = s.id
      WHERE s.id = {$station_id}
      ORDER by r.id";
        $result = $this->Query($statement);
        return $result;
    }

    //Mode 13
    public function GetRouteAndStationDataForQRCode($station_id)
    {
        $statement = "SELECT DISTINCT r.id AS routeID, r.name AS routeName, r.description AS routeDescription, s.id AS stationID, s.name AS stationName, s.latitude, s.longitude
      FROM " . self::WAYPOINT_TABLE_NAME . " AS wp
      INNER JOIN " . self::ROUTE_TABLE_NAME . " AS r ON wp.route_id = r.id
      INNER JOIN " . self::STATION_TABLE_NAME . " AS s ON wp.station_id = s.id
      WHERE s.id = {$station_id}
      ORDER BY r.id";
        $result = $this->Query($statement);
        return $result;
    }

    //Arduino Usage
    //POST: Insert/Update Car Function

    /*
    public function UpdateBusData($data)
    {
        $isBusLocationExist = $this->CheckBusLocationExist($data->bus_id);
        if ($isBusLocationExist) {
            //echo ('Go Update');
            $result = $this->UpdateBusDataQuery($data);
        } else {
            //echo ('Go Insert');
            $result = $this->InsertBusDataQuery($data);
        }
        return $result;
    }
    */
    public function InsertBusDataQuery($id, $data)
    {
        $statement = "INSERT INTO " . self::BUSLOCATION_TABLE_NAME . " (bus_id,latitude,longitude,speed,course,step,is_active)
        VALUES ({$id},{$data->latitude},{$data->longitude},{$data->speed},{$data->course},{$data->step},1)";
        $result = $this->NonFetchQuery($statement);
        return $result;
    }
    public function UpdateBusDataQuery($id, $data)
    {
        $statement = "UPDATE " . self::BUSLOCATION_TABLE_NAME . "
        SET latitude = {$data->latitude}, longitude = {$data->longitude}, speed = {$data->speed}, course = {$data->course},step = {$data->step}, is_active = 1
        WHERE bus_id = {$id}";
        $result = $this->NonFetchQuery($statement);
        return $result;

    }
    public function CheckBusLocationExist($bus_id)
    {
        $statement = "SELECT bus_id FROM " . self::BUSLOCATION_TABLE_NAME . " WHERE bus_id = {$bus_id} LIMIT 1";
        $result = $this->GetRowCount($statement);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    //Internal Usage
    public function _FindWaypoint($bus_id)
    {
        $statement = "SELECT route_id FROM " . self::BUSINROUTE_TABLE_NAME . "
                      WHERE bus_id = {$bus_id}";
        //JOIN busID with routeID then get step from that route
        $routeID = $this->SingleQuery($statement);
        $waypointsData = $this->_GetWaypointInRouteMinimal($routeID->route_id);
        return $waypointsData;
    }
    private function _GetWaypointInRouteMinimal($route_id)
    {
      $statement = "SELECT step, station_id, wp.route_id, s.latitude, s.longitude FROM " . self::WAYPOINT_TABLE_NAME . " as wp
      INNER JOIN " . self::ROUTE_TABLE_NAME . " as r ON wp.route_id = r.id
      INNER JOIN " . self::STATION_TABLE_NAME . " as s ON wp.station_id = s.id
      WHERE route_id = {$route_id} ORDER BY step";
        $result = $this->Query($statement);
        return $result;
    }
}
