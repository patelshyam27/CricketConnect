<?php
require_once 'config.php';
if (!isAdmin()) redirect('login.php');

// Get statistics
$stats = [
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'venues' => $pdo->query("SELECT COUNT(*) FROM boxcricket_venues")->fetchColumn(),
    'bookings' => $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn(),
    'coaching' => $pdo->query("SELECT COUNT(*) FROM coaching_ads")->fetchColumn(),
    'enrollments' => $pdo->query("SELECT COUNT(*) FROM coaching_enrollments")->fetchColumn(),
    'revenue' => $pdo->query("SELECT SUM(total_amount) FROM bookings")->fetchColumn() ?: 0
];

// Recent activities
$recent_users = $pdo->query("SELECT name, email, created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recent_bookings = $pdo->query("SELECT b.*, v.name as venue_name, u.name as user_name FROM bookings b JOIN boxcricket_venues v ON b.venue_id = v.id JOIN users u ON b.captain_id = u.id ORDER BY b.created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Cricket Connect Box</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 admin-sidebar p-0">
                <div class="p-4">
                    <h4><i class="fas fa-cog"></i> Admin Panel</h4>
                </div>
                <nav class="nav flex-column px-3">
                    <a class="nav-link admin-nav-link active" href="admin_dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link admin-nav-link" href="admin_users.php">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <a class="nav-link admin-nav-link" href="admin_venues.php">
                        <i class="fas fa-building"></i> Venues
                    </a>
                    <a class="nav-link admin-nav-link" href="admin_coaching.php">
                        <i class="fas fa-graduation-cap"></i> Coaching
                    </a>
                    <a class="nav-link admin-nav-link" href="admin_products.php">
                        <i class="fas fa-shopping-cart"></i> Products
                    </a>
                    <a class="nav-link admin-nav-link" href="admin_bookings.php">
                        <i class="fas fa-calendar"></i> Bookings
                    </a>
                    <a class="nav-link admin-nav-link" href="admin_database.php">
                        <i class="fas fa-database"></i> Database
                    </a>
                    <hr class="text-white">
                    <a class="nav-link admin-nav-link" href="index.php">
                        <i class="fas fa-home"></i> Main Site
                    </a>
                    <a class="nav-link admin-nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10">
                <div class="admin-header">
                    <div class="container-fluid">
                        <h2><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h2>
                    </div>
                </div>

                <div class="container-fluid py-4">
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-2 mb-3">
                            <div class="card admin-card text-center">
                                <div class="card-body">
                                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                    <h3><?= $stats['users'] ?></h3>
                                    <p class="text-muted">Users</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="card admin-card text-center">
                                <div class="card-body">
                                    <i class="fas fa-building fa-2x text-success mb-2"></i>
                                    <h3><?= $stats['venues'] ?></h3>
                                    <p class="text-muted">Venues</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="card admin-card text-center">
                                <div class="card-body">
                                    <i class="fas fa-calendar fa-2x text-warning mb-2"></i>
                                    <h3><?= $stats['bookings'] ?></h3>
                                    <p class="text-muted">Bookings</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="card admin-card text-center">
                                <div class="card-body">
                                    <i class="fas fa-graduation-cap fa-2x text-info mb-2"></i>
                                    <h3><?= $stats['coaching'] ?></h3>
                                    <p class="text-muted">Coaching</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="card admin-card text-center">
                                <div class="card-body">
                                    <i class="fas fa-user-graduate fa-2x text-secondary mb-2"></i>
                                    <h3><?= $stats['enrollments'] ?></h3>
                                    <p class="text-muted">Enrollments</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <div class="card admin-card text-center">
                                <div class="card-body">
                                    <i class="fas fa-rupee-sign fa-2x text-danger mb-2"></i>
                                    <h3>₹<?= number_format($stats['revenue']) ?></h3>
                                    <p class="text-muted">Revenue</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card admin-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-user-plus"></i> Recent Users</h5>
                                </div>
                                <div class="card-body">
                                    <?php foreach($recent_users as $user): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <strong><?= htmlspecialchars($user['name']) ?></strong><br>
                                                <small class="text-muted"><?= $user['email'] ?></small>
                                            </div>
                                            <small><?= date('M j', strtotime($user['created_at'])) ?></small>
                                        </div>
                                        <hr>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card admin-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-calendar-check"></i> Recent Bookings</h5>
                                </div>
                                <div class="card-body">
                                    <?php foreach($recent_bookings as $booking): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <strong><?= htmlspecialchars($booking['venue_name']) ?></strong><br>
                                                <small class="text-muted">by <?= htmlspecialchars($booking['user_name']) ?></small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-success">₹<?= number_format($booking['total_amount']) ?></span><br>
                                                <small><?= date('M j', strtotime($booking['created_at'])) ?></small>
                                            </div>
                                        </div>
                                        <hr>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>