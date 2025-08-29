<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit();
}

require_once __DIR__ . '/db.php';

$userEmail = $_SESSION['user_email'];

$conn = db();
$sql = "UPDATE rides
        SET ride_status='completed'
        WHERE user_email=? AND ride_status='arrived_at_destination'
        ORDER BY ride_date DESC
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$stmt->close();
$conn->close();

$_SESSION['notification'] = 'Payment successful! Trip saved to history.';
header("Location: dashboard.php");
exit();
