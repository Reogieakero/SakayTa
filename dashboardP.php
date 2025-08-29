<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit();
}

$userName = $_SESSION['user_name'];

// Placeholder logic to check for rides
// You would replace this with actual database queries
$hasCurrentRide = false;
$hasRecentRides = false;

// If you have a 'rides' table, you would do something like this:
/*
$servername = "localhost";
$username = "root";
$dbpassword = "";
$dbname = "sakay_ta";

$conn = new mysqli($servername, $username, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sqlCurrent = "SELECT * FROM rides WHERE user_id = ? AND status = 'in_progress'";
$stmtCurrent = $conn->prepare($sqlCurrent);
$stmtCurrent->bind_param("i", $_SESSION['user_id']);
$stmtCurrent->execute();
$resultCurrent = $stmtCurrent->get_result();
$hasCurrentRide = $resultCurrent->num_rows > 0;
$stmtCurrent->close();

$sqlRecent = "SELECT * FROM rides WHERE user_id = ? AND status = 'completed'";
$stmtRecent = $conn->prepare($sqlRecent);
$stmtRecent->bind_param("i", $_SESSION['user_id']);
$stmtRecent->execute();
$resultRecent = $stmtRecent->get_result();
$hasRecentRides = $resultRecent->num_rows > 0;
$stmtRecent->close();

$conn->close();
*/

// Include the HTML content
include('dashboardPassenger.php');
?>