<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

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

    // Corrected SQL statement: Select only 'password' and 'role'
    $sql = "SELECT password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];

            if (password_verify($password, $hashedPassword)) {
                // User is authenticated, store user info in session
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $row['role']; // Store the user's role

                // Redirect based on role
                if ($row['role'] === 'passenger') {
                    header("Location: dashboardPassenger.php");
                } elseif ($row['role'] === 'driver') {
                    // header("Location: dashboardDriver.php");
                    echo "🚗 Welcome, Driver!";
                }
                exit();
            } else {
                echo "❌ Incorrect password. Please try again.";
            }
        } else {
            echo "❌ Register first.";
        }
        $stmt->close();
    } else {
        echo "❌ Error preparing statement: " . $conn->error;
    }
    $conn->close();
}
?>