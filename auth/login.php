<?php
session_start();
require '../config/database.php';
require '../log_activity.php';

// Prevent session fixation attacks
session_regenerate_id(true);

// Initialize CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize login attempts if not set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed!");
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $ip_address = $_SERVER['REMOTE_ADDR']; // Capture user IP

    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }

    // Check if the user exceeded login attempts
    if ($_SESSION['login_attempts'] > 5) {
        logActivity(0, "Too many failed login attempts from IP: $ip_address");
        die("Too many failed attempts. Try again later.");
    }

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify password securely
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login_attempts'] = 0; // Reset failed attempts
        logActivity($user['id'], "Successful Login from IP: $ip_address");

        header("Location: ../dashboard/dashboard.php");
        exit();
    } else {
        $_SESSION['login_attempts']++; // Increment failed login attempts
        logActivity(NULL, "Failed Login Attempt for username: $username from IP: $ip_address");

        // Generic error message to prevent user enumeration attacks
        die("Invalid login credentials!");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | TransactiWar</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 400px;">
            <div class="card-body">
                <h2 class="text-center">Login</h2>
                <form action="login.php" method="POST">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required placeholder="Enter your username">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Enter your password">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Login</button>
                </form>
                <p class="mt-3 text-center">New user? <a href="register.php">Register</a></p>
            </div>
        </div>
    </div>
</body>
</html>
