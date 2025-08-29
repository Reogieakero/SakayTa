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

// ✅ Update the ride status in the database
$userEmail = $_SESSION['user_email'];
$sql = "UPDATE rides SET ride_status = 'arrived_at_destination' WHERE user_email = ? AND ride_status = 'accepted' ORDER BY ride_date DESC LIMIT 1";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $stmt->close();
} else {
    echo "❌ Error preparing statement: " . $conn->error;
    $conn->close();
    exit();
}

// ✅ Update the session to reflect the new status
$_SESSION['ride_status'] = 'arrived_at_destination';
$_SESSION['notification'] = 'You have arrived at your destination! Please proceed to payment.';

$conn->close();

// Redirect back to the dashboard
header("Location: dashboard.php");
exit();
?>