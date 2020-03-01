<?php
namespace Src\Controller;

use Src\TableGateways\VesselPositionGateway;
use Src\TableGateways\RequestGateway;
use Src\Interfaces\Xml;

class VesselPositionController {
    use Xml;

    private $db;
    private $requestMethod;
    private $requestGateway;
    private $mmsi;
    private $requestIP;
    private $contentType;

    private $vesselPositionGateway;

    public function __construct($db, $requestMethod, $params, $remoteAddr, $contentType)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->params = $params;
        $this->remoteAddr = $remoteAddr;
        $this->contentType = $contentType;

        $this->vesselPositionGateway = new VesselPositionGateway($db);
        $this->requestGateway = new RequestGateway($db);
    }
    
    public function processRequest()
    {
        if ($requests = $this->requestGateway->count($this->remoteAddr)) {
            if ($requests >= 10) {
                return $this->limitRequestsResponse();
            }
        }

        switch ($this->requestMethod) {
            case 'GET':
                if ($this->params) {
                    $response = $this->getVesselPosition($this->params);
                }
                else {
                    $response = $this->getAllVesselPositions();
                };
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }

        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }

        $this->requestGateway->log($this->remoteAddr);
    }

    private function limitRequestsResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Unprocessable Entity';
        $response['body'] = [
            'error' => 'you have exceeded the limit of verification attempts requests'
        ];
        
        if ($this->contentType == 'xml') {
            $xml_data = new \SimpleXMLElement('<?xml version="1.0"?><data></data>');
            $this->array_to_xml($response, $xml_data);
            echo $xml_data->asXML();
        } 
        else if ($this->contentType == 'json') {
            echo json_encode($response['body']);
        }
    }

    private function getAllVesselPositions()
    {
        $result = $this->vesselPositionGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = $result;

        if ($this->contentType == 'xml') {
            $xml_data = new \SimpleXMLElement('<?xml version="1.0"?><vesselPositions></vesselPositions>');
            $this->array_to_xml($response, $xml_data);
            echo $xml_data->asXML();
        } 
        else if ($this->contentType == 'json') {
            return json_encode($response['body']);
        }
    }
    
    private function getVesselPosition($params)
    {
        $result = $this->vesselPositionGateway->find($params);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);

        if ($this->contentType == 'xml') {
            $xml_data = new \SimpleXMLElement('<?xml version="1.0"?><vesselPositions></vesselPositions>');
            
            $this->array_to_xml($result, $xml_data);
            $xml_data->formatOutput = true;

            echo $xml_data->asXML();
        } 
        else if ($this->contentType == 'json') {
            return $response;
        }
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = [
            'error' => 'No results found'
        ];

        if ($this->contentType == 'xml') {
            $xml_data = new \SimpleXMLElement('<?xml version="1.0"?><data></data>');
            $this->array_to_xml($response, $xml_data);
            echo $xml_data->asXML();
        } 
        else if ($this->contentType == 'json') {
            echo json_encode($response['body']);
        }
    }
}