<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'accept') {
        $_SESSION['ride_status'] = 'accepted';
    } elseif ($action === 'decline') {
        $_SESSION['ride_status'] = 'declined';
    }
    
    // Redirect back to the dashboard to show the updated content
    header("Location: dashboard.php");
    exit();
} else {
    // If accessed improperly, just go back to the dashboard
    header("Location: dashboard.php");
    exit();
}
?>