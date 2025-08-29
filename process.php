<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form values
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Print values directly (for testing)
    echo "<h2>📌 Data Received:</h2>";
    echo "Email: " . htmlspecialchars($email) . "<br>";
    echo "Password: " . htmlspecialchars($password) . "<br>"; // (don’t show in production)
    echo "Role: " . htmlspecialchars($role) . "<br>";

    // Later, you can insert into DB after confirming values
} else {
    echo "⚠️ Please submit the form.";
}
?>
