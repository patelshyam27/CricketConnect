<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Admin login check
    if ($email === 'abpmech73@gmail.com' && $password === '271106') {
        $_SESSION['user_id'] = 0;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = 'Admin';
        redirect('admin.php');
    }
    
    // Regular user login
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        redirect('index.php');
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cricket Connect Box</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .card { border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .form-control { border-radius: 10px; border: 2px solid #e3f2fd; }
        .form-control:focus { border-color: #2196f3; box-shadow: 0 0 0 0.2rem rgba(33,150,243,0.25); }
        .btn-primary { background: linear-gradient(45deg, #2196f3, #21cbf3); border: none; border-radius: 10px; }
        .cricket-icon { color: #ff9800; font-size: 4rem; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-baseball-ball cricket-icon"></i>
                            <h2 class="mt-3">Welcome Back!</h2>
                            <p class="text-muted">Login to Cricket Connect Box</p>
                        </div>
                        
                        <?php if(isset($_GET['registered'])): ?>
                            <div class="alert alert-success">Registration successful! Please login.</div>
                        <?php endif; ?>
                        
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label"><i class="fas fa-lock"></i> Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-3">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p>Don't have an account? <a href="register.php" class="text-decoration-none">Register here</a></p>
                        </div>
                        

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>