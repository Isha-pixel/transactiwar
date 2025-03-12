<?php
require 'config/database.php';

function logActivity($user_id, $action) {
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Insert activity log into MySQL database
    $stmt = $conn->prepare("INSERT INTO logs (user_id, action, ip_address) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $action, $ip_address);
    $stmt->execute();
}
?>
