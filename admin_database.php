<?php
require_once 'config.php';
if (!isAdmin()) redirect('login.php');

// Handle database operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['backup_db'])) {
        $backup_file = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $command = "mysqldump --user=root --host=localhost cricket_connect_box > backups/$backup_file";
        exec($command);
        $success = "Database backup created: $backup_file";
    }
    
    if (isset($_POST['execute_sql'])) {
        $sql = $_POST['sql_query'];
        try {
            if (stripos($sql, 'SELECT') === 0) {
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $results = $stmt->fetchAll();
            } else {
                $pdo->exec($sql);
                $success = "SQL executed successfully";
            }
        } catch(PDOException $e) {
            $error = "SQL Error: " . $e->getMessage();
        }
    }
}

// Get table info
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Management - Cricket Connect Box</title>
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
                    <a class="nav-link admin-nav-link active" href="admin_database.php">
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
                        <h2><i class="fas fa-database"></i> Database Management</h2>
                    </div>
                </div>

                <div class="container-fluid py-4">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <!-- Database Overview -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card admin-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-table"></i> Database Tables</h5>
                                </div>
                                <div class="card-body">
                                    <?php foreach($tables as $table): ?>
                                        <?php
                                        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
                                        ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span><i class="fas fa-table text-primary"></i> <?= $table ?></span>
                                            <span class="badge bg-secondary"><?= $count ?> rows</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card admin-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-download"></i> Database Backup</h5>
                                </div>
                                <div class="card-body">
                                    <p>Create a backup of the entire database.</p>
                                    <form method="POST">
                                        <button type="submit" name="backup_db" class="btn btn-success">
                                            <i class="fas fa-download"></i> Create Backup
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SQL Query Executor -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card admin-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-code"></i> SQL Query Executor</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">SQL Query</label>
                                            <textarea class="form-control" name="sql_query" rows="5" placeholder="Enter your SQL query here..." required></textarea>
                                            <small class="text-muted">⚠️ Be careful with DELETE, UPDATE, and DROP statements!</small>
                                        </div>
                                        <button type="submit" name="execute_sql" class="btn btn-primary">
                                            <i class="fas fa-play"></i> Execute Query
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Query Results -->
                    <?php if (isset($results)): ?>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card admin-card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-table"></i> Query Results</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <?php if (!empty($results)): ?>
                                                    <thead>
                                                        <tr>
                                                            <?php foreach(array_keys($results[0]) as $column): ?>
                                                                <th><?= htmlspecialchars($column) ?></th>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($results as $row): ?>
                                                            <tr>
                                                                <?php foreach($row as $value): ?>
                                                                    <td><?= htmlspecialchars($value) ?></td>
                                                                <?php endforeach; ?>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                <?php else: ?>
                                                    <tr><td>No results found</td></tr>
                                                <?php endif; ?>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Quick Actions -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card admin-card">
                                <div class="card-header">
                                    <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <button class="btn btn-outline-primary w-100 mb-2" onclick="setQuery('SELECT * FROM users ORDER BY created_at DESC LIMIT 10')">
                                                Recent Users
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <button class="btn btn-outline-success w-100 mb-2" onclick="setQuery('SELECT * FROM bookings ORDER BY created_at DESC LIMIT 10')">
                                                Recent Bookings
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <button class="btn btn-outline-warning w-100 mb-2" onclick="setQuery('SELECT COUNT(*) as total_revenue, SUM(total_amount) as revenue FROM bookings')">
                                                Revenue Stats
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <button class="btn btn-outline-info w-100 mb-2" onclick="setQuery('SHOW TABLE STATUS')">
                                                Table Status
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setQuery(query) {
            document.querySelector('textarea[name="sql_query"]').value = query;
        }
    </script>
</body>
</html>