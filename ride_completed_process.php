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

// ✅ Update the ride status in the database to 'completed'
$userEmail = $_SESSION['email'];
$sql = "UPDATE rides SET ride_status = 'completed' WHERE email = ? AND ride_status = 'arrived_at_destination' ORDER BY ride_date DESC LIMIT 1";
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

// Unset all ride-related session variables
unset($_SESSION['ride_status']);
unset($_SESSION['pickup_location']);
unset($_SESSION['dropoff_location']);
unset($_SESSION['ride_price']);
unset($_SESSION['driver_name']);
unset($_SESSION['vehicle_info']);

// ✅ Redirect to the dashboard with a success notification
$_SESSION['notification'] = 'Payment successful! Your trip has been added to your ride history.';
header("Location: dashboard.php");
exit();
?>