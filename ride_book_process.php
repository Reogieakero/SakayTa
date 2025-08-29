<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if a ride is not already active
    if (!isset($_SESSION['ride_status']) || $_SESSION['ride_status'] === 'completed' || $_SESSION['ride_status'] === 'declined') {
        // Create arrays for random data
        $drivers = ["Sitoy Santos", "Juan dela Cruz", "Maria Alcantara", "Pedro Amada"];
        $vehicles = ["Bao-bao", "Pedicab", "Motor"];
        $ridePrices = ["150", "220"];

        // Select random data
        $randomDriverKey = array_rand($drivers);
        $_SESSION['driver_name'] = $drivers[$randomDriverKey];

        $randomVehicleKey = array_rand($vehicles);
        $bodyNumber = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $_SESSION['vehicle_info'] = $vehicles[$randomVehicleKey] . ' ' . $bodyNumber;

        // Generate a random ETA between 2 and 10 minutes
        $_SESSION['eta'] = rand(2, 10) . ' minutes';
        
        // Select a random price for the ride
        $randomPriceKey = array_rand($ridePrices);
        $_SESSION['ride_price'] = $ridePrices[$randomPriceKey];

        // Set the initial ride status to 'accepted'
        $_SESSION['ride_status'] = 'accepted';
    }

    // Redirect to the dashboard
    header("Location: dashboard.php");
    exit();
} else {
    header("Location: dashboard.php");
    exit();
}
?>