<?php
session_start();
require '../config/database.php';
require '../log_activity.php';

// CSRF Protection
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed!");
    }

    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    $amount = $_POST['amount'];

    $stmt = $conn->prepare("SELECT balance FROM users WHERE id=?");
    $stmt->bind_param("i", $sender_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sender = $result->fetch_assoc();

    if ($sender['balance'] >= $amount) {
        $conn->begin_transaction();
        $stmt = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id=?");
        $stmt->bind_param("di", $amount, $sender_id);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id=?");
        $stmt->bind_param("di", $amount, $receiver_id);
        $stmt->execute();

        $stmt = $conn->prepare("INSERT INTO transactions (sender_id, receiver_id, amount) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $sender_id, $receiver_id, $amount);
        $stmt->execute();

        logActivity($sender_id, "Transferred Rs. $amount to User ID $receiver_id");
        $conn->commit();
    } else {
        logActivity($sender_id, "Failed Transfer Attempt (Insufficient Funds)");
        die("Insufficient funds.");
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Money | TransactiWar</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-body">
                <h2 class="text-center">Transfer Money</h2>
                <form action="transfer.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Receiver User ID</label>
                        <input type="text" name="receiver_id" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (Rs.)</label>
                        <input type="number" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comment (optional)</label>
                        <input type="text" name="comment" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Transfer</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
