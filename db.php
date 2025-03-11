<?php

function getOracleConnection()
{
    $config = require __DIR__ . '/config.php';
    $username = $config['oracle']['username'];
    $password = $config['oracle']['password'];
    $connection_string = $config['oracle']['connection_string'];

    $connection = oci_connect($username, $password, $connection_string);
    if (!$connection) {
        $e = oci_error();
        echo json_encode(['status' => 'error', 'message' => 'Could not connect to  database!', 'error' => $e['message']]);
        exit;
    }
    return $connection;
}
