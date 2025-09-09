<?php
require_once 'config.php';
if (!isLoggedIn()) redirect('login.php');

$user_id = $_SESSION['user_id'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $role = $_POST['role'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $area = $_POST['area'];
    $availability = $_POST['availability'];
    
    $stmt = $pdo->prepare("UPDATE users SET name=?, age=?, role=?, state=?, city=?, area=?, availability=? WHERE id=?");
    $stmt->execute([$name, $age, $role, $state, $city, $area, $availability, $user_id]);
    $success = "Profile updated successfully!";
}

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Cricket Connect Box</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); }
        .navbar { background: linear-gradient(45deg, #2196f3, #21cbf3) !important; }
        .card { border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .profile-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0; }
        .stat-card { background: linear-gradient(45deg, #ff6b6b, #ee5a24); color: white; border-radius: 10px; }
        .btn-edit { background: linear-gradient(45deg, #4caf50, #45a049); border: none; }
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
                <a class="nav-link" href="search.php"><i class="fas fa-search"></i> Search</a>
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <!-- Profile Card -->
                <div class="card">
                    <div class="profile-header p-4 text-center">
                        <i class="fas fa-user-circle fa-5x mb-3"></i>
                        <h2><?= htmlspecialchars($user['name']) ?></h2>
                        <p class="mb-0"><?= $user['role'] ?> • <?= $user['age'] ?> years old</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Stats -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="stat-card p-3 text-center">
                                    <h4><i class="fas fa-users"></i> <?= $user['followers'] ?></h4>
                                    <p class="mb-0">Followers</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="stat-card p-3 text-center">
                                    <h4><i class="fas fa-user-friends"></i> <?= $user['following'] ?></h4>
                                    <p class="mb-0">Following</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Profile Details -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6><i class="fas fa-envelope text-primary"></i> Email</h6>
                                <p><?= htmlspecialchars($user['email']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-venus-mars text-primary"></i> Gender</h6>
                                <p><?= $user['gender'] ?></p>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6><i class="fas fa-map-marker-alt text-primary"></i> Location</h6>
                                <p><?= htmlspecialchars($user['area'] . ', ' . $user['city'] . ', ' . $user['state']) ?></p>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6><i class="fas fa-clock text-primary"></i> Availability</h6>
                                <p><?= htmlspecialchars($user['availability']) ?></p>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="text-center">
                            <button class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#editModal">
                                <i class="fas fa-edit"></i> Edit Profile
                            </button>
                            <a href="https://wa.me/+91<?= $user['id'] ?>?text=Hi, let's connect for cricket!" 
                               class="btn btn-success" target="_blank">
                                <i class="fab fa-whatsapp"></i> Share WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Age</label>
                                <input type="number" class="form-control" name="age" value="<?= $user['age'] ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-control" name="role" required>
                                <option value="Batsman" <?= $user['role'] == 'Batsman' ? 'selected' : '' ?>>Batsman</option>
                                <option value="Bowler" <?= $user['role'] == 'Bowler' ? 'selected' : '' ?>>Bowler</option>
                                <option value="All-rounder" <?= $user['role'] == 'All-rounder' ? 'selected' : '' ?>>All-rounder</option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control" name="state" value="<?= htmlspecialchars($user['state']) ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="city" value="<?= htmlspecialchars($user['city']) ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Area</label>
                                <input type="text" class="form-control" name="area" value="<?= htmlspecialchars($user['area']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Availability</label>
                            <input type="text" class="form-control" name="availability" value="<?= htmlspecialchars($user['availability']) ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>