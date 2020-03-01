<?php
namespace Src\TableGateways;

class RequestGateway {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function count($request_ip)
    {
        $statement = "
            SELECT 
                COUNT(tb1.id) AS requests
            FROM
                requests AS tb1
                    LEFT JOIN
                requests AS tb2 ON tb2.id = tb1.id
            WHERE
                tb1.remote_address = ?
                    AND tb1.created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($request_ip));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result['requests'];
        } 
        catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function log($request_ip)
    {
        $statement = "INSERT INTO requests (remote_address) VALUES (?)";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($request_ip));
        }
        catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}