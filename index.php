<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to TransactiWar</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5 text-center">
        <h1>Welcome to TransactiWar</h1>
        <p>Securely manage your transactions and money transfers.</p>
        <a href="auth/login.php" class="btn btn-success">Login</a>
        <a href="auth/register.php" class="btn btn-primary">Register</a>
    </div>
</body>
</html>
