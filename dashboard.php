<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit();
}

$userEmail = $_SESSION['user_email'];
$userName = $_SESSION['user_name'];

// Check the current ride status from the session. If not set, it's a new user.
$rideStatus = $_SESSION['ride_status'] ?? 'none';
$ridePrice = $_SESSION['ride_price'] ?? '0';

// Connect to the database
$servername = "localhost";
$username   = "root";
$dbpassword = "";
$dbname     = "sakay_ta";

$conn = new mysqli($servername, $username, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Query for the 3 most recent completed rides, including driver and vehicle info
$sql = "SELECT pickup_location, dropoff_location, ride_price, driver_name, vehicle_info, ride_date FROM rides WHERE user_email = ? AND ride_status = 'completed' ORDER BY ride_date DESC LIMIT 3";
$stmt = $conn->prepare($sql);
$rides = [];

if ($stmt) {
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $rides[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SakayTa - Dashboard</title>
    <link rel="stylesheet" href="dashboard-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <header class="dashboard-header">
        <div class="user-info-container">
            <h1 class="dashboard-title">SakayTa</h1>
            <div class="user-menu">
                <div class="user-avatar">
                    <img src="placeholder.svg" alt="User Avatar">
                </div>
                <div class="user-details">
                    <div class="user-name">Hello, <?php echo htmlspecialchars($userName); ?></div>
                    <a href="logout.php" class="logout-link">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <main class="dashboard-main">
        <div class="dashboard-container">
            <?php if (isset($_SESSION['notification'])): ?>
                <div class="notification-success" id="notification-banner">
                    <?php echo $_SESSION['notification']; ?>
                </div>
                <?php unset($_SESSION['notification']); ?>
            <?php endif; ?>

            <?php if ($rideStatus !== 'none' && $rideStatus !== 'completed'): ?>
                <section class="current-ride-section">
                    <div class="section-header">
                        <h2>Current Trip</h2>
                    </div>
                    <div class="ride-details-card">
                        <h3><?php echo htmlspecialchars($_SESSION['notification']); ?></h3>
                        <div class="details-row">
                            <span>Driver:</span>
                            <b><?php echo htmlspecialchars($_SESSION['driver_name']); ?></b>
                        </div>
                        <div class="details-row">
                            <span>Vehicle:</span>
                            <b><?php echo htmlspecialchars($_SESSION['vehicle_info']); ?></b>
                        </div>
                        <div class="details-row">
                            <span>ETA:</span>
                            <b><?php echo htmlspecialchars($_SESSION['eta']); ?></b>
                        </div>
                        <div class="details-row">
                            <span>Price:</span>
                            <b>₱<?php echo htmlspecialchars($ridePrice); ?></b>
                        </div>
                        <div class="current-ride-buttons">
                            <?php if ($rideStatus === 'accepted'): ?>
                                <form action="ride_arrived_process.php" method="POST" style="width: 100%;">
                                    <button type="submit" class="button arrive-button">I have Arrived</button>
                                </form>
                            <?php elseif ($rideStatus === 'arrived_at_destination'): ?>
                                <form action="ride_completed_process.php" method="POST" style="width: 100%;">
                                    <button type="submit" class="button pay-button">Pay Bill</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            <?php else: ?>
                <section class="booking-section">
                    <div class="section-header">
                        <h2>Book a Ride</h2>
                    </div>
                    <form action="ride_book_process.php" method="POST" class="booking-form" id="bookingForm">
                        <div class="form-group">
                            <label for="pickup_location">Pickup Location</label>
                            <input type="text" id="pickup_location" name="pickup_location" placeholder="Enter pickup location" required>
                        </div>
                        <div class="form-group">
                            <label for="dropoff_location">Dropoff Location</label>
                            <input type="text" id="dropoff_location" name="dropoff_location" placeholder="Enter dropoff location" required>
                        </div>

                        <div class="ride-options">
                            <div class="option-card" data-price="150.00">
                                <div class="icon-placeholder">🚗</div>
                                <div>
                                    <h4>Standard</h4>
                                    <p>Economical ride for daily commutes</p>
                                </div>
                                <span class="price">₱150</span>
                            </div>
                            <div class="option-card active" data-price="250.00">
                                <div class="icon-placeholder">🚕</div>
                                <div>
                                    <h4>Premium</h4>
                                    <p>More space and comfort</p>
                                </div>
                                <span class="price">₱250</span>
                            </div>
                            <div class="option-card" data-price="350.00">
                                <div class="icon-placeholder">🚐</div>
                                <div>
                                    <h4>Van</h4>
                                    <p>Ride with a group of friends</p>
                                </div>
                                <span class="price">₱350</span>
                            </div>
                        </div>

                        <input type="hidden" name="ride_price" id="ridePriceInput" value="250.00">
                        <button type="submit" class="button book-button">Book Now</button>
                    </form>
                </section>
            <?php endif; ?>

            <section class="recent-rides-section">
                <div class="section-header">
                    <h2>Recent Rides</h2>
                    <a href="view_all_rides.php" class="view-all-link">View All</a>
                </div>
                <div class="ride-history-list">
                    <?php if (!empty($rides)): ?>
                        <?php foreach ($rides as $ride): ?>
                            <div class="ride-card">
                                <div class="status-icon completed-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-car-taxi-front"><path d="M12 2h4l3 3v5c0 1.1.9 2 2 2h0c1.1 0 2 .9 2 2v2a2 2 0 0 1-2 2h-1a2 2 0 0 0-2 2v2a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-2a2 2 0 0 0-2-2H2a2 2 0 0 1-2-2v-2c0-1.1.9-2 2-2h0c1.1 0 2-.9 2-2V5l3-3h4Z"/><path d="M7 16v-2a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2"/><path d="M5 5h14"/><path d="M6 9h12"/></svg>
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
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <p>Finding a driver...</p>
    </div>

    <script src="dashboard-script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bookingForm = document.getElementById('bookingForm');
            const rideOptionCards = document.querySelectorAll('.ride-options .option-card');
            const ridePriceInput = document.getElementById('ridePriceInput');
            const loadingOverlay = document.getElementById('loadingOverlay');

            // Handle card selection
            rideOptionCards.forEach(card => {
                card.addEventListener('click', () => {
                    // Remove 'active' class from all cards
                    rideOptionCards.forEach(c => c.classList.remove('active'));
                    // Add 'active' class to the clicked card
                    card.classList.add('active');
                    // Update the hidden input value with the data-price attribute
                    ridePriceInput.value = card.dataset.price;
                });
            });

            // Handle form submission with loading overlay
            if (bookingForm) {
                bookingForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    loadingOverlay.classList.add('active');
                    
                    // Simulate a delay before submitting the form
                    const randomDelay = Math.random() * 5000 + 5000;
                    setTimeout(() => {
                        this.submit();
                    }, randomDelay);
                });
            }
        });

        // Automatically trigger the 'arrived' and 'completed' processes
        window.addEventListener('load', () => {
            const currentRideStatus = '<?php echo $rideStatus; ?>';

            if (currentRideStatus === 'accepted') {
                setTimeout(() => {
                    const arrivedForm = document.querySelector('form[action="ride_arrived_process.php"]');
                    if (arrivedForm) {
                        arrivedForm.submit();
                    }
                }, 1000);
            }

            if (currentRideStatus === 'arrived_at_destination') {
                setTimeout(() => {
                    const payBillForm = document.querySelector('form[action="ride_completed_process.php"]');
                    if (payBillForm) {
                        payBillForm.submit();
                    }
                }, 1000);
            }
        });
    </script>
</body>
</html>