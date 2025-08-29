<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_email']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.html");
    exit();
}

require_once __DIR__ . '/db.php';

$action = $_POST['action'] ?? '';
$userEmail = $_SESSION['user_email'];

if (!in_array($action, ['accept','decline'], true)) {
    header("Location: dashboard.php");
    exit();
}

$conn = db();

if ($action === 'accept') {
    $sql = "UPDATE rides
            SET ride_status='accepted'
            WHERE user_email=? AND ride_status='driver_assigned'
            ORDER BY ride_date DESC
            LIMIT 1";
    $_SESSION['notification'] = 'Ride accepted! Driver is on the way.';
} else { // decline
    $sql = "UPDATE rides
            SET ride_status='declined'
            WHERE user_email=? AND ride_status='driver_assigned'
            ORDER BY ride_date DESC
            LIMIT 1";
    $_SESSION['notification'] = 'Ride declined. You can book again.';
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: dashboard.php");
exit();
