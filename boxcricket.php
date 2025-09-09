<?php
require_once 'config.php';
if (!isLoggedIn()) redirect('login.php');

$user_id = $_SESSION['user_id'];

// Handle booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_venue'])) {
    $venue_id = $_POST['venue_id'];
    $booking_date = $_POST['booking_date'];
    $time_slot = $_POST['time_slot'];
    $team_players = $_POST['team_players'];
    
    // Parse team players (comma separated emails)
    $player_emails = array_map('trim', explode(',', $team_players));
    $player_emails = array_filter($player_emails); // Remove empty emails
    $player_emails[] = $_SESSION['user_email']; // Add captain
    $player_emails = array_unique($player_emails); // Remove duplicates
    
    // Check which players are registered
    $placeholders = str_repeat('?,', count($player_emails) - 1) . '?';
    $stmt = $pdo->prepare("SELECT email FROM users WHERE email IN ($placeholders)");
    $stmt->execute($player_emails);
    $registered_emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Find unregistered players
    $unregistered = array_diff($player_emails, $registered_emails);
    
    if (empty($unregistered)) {
        // All players registered, proceed with booking
        $stmt = $pdo->prepare("SELECT * FROM boxcricket_venues WHERE id = ?");
        $stmt->execute([$venue_id]);
        $venue = $stmt->fetch();
        
        $original_price = $venue['price_per_hour'];
        $discount = $venue['discount_percent'];
        $final_amount = $original_price - ($original_price * $discount / 100);
        
        $stmt = $pdo->prepare("INSERT INTO bookings (venue_id, captain_id, team_players, booking_date, time_slot, total_amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$venue_id, $user_id, $team_players, $booking_date, $time_slot, $final_amount]);
        
        $success = "Booking successful! Pay ₹$final_amount to UPI ID: " . $venue['upi_id'];
    } else {
        $error = "❌ These players are not registered: " . implode(', ', $unregistered) . ". All team members must be registered to get discount!";
    }
}

// Get venues with filters
$city_filter = $_GET['city'] ?? '';
$area_filter = $_GET['area'] ?? '';

$query = "SELECT * FROM boxcricket_venues WHERE 1=1";
$params = [];

if ($city_filter) {
    $query .= " AND city LIKE ?";
    $params[] = "%$city_filter%";
}
if ($area_filter) {
    $query .= " AND area LIKE ?";
    $params[] = "%$area_filter%";
}

$query .= " ORDER BY city, name";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$venues = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BoxCricket Booking - Cricket Connect Box</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); }
        .navbar { background: linear-gradient(45deg, #2196f3, #21cbf3) !important; }
        .card { border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .card:hover { transform: translateY(-3px); }
        .venue-card { border-left: 4px solid #4caf50; }
        .price-badge { background: linear-gradient(45deg, #ff6b6b, #ee5a24); }
        .discount-badge { background: linear-gradient(45deg, #4caf50, #45a049); }
        .btn-book { background: linear-gradient(45deg, #2196f3, #21cbf3); border: none; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-baseball-ball"></i> Cricket Connect Box
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Home</a>
                <a class="nav-link" href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-calendar-check"></i> BoxCricket Venue Booking</h2>
            <span class="badge bg-success fs-6"><?= count($venues) ?> venues available</span>
        </div>
        
        <!-- Venue Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" name="city" value="<?= htmlspecialchars($_GET['city'] ?? '') ?>" placeholder="Enter city">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Area</label>
                        <input type="text" class="form-control" name="area" value="<?= htmlspecialchars($_GET['area'] ?? '') ?>" placeholder="Enter area">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search Venues
                        </button>
                        <a href="boxcricket.php" class="btn btn-secondary">
                            <i class="fas fa-refresh"></i> Clear Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php foreach ($venues as $venue): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card venue-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title">
                                    <i class="fas fa-building text-success"></i>
                                    <?= htmlspecialchars($venue['name']) ?>
                                </h5>
                                <span class="badge discount-badge">
                                    <?= $venue['discount_percent'] ?>% OFF
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <p class="mb-1">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                    <?= htmlspecialchars($venue['area'] . ', ' . $venue['city']) ?>
                                </p>
                                <p class="mb-1">
                                    <i class="fas fa-phone text-info"></i>
                                    <?= $venue['contact'] ?>
                                </p>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-decoration-line-through text-muted">
                                        ₹<?= number_format($venue['price_per_hour']) ?>
                                    </span>
                                    <span class="badge price-badge">
                                        ₹<?= number_format($venue['price_per_hour'] - ($venue['price_per_hour'] * $venue['discount_percent'] / 100)) ?>/hour
                                    </span>
                                </div>
                            </div>
                            
                            <button class="btn btn-book w-100" data-bs-toggle="modal" data-bs-target="#bookModal<?= $venue['id'] ?>">
                                <i class="fas fa-calendar-plus"></i> Book Now
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Booking Modal -->
                <div class="modal fade" id="bookModal<?= $venue['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Book <?= htmlspecialchars($venue['name']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="venue_id" value="<?= $venue['id'] ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Booking Date</label>
                                        <input type="date" class="form-control" name="booking_date" min="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Time Slot</label>
                                        <select class="form-control" name="time_slot" required>
                                            <option value="">Select Time</option>
                                            <option value="6:00 AM - 7:00 AM">6:00 AM - 7:00 AM</option>
                                            <option value="7:00 AM - 8:00 AM">7:00 AM - 8:00 AM</option>
                                            <option value="8:00 AM - 9:00 AM">8:00 AM - 9:00 AM</option>
                                            <option value="6:00 PM - 7:00 PM">6:00 PM - 7:00 PM</option>
                                            <option value="7:00 PM - 8:00 PM">7:00 PM - 8:00 PM</option>
                                            <option value="8:00 PM - 9:00 PM">8:00 PM - 9:00 PM</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Team Players (Email addresses, comma separated)</label>
                                        <textarea class="form-control" name="team_players" rows="3" 
                                                  placeholder="player1@email.com, player2@email.com, ..." required 
                                                  onblur="checkPlayers(this.value, <?= $venue['id'] ?>)"></textarea>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i> All players must be registered to get discount!
                                        </small>
                                        <div id="playerCheck<?= $venue['id'] ?>" class="mt-2"></div>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <h6>Pricing:</h6>
                                        <p class="mb-1">Original: ₹<?= number_format($venue['price_per_hour']) ?></p>
                                        <p class="mb-1">Discount: <?= $venue['discount_percent'] ?>%</p>
                                        <p class="mb-0"><strong>Final Amount: ₹<?= number_format($venue['price_per_hour'] - ($venue['price_per_hour'] * $venue['discount_percent'] / 100)) ?></strong></p>
                                        <small>Pay to UPI ID: <strong><?= $venue['upi_id'] ?></strong></small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="book_venue" class="btn btn-primary">Confirm Booking</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($venues)): ?>
            <div class="col-12">
                <div class="card text-center">
                    <div class="card-body py-5">
                        <i class="fas fa-building fa-3x text-muted mb-3"></i>
                        <h5>No venues found</h5>
                        <?php if (!empty($city_filter) || !empty($area_filter)): ?>
                            <p class="text-muted">No venues match your search criteria. Try different filters.</p>
                            <a href="boxcricket.php" class="btn btn-success">Show All Venues</a>
                        <?php else: ?>
                            <p class="text-muted">No venues available yet. Check back later!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function checkPlayers(emails, venueId) {
        if (!emails.trim()) return;
        
        const emailList = emails.split(',').map(e => e.trim()).filter(e => e);
        const checkDiv = document.getElementById('playerCheck' + venueId);
        
        fetch('check_players.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({emails: emailList})
        })
        .then(response => response.json())
        .then(data => {
            if (data.unregistered.length > 0) {
                checkDiv.innerHTML = `<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Unregistered: ${data.unregistered.join(', ')}</div>`;
            } else {
                checkDiv.innerHTML = `<div class="alert alert-success"><i class="fas fa-check"></i> All players verified!</div>`;
            }
        });
    }
    </script>
</body>
</html>