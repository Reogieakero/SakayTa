<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if a ride is not already active
    if (!isset($_SESSION['ride_status'])) {
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

        // Set the initial ride status to 'pending'
        $_SESSION['ride_status'] = 'pending';
    }

    // Redirect to the dashboard
    header("Location: dashboard.php");
    exit();
} else {
    header("Location: dashboard.php");
    exit();
}
?>