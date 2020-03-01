<?php
require "../bootstrap.php";
use Src\Controller\VesselPositionController;

error_reporting(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

// all of our endpoints start with /position
// everything else results in a 404 Not Found
if ($uri[1] == 'json') {
    header("Content-Type: application/json; charset=UTF-8");
    $type = 'json';
} 
else if ($uri[1] == 'xml') {
    header("Content-Type: application/xml; charset=UTF-8");
    $type = 'xml';
} 
else {
    header("HTTP/1.1 404 Not Found");
    exit();
}


// the vessel position mmsi must be a number:
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$query = parse_url($actual_link, PHP_URL_QUERY);
parse_str($query, $parameters);

$requestMethod = $_SERVER["REQUEST_METHOD"];
$remoteAddr = $_SERVER["REMOTE_ADDR"];

// pass the request method and position MMSI to the VesselPositionController and process the HTTP request:
$controller = new VesselPositionController($dbConnection, $requestMethod, $parameters, $remoteAddr, $type);
$controller->processRequest();