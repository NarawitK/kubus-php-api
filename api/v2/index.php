<?php
require_once "./core/bootstrap.php";
require_once "./controller/BusController.php";
require_once "./controller/BusLocationController.php";
require_once "./controller/RouteController.php";
require_once "./controller/StationController.php";
require_once "./controller/WaypointController.php";
require_once "./controller/BusLocationInRouteController.php";
require_once "./controller/device/DeviceUpdateLocation.php";
require_once "./controller/extras/StationInRouteController.php";
require_once "./controller/extras/RouteInStationController.php";
require_once "./controller/extras/WaypointInRouteController.php";

use Controller\BusController;
use Controller\BusLocationController;
use Controller\BusLocationInRouteController;
use Controller\Device\DeviceUpdateLocationController;
use Controller\RouteController;
use Controller\StationController;
use Controller\WaypointController;
use Controller\Extras\StationInRouteController;
use Controller\Extras\RouteInStationController;
use Controller\Extras\WaypointInRouteController;
//use whatever it necessary to fetch data.

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 60");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
$requestMethod = $_SERVER["REQUEST_METHOD"];
//var_dump($uri);

// all of our endpoints start with /person
// everything else results in a 404 Not Found
switch ($uri[4]){
    case "bus": //pass
        $inputId = CheckId($uri);
        $controller = new BusController($dbConnection, $requestMethod, $inputId);
        $controller->processRequest();
        break;
    case "buslocation": //pass
        $inputId = CheckId($uri);
        $controller = new BusLocationController($dbConnection, $requestMethod, $inputId);
        $controller->processRequest();
        break;
	case "buslocation-in-route":
		$inputId = CheckId($uri);
        $controller = new BusLocationInRouteController($dbConnection, $requestMethod, $inputId);
        $controller->processRequest();
		break;
    case "route": //pass
        $inputId = CheckId($uri);
        $controller = new RouteController($dbConnection, $requestMethod, $inputId);
        $controller->processRequest();
        break;
    case "station": //pass
        $inputId = CheckId($uri);
        $controller = new StationController($dbConnection, $requestMethod, $inputId);
        $controller->processRequest();
        break;
    case "waypoint": //pass
        $inputId = CheckId($uri);
        $controller = new WaypointController($dbConnection, $requestMethod, $inputId);
        $controller->processRequest();
        break;
    case "waypoint-in-route":
        $inputId = CheckId($uri);
        $controller = new WaypointInRouteController($dbConnection, $requestMethod, $inputId);
        $controller->processRequest();
        break;
    case "routes-in-station": //pass
        $inputId = CheckId($uri);
        $controller = new StationInRouteController($dbConnection, $requestMethod, $inputId);
        $controller->processRequest();
        break;
		/*
    case "routes-in-station":
        $inputId = CheckId($uri);
        $controller = new RouteInStationController($dbConnection, $requestMethod, $inputId);
        $controller->processRequest();
        break;
		*/
    case "device-location-update":
        $inputId = CheckId($uri);
        $controller = new DeviceUpdateLocationController($dbConnection, $requestMethod, $inputId);
        $controller->processRequest();
        break;
    default:
        header("HTTP/1.1 404 Not Found");
        exit();

}

// pass the request method and ID to the Controller and process the HTTP request:

function CheckId($uri){
    $Id = null;
    if(isset($uri[5]) && is_numeric($uri[5])){
        $Id = (int)$uri[5];
    }
	else if(isset($uri[5]) && is_string($uri[5])){
		header("HTTP/1.1 404 Not Found");
        exit();
	}
	return $Id;
}
?>