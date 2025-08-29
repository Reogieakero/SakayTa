<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit();
}

$servername = "localhost";
$username   = "root";
$dbpassword = "";
$dbname     = "sakay_ta";

$conn = new mysqli($servername, $username, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

$userEmail = $_SESSION['user_email'];

// 1️⃣ Get the latest ride id with status "arrived_at_destination"
$sql = "SELECT id FROM rides 
        WHERE user_email = ? AND ride_status = 'arrived_at_destination' 
        ORDER BY ride_date DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$ride = $result->fetch_assoc();
$stmt->close();

if ($ride) {
    $rideId = $ride['id'];

    // 2️⃣ Update that specific ride
    $sql = "UPDATE rides SET ride_status = 'completed' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $rideId);
    $stmt->execute();
    $stmt->close();

    // ✅ Clear ride session
    unset($_SESSION['ride_status']);
    unset($_SESSION['pickup_location']);
    unset($_SESSION['dropoff_location']);
    unset($_SESSION['ride_price']);
    unset($_SESSION['driver_name']);
    unset($_SESSION['vehicle_info']);

    // ✅ Add success notification
    $_SESSION['notification'] = 'Payment successful! Your trip has been added to your ride history.';
} else {
    $_SESSION['notification'] = '⚠️ No active ride found to complete.';
}

$conn->close();

// Redirect back to dashboard
header("Location: dashboard.php");
exit();
