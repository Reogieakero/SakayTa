<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_email']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    exit('Bad request');
}

require_once __DIR__ . '/db.php';

$userEmail = $_SESSION['user_email'];

// Random driver/vehicle
$drivers = [
    'John Dela Cruz' => 'Toyota Vios (WQR-567)',
    'Maria Santos'   => 'Honda City (XYZ-123)',
    'Peter Lim'      => 'Nissan Almera (ABC-456)',
];
$randomDriver = array_rand($drivers);
$vehicleInfo  = $drivers[$randomDriver];

$conn = db();

// Update ONLY the most recent pending ride
$sql = "UPDATE rides
        SET driver_name=?, vehicle_info=?, ride_status='driver_assigned'
        WHERE user_email=? AND ride_status='pending'
        ORDER BY ride_date DESC
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $randomDriver, $vehicleInfo, $userEmail);
$stmt->execute();

$stmt->close();
$conn->close();

$_SESSION['notification'] = 'Driver found! Please accept or decline.';
http_response_code(204); // no content
