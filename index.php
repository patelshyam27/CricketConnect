<?php
require_once 'config.php';
if (!isLoggedIn()) redirect('login.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cricket Connect Box - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-baseball-ball"></i> Cricket Connect Box
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="search.php"><i class="fas fa-search"></i> Find Players</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="boxcricket.php"><i class="fas fa-calendar"></i> Book Venues</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="coaching.php"><i class="fas fa-graduation-cap"></i> Coaching</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gear.php"><i class="fas fa-shopping-cart"></i> Gear Store</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-cog"></i> Admin</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php"><i class="fas fa-user"></i> <?= $_SESSION['user_name'] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center fade-in" style="margin-top: 76px;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-3 mb-4 fw-bold">Connect. Play. Excel.</h1>
                    <p class="lead mb-4">India's premier cricket community platform. Find players, book premium venues, join professional coaching, and gear up with the best equipment.</p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="search.php" class="btn btn-light btn-lg btn-custom">
                            <i class="fas fa-search"></i> Find Players
                        </a>
                        <a href="boxcricket.php" class="btn btn-outline-light btn-lg btn-custom">
                            <i class="fas fa-calendar"></i> Book Venue
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="fas fa-baseball-ball" style="font-size: 15rem; opacity: 0.1;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Features -->
    <div class="container py-5">
        <div class="row g-4">
            <!-- Search Players -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="feature-card card-custom">
                    <i class="fas fa-users feature-icon"></i>
                    <h5 class="fw-bold mb-3">Find Players</h5>
                    <p class="text-muted mb-4">Connect with cricket enthusiasts in your locality. Filter by role, location, and availability.</p>
                    <a href="search.php" class="btn btn-primary-custom btn-custom w-100">
                        <i class="fas fa-search"></i> Search Players
                    </a>
                </div>
            </div>

            <!-- BoxCricket Booking -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="feature-card card-custom">
                    <i class="fas fa-calendar-check feature-icon"></i>
                    <h5 class="fw-bold mb-3">Book Venues</h5>
                    <p class="text-muted mb-4">Premium BoxCricket venues with team verification and exclusive member discounts.</p>
                    <a href="boxcricket.php" class="btn btn-primary-custom btn-custom w-100">
                        <i class="fas fa-calendar-plus"></i> Book Venue
                    </a>
                </div>
            </div>

            <!-- Coaching -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="feature-card card-custom">
                    <i class="fas fa-graduation-cap feature-icon"></i>
                    <h5 class="fw-bold mb-3">Professional Coaching</h5>
                    <p class="text-muted mb-4">Expert coaching programs with certified trainers and member-only pricing.</p>
                    <a href="coaching.php" class="btn btn-primary-custom btn-custom w-100">
                        <i class="fas fa-user-graduate"></i> Join Coaching
                    </a>
                </div>
            </div>

            <!-- Gear Store -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="feature-card card-custom">
                    <i class="fas fa-shopping-cart feature-icon"></i>
                    <h5 class="fw-bold mb-3">Cricket Gear</h5>
                    <p class="text-muted mb-4">Premium cricket equipment from trusted brands at competitive prices.</p>
                    <a href="gear.php" class="btn btn-primary-custom btn-custom w-100">
                        <i class="fas fa-shopping-bag"></i> Shop Gear
                    </a>
                </div>
            </div>
        </div>

        <!-- Platform Stats -->
        <div class="row mt-5">
            <div class="col-12 text-center mb-4">
                <h2 class="fw-bold">Join India's Fastest Growing Cricket Community</h2>
                <p class="text-muted">Trusted by thousands of cricket enthusiasts nationwide</p>
            </div>
            <?php
            $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
            $total_users = $stmt->fetch()['total_users'];
            
            $stmt = $pdo->query("SELECT COUNT(*) as total_venues FROM boxcricket_venues");
            $total_venues = $stmt->fetch()['total_venues'];
            
            $stmt = $pdo->query("SELECT COUNT(*) as total_coaches FROM coaching_ads");
            $total_coaches = $stmt->fetch()['total_coaches'];
            
            $stmt = $pdo->query("SELECT COUNT(*) as total_bookings FROM bookings");
            $total_bookings = $stmt->fetch()['total_bookings'];
            ?>
            <div class="col-md-3 mb-3">
                <div class="card card-custom text-center">
                    <div class="card-body">
                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                        <h2 class="fw-bold text-primary"><?= number_format($total_users) ?>+</h2>
                        <p class="text-muted mb-0">Active Players</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-custom text-center">
                    <div class="card-body">
                        <i class="fas fa-building fa-3x text-success mb-3"></i>
                        <h2 class="fw-bold text-success"><?= number_format($total_venues) ?>+</h2>
                        <p class="text-muted mb-0">Premium Venues</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-custom text-center">
                    <div class="card-body">
                        <i class="fas fa-graduation-cap fa-3x text-warning mb-3"></i>
                        <h2 class="fw-bold text-warning"><?= number_format($total_coaches) ?>+</h2>
                        <p class="text-muted mb-0">Expert Coaches</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card card-custom text-center">
                    <div class="card-body">
                        <i class="fas fa-calendar-check fa-3x text-info mb-3"></i>
                        <h2 class="fw-bold text-info"><?= number_format($total_bookings) ?>+</h2>
                        <p class="text-muted mb-0">Successful Bookings</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-custom">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-baseball-ball"></i> Cricket Connect Box</h5>
                    <p class="text-muted">India's premier cricket community platform connecting players, venues, and coaches nationwide.</p>
                    <div class="footer-links">
                        <a href="#" class="me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h6>Platform</h6>
                    <div class="footer-links">
                        <a href="search.php">Find Players</a><br>
                        <a href="boxcricket.php">Book Venues</a><br>
                        <a href="coaching.php">Coaching</a><br>
                        <a href="gear.php">Gear Store</a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h6>Account</h6>
                    <div class="footer-links">
                        <a href="profile.php">My Profile</a><br>
                        <a href="register.php">Sign Up</a><br>
                        <a href="login.php">Login</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h6>Contact & Support</h6>
                    <p class="mb-1"><i class="fas fa-envelope"></i> shyamnp27@gmail.com</p>
                    <p class="mb-1"><i class="fas fa-phone"></i> +91 98765 43210</p>
                    <p class="mb-0"><i class="fas fa-map-marker-alt"></i> Mumbai, India</p>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 Cricket Connect Box. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">Made with ❤️ for cricket lovers</small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add smooth scrolling and animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stats on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            }, observerOptions);
            
            document.querySelectorAll('.feature-card, .card-custom').forEach(card => {
                observer.observe(card);
            });
        });
    </script>
</body>
</html>