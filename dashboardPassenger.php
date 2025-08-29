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
                        <span><?php echo substr($userName, 0, 2); ?></span>
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                        <a href="logout.php" class="logout-link">Log Out</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="dashboard-main">
        <div class="dashboard-background"></div>
        <div class="container">
            <div class="dashboard-grid">
                <section class="dashboard-card current-ride" data-animate data-delay="100">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M17 17.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zM12 5v3h3V5H12zM2 12c0-5.52 4.48-10 10-10s10 4.48 10 10-4.48 10-10 10-10-4.48-10-10z"/>
                            </svg>
                        </div>
                        <h2 class="card-title">Current Ride</h2>
                    </div>
                    <div class="card-content">
                        <?php if ($hasCurrentRide) { ?>
                            <?php } else { ?>
                            <p class="no-ride-message">No current ride. Book a ride now!</p>
                        <?php } ?>
                    </div>
                </section>

                <section class="dashboard-card recent-rides" data-animate data-delay="300">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M19 12.5H5.5M12 19l-7-7 7-7"/>
                            </svg>
                        </div>
                        <h2 class="card-title">Recent Rides</h2>
                    </div>
                    <div class="card-content">
                        <?php if ($hasRecentRides) { ?>
                            <?php } else { ?>
                            <p class="no-ride-message">No recent rides.</p>
                        <?php } ?>
                    </div>
                </section>
                
            </div>
        </div>
    </main>
</body>
</html>