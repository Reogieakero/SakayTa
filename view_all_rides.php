<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit();
}

$userEmail = $_SESSION['user_email'];

// ✅ Connect to the database to fetch ALL completed rides
$servername = "localhost";
$username   = "root";
$dbpassword = "";
$dbname     = "sakay_ta";

$conn = new mysqli($servername, $username, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// ✅ FIX: Query for ALL completed rides for the user, including driver and vehicle info
$sql = "SELECT pickup_location, dropoff_location, ride_price, driver_name, vehicle_info, ride_date FROM rides WHERE user_email = ? AND ride_status = 'completed' ORDER BY ride_date DESC";
$stmt = $conn->prepare($sql);
$allRides = [];

if ($stmt) {
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $allRides[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ride History - Sakay Ta</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dashboard-styles.css">
    <style>
        .page-header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 15px;
            margin-bottom: 20px;
        }

        .back-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
        }

        .back-btn svg {
            color: var(--color-text-light);
            transition: color 0.3s ease;
        }

        .back-btn:hover svg {
            color: var(--color-primary);
        }
    </style>
</head>
<body>
    <main class="dashboard-main">
        <div class="dashboard-background"></div>
        <div class="container">
            <div class="page-header">
                <a href="dashboard.php" class="back-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <h1>All Ride History</h1>
            </div>
            
            <section class="dashboard-card recent-rides-card" style="max-width: 600px; margin: 0 auto;">
                <div class="card-header">
                    <div class="card-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2" stroke="currentColor" stroke-width="2" fill="none"/>
                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                    </div>
                    <h2>Your Complete Ride History</h2>
                </div>
                
                <div class="rides-list">
                    <?php if (count($allRides) > 0): ?>
                        <?php foreach ($allRides as $ride): ?>
                            <div class="ride-item">
                                <div class="ride-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                        <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" fill="none"/>
                                    </svg>
                                </div>
                                <div class="ride-info">
                                    <div class="ride-route">
                                        <b><?php echo htmlspecialchars($ride['pickup_location']); ?> → <?php echo htmlspecialchars($ride['dropoff_location']); ?></b>
                                    </div>
                                    <div class="ride-details">
                                        <span>Driver: <?php echo htmlspecialchars($ride['driver_name']); ?></span>
                                        <span>Vehicle: <?php echo htmlspecialchars($ride['vehicle_info']); ?></span>
                                    </div>
                                    <div class="ride-date">
                                        <?php echo date("F d, Y • h:i A", strtotime($ride['ride_date'])); ?>
                                    </div>
                                </div>
                                <div class="ride-price">
                                    <span class="price">₱<?php echo htmlspecialchars($ride['ride_price']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No ride history found.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
</body>
</html>