<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login_system_with_security_layer";

$connection = new mysqli($servername, $username, $password, $dbname);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$connection->set_charset('utf8mb4');
