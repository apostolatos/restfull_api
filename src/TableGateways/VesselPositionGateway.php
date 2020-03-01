<?php
namespace Src\TableGateways;

class VesselPositionGateway {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "
            SELECT 
                mmsi, status, station, speed, lon, lat, course, heading, rot, timestamp
            FROM
                vessel_positions;
        ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } 
        catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($params)
    {
        $values = [];
        $statement = "
            SELECT 
                mmsi, status, station, speed, lon, lat, course, heading, rot, timestamp
            FROM
                vessel_positions
        ";

        $condition = "";
        foreach ($params as $key => $parameter) {
            if ($key == 'mmsi') {
                if (is_array($parameter)) {
                    $values[] = implode(', ', $parameter);
                    $statement .= "WHERE mmsi IN(?)";
                }
                else {
                    $values[] = $parameter;
                    $statement .= "WHERE `mmsi` = ?";
                }
            }
            
            if ($key == 'minLat') {
                $values[] = $parameter;
                $statement .= " AND `lat` > ?";
            }
            
            if ($key == 'maxLat') {
                $values[] = $parameter;
                $statement .= " AND `lat` <= ?";
            }
            
            if ($key == 'minLon') {
                $values[] = $parameter;
                $statement .= " AND `lon` > ?";
            }
            
            if ($key == 'maxLon') {
                $values[] = $parameter;
                $statement .= " AND `lon` <= ?";
            }
        }

        $statement .= " ORDER BY `timestamp` DESC";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute($values);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } 
        catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}