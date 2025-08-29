<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_email']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.html");
    exit();
}

require_once __DIR__ . '/db.php';

$userEmail = $_SESSION['user_email'];
$pickupLocation  = trim($_POST['pickup_location'] ?? '');
$dropoffLocation = trim($_POST['dropoff_location'] ?? '');
$ridePrice       = (float)($_POST['ride_price'] ?? 0);

if ($pickupLocation === '' || $dropoffLocation === '' || $ridePrice <= 0) {
    $_SESSION['notification'] = 'Please provide valid pickup, drop-off, and price.';
    header("Location: dashboard.php");
    exit();
}

$conn = db();

// Insert as PENDING; driver will be assigned later
$sql = "INSERT INTO rides
        (user_email, pickup_location, dropoff_location, ride_price, driver_name, vehicle_info, ride_date, ride_status)
        VALUES (?,?,?,?,?,?,NOW(),'pending')";
$stmt = $conn->prepare($sql);

$empty = '';
$stmt->bind_param("sssdds", $userEmail, $pickupLocation, $dropoffLocation, $ridePrice, $empty, $empty);
$stmt->execute();
$stmt->close();
$conn->close();

$_SESSION['notification'] = 'Booking successful! Looking for a driver…';
header("Location: dashboard.php");
exit();
