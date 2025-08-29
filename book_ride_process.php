<?php
session_start();

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // You can retrieve pickup and dropoff locations from the form if you need them for a database.
    // $pickupLocation = $_POST['pickup'] ?? '';
    // $dropoffLocation = $_POST['dropoff'] ?? '';

    // Create arrays for random data
    $drivers = ["Sitoy Santos", "Juan dela Cruz", "Maria Alcantara", "Pedro Amada"];
    $vehicles = ["Bao-bao", "Pedicab", "Motor"];

    // Select a random driver
    $randomDriverKey = array_rand($drivers);
    $_SESSION['driver_name'] = $drivers[$randomDriverKey];

    // Select a random vehicle type and generate a random body number
    $randomVehicleKey = array_rand($vehicles);
    $bodyNumber = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $_SESSION['vehicle_info'] = $vehicles[$randomVehicleKey] . ' ' . $bodyNumber;

    // Generate a random ETA between 2 and 10 minutes
    $_SESSION['eta'] = rand(2, 10) . ' minutes';

    // Set a flag to indicate that a ride is now active
    $_SESSION['current_ride_active'] = true;

    // Redirect the user back to the dashboard
    header("Location: dashboard.php");
    exit();
} else {
    // If someone tries to access this page directly, redirect them away
    header("Location: dashboard.php");
    exit();
}
?>