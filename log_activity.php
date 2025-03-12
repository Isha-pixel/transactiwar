<?php
require 'config/database.php';

function logActivity($user_id, $action) {
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Log in MySQL database
    $stmt = $conn->prepare("INSERT INTO logs (user_id, action, ip_address) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $action, $ip_address);
    $stmt->execute();

    // Log in a separate log file
    $log_message = "[" . date("Y-m-d H:i:s") . "] UserID: $user_id | Action: $action | IP: $ip_address" . PHP_EOL;
    file_put_contents(__DIR__ . "/logs/activity.log", $log_message, FILE_APPEND);
}
?>
