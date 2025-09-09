<?php
require_once 'config.php';
if (!isAdmin()) redirect('login.php');
redirect('admin_dashboard.php');

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_venue'])) {
        $stmt = $pdo->prepare("INSERT INTO boxcricket_venues (name, city, area, price_per_hour, upi_id, contact) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['venue_name'], $_POST['venue_city'], $_POST['venue_area'], $_POST['venue_price'], $_POST['venue_upi'], $_POST['venue_contact']]);
        $success = "Venue added successfully!";
    }
    
    if (isset($_POST['add_coaching'])) {
        $stmt = $pdo->prepare("INSERT INTO coaching_ads (coach_name, city, description, price, coupon_code, upi_id, contact) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['coach_name'], $_POST['coach_city'], $_POST['coach_desc'], $_POST['coach_price'], $_POST['coach_coupon'], $_POST['coach_upi'], $_POST['coach_contact']]);
        $success = "Coaching ad added successfully!";
    }
    
    if (isset($_POST['add_product'])) {
        $stmt = $pdo->prepare("INSERT INTO gear_store (product_name, price, affiliate_link) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['product_name'], $_POST['product_price'], $_POST['product_link']]);
        $success = "Product added successfully!";
    }
    
    if (isset($_POST['delete_user'])) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$_POST['user_id']]);
        $success = "User deleted successfully!";
    }
}

// Get data
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
$venues = $pdo->query("SELECT * FROM boxcricket_venues ORDER BY created_at DESC")->fetchAll();
$coaching = $pdo->query("SELECT * FROM coaching_ads ORDER BY created_at DESC")->fetchAll();
$products = $pdo->query("SELECT * FROM gear_store ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Cricket Connect Box</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); }
        .navbar { background: linear-gradient(45deg, #e91e63, #ad1457) !important; }
        .card { border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .nav-pills .nav-link.active { background: linear-gradient(45deg, #e91e63, #ad1457); }
        .btn-add { background: linear-gradient(45deg, #4caf50, #45a049); border: none; }
        .btn-delete { background: linear-gradient(45deg, #f44336, #d32f2f); border: none; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="admin.php">
                <i class="fas fa-cog"></i> Admin Panel
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Home</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <!-- Tabs -->
        <ul class="nav nav-pills mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="users-tab" data-bs-toggle="pill" data-bs-target="#users" type="button">
                    <i class="fas fa-users"></i> Users
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="venues-tab" data-bs-toggle="pill" data-bs-target="#venues" type="button">
                    <i class="fas fa-building"></i> Venues
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="coaching-tab" data-bs-toggle="pill" data-bs-target="#coaching" type="button">
                    <i class="fas fa-graduation-cap"></i> Coaching
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="products-tab" data-bs-toggle="pill" data-bs-target="#products" type="button">
                    <i class="fas fa-shopping-cart"></i> Products
                </button>
            </li>
        </ul>

        <div class="tab-content" id="adminTabsContent">
            <!-- Users Tab -->
            <div class="tab-pane fade show active" id="users" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-users"></i> Manage Users</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Location</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?= $user['id'] ?></td>
                                            <td><?= htmlspecialchars($user['name']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td><?= $user['role'] ?></td>
                                            <td><?= htmlspecialchars($user['city'] . ', ' . $user['state']) ?></td>
                                            <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                            <td>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this user?')">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                    <button type="submit" name="delete_user" class="btn btn-delete btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Venues Tab -->
            <div class="tab-pane fade" id="venues" role="tabpanel">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-plus"></i> Add Venue</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Venue Name</label>
                                        <input type="text" class="form-control" name="venue_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control" name="venue_city" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Area</label>
                                        <input type="text" class="form-control" name="venue_area" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Price per Hour</label>
                                        <input type="number" class="form-control" name="venue_price" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">UPI ID</label>
                                        <input type="text" class="form-control" name="venue_upi" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Contact</label>
                                        <input type="text" class="form-control" name="venue_contact" required>
                                    </div>
                                    <button type="submit" name="add_venue" class="btn btn-add w-100">Add Venue</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-building"></i> Existing Venues</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Location</th>
                                                <th>Price</th>
                                                <th>UPI ID</th>
                                                <th>Contact</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($venues as $venue): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($venue['name']) ?></td>
                                                    <td><?= htmlspecialchars($venue['area'] . ', ' . $venue['city']) ?></td>
                                                    <td>₹<?= number_format($venue['price_per_hour']) ?></td>
                                                    <td><?= htmlspecialchars($venue['upi_id']) ?></td>
                                                    <td><?= $venue['contact'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coaching Tab -->
            <div class="tab-pane fade" id="coaching" role="tabpanel">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-plus"></i> Add Coaching</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Coach Name</label>
                                        <input type="text" class="form-control" name="coach_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control" name="coach_city" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="coach_desc" rows="3" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Price</label>
                                        <input type="number" class="form-control" name="coach_price" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Coupon Code</label>
                                        <input type="text" class="form-control" name="coach_coupon" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">UPI ID</label>
                                        <input type="text" class="form-control" name="coach_upi" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Contact</label>
                                        <input type="text" class="form-control" name="coach_contact" required>
                                    </div>
                                    <button type="submit" name="add_coaching" class="btn btn-add w-100">Add Coaching</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-graduation-cap"></i> Existing Coaching</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Coach</th>
                                                <th>City</th>
                                                <th>Price</th>
                                                <th>Coupon</th>
                                                <th>Contact</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($coaching as $coach): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($coach['coach_name']) ?></td>
                                                    <td><?= htmlspecialchars($coach['city']) ?></td>
                                                    <td>₹<?= number_format($coach['price']) ?></td>
                                                    <td><?= $coach['coupon_code'] ?></td>
                                                    <td><?= $coach['contact'] ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Tab -->
            <div class="tab-pane fade" id="products" role="tabpanel">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-plus"></i> Add Product</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Product Name</label>
                                        <input type="text" class="form-control" name="product_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Price</label>
                                        <input type="number" class="form-control" name="product_price" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Affiliate Link</label>
                                        <input type="url" class="form-control" name="product_link" required>
                                    </div>
                                    <button type="submit" name="add_product" class="btn btn-add w-100">Add Product</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-shopping-cart"></i> Existing Products</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Link</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products as $product): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                                                    <td>₹<?= number_format($product['price']) ?></td>
                                                    <td><a href="<?= htmlspecialchars($product['affiliate_link']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">View</a></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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