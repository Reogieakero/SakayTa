<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit();
}

$userName = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Passenger Dashboard</title>
</head>
<body>
    <div class="user-profile">
        <div class="profile-avatar">
            <span><?php echo htmlspecialchars(substr($userName, 0, 2)); ?></span>
        </div>
        <div class="profile-info">
            <h3><?php echo htmlspecialchars($userName); ?></h3>
            <p><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
        </div>
    </div>
</body>
</html>