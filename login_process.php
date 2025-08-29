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

    // Corrected SQL statement: Select 'password', 'role', AND 'name'
    $sql = "SELECT password, role, name FROM users WHERE email = ?";
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
                $_SESSION['user_role'] = $row['role'];
                $_SESSION['user_name'] = $row['name']; // Store the user's name

                // Redirect based on role
                if ($row['role'] === 'passenger') {
                    header("Location: dashboard.html");
                } elseif ($row['role'] === 'driver') {
                    header("Location: dashboardDriver.php");
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