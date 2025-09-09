<?php
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect without database first
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute SQL file
    $sql = file_get_contents('database.sql');
    $pdo->exec($sql);
    
    echo "Database created successfully!<br>";
    echo "<a href='index.php'>Go to Cricket Connect Box</a>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>