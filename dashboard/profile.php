<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bio = htmlspecialchars($_POST['bio']);
    
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);

        $stmt = $conn->prepare("UPDATE users SET bio=?, profile_image=? WHERE id=?");
        $stmt->bind_param("ssi", $bio, $target_file, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET bio=? WHERE id=?");
        $stmt->bind_param("si", $bio, $user_id);
    }
    $stmt->execute();
    header("Location: profile.php?success=1");
}

$stmt = $conn->prepare("SELECT username, bio, profile_image FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | TransactiWar</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Profile</h2>
        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <label>Bio:</label>
            <input type="text" name="bio" value="<?php echo htmlspecialchars($user['bio']); ?>" class="form-control">
            <label>Profile Picture:</label>
            <input type="file" name="profile_image" class="form-control">
            <button type="submit" class="btn btn-primary mt-3">Update Profile</button>
        </form>
    </div>
</body>
</html>
