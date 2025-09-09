<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $emails = $input['emails'] ?? [];
    
    if (empty($emails)) {
        echo json_encode(['registered' => [], 'unregistered' => []]);
        exit;
    }
    
    // Add current user email
    $emails[] = $_SESSION['user_email'];
    $emails = array_unique($emails);
    
    // Check which emails are registered
    $placeholders = str_repeat('?,', count($emails) - 1) . '?';
    $stmt = $pdo->prepare("SELECT email FROM users WHERE email IN ($placeholders)");
    $stmt->execute($emails);
    $registered = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $unregistered = array_diff($emails, $registered);
    
    echo json_encode([
        'registered' => array_values($registered),
        'unregistered' => array_values($unregistered)
    ]);
}
?>