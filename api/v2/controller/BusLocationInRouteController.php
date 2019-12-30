<?php
namespace Controller;
require_once './model/BusLocationInRouteModel.php';
use Model\BusLocationInRouteModel;

class BusLocationInRouteController {

    private $db;
    private $requestMethod;
    private $routeId;
    private $busLocationInRouteModel;
    private $queryBuilder;

    public function __construct($db, $requestMethod, $routeId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->routeId = $routeId;

        $this->busLocationInRouteModel = new BusLocationInRouteModel($db);
        
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->routeId) {
                    $response = $this->getBusLocationInRoute($this->routeId);
                }
                else{
                    $response = $this->unprocessableEntityResponse();
                }
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

    private function getBusLocationInRoute($id)
    {
        $result = $this->busLocationInRouteModel->find($id);
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