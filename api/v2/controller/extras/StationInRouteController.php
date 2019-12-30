<?php
namespace Controller\Extras;
require_once "./model/extras/StationInRouteModel.php";
use Model\Extras\StationInRouteModel;

class StationInRouteController {

    private $db;
    private $requestMethod;
    private $stationId;
    private $routeAndStationModel;
    private $queryBuilder;

    public function __construct($db, $requestMethod, $stationId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->stationId = $stationId;

        $this->routeAndStationModel = new StationInRouteModel($db);
        
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->stationId) {
                    $response = $this->getRouteAndStation($this->stationId);
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

    private function getRouteAndStation($id)
    {
        $result = $this->routeAndStationModel->find($id);
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