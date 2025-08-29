<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.html");
    exit();
}

$userEmail = $_SESSION['user_email'];
$userName = $_SESSION['user_name'];

// Check the current ride status
$rideStatus = $_SESSION['ride_status'] ?? 'none';
$ridePrice = $_SESSION['ride_price'] ?? '0';

// ✅ Connect to MySQL to fetch ride history
$servername = "localhost";
$username   = "root";
$dbpassword = "";
$dbname     = "sakay_ta";

$conn = new mysqli($servername, $username, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Fetch the user's recent rides from the database, ordered by most recent
$sql = "SELECT pickup_location, dropoff_location, ride_price, ride_date, driver_name, vehicle_info FROM rides WHERE user_email = ? ORDER BY ride_date DESC LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$rideHistory = [];
while ($row = $result->fetch_assoc()) {
    $rideHistory[] = $row;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Dashboard - Sakay Ta</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dashboard-styles.css">

</head>
<body>
    <?php
    if (isset($_SESSION['notification'])): ?>
        <div class="notification-success" id="notification">
            <p><?php echo htmlspecialchars($_SESSION['notification']); ?></p>
            <button class="close-btn" onclick="document.getElementById('notification').style.display='none';">&times;</button>
        </div>
    <?php
    unset($_SESSION['notification']);
    endif;
    ?>

    <header class="header">
        <div class="container">
            <div class="logo">
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                    <circle cx="20" cy="20" r="18" fill="#87CEEB" stroke="#4A90E2" stroke-width="2"/>
                    <path d="M12 16h16l-2 8H14l-2-8z" fill="#FF8C00"/>
                    <circle cx="16" cy="26" r="2" fill="#333"/>
                    <circle cx="24" cy="26" r="2" fill="#333"/>
                </svg>
                <span class="logo-text">Sakay Ta</span>
            </div>
            <div class="dashboard-header">
                <h1 class="dashboard-title">Passenger Dashboard</h1>
                <div class="user-menu">
                    <div class="user-avatar">
                        <span><?php echo htmlspecialchars(substr($userName, 0, 1) . substr(strstr($userName, ' '), 1, 1)); ?></span>
                    </div>
                    <button class="menu-toggle" onclick="toggleUserMenu()">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="#" class="dropdown-item">Profile</a>
                        <a href="#" class="dropdown-item">Settings</a>
                        <a href="index.html" class="dropdown-item">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="dashboard-main">
        <div class="dashboard-background"></div>
        <div class="container">
            <div class="dashboard-grid">
                <?php if ($rideStatus === 'none' || $rideStatus === 'declined' || $rideStatus === 'completed'): ?>
                <section class="dashboard-card book-ride-card" data-animate="slideUp">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M12 2L2 7v10c0 5.55 3.84 10 9 11 1.16.21 2.76.21 3.92 0C20.16 27 24 22.55 24 17V7l-10-5z" fill="currentColor"/>
                            </svg>
                        </div>
                        <h2>Book Your Ride</h2>
                    </div>
                    
                    <form class="booking-form" id="bookingForm" action="ride_book_process.php" method="POST">
                        <div class="form-group">
                            <label for="pickup">Pickup Location</label>
                            <div class="location-input">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" stroke="currentColor" stroke-width="2" fill="none"/>
                                    <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2" fill="none"/>
                                </svg>
                                <input type="text" id="pickup" placeholder="Enter pickup location" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="dropoff">Drop-off Location</label>
                            <div class="location-input">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" stroke="currentColor" stroke-width="2" fill="none"/>
                                    <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2" fill="none"/>
                                </svg>
                                <input type="text" id="dropoff" placeholder="Enter destination" required>
                            </div>
                        </div>
                        
                        <div class="ride-options">
                            <div class="option-card" data-price="150" data-type="economy">
                                <div class="option-info">
                                    <h4>₱150</h4>
                                    <p>Economy</p>
                                    <span>3-4 seats</span>
                                </div>
                            </div>
                            <div class="option-card active" data-price="220" data-type="premium">
                                <div class="option-info">
                                    <h4>₱220</h4>
                                    <p>Premium</p>
                                    <span>4-6 seats</span>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="ride_price" id="ridePriceInput" value="220">
                        
                        <button type="submit" class="btn btn-orange btn-full btn-animated">
                            <span>Book Now</span>
                            <div class="btn-loader"></div>
                        </button>
                    </form>
                </section>
                <?php endif; ?>

                <section class="dashboard-card account-card" data-animate="slideUp" data-delay="200">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="8" r="4" fill="currentColor"/>
                                <path d="M12 14c-4 0-8 2-8 6v2h16v-2c0-4-4-6-8-6z" fill="currentColor"/>
                            </svg>
                        </div>
                        <h2>My Account</h2>
                    </div>
                    
                    <div class="user-profile">
                        <div class="profile-avatar">
                            <span><?php echo htmlspecialchars(substr($userName, 0, 1) . substr(strstr($userName, ' '), 1, 1)); ?></span>
                        </div>
                        <div class="profile-info">
                            <h3><?php echo htmlspecialchars($userName); ?></h3>
                            <p><?php echo htmlspecialchars($userEmail); ?></p>
                        </div>
                    </div>
                    
                    <div class="account-actions">
                        <button class="action-btn" onclick="showModal('payment')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2" stroke="currentColor" stroke-width="2" fill="none"/>
                                <line x1="1" y1="10" x2="23" y2="10" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            Payment Methods
                        </button>
                        <button class="action-btn" onclick="showModal('history')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                <polyline points="12,6 12,12 16,14" stroke="currentColor" stroke-width="2" fill="none"/>
                            </svg>
                            Ride History
                        </button>
                        <button class="action-btn" onclick="showModal('preferences')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" fill="none"/>
                                <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" stroke="currentColor" stroke-width="2" fill="none"/>
                            </svg>
                            Preferences
                        </button>
                        <button class="action-btn" onclick="showModal('support')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" fill="none"/>
                                <path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z" stroke="currentColor" stroke-width="2" fill="none"/>
                            </svg>
                            Support
                        </button>
                    </div>
                    
                    <button class="btn btn-primary btn-full">Settings</button>
                </section>

                <?php if ($rideStatus === 'accepted'): ?>
                    <section class="dashboard-card current-ride-card" data-animate="slideUp" data-delay="400">
                        <div class="card-header">
                            <div class="card-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                    <polyline points="12,6 12,12 16,14" stroke="currentColor" stroke-width="2" fill="none"/>
                                </svg>
                            </div>
                            <h2>Current Ride</h2>
                        </div>
                        
                        <div class="ride-status">
                            <div class="status-badge driver-en-route">Driver En Route</div>
                            <div class="ride-details">
                                <div class="detail-row">
                                    <span class="label">Driver</span>
                                    <div class="driver-info">
                                        <span class="driver-name"><?php echo htmlspecialchars($_SESSION['driver_name']); ?></span>
                                        <div class="rating">
                                            <span>4.8</span>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="#FFD700">
                                                <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="label">Vehicle</span>
                                    <span class="value"><?php echo htmlspecialchars($_SESSION['vehicle_info']); ?></span>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="label">ETA</span>
                                    <span class="value eta-time"><?php echo htmlspecialchars($_SESSION['eta']); ?></span>
                                </div>
                            </div>
                            <div class="ride-actions">
                                <form action="ride_arrived_process.php" method="POST">
                                    <button type="submit" class="btn btn-primary btn-full">I have arrived</button>
                                </form>
                            </div>
                        </div>
                    </section>
                <?php elseif ($rideStatus === 'arrived_at_destination'): ?>
                    <section class="dashboard-card current-ride-card" data-animate="slideUp" data-delay="400">
                        <div class="card-header">
                            <div class="card-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                    <polyline points="12,6 12,12 16,14" stroke="currentColor" stroke-width="2" fill="none"/>
                                </svg>
                            </div>
                            <h2>Current Ride</h2>
                        </div>
                        
                        <div class="ride-status">
                            <div class="status-badge driver-arrived">Arrived at Destination</div>
                            <div class="ride-details">
                                <div class="detail-row">
                                    <span class="label">Driver</span>
                                    <div class="driver-info">
                                        <span class="driver-name"><?php echo htmlspecialchars($_SESSION['driver_name']); ?></span>
                                        <div class="rating">
                                            <span>4.8</span>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="#FFD700">
                                                <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="detail-row">
                                    <span class="label">Vehicle</span>
                                    <span class="value"><?php echo htmlspecialchars($_SESSION['vehicle_info']); ?></span>
                                </div>
                                
                                <p>You have arrived at your destination!</p>
                                <p>Total Bill: <strong>₱<?php echo htmlspecialchars($ridePrice); ?></strong></p>
                            </div>
                            <div class="ride-actions full-width">
                                <form action="ride_completed_process.php" method="POST">
                                    <button type="submit" class="btn btn-primary btn-full">Pay Bill</button>
                                </form>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <section class="dashboard-card recent-rides-card" data-animate="slideUp" data-delay="600">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2" stroke="currentColor" stroke-width="2" fill="none"/>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1" stroke="currentColor" stroke-width="2" fill="none"/>
                            </svg>
                        </div>
                        <h2>Recent Rides</h2>
                    </div>
                    
                    <div class="rides-list">
                        <?php if (!empty($rideHistory)): ?>
                            <?php foreach ($rideHistory as $ride): ?>
                                <div class="ride-item">
                                    <div class="ride-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                                            <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" fill="none"/>
                                        </svg>
                                    </div>
                                    <div class="ride-info">
                                        <div class="ride-route"><?php echo htmlspecialchars($ride['pickup_location']); ?> → <?php echo htmlspecialchars($ride['dropoff_location']); ?></div>
                                        <div class="ride-date"><?php echo htmlspecialchars(date('F j, Y • g:i A', strtotime($ride['ride_date']))); ?></div>
                                        <div class="driver-info-list">
                                            <span>Driver: <?php echo htmlspecialchars($ride['driver_name']); ?></span>
                                            <span>Vehicle: <?php echo htmlspecialchars($ride['vehicle_info']); ?></span>
                                        </div>
                                    </div>
                                    <div class="ride-price">
                                        <span class="price">₱<?php echo htmlspecialchars(number_format($ride['ride_price'], 2)); ?></span>
                                        <div class="rating">
                                            <span>4.8</span>
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="#FFD700">
                                                <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No recent rides found.</p>
                        <?php endif; ?>
                    </div>
                    
                    <button class="btn btn-outline btn-full" onclick="showAllRides()">View All Rides</button>
                </section>
            </div>
        </div>
    </main>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <p>Finding your ride...</p>
    </div>
    <script src="dashboard-script.js"></script>
    <script>
        document.getElementById('bookingForm').addEventListener('submit', function(event) {
            event.preventDefault();
            document.getElementById('loadingOverlay').classList.add('active');
            const randomDelay = Math.random() * 5000 + 5000;
            setTimeout(() => {
                this.submit();
            }, randomDelay);
        });

        // ✅ New JavaScript to handle price selection
        document.addEventListener('DOMContentLoaded', function() {
            const optionCards = document.querySelectorAll('.option-card');
            const ridePriceInput = document.getElementById('ridePriceInput');

            optionCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove 'active' class from all cards
                    optionCards.forEach(c => c.classList.remove('active'));
                    
                    // Add 'active' class to the clicked card
                    this.classList.add('active');
                    
                    // Update the hidden input field with the selected price
                    ridePriceInput.value = this.dataset.price;
                });
            });
        });
    </script>
</body>
</html>