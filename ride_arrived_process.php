<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if the current ride status is 'accepted'
    if (isset($_SESSION['ride_status']) && $_SESSION['ride_status'] === 'accepted') {
        // Change the ride status to 'arrived_at_destination'
        $_SESSION['ride_status'] = 'arrived_at_destination';
    }

    // Redirect back to the dashboard
    header("Location: dashboard.php");
    exit();
} else {
    // If accessed improperly, just go back to the dashboard
    header("Location: dashboard.php");
    exit();
}
?>