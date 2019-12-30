<?php
namespace Controller\Extras;
require_once './model/extras/WaypointInRouteModel.php';
use Model\Extras\WaypointInRouteModel;

class WaypointInRouteController {

    private $db;
    private $requestMethod;
    private $Id;
    private $waypointInRouteModel;
    private $queryBuilder;

    public function __construct($db, $requestMethod, $Id)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->Id = $Id;

        $this->waypointInRouteModel = new WaypointInRouteModel($db);
        
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if($this->Id)
                    $response = $this->getWaypointInRoute($this->Id);
                else
                    $response = $this->unprocessableEntityResponse();
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getWaypointInRoute($id)
    {
        $result = $this->waypointInRouteModel->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}