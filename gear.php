<?php
require_once 'config.php';
if (!isLoggedIn()) redirect('login.php');

// Get gear products
$stmt = $pdo->query("SELECT * FROM gear_store ORDER BY created_at DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cricket Gear Store - Cricket Connect Box</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); }
        .navbar { background: linear-gradient(45deg, #2196f3, #21cbf3) !important; }
        .card { border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .product-card { border-left: 4px solid #e91e63; }
        .price-badge { background: linear-gradient(45deg, #e91e63, #ad1457); color: white; }
        .btn-buy { background: linear-gradient(45deg, #ff5722, #d84315); border: none; }
        .product-icon { font-size: 3rem; color: #ff5722; }
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
        <div class="text-center mb-5">
            <h2><i class="fas fa-shopping-cart"></i> Cricket Gear Store</h2>
            <p class="text-muted">Quality cricket equipment for passionate players</p>
        </div>

        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card product-card h-100">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-baseball-ball product-icon"></i>
                            </div>
                            
                            <h5 class="card-title mb-3">
                                <?= htmlspecialchars($product['product_name']) ?>
                            </h5>
                            
                            <div class="mb-3">
                                <span class="badge price-badge fs-6">
                                    ₹<?= number_format($product['price']) ?>
                                </span>
                            </div>
                            
                            <a href="<?= htmlspecialchars($product['affiliate_link']) ?>" 
                               target="_blank" 
                               class="btn btn-buy w-100">
                                <i class="fas fa-external-link-alt"></i> Buy Now
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5>No products available</h5>
                <p class="text-muted">Check back later for new cricket gear and equipment.</p>
            </div>
        <?php endif; ?>

        <!-- Featured Categories -->
        <div class="row mt-5">
            <div class="col-12">
                <h4 class="text-center mb-4">Popular Categories</h4>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-baseball-ball fa-2x text-primary mb-2"></i>
                        <h6>Cricket Bats</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                        <h6>Protective Gear</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-tshirt fa-2x text-warning mb-2"></i>
                        <h6>Cricket Kits</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-running fa-2x text-info mb-2"></i>
                        <h6>Footwear</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Affiliate Disclaimer -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Note:</strong> We are affiliated with various cricket equipment retailers. 
                    When you purchase through our links, we may earn a small commission at no extra cost to you. 
                    This helps us maintain and improve our platform.
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>