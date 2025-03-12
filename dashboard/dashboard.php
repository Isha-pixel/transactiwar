<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, balance FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
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
        <p>Your Balance: Rs. <?php echo number_format($user['balance'], 2); ?></p>
    </div>
</body>
</html>
