<?php
namespace Controller;
require_once "./model/BusModel.php";
use Model\BusModel;

class BusController {

    private $db;
    private $requestMethod;
    private $busId;
    private $busModel;
    private $queryBuilder;

    public function __construct($db, $requestMethod, $busId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->busId = $busId;

        $this->busModel = new BusModel($db);
        
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->busId || $this->busId === 0) {
                    $response = $this->getBus($this->busId);
                } else {
                    $response = $this->getAllBuses();
                }
                break;
            case 'POST':
                $response = $this->createBusFromRequest();
                break;
            case 'PUT':
                $response = $this->updateBusFromRequest($this->busId);
                break;
            case 'DELETE':
                $response = $this->deleteBus($this->busId);
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

    private function getAllBuses()
    {
        $result = $this->busModel->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        
        return $response;
    }

    private function getBus($id)
    {
        $result = $this->busModel->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createBusFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateBus($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->busModel->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateBusFromRequest($id)
    {
        $result = $this->busModel->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateBus($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->busModel->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteBus($id)
    {
        $result = $this->busModel->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->busModel->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateBus($input)
    {
        if (!isset($input['plate'], $input['status'])) {
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