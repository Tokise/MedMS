<?php
session_start();
require_once '../config/db.php';

// Log the logout action if user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    
    // Insert logout action to logs
    $logQuery = "INSERT INTO system_logs (user_id, action, description, ip_address, user_agent, created_at) 
               VALUES (?, ?, ?, ?, ?, NOW())";
    $logStmt = $conn->prepare($logQuery);
    $action = 'Logout';
    $description = 'User logged out successfully';
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $logStmt->bind_param("issss", $userId, $action, $description, $ipAddress, $userAgent);
    $logStmt->execute();
}

// Unset all session variables
$_SESSION = array();

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: /MedMS/auth/login.php");
exit;
?> 