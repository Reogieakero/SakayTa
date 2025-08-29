<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form values
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // ✅ Connect to MySQL
    $servername = "localhost";
    $username   = "root";      // default XAMPP username
    $dbpassword = "";          // default XAMPP password is empty
    $dbname     = "sakay_ta";  // make sure this DB exists

    $conn = new mysqli($servername, $username, $dbpassword, $dbname);

    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    }

    // ✅ Hash the password (good practice)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Insert into table
    $sql = "INSERT INTO users (email, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sss", $email, $hashedPassword, $role);
        if ($stmt->execute()) {
            // Redirect to the login page after successful registration
            header("Location: login.html");
            exit();
        } else {
            echo "❌ Error inserting data: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "❌ SQL prepare failed: " . $conn->error;
    }

    $conn->close();
} else {
    echo "⚠️ Please submit the form.";
}
?>