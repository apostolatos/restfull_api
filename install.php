<?php
require 'bootstrap.php';

// create vsesel_positions table 
$statement = <<<EOS
    CREATE TABLE `vessel_positions` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `mmsi` int(9) NOT NULL,
        `status` tinyint(4) DEFAULT '0',
        `station` int(11) NOT NULL,
        `speed` int(11) NOT NULL,
        `lon` DECIMAL(6,4) NOT NULL,
        `lat` DECIMAL(6,4) NOT NULL,
        `course` int(11) DEFAULT NULL,
        `heading` int(11) DEFAULT NULL,
        `rot` varchar(45) DEFAULT NULL,
        `timestamp` bigint unsigned DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
EOS;

try {
    $createTable = $dbConnection->exec($statement);
    echo "Table vessel_positions was created succesfully \n";
} 
catch (\PDOException $e) {
    exit($e->getMessage());
}

// table seeder
$f = file_get_contents('ship_positions.json');

if (!function_exists('json_decode')) die('Your host does not support json');
$feed = json_decode($f);
$sqlclause = '';

for ($i=0; $i<count($feed); $i++) 
{
    $sql = array();
    foreach ($feed[$i] as $key => $value) {

        $sql[] = (is_numeric($value)) ? "$value" : "'" . $value . "'";
    }
    $sqlclause .= "(" . implode(", ", $sql) . ")"  . (count($feed) != $i+1 ? ", " : "") . "\n";
}

$statement = <<<EOS
    INSERT INTO vessel_positions
        (mmsi, status, station, speed, lon, lat, course, heading, rot, timestamp)
    VALUES
        $sqlclause
EOS;

try {
    $insertVesselPosition = $dbConnection->exec($statement);
    echo "Table vessel_positions was seeded succesfully \n";
} 
catch (\PDOException $e) {
    exit($e->getMessage());
}

// create requests table
$statement = <<<EOS
    CREATE TABLE `requests` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `remote_address` varchar(255) CHARACTER SET utf8 NOT NULL,
        `created_at` timestamp default now() on update now(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
EOS;

try {
    $createTable = $dbConnection->exec($statement);
} 
catch (\PDOException $e) {
    exit($e->getMessage());
}