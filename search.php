<?php
require_once 'config.php';
if (!isLoggedIn()) redirect('login.php');

$current_user_id = $_SESSION['user_id'];

// Handle follow/unfollow
if ($_POST['action'] ?? '' === 'follow') {
    $target_id = $_POST['target_id'];
    $stmt = $pdo->prepare("INSERT IGNORE INTO follows (follower_id, following_id) VALUES (?, ?)");
    $stmt->execute([$current_user_id, $target_id]);
    
    // Update counts
    $pdo->prepare("UPDATE users SET following = (SELECT COUNT(*) FROM follows WHERE follower_id = ?) WHERE id = ?")->execute([$current_user_id, $current_user_id]);
    $pdo->prepare("UPDATE users SET followers = (SELECT COUNT(*) FROM follows WHERE following_id = ?) WHERE id = ?")->execute([$target_id, $target_id]);
}

if ($_POST['action'] ?? '' === 'unfollow') {
    $target_id = $_POST['target_id'];
    $stmt = $pdo->prepare("DELETE FROM follows WHERE follower_id = ? AND following_id = ?");
    $stmt->execute([$current_user_id, $target_id]);
    
    // Update counts
    $pdo->prepare("UPDATE users SET following = (SELECT COUNT(*) FROM follows WHERE follower_id = ?) WHERE id = ?")->execute([$current_user_id, $current_user_id]);
    $pdo->prepare("UPDATE users SET followers = (SELECT COUNT(*) FROM follows WHERE following_id = ?) WHERE id = ?")->execute([$target_id, $target_id]);
}

// Get current user's join date
$stmt = $pdo->prepare("SELECT created_at FROM users WHERE id = ?");
$stmt->execute([$current_user_id]);
$current_user_join_date = $stmt->fetch()['created_at'];

// Search filters
$state = $_GET['state'] ?? '';
$city = $_GET['city'] ?? '';
$area = $_GET['area'] ?? '';
$role = $_GET['role'] ?? '';

$query = "SELECT u.*, 
          CASE WHEN f.follower_id IS NOT NULL THEN 1 ELSE 0 END as is_following
          FROM users u 
          LEFT JOIN follows f ON u.id = f.following_id AND f.follower_id = ?
          WHERE u.id != ?";
$params = [$current_user_id, $current_user_id];

if ($state) {
    $query .= " AND u.state LIKE ?";
    $params[] = "%$state%";
}
if ($city) {
    $query .= " AND u.city LIKE ?";
    $params[] = "%$city%";
}
if ($area) {
    $query .= " AND u.area LIKE ?";
    $params[] = "%$area%";
}
if ($role) {
    $query .= " AND u.role = ?";
    $params[] = $role;
}

$query .= " ORDER BY u.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$players = $stmt->fetchAll();

// Debug info
$total_count = count($players);
$filter_applied = !empty($state) || !empty($city) || !empty($area) || !empty($role);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Players - Cricket Connect Box</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); }
        .navbar { background: linear-gradient(45deg, #2196f3, #21cbf3) !important; }
        .card { border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .card:hover { transform: translateY(-3px); }
        .player-card { border-left: 4px solid #2196f3; }
        .btn-follow { background: linear-gradient(45deg, #4caf50, #45a049); border: none; }
        .btn-unfollow { background: linear-gradient(45deg, #f44336, #d32f2f); border: none; }
        .btn-whatsapp { background: linear-gradient(45deg, #25d366, #128c7e); border: none; }
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
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-search"></i> Find Cricket Players</h2>
                    <span class="badge bg-primary fs-6"><?= $total_count ?> players found</span>
                </div>
                
                <!-- Search Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control" name="state" value="<?= htmlspecialchars($state) ?>" placeholder="Enter state">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="city" value="<?= htmlspecialchars($city) ?>" placeholder="Enter city">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Area</label>
                                <input type="text" class="form-control" name="area" value="<?= htmlspecialchars($area) ?>" placeholder="Enter area">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Role</label>
                                <select class="form-control" name="role">
                                    <option value="">All Roles</option>
                                    <option value="Batsman" <?= ($_GET['role'] ?? '') == 'Batsman' ? 'selected' : '' ?>>Batsman</option>
                                    <option value="Bowler" <?= ($_GET['role'] ?? '') == 'Bowler' ? 'selected' : '' ?>>Bowler</option>
                                    <option value="All-rounder" <?= ($_GET['role'] ?? '') == 'All-rounder' ? 'selected' : '' ?>>All-rounder</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Search Players
                                </button>
                                <a href="search.php" class="btn btn-secondary">
                                    <i class="fas fa-refresh"></i> Clear Filters
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Players List -->
                <div class="row">
                    <?php if (empty($players)): ?>
                        <div class="col-12">
                            <div class="card text-center">
                                <div class="card-body py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5>No players found</h5>
                                    <?php if ($filter_applied): ?>
                                        <p class="text-muted">No players match your search criteria. Try different filters.</p>
                                        <a href="search.php" class="btn btn-primary">Show All Players</a>
                                    <?php else: ?>
                                        <p class="text-muted">No other players have joined yet. Be the first to connect!</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($players as $player): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card player-card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-user-circle text-primary"></i>
                                                <?= htmlspecialchars($player['name']) ?>
                                            </h5>
                                            <span class="badge bg-primary"><?= $player['age'] ?> yrs</span>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <p class="mb-1">
                                                <i class="fas fa-baseball-ball text-warning"></i>
                                                <strong><?= $player['role'] ?></strong>
                                            </p>
                                            <p class="mb-1">
                                                <i class="fas fa-map-marker-alt text-danger"></i>
                                                <?= htmlspecialchars($player['area'] . ', ' . $player['city'] . ', ' . $player['state']) ?>
                                            </p>
                                            <p class="mb-1">
                                                <i class="fas fa-clock text-info"></i>
                                                <?= htmlspecialchars($player['availability']) ?>
                                            </p>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-users"></i> <?= $player['followers'] ?> followers
                                            </small>
                                            <small class="text-muted">
                                                <i class="fas fa-user-friends"></i> <?= $player['following'] ?> following
                                            </small>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="target_id" value="<?= $player['id'] ?>">
                                                <?php if ($player['is_following']): ?>
                                                    <button type="submit" name="action" value="unfollow" class="btn btn-unfollow btn-sm w-100">
                                                        <i class="fas fa-user-minus"></i> Unfollow
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" name="action" value="follow" class="btn btn-follow btn-sm w-100">
                                                        <i class="fas fa-user-plus"></i> Follow
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                            
                                            <a href="https://wa.me/+91<?= $player['id'] ?>?text=Hi <?= urlencode($player['name']) ?>, I found you on Cricket Connect Box!" 
                                               class="btn btn-whatsapp btn-sm" target="_blank">
                                                <i class="fab fa-whatsapp"></i> WhatsApp Chat
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>