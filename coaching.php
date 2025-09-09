<?php
require_once 'config.php';
if (!isLoggedIn()) redirect('login.php');

$user_id = $_SESSION['user_id'];

// Get user's city
$stmt = $pdo->prepare("SELECT city FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_city = $stmt->fetch()['city'];

// Handle enrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['join_coaching'])) {
    $coaching_id = $_POST['coaching_id'];
    
    // Check if user is registered (they are, since they're logged in)
    $stmt = $pdo->prepare("SELECT * FROM coaching_ads WHERE id = ?");
    $stmt->execute([$coaching_id]);
    $coaching = $stmt->fetch();
    
    $original_price = $coaching['price'];
    $discount = $coaching['discount_percent'];
    $final_amount = $original_price - ($original_price * $discount / 100);
    
    // Check if already enrolled
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM coaching_enrollments WHERE user_id = ? AND coaching_id = ?");
    $stmt->execute([$user_id, $coaching_id]);
    $already_enrolled = $stmt->fetch()['count'] > 0;
    
    if (!$already_enrolled) {
        $stmt = $pdo->prepare("INSERT INTO coaching_enrollments (user_id, coaching_id, amount_paid) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $coaching_id, $final_amount]);
        
        $success = "Enrollment successful! Pay ₹$final_amount to UPI ID: " . $coaching['upi_id'];
    } else {
        $error = "You are already enrolled in this coaching!";
    }
}

// Get coaching ads filtered by user's city
$stmt = $pdo->prepare("SELECT c.*, 
                      CASE WHEN e.user_id IS NOT NULL THEN 1 ELSE 0 END as is_enrolled
                      FROM coaching_ads c 
                      LEFT JOIN coaching_enrollments e ON c.id = e.coaching_id AND e.user_id = ?
                      WHERE c.city = ? 
                      ORDER BY c.created_at DESC");
$stmt->execute([$user_id, $user_city]);
$coaching_ads = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cricket Coaching - Cricket Connect Box</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); }
        .navbar { background: linear-gradient(45deg, #2196f3, #21cbf3) !important; }
        .card { border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .card:hover { transform: translateY(-3px); }
        .coaching-card { border-left: 4px solid #ff9800; }
        .price-badge { background: linear-gradient(45deg, #ff6b6b, #ee5a24); }
        .discount-badge { background: linear-gradient(45deg, #4caf50, #45a049); }
        .btn-join { background: linear-gradient(45deg, #ff9800, #f57c00); border: none; }
        .btn-enrolled { background: linear-gradient(45deg, #4caf50, #45a049); border: none; }
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
        <h2 class="mb-4"><i class="fas fa-graduation-cap"></i> Cricket Coaching in <?= htmlspecialchars($user_city) ?></h2>
        
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
            <?php foreach ($coaching_ads as $coaching): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card coaching-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title">
                                    <i class="fas fa-graduation-cap text-warning"></i>
                                    <?= htmlspecialchars($coaching['coach_name']) ?>
                                </h5>
                                <span class="badge discount-badge">
                                    <?= $coaching['discount_percent'] ?>% OFF
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <p class="mb-2">
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                    <?= htmlspecialchars($coaching['city']) ?>
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-phone text-info"></i>
                                    <?= $coaching['contact'] ?>
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-ticket-alt text-success"></i>
                                    Coupon: <strong><?= $coaching['coupon_code'] ?></strong>
                                </p>
                            </div>
                            
                            <div class="mb-3">
                                <p class="text-muted"><?= htmlspecialchars($coaching['description']) ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-decoration-line-through text-muted">
                                        ₹<?= number_format($coaching['price']) ?>
                                    </span>
                                    <span class="badge price-badge">
                                        ₹<?= number_format($coaching['price'] - ($coaching['price'] * $coaching['discount_percent'] / 100)) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <?php if ($coaching['is_enrolled']): ?>
                                <button class="btn btn-enrolled w-100" disabled>
                                    <i class="fas fa-check"></i> Already Enrolled
                                </button>
                            <?php else: ?>
                                <button class="btn btn-join w-100" data-bs-toggle="modal" data-bs-target="#joinModal<?= $coaching['id'] ?>">
                                    <i class="fas fa-play"></i> Join Coaching
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Join Modal -->
                <div class="modal fade" id="joinModal<?= $coaching['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Join <?= htmlspecialchars($coaching['coach_name']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <h6>Coaching Details:</h6>
                                    <p class="mb-1"><strong>Coach:</strong> <?= htmlspecialchars($coaching['coach_name']) ?></p>
                                    <p class="mb-1"><strong>Location:</strong> <?= htmlspecialchars($coaching['city']) ?></p>
                                    <p class="mb-1"><strong>Contact:</strong> <?= $coaching['contact'] ?></p>
                                    <p class="mb-3"><strong>Description:</strong> <?= htmlspecialchars($coaching['description']) ?></p>
                                    
                                    <h6>Pricing:</h6>
                                    <p class="mb-1">Original: ₹<?= number_format($coaching['price']) ?></p>
                                    <p class="mb-1">Discount: <?= $coaching['discount_percent'] ?>% (Code: <?= $coaching['coupon_code'] ?>)</p>
                                    <p class="mb-0"><strong>Final Amount: ₹<?= number_format($coaching['price'] - ($coaching['price'] * $coaching['discount_percent'] / 100)) ?></strong></p>
                                    <small>Pay to UPI ID: <strong><?= $coaching['upi_id'] ?></strong></small>
                                </div>
                                
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> You are registered and eligible for discount!
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="coaching_id" value="<?= $coaching['id'] ?>">
                                    <button type="submit" name="join_coaching" class="btn btn-primary">Confirm Enrollment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($coaching_ads)): ?>
            <div class="text-center py-5">
                <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                <h5>No coaching available in <?= htmlspecialchars($user_city) ?></h5>
                <p class="text-muted">Check back later for new coaching centers in your city.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>