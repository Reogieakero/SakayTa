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
    // Show a more specific connection error
    $_SESSION['notification'] = '❌ Database connection failed: ' . $conn->connect_error;
    header("Location: dashboard.php");
    exit();
}

// ✅ Update the ride status in the database
$userEmail = $_SESSION['user_email'];

// First, check the current ride status for debugging
$check_sql = "SELECT ride_status FROM rides WHERE user_email = ? ORDER BY ride_date DESC LIMIT 1";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $userEmail);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$current_status_row = $check_result->fetch_assoc();
$check_stmt->close();

if ($current_status_row['ride_status'] !== 'accepted') {
    // If the status is not 'accepted', the update will fail. Notify the user.
    $_SESSION['notification'] = '❌ Error: The last ride is not currently "accepted". Current status: ' . $current_status_row['ride_status'];
    $conn->close();
    header("Location: dashboard.php");
    exit();
}

// Run the actual update
$sql = "UPDATE rides SET ride_status = 'arrived_at_destination' WHERE user_email = ? AND ride_status = 'accepted' ORDER BY ride_date DESC LIMIT 1";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        // Success: The update worked
        $_SESSION['ride_status'] = 'arrived_at_destination';
        $_SESSION['notification'] = 'You have arrived at your destination! Please proceed to payment.';
    } else {
        // Failure: No rows were updated
        $_SESSION['notification'] = '❌ Error: The update to "arrived at destination" failed. Affected rows: ' . $stmt->affected_rows;
    }
    
    $stmt->close();
} else {
    $_SESSION['notification'] = "❌ Error preparing statement: " . $conn->error;
    $conn->close();
    exit();
}

$conn->close();
header("Location: dashboard.php");
exit();
?>