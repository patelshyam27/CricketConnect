<?php
require_once 'config.php';
if (!isAdmin()) redirect('login.php');

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        $pdo->prepare("DELETE FROM follows WHERE follower_id = ? OR following_id = ?")->execute([$user_id, $user_id]);
        $pdo->prepare("DELETE FROM bookings WHERE captain_id = ?")->execute([$user_id]);
        $pdo->prepare("DELETE FROM coaching_enrollments WHERE user_id = ?")->execute([$user_id]);
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);
        $success = "User deleted successfully!";
    }
    
    if (isset($_POST['toggle_status'])) {
        $user_id = $_POST['user_id'];
        $pdo->prepare("UPDATE users SET status = CASE WHEN status = 'active' THEN 'blocked' ELSE 'active' END WHERE id = ?")->execute([$user_id]);
        $success = "User status updated!";
    }
}

// Get users with pagination
$page = $_GET['page'] ?? 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$filter_city = $_GET['city'] ?? '';
$filter_role = $_GET['role'] ?? '';

$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(name LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($filter_city) {
    $where_conditions[] = "city LIKE ?";
    $params[] = "%$filter_city%";
}

if ($filter_role) {
    $where_conditions[] = "role = ?";
    $params[] = $filter_role;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$count_query = "SELECT COUNT(*) FROM users $where_clause";
$stmt = $pdo->prepare($count_query);
$stmt->execute($params);
$total_users = $stmt->fetchColumn();
$total_pages = ceil($total_users / $limit);

$query = "SELECT * FROM users $where_clause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Cricket Connect Box</title>
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
                    <a class="nav-link admin-nav-link" href="admin_dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link admin-nav-link active" href="admin_users.php">
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
                        <h2><i class="fas fa-users"></i> User Management</h2>
                    </div>
                </div>

                <div class="container-fluid py-4">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <!-- Filters -->
                    <div class="card admin-card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name or email">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="city" value="<?= htmlspecialchars($filter_city) ?>" placeholder="Filter by city">
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" name="role">
                                        <option value="">All Roles</option>
                                        <option value="Batsman" <?= $filter_role == 'Batsman' ? 'selected' : '' ?>>Batsman</option>
                                        <option value="Bowler" <?= $filter_role == 'Bowler' ? 'selected' : '' ?>>Bowler</option>
                                        <option value="All-rounder" <?= $filter_role == 'All-rounder' ? 'selected' : '' ?>>All-rounder</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="card admin-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-users"></i> Users (<?= number_format($total_users) ?>)</h5>
                            <span class="badge bg-primary">Page <?= $page ?> of <?= $total_pages ?></span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-custom">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Location</th>
                                            <th>Followers</th>
                                            <th>Joined</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?= $user['id'] ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($user['name']) ?></strong><br>
                                                    <small class="text-muted"><?= $user['age'] ?> years, <?= $user['gender'] ?></small>
                                                </td>
                                                <td><?= htmlspecialchars($user['email']) ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?= $user['role'] ?></span>
                                                </td>
                                                <td>
                                                    <?= htmlspecialchars($user['area']) ?><br>
                                                    <small class="text-muted"><?= htmlspecialchars($user['city'] . ', ' . $user['state']) ?></small>
                                                </td>
                                                <td>
                                                    <i class="fas fa-users text-primary"></i> <?= $user['followers'] ?><br>
                                                    <small class="text-muted">Following: <?= $user['following'] ?></small>
                                                </td>
                                                <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                                <td>
                                                    <div class="btn-group-vertical btn-group-sm">
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this user and all related data?')">
                                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                            <button type="submit" name="delete_user" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <nav class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&city=<?= urlencode($filter_city) ?>&role=<?= urlencode($filter_role) ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>