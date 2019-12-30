<?php
namespace Controller;
require_once './model/StationModel.php';
use Model\StationModel;

class StationController {

    private $db;
    private $requestMethod;
    private $Id;
    private $stationModel;
    private $queryBuilder;

    public function __construct($db, $requestMethod, $Id)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->Id = $Id;

        $this->stationModel = new StationModel($db);
        
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->Id || $this->Id === 0) {
                    $response = $this->getStation($this->Id);
                }
                else{
                    $response = $this->getAllStation();
                }
                break;
            case 'POST':
                $response = $this->createStationFromRequest();
                break;
            case 'PUT':
                if($this->Id)
                    $response = $this->updateStationFromRequest($this->Id);
                else
                    $response = $this->unprocessableEntityResponse();
                break;
            case 'DELETE':
                if($this->Id)
                    $response = $this->deleteStation($this->Id);
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

    private function getAllStation()
    {
        $result = $this->stationModel->findAll();
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getStation($id)
    {
        $result = $this->stationModel->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createStationFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateStation($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->stationModel->createStationFromRequest($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateStationFromRequest($id)
    {
        $result = $this->stationModel->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateStation($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->stationModel->updateStationFromRequest($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteStation($id)
    {
        $result = $this->stationModel->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->stationModel->deleteStation($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateStation($input)
    {
        if (!isset($input['name'], $input['latitude'], $input['longitude'])) {
            return false;
        }
        return true;
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