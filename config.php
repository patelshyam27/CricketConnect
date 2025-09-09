<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'cricket_connect_box';

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if tables exist, if not create them
    $result = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($result->rowCount() == 0) {
        $sql = file_get_contents(__DIR__ . '/database.sql');
        $statements = explode(';', $sql);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
    }
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'abpmech73@gmail.com';
}

function redirect($url) {
    header("Location: $url");
    exit();
}
?>