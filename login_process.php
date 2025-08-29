<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // ✅ Connect to MySQL
    $servername = "localhost";
    $username   = "root";
    $dbpassword = "";
    $dbname     = "sakay_ta";

    $conn = new mysqli($servername, $username, $dbpassword, $dbname);

    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    }

    // ✅ Prepare SQL statement to find the user by email
    $sql = "SELECT password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User found, verify password
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];

            if (password_verify($password, $hashedPassword)) {
                echo "✅ You are a user for this website!";
            } else {
                echo "❌ Incorrect password. Please try again.";
            }
        } else {
            // User not found
            echo "❌ Register first.";
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