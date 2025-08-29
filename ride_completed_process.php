<?php
// Show all PHP errors (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username   = "root";  // default XAMPP username
$password   = "";      // default XAMPP password is empty
$dbname     = "sakayta"; // change if your DB has a different name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Process only if POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get user email from form
    $userEmail = $_POST['email'] ?? '';

    if (empty($userEmail)) {
        echo "⚠️ Email is required.";
        exit;
    }

    // 1️⃣ Find the latest ride with status 'arrived_at_destination'
    $sql = "SELECT ride_id 
            FROM rides 
            WHERE user_email = ? AND ride_status = 'arrived_at_destination' 
            ORDER BY ride_date DESC 
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($ride = $result->fetch_assoc()) {
        $rideId = $ride['ride_id'];

        // 2️⃣ Update the ride to 'completed'
        $sql = "UPDATE rides SET ride_status = 'completed' WHERE ride_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $rideId);

        if ($stmt->execute()) {
            echo "✅ Ride with ID <b>$rideId</b> for user <b>$userEmail</b> marked as <b>completed</b>!";
        } else {
            echo "❌ Error updating ride: " . $stmt->error;
        }
    } else {
        echo "⚠️ No ride found with status 'arrived_at_destination' for $userEmail.";
    }

    $stmt->close();
} else {
    echo "⚠️ Invalid request method. Please submit the form.";
}

$conn->close();
?>
