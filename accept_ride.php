<?php
session_start();

// Check for a POST request and that a ride is pending acceptance
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['ride_status']) && $_SESSION['ride_status'] === 'arrived') {
    // Set the ride status to 'accepted'
    $_SESSION['ride_status'] = 'accepted';

    // Redirect the user back to the dashboard
    header("Location: dashboard.php");
    exit();
} else {
    // If accessed improperly, just go back to the dashboard
    header("Location: dashboard.php");
    exit();
}
?>