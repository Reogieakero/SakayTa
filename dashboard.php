<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// For demo: redirect to login if these aren’t set
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_name'])) {
    header("Location: login.html");
    exit();
}

require_once __DIR__ . '/db.php';

$userEmail = $_SESSION['user_email'];
$userName  = $_SESSION['user_name'];

// Pull latest ride status for this user from DB (authoritative)
$rideStatus = 'none';
$ridePrice = '0.00';
$driverName = '';
$vehicleInfo = '';
$eta = '5–7 min';
$pickup = '';
$dropoff = '';

$conn = db();

// latest ride (any status) just to show current
$sql = "SELECT id, pickup_location, dropoff_location, ride_price, driver_name, vehicle_info, ride_date, ride_status
        FROM rides
        WHERE user_email=?
        ORDER BY ride_date DESC
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$res = $stmt->get_result();
$currentRide = $res->fetch_assoc();
$stmt->close();

if ($currentRide) {
    $rideStatus = $currentRide['ride_status'];
    $ridePrice  = $currentRide['ride_price'];
    $driverName = $currentRide['driver_name'] ?? '';
    $vehicleInfo = $currentRide['vehicle_info'] ?? '';
    $pickup = $currentRide['pickup_location'] ?? '';
    $dropoff = $currentRide['dropoff_location'] ?? '';
}

// fetch 3 most recent completed rides
$sql = "SELECT pickup_location, dropoff_location, ride_price, driver_name, vehicle_info, ride_date
        FROM rides
        WHERE user_email=? AND ride_status='completed'
        ORDER BY ride_date DESC
        LIMIT 3";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$ridesRes = $stmt->get_result();
$recentRides = $ridesRes->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

// for avatar initials
function initials($name) {
    $parts = preg_split('/\s+/', trim($name));
    $first = mb_substr($parts[0] ?? '', 0, 1);
    $second = mb_substr($parts[1] ?? '', 0, 1);
    return strtoupper($first . $second);
}
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
<?php if (isset($_SESSION['notification'])): ?>
  <div class="notification-success" id="notification">
    <p><?= htmlspecialchars($_SESSION['notification']); ?></p>
    <button class="close-btn" onclick="document.getElementById('notification').style.display='none';">&times;</button>
  </div>
  <?php unset($_SESSION['notification']); ?>
<?php endif; ?>

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
        <div class="user-avatar"><span><?= htmlspecialchars(initials($userName)); ?></span></div>
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

      <?php if (in_array($rideStatus, ['none','declined','completed'])): ?>
      <!-- Book a Ride -->
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
              <input type="text" id="pickup" name="pickup_location" placeholder="Enter pickup location" required>
            </div>
          </div>

          <div class="form-group">
            <label for="dropoff">Drop-off Location</label>
            <div class="location-input">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z" stroke="currentColor" stroke-width="2" fill="none"/>
                <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2" fill="none"/>
              </svg>
              <input type="text" id="dropoff" name="dropoff_location" placeholder="Enter destination" required>
            </div>
          </div>

          <div class="ride-options">
            <div class="option-card" data-price="150" data-type="economy">
              <div class="option-info">
                <h4>₱150</h4><p>Economy</p><span>3–4 seats</span>
              </div>
            </div>
            <div class="option-card active" data-price="220" data-type="premium">
              <div class="option-info">
                <h4>₱220</h4><p>Premium</p><span>4–6 seats</span>
              </div>
            </div>
          </div>

          <input type="hidden" id="ridePriceInput" name="ride_price" value="220">

          <button type="submit" class="btn btn-orange btn-full btn-animated">
            <span>Book Now</span>
            <div class="btn-loader"></div>
          </button>
        </form>
      </section>
      <?php endif; ?>

      <!-- My Account -->
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
          <div class="profile-avatar"><span><?= htmlspecialchars(initials($userName)); ?></span></div>
          <div class="profile-info">
            <h3><?= htmlspecialchars($userName); ?></h3>
            <p><?= htmlspecialchars($userEmail); ?></p>
          </div>
        </div>
        <div class="account-actions">
          <button class="action-btn"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"><rect x="1" y="4" width="22" height="16" rx="2" ry="2" stroke="currentColor" stroke-width="2" fill="none"/><line x1="1" y1="10" x2="23" y2="10" stroke="currentColor" stroke-width="2"/></svg>Payment Methods</button>
          <button class="action-btn"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/><polyline points="12,6 12,12 16,14" stroke="currentColor" stroke-width="2" fill="none"/></svg>Ride History</button>
          <button class="action-btn"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" fill="none"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" stroke="currentColor" stroke-width="2" fill="none"/></svg>Preferences</button>
          <button class="action-btn"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" fill="none"/><path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z" stroke="currentColor" stroke-width="2" fill="none"/></svg>Support</button>
        </div>
        <button class="btn btn-primary btn-full">Settings</button>
      </section>

      <!-- Current Ride / States -->
      <?php if ($rideStatus === 'pending'): ?>
        <section class="dashboard-card current-ride-card" data-animate="slideUp" data-delay="300">
          <div class="card-header">
            <div class="card-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" fill="none"/>
              </svg>
            </div>
            <h2>Finding your ride…</h2>
          </div>
          <p>Looking for a nearby driver for <b><?= htmlspecialchars($pickup); ?></b> → <b><?= htmlspecialchars($dropoff); ?></b></p>
        </section>
      <?php elseif ($rideStatus === 'driver_assigned'): ?>
        <section class="dashboard-card current-ride-card" data-animate="slideUp" data-delay="300">
          <div class="card-header">
            <div class="card-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" fill="none"/>
              </svg>
            </div>
            <h2>Driver Found</h2>
          </div>
          <div class="ride-details">
            <div class="detail-row"><span class="label">Driver</span><span class="value"><?= htmlspecialchars($driverName); ?></span></div>
            <div class="detail-row"><span class="label">Vehicle</span><span class="value"><?= htmlspecialchars($vehicleInfo); ?></span></div>
            <div class="detail-row"><span class="label">ETA</span><span class="value"><?= htmlspecialchars($eta); ?></span></div>
          </div>
          <form action="ride_action.php" method="POST" class="ride-actions" style="display:flex; gap:.5rem;">
            <input type="hidden" name="action" value="accept">
            <button class="btn btn-primary">Accept</button>
          </form>
          <form action="ride_action.php" method="POST" class="ride-actions" style="display:flex; gap:.5rem;">
            <input type="hidden" name="action" value="decline">
            <button class="btn btn-outline">Decline</button>
          </form>
        </section>
      <?php elseif ($rideStatus === 'accepted'): ?>
        <section class="dashboard-card current-ride-card" data-animate="slideUp" data-delay="300">
          <div class="card-header">
            <div class="card-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
              </svg>
            </div>
            <h2>Current Ride</h2>
          </div>
          <div class="ride-status">
            <div class="status-badge driver-en-route">Driver En Route</div>
            <div class="ride-details">
              <div class="detail-row"><span class="label">Driver</span><span class="value"><?= htmlspecialchars($driverName); ?></span></div>
              <div class="detail-row"><span class="label">Vehicle</span><span class="value"><?= htmlspecialchars($vehicleInfo); ?></span></div>
              <div class="detail-row"><span class="label">ETA</span><span class="value"><?= htmlspecialchars($eta); ?></span></div>
              <div class="detail-row"><span class="label">Fare</span><span class="value">₱<?= htmlspecialchars(number_format((float)$ridePrice, 2)); ?></span></div>
            </div>
            <div class="ride-actions full-width">
              <form action="ride_arrived_process.php" method="POST">
                <button id="arrivedBtn" type="submit" class="btn btn-primary btn-full" disabled>I have arrived</button>
              </form>
              <p id="arrivedHint" style="opacity:.8; font-size:.9rem; margin-top:.25rem;">Activating button in a moment…</p>
            </div>
          </div>
        </section>
      <?php elseif ($rideStatus === 'arrived_at_destination'): ?>
        <section class="dashboard-card current-ride-card" data-animate="slideUp" data-delay="300">
          <div class="card-header">
            <div class="card-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" fill="none"/>
              </svg>
            </div>
            <h2>Arrived at Destination</h2>
          </div>
          <div class="ride-details">
            <p>You have arrived! Total Bill: <strong>₱<?= htmlspecialchars(number_format((float)$ridePrice, 2)); ?></strong></p>
          </div>
          <div class="ride-actions full-width">
            <form action="ride_completed_process.php" method="POST">
              <button type="submit" class="btn btn-primary btn-full">Pay Bill</button>
            </form>
          </div>
        </section>
      <?php endif; ?>

      <!-- Recent Rides -->
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
          <?php if (!empty($recentRides)): ?>
            <?php foreach ($recentRides as $ride): ?>
              <div class="ride-item">
                <div class="ride-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                    <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" fill="none"/>
                  </svg>
                </div>
                <div class="ride-info">
                  <div class="ride-route">
                    <b><?= htmlspecialchars($ride['pickup_location']); ?> → <?= htmlspecialchars($ride['dropoff_location']); ?></b>
                  </div>
                  <div class="ride-details">
                    <span>Driver: <?= htmlspecialchars($ride['driver_name']); ?></span>
                    <span>Vehicle: <?= htmlspecialchars($ride['vehicle_info']); ?></span>
                  </div>
                  <div class="ride-date">
                    <?= date("F d, Y • h:i A", strtotime($ride['ride_date'])); ?>
                  </div>
                </div>
                <div class="ride-price">
                  <span class="price">₱<?= htmlspecialchars(number_format((float)$ride['ride_price'], 2)); ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p>No recent rides found.</p>
          <?php endif; ?>
        </div>
      </section>

    </div>
  </div>
</main>

<div class="loading-overlay" id="loadingOverlay">
  <div class="loading-spinner"></div>
  <p>Finding your ride...</p>
</div>

<script>
function toggleUserMenu(){
  const el = document.getElementById('userDropdown');
  el.style.display = (el.style.display === 'block') ? 'none' : 'block';
}

document.addEventListener('DOMContentLoaded', () => {
  const bookingForm = document.getElementById('bookingForm');
  const rideOptionCards = document.querySelectorAll('.ride-options .option-card');
  const ridePriceInput = document.getElementById('ridePriceInput');
  const loadingOverlay = document.getElementById('loadingOverlay');

  if (rideOptionCards && rideOptionCards.length) {
    rideOptionCards.forEach(card => {
      card.addEventListener('click', () => {
        rideOptionCards.forEach(c => c.classList.remove('active'));
        card.classList.add('active');
        ridePriceInput.value = card.dataset.price;
      });
    });
  }

  if (bookingForm) {
    bookingForm.addEventListener('submit', function(e) {
      e.preventDefault();
      loadingOverlay.classList.add('active');
      const delay = Math.random() * 5000 + 5000; // 5–10s
      setTimeout(() => this.submit(), delay);
    });
  }

  // When the ride is PENDING, after 5–10s assign a driver (server-side) then reload
  <?php if ($rideStatus === 'pending'): ?>
    setTimeout(() => {
      fetch('assign_driver.php', { method: 'POST' })
        .then(() => location.reload());
    }, Math.random() * 5000 + 5000);
  <?php endif; ?>

  // When ACCEPTED, enable "I have arrived" after 5–10s
  <?php if ($rideStatus === 'accepted'): ?>
    const btn = document.getElementById('arrivedBtn');
    const hint = document.getElementById('arrivedHint');
    setTimeout(() => {
      if (btn) btn.disabled = false;
      if (hint) hint.textContent = 'You can now confirm arrival.';
    }, Math.random() * 5000 + 5000);
  <?php endif; ?>
});
</script>
</body>
</html>
