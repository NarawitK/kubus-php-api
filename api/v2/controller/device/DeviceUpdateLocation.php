<?php
namespace Controller\Device;
require_once "./model/device/DeviceUpdateLocationModel.php";
use Model\Device\DeviceUpdateLocationModel;

class DeviceUpdateLocationController {

    private $db;
    private $requestMethod;
    private $busId;
    private $deviceUpdateLocationModel;
    private $queryBuilder;

    public function __construct($db, $requestMethod, $busId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->busId = $busId;

        $this->deviceUpdateLocationModel = new DeviceUpdateLocationModel($db);
        
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'POST':
                $response = $this->InsertBusLocationData($this->busId);
                break;
            case 'PUT':
                $response = $this->UpdateBusLocationFromDevice($this->busId);
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

    private function InsertBusLocationData($id){
        $input = json_decode(file_get_contents('php://input'), FALSE);
        if(!$this->validateDevicePOSTData($input)){
            return $this->unprocessableEntityResponse();
        }
        $result = $this->deviceUpdateLocationModel->insert($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function UpdateBusLocationFromDevice($id)
    {
        $result = $this->deviceUpdateLocationModel->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = json_decode(file_get_contents('php://input'), FALSE);
        if (!$this->validateDevicePOSTData($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->deviceUpdateLocationModel->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function validateDevicePOSTData($input){
        var_dump($input);
        if (!isset($input->latitude, $input->longitude, $input->course, $input->speed)) {
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