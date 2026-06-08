<?php
function getDBConnection(): mysqli {
    $host     = '127.0.0.1';
    $dbname   = 'myapp_db';
    $username = 'root';
    $password = 'root';

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("接続失敗: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}
