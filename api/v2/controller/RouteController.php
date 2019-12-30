<?php
namespace Controller;
require_once "./model/RouteModel.php";
use Model\RouteModel;

class RouteController {

    private $db;
    private $requestMethod;
    private $Id;
    private $routeModel;
    private $queryBuilder;

    public function __construct($db, $requestMethod, $Id)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->Id = $Id;

        $this->routeModel = new RouteModel($db);
        
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->Id || $this->Id === 0) {
                    $response = $this->getRoute($this->Id);
                } else {
                    $response = $this->getAllRoutes();
                }
                break;
            case 'POST':
                $response = $this->createRouteFromRequest();
                break;
            case 'PUT':
                $response = $this->updateRouteFromRequest($this->Id);
                break;
            case 'DELETE':
                $response = $this->deleteRoute($this->Id);
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

    private function getAllRoutes()
    {
        $result = $this->routeModel->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        
        return $response;
    }

    private function getRoute($id)
    {
        $result = $this->routeModel->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createRouteFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateRoute($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->routeModel->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateRouteFromRequest($id)
    {
        $result = $this->routeModel->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        /*
        if (!$this->validateRoute($input)) {
            return $this->unprocessableEntityResponse();
        }
        */
        $this->routeModel->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteRoute($id)
    {
        $result = $this->routeModel->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->routeModel->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateRoute($input)
    {
        if (!isset($input['name'], $input['description'], $input['color'])) {
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