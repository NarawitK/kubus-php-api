<?php
namespace Controller;
require_once "./model/BusLocationModel.php";
use Model\BusLocationModel;

class BusLocationController {

    private $db;
    private $requestMethod;
    private $busLocationId;
    private $busLocationModel;
    private $queryBuilder;

    public function __construct($db, $requestMethod, $busLocationId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->busLocationId = $busLocationId;

        $this->busLocationModel = new BusLocationModel($db);
        
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->busLocationId) {
                    $response = $this->getBusLocation($this->busLocationId);
                } else {
                    $response = $this->getAllBusLocation();
                };
                break;
            case 'POST':
                $response = $this->createBusLocationFromRequest();
                break;
            case 'PUT':
                $response = $this->updateBusLocationFromRequest($this->busLocationId);
                break;
            case 'DELETE':
                $response = $this->deleteBusLocation($this->busLocationId);
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

    private function getAllBusLocation()
    {
        $result = $this->busLocationModel->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getBusLocation($bus_id)
    {
        $result = $this->busLocationModel->find($bus_id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createBusLocationFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validateBusLocation($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->busLocationModel->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateBusLocationFromRequest($id)
    {
        $result = $this->busLocationModel->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateUpdateBusLocation($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->busLocationModel->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteBusLocation($id)
    {
        /*
        $result = $this->busLocationModel->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        */
        $this->busLocationModel->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateBusLocation($input)
    {
        if (!isset($input['id'], $input['latitude'], $input['longitude'], $input['is_active'])) {
            return false;
        }
        return true;
    }

    private function validateUpdateBusLocation($input)
    {
        if (!isset($input['latitude'], $input['longitude'], $input['is_active'])) {
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