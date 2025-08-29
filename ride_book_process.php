<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['user_email']) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.html");
    exit();
}

echo "✅ Debug: Starting ride booking process.<br>";

$userEmail = $_SESSION['user_email'];
$pickupLocation = $_POST['pickup_location'] ?? 'Not specified';
$dropoffLocation = $_POST['dropoff_location'] ?? 'Not specified';
$ridePrice = $_POST['ride_price'] ?? '0';

$servername = "localhost";
$username   = "root";
$dbpassword = "";
$dbname     = "sakay_ta";

$conn = new mysqli($servername, $username, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

$drivers = [
    'John Dela Cruz' => 'Toyota Vios (WQR-567)',
    'Maria Santos'    => 'Honda City (XYZ-123)',
    'Peter Lim'       => 'Nissan Almera (ABC-456)'
];
$randomDriver = array_rand($drivers);
$vehicleInfo = $drivers[$randomDriver];

$sql = "INSERT INTO rides (user_email, pickup_location, dropoff_location, ride_price, driver_name, vehicle_info, ride_date, ride_status) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $rideStatus = 'accepted';
    $stmt->bind_param("sssssss", $userEmail, $pickupLocation, $dropoffLocation, $ridePrice, $randomDriver, $vehicleInfo, $rideStatus);
    $stmt->execute();
    
    echo "✅ Debug: Ride booked successfully with status 'accepted'.<br>";
    
    $stmt->close();
} else {
    echo "❌ Error preparing statement: " . $conn->error;
    $conn->close();
    exit();
}

$_SESSION['ride_status'] = $rideStatus;
$_SESSION['pickup_location'] = $pickupLocation;
$_SESSION['dropoff_location'] = $dropoffLocation;
$_SESSION['ride_price'] = $ridePrice;
$_SESSION['driver_name'] = $randomDriver;
$_SESSION['vehicle_info'] = $vehicleInfo;
$_SESSION['eta'] = '5 mins';
$_SESSION['notification'] = 'Your ride has been booked! Your driver is on the way.';

$conn->close();
header("Location: dashboard.php");
exit();
?>