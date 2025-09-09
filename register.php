<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $role = $_POST['role'];
    $state = $_POST['state'];
    $city = $_POST['city'];
    $area = $_POST['area'];
    $availability = $_POST['availability'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, age, gender, role, state, city, area, availability, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $age, $gender, $role, $state, $city, $area, $availability, $email, $password]);
        redirect('login.php?registered=1');
    } catch(PDOException $e) {
        $error = "Email already exists!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Cricket Connect Box</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .card { border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .form-control { border-radius: 10px; border: 2px solid #e3f2fd; }
        .form-control:focus { border-color: #2196f3; box-shadow: 0 0 0 0.2rem rgba(33,150,243,0.25); }
        .btn-primary { background: linear-gradient(45deg, #2196f3, #21cbf3); border: none; border-radius: 10px; }
        .cricket-icon { color: #ff9800; font-size: 3rem; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-baseball-ball cricket-icon"></i>
                            <h2 class="mt-3">Join Cricket Connect Box</h2>
                            <p class="text-muted">Connect with players, book venues, join coaching</p>
                        </div>
                        
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="fas fa-user"></i> Name</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="fas fa-calendar"></i> Age</label>
                                    <input type="number" class="form-control" name="age" min="10" max="60" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="fas fa-venus-mars"></i> Gender</label>
                                    <select class="form-control" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="fas fa-baseball-ball"></i> Role</label>
                                    <select class="form-control" name="role" required>
                                        <option value="">Select Role</option>
                                        <option value="Batsman">Batsman</option>
                                        <option value="Bowler">Bowler</option>
                                        <option value="All-rounder">All-rounder</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"><i class="fas fa-map"></i> State</label>
                                    <input type="text" class="form-control" name="state" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"><i class="fas fa-city"></i> City</label>
                                    <input type="text" class="form-control" name="city" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"><i class="fas fa-location-dot"></i> Area</label>
                                    <input type="text" class="form-control" name="area" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-clock"></i> Availability</label>
                                <input type="text" class="form-control" name="availability" placeholder="e.g., Weekends, Evenings" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label"><i class="fas fa-lock"></i> Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-3">
                                <i class="fas fa-user-plus"></i> Register Now
                            </button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p>Already have an account? <a href="login.php" class="text-decoration-none">Login here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>