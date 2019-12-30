<?php
namespace Model;
require_once './core/queryBuilder.php';
use Core\QueryBuilder;

class RouteModel {

    private $db = null;
    private $queryBuilder = null;

    public function __construct($db)
    {
        $this->db = $db;
        $this->queryBuilder = new QueryBuilder($db);
    }
    
    public function findAll(){
        try{
            $result = $this->queryBuilder->GetAllRouteInfo();
            return $result;
        }
        catch(PDOException $e){
            exit($e->getMessage());
        }
    }

    public function find($id){
        try{
            $result = $this->queryBuilder->GetSomeRouteInfoByID($id);
            return $result;
        }
        catch(PDOException $e){
            exit($e->getMessage());
        }
    }

    public function insert(Array $input){
        $result = $this->queryBuilder->InsertRoute($input);
        return $result;
    }

    public function update($id, Array $input){
        $result = $this->queryBuilder->UpdateRoute($id, $input);
        return $result;
    }

    public function delete($id){
        $result = $this->queryBuilder->DeleteRoute($id);
        return $result;
    }
}