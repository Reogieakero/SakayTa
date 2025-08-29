<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Redirect if the user is not logged in or the request is not a POST request
if (!isset($_SESSION['user_email']) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.html");
    exit();
}

// ✅ Retrieve user details and booking information from the POST data and session
$userEmail = $_SESSION['user_email'];
$pickupLocation = $_POST['pickup_location'] ?? 'Not specified';
$dropoffLocation = $_POST['dropoff_location'] ?? 'Not specified';
$ridePrice = $_POST['ride_price'] ?? '0';

// ✅ Connect to MySQL database to save the ride information
$servername = "localhost";
$username   = "root";
$dbpassword = "";
$dbname     = "sakay_ta";

$conn = new mysqli($servername, $username, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Generate a random driver and vehicle for demonstration purposes
$drivers = [
    'John Dela Cruz' => 'Toyota Vios (WQR-567)',
    'Maria Santos'    => 'Honda City (XYZ-123)',
    'Peter Lim'       => 'Nissan Almera (ABC-456)'
];
$randomDriver = array_rand($drivers);
$vehicleInfo = $drivers[$randomDriver];

// Prepare the SQL statement to insert the new ride into the 'rides' table
$sql = "INSERT INTO rides (user_email, pickup_location, dropoff_location, ride_price, driver_name, vehicle_info, ride_date, ride_status) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    // ✅ FIX: The 'ssssssd' string now correctly matches the seven variables being bound.
    $rideStatus = 'accepted';
    $stmt->bind_param("ssssssd", $userEmail, $pickupLocation, $dropoffLocation, $ridePrice, $randomDriver, $vehicleInfo, $rideStatus);
    $stmt->execute();
    $stmt->close();
} else {
    // Handle SQL preparation error
    echo "❌ Error preparing statement: " . $conn->error;
    $conn->close();
    exit();
}

// ✅ Store ride details in session for `dashboard.php` to display
$_SESSION['ride_status'] = $rideStatus;
$_SESSION['pickup_location'] = $pickupLocation;
$_SESSION['dropoff_location'] = $dropoffLocation;
$_SESSION['ride_price'] = $ridePrice;
$_SESSION['driver_name'] = $randomDriver;
$_SESSION['vehicle_info'] = $vehicleInfo;
$_SESSION['eta'] = '5 mins';
$_SESSION['notification'] = 'Your ride has been booked! Your driver is on the way.';

// Close the database connection
$conn->close();

// Redirect back to the dashboard to show the current ride status
header("Location: dashboard.php");
exit();
?>