<?php
namespace Model\Device;
require_once './core/queryBuilder.php';
require_once "../helpers/stepadder.php";
use Core\QueryBuilder;

class DeviceUpdateLocationModel {

    private $db = null;
    private $queryBuilder = null;

    public function __construct($db)
    {
        $this->db = $db;
        $this->queryBuilder = new QueryBuilder($db);
    }

    public function find($id){
        try{
            $result = $this->queryBuilder->CheckBusLocationExist($id);
            return $result;
        }
        catch(\PDOException $e){
            exit($e->getMessage());
        }
    }

    public function insert($id, object $input){
        try{
            $input_data = $this->prepareStep($id, $input);
            $result = $this->queryBuilder->InsertBusDataQuery($id, $input_data);
            return $result;
        }
        catch(\PDOException $e){
            exit($e->getMessage());
        }
    }

    public function update($id, object $input){
        try{
            $input_data = $this->prepareStep($id, $input);
            $result = $this->queryBuilder->UpdateBusDataQuery($id, $input_data);
            return $result;
        }
        catch(\PDOException $e){
            exit($e->getMessage());
        }
    }
    private function prepareStep($id, $input){
        $input_data = $input;
        $waypoint = $this->queryBuilder->_FindWaypoint($id);
        $busLocationInRecord = $this->queryBuilder->GetRecentBusLocation($id);
        $input_data->step = AddStepToArduinoPOST($input_data, $waypoint, $busLocationInRecord);
        return $input_data;
    }
}