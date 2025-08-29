<?php
session_start();

// Check if a ride is in progress and the button was clicked
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['ride_status']) && $_SESSION['ride_status'] === 'accepted') {

    // ✅ Connect to MySQL
    $servername = "localhost";
    $username   = "root";
    $dbpassword = "";
    $dbname     = "sakay_ta";

    $conn = new mysqli($servername, $username, $dbpassword, $dbname);

    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    }

    // Get ride data from session
    $userEmail       = $_SESSION['user_email'];
    $pickupLocation  = $_SESSION['pickup_location'] ?? 'Unknown';
    $dropoffLocation = $_SESSION['dropoff_location'] ?? 'Unknown';
    $ridePrice       = $_SESSION['ride_price'] ?? 0;
    $driverName      = $_SESSION['driver_name'] ?? 'Unknown';
    $vehicleInfo     = $_SESSION['vehicle_info'] ?? 'Unknown';

    // Prepare an insert statement to save ride details to the database
    $sql = "INSERT INTO rides (user_email, pickup_location, dropoff_location, ride_price, driver_name, vehicle_info) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdss", $userEmail, $pickupLocation, $dropoffLocation, $ridePrice, $driverName, $vehicleInfo);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    // Set ride status to 'arrived_at_destination' to trigger the "Pay Bill" card
    $_SESSION['ride_status'] = 'arrived_at_destination';

    // Redirect back to the dashboard
    header("Location: dashboard.php");
    exit();
} else {
    // If accessed directly or no ride is active, redirect to dashboard
    header("Location: dashboard.php");
    exit();
}
?>