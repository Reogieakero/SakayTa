<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit();
}

echo "✅ Debug: Starting ride completion process.<br>";

$servername = "localhost";
$username   = "root";
$dbpassword = "";
$dbname     = "sakay_ta";

$conn = new mysqli($servername, $username, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

$userEmail = $_SESSION['user_email'];

$check_sql = "SELECT ride_status FROM rides WHERE user_email = ? ORDER BY ride_date DESC LIMIT 1";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $userEmail);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$current_status_row = $check_result->fetch_assoc();
$check_stmt->close();

echo "✅ Debug: Current ride status is: " . $current_status_row['ride_status'] . ".<br>";

if ($current_status_row && $current_status_row['ride_status'] !== 'arrived_at_destination') {
    $_SESSION['notification'] = '❌ Error: Ride status is ' . $current_status_row['ride_status'] . '. You must confirm arrival first.';
    $conn->close();
    header("Location: dashboard.php");
    exit();
}

$sql = "UPDATE rides SET ride_status = 'completed' WHERE user_email = ? AND ride_status = 'arrived_at_destination' ORDER BY ride_date DESC LIMIT 1";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $_SESSION['notification'] = 'Payment successful! Your trip has been added to your ride history.';
        echo "✅ Debug: Ride status updated to 'completed'.<br>";
    } else {
        $_SESSION['notification'] = '❌ Error: No ride was completed. Please try again.';
        echo "❌ Debug: Update failed. No rows affected.<br>";
    }

    $stmt->close();
} else {
    echo "❌ Error preparing statement: " . $conn->error;
    $conn->close();
    exit();
}

unset($_SESSION['ride_status']);
unset($_SESSION['pickup_location']);
unset($_SESSION['dropoff_location']);
unset($_SESSION['ride_price']);
unset($_SESSION['driver_name']);
unset($_SESSION['vehicle_info']);

$conn->close();
header("Location: dashboard.php");
exit();
?>