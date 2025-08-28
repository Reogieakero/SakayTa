<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get values from form
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? '';

    // Validate inputs
    if (empty($email) || empty($password) || empty($role)) {
        die("❌ Please fill in all fields.");
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Database connection
    $conn = new mysqli("localhost", "root", "", "sakay_ta");

    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    }

    // Check if email already exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $checkStmt->close();
        $conn->close();
        die("⚠️ Email already registered. Please use another one.");
    }
    $checkStmt->close();

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $hashedPassword, $role);

    if ($stmt->execute()) {
        // ✅ Redirect to login page with success
        header("Location: login.html?success=1");
        exit();
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "⚠️ Invalid request method. Please submit the form.";
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
