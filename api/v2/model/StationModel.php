<?php
namespace Model;
require_once './core/queryBuilder.php';
use Core\QueryBuilder;

class StationModel {

    private $db = null;
    private $queryBuilder = null;

    public function __construct($db)
    {
        $this->db = $db;
        $this->queryBuilder = new QueryBuilder($db);
    }
    
    public function findAll(){
        try{
            $result = $this->queryBuilder->GetAllStation();
            return $result;
        }
        catch(PDOException $e){
            exit($e->getMessage());
        }
    }

    public function find($id){
        try{
            $result = $this->queryBuilder->GetStation($id);
            return $result;
        }
        catch(PDOException $e){
            exit($e->getMessage());
        }
    }

    public function createStationFromRequest(Array $input){
        $result = $this->queryBuilder->InsertStation($input);
        return $result;
    }

    public function updateStationFromRequest($id, Array $input){
        $result = $this->queryBuilder->UpdateStation($id, $input);
        return $result;
    }

    public function deleteStation($id){
        $result = $this->queryBuilder->deleteStation($id);
        return $result;
    }
}