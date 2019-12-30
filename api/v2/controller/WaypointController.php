<?php
namespace Controller;
require_once './model/WaypointModel.php';
use Model\WaypointModel;

class WaypointController {

    private $db;
    private $requestMethod;
    private $Id;
    private $waypointModel;
    private $queryBuilder;

    public function __construct($db, $requestMethod, $Id)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->Id = $Id;

        $this->waypointModel = new WaypointModel($db);
        
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->Id) {
                    $response = $this->getWaypoint($this->Id);
                }
                else{
                    $response = $this->getAllWaypoint($this->Id);
                }
                break;
            case 'POST':
                $response = $this->createWaypointFromRequest();
                break;
            case 'PUT':
                $response = $this->updateWaypointFromRequest($this->Id);
                break;
            case 'DELETE':
                $response = $this->deleteWaypoint($this->Id);
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

    private function getAllWaypoint()
    {
        $result = $this->waypointModel->findAll();
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getWaypoint($id)
    {
        $result = $this->waypointModel->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createWaypointFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateWaypoint($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->waypointModel->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateWaypointFromRequest($id)
    {
        $result = $this->waypointModel->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateWaypoint($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->waypointModel->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteWaypoint($id)
    {
        $result = $this->waypointModel->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->waypointModel->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateWaypoint($input)
    {
        if (! isset($input['step'], $input['route_id'], $input['station_id'])) {
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