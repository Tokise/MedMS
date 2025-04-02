<?php
session_start();
require_once '../config/db.php';

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['show_demo'])) {
    $_SESSION['show_demo'] = true;
    $response = ['success' => true];
} elseif (isset($data['first_login'])) {
    $_SESSION['first_login'] = false;
    
    // Update user's demo status in database
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $query = "UPDATE users SET has_seen_demo = 1 WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
    
    $response = ['success' => true];
} else {
    $response = ['success' => false, 'error' => 'Invalid request'];
}

header('Content-Type: application/json');
echo json_encode($response);
