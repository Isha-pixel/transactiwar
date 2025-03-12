<?php
session_start();
require '../config/database.php';
require '../config/log_activity.php';

// Secure session management
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

session_regenerate_id(true); // Prevent session fixation attacks

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT username, balance FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Log user activity
logActivity($user_id, "Accessed Dashboard");

// Fetch last 5 transactions
$stmt = $conn->prepare("SELECT sender_id, receiver_id, amount, comment, created_at FROM transactions WHERE sender_id = ? OR receiver_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$transactions = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | TransactiWar</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">TransactiWar</a>
            <div class="navbar-nav">
                <a class="nav-link" href="profile.php">Profile</a>
                <a class="nav-link" href="transfer.php">Transfer Money</a>
                <a class="nav-link" href="transactions.php">Transaction History</a>
                <a class="nav-link text-danger" href="../auth/logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container mt-5">
        <h2 class="text-center">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        <div class="alert alert-info text-center">
            <strong>Your Balance: ₹<?php echo number_format($user['balance'], 2); ?></strong>
        </div>

        <h4 class="mt-4">Recent Transactions</h4>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Sender</th>
                    <th>Receiver</th>
                    <th>Amount</th>
                    <th>Comment</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($transaction = $transactions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $transaction['sender_id'] == $user_id ? "You" : "User #" . $transaction['sender_id']; ?></td>
                        <td><?php echo $transaction['receiver_id'] == $user_id ? "You" : "User #" . $transaction['receiver_id']; ?></td>
                        <td>₹<?php echo number_format($transaction['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($transaction['comment'] ?: "N/A"); ?></td>
                        <td><?php echo date("d M Y, H:i", strtotime($transaction['created_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
