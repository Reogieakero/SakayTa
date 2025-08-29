<?php
session_start();

// Ensure the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Retrieve ride details from the current session
    $ride_history_entry = [
        'date' => date('F j, Y • g:i A'),
        'pickup' => $_SESSION['pickup_location'] ?? 'Unknown',
        'dropoff' => $_SESSION['dropoff_location'] ?? 'Unknown',
        'price' => $_SESSION['ride_price'] ?? '0',
    ];

    // Initialize ride history array if it doesn't exist
    if (!isset($_SESSION['ride_history'])) {
        $_SESSION['ride_history'] = [];
    }

    // Add the new ride to the history
    array_unshift($_SESSION['ride_history'], $ride_history_entry);

    // Clear all ride-related session variables
    unset($_SESSION['ride_status']);
    unset($_SESSION['pickup_location']);
    unset($_SESSION['dropoff_location']);
    unset($_SESSION['ride_price']);
    unset($_SESSION['driver_name']);
    unset($_SESSION['vehicle_info']);
    unset($_SESSION['eta']);

    // Set a success message to be displayed on the dashboard
    $_SESSION['notification'] = "Payment successful! Your ride is complete. Thank you for using Sakay Ta!";

    // Redirect back to the dashboard
    header("Location: dashboard.php");
    exit();
} else {
    // If someone tries to access this page directly, redirect them
    header("Location: dashboard.php");
    exit();
}
?>