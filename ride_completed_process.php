<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Redirect if the user is not logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit();
}

// Connect to the database
$servername = "localhost";
$username   = "root";
$dbpassword = "";
$dbname     = "sakay_ta";

$conn = new mysqli($servername, $username, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// ** ✅ FIX: Check the current ride status before attempting to update it **
$userEmail = $_SESSION['user_email'];

// First, check the current ride status in the database for debugging purposes
$check_sql = "SELECT ride_status FROM rides WHERE user_email = ? ORDER BY ride_date DESC LIMIT 1";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $userEmail);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$current_status_row = $check_result->fetch_assoc();
$check_stmt->close();

if ($current_status_row && $current_status_row['ride_status'] !== 'arrived_at_destination') {
    // If the status is not 'arrived_at_destination', explain why the update failed
    $_SESSION['notification'] = '❌ Error: Ride status is ' . $current_status_row['ride_status'] . '. You must confirm arrival first.';
    $conn->close();
    header("Location: dashboard.php");
    exit();
}

// ✅ Update the ride status in the database to 'completed'
$sql = "UPDATE rides SET ride_status = 'completed' WHERE user_email = ? AND ride_status = 'arrived_at_destination' ORDER BY ride_date DESC LIMIT 1";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    
    // Check if any rows were affected
    if ($stmt->affected_rows > 0) {
        // Success: the ride status was updated
        $_SESSION['notification'] = 'Payment successful! Your trip has been added to your ride history.';
    } else {
        // Failure: no rows were updated, possibly due to a race condition or incorrect status
        $_SESSION['notification'] = '❌ Error: No ride was completed. Please try again.';
    }

    $stmt->close();
} else {
    echo "❌ Error preparing statement: " . $conn->error;
    $conn->close();
    exit();
}

// Unset all ride-related session variables
unset($_SESSION['ride_status']);
unset($_SESSION['pickup_location']);
unset($_SESSION['dropoff_location']);
unset($_SESSION['ride_price']);
unset($_SESSION['driver_name']);
unset($_SESSION['vehicle_info']);

// Redirect to the dashboard
header("Location: dashboard.php");
exit();
?>