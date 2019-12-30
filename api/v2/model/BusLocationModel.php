<?php
namespace Model;
require_once './core/queryBuilder.php';
use Core\QueryBuilder;

class BusLocationModel {

    private $db = null;
    private $queryBuilder = null;

    public function __construct($db)
    {
        $this->db = $db;
        $this->queryBuilder = new QueryBuilder($db);
    }
    
    public function findAll(){
        try{
            $result = $this->queryBuilder->GetAllRecentBusLocation();
            return $result;
        }
        catch(PDOException $e){
            exit($e->getMessage());
        }
    }

    public function find($id){
        try{
            $result = $this->queryBuilder->GetRecentBusLocation($id);
            return $result;
        }
        catch(PDOException $e){
            exit($e->getMessage());
        }
    }

    public function insert(Array $input){
        $result = $this->queryBuilder->InsertBusLocation($input);
        return $result;
    }

    public function update($id, Array $input){
        $result = $this->queryBuilder->UpdateBusLocation($id, $input);
        return $result;
    }

    public function delete($id){
        $result = $this->queryBuilder->deleteBusLocation($id);
        return $result;
    }
}