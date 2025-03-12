<?php
$host = "db";  // Service name defined in docker-compose.yml
$dbname = "transactiwar";
$username = "user";
$password = "password";

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
