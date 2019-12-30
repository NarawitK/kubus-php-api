<?php
namespace Model\Extras;
require_once './core/queryBuilder.php';
use Core\QueryBuilder;

class WaypointInRouteModel {

    private $db = null;
    private $queryBuilder = null;

    public function __construct($db)
    {
        $this->db = $db;
        $this->queryBuilder = new QueryBuilder($db);
    }  
    public function find($id){
        try{
            $result = $this->queryBuilder->GetWaypointInRoute($id);
            return $result;
        }
        catch(PDOException $e){
            exit($e->getMessage());
        }
    }
}