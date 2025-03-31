<?php
session_start();
require_once '../config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role_name'];
    
    // Redirect based on role
    switch ($role) {
        case 'Admin':
            header("Location: /MedMS/src/dashboard/admin/index.php");
            break;
        case 'Doctor':
            header("Location: /MedMS/src/dashboard/doctor/index.php");
            break;
        case 'Nurse':
            header("Location: /MedMS/src/dashboard/nurse/index.php");
            break;
        case 'Teacher':
            header("Location: /MedMS/src/dashboard/teacher/index.php");
            break;
        case 'Student':
            header("Location: /MedMS/src/dashboard/student/index.php");
            break;
        default:
            header("Location: /MedMS/index.php");
    }
    exit;
}

$error = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $school_id = trim($_POST['school_id']);
    $password = $_POST['password'];
    
    if (empty($school_id) || empty($password)) {
        $error = "Please enter both School ID and password";
    } else {
        // Get user data
        $query = "SELECT u.user_id, u.username, u.school_id, u.password, u.email, u.first_name, u.last_name, 
                         r.role_id, r.role_name 
                  FROM users u 
                  JOIN roles r ON u.role_id = r.role_id 
                  WHERE u.school_id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $school_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $error = "Invalid School ID or password";
        } else {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['school_id'] = $user['school_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['role_name'] = $user['role_name'];
                $_SESSION['show_tutorial'] = true; // Flag to show tutorial after login
                
                // No need to update last_login as the column doesn't exist
                
                // Log login action
                $logQuery = "INSERT INTO system_logs (user_id, action, description, ip_address, user_agent, created_at) 
                           VALUES (?, ?, ?, ?, ?, NOW())";
                $logStmt = $conn->prepare($logQuery);
                $action = 'Login';
                $description = 'User logged in successfully';
                $ipAddress = $_SERVER['REMOTE_ADDR'];
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                $logStmt->bind_param("issss", $user['user_id'], $action, $description, $ipAddress, $userAgent);
                $logStmt->execute();
                
                // Redirect based on role
                switch ($user['role_name']) {
                    case 'Admin':
                        header("Location: /MedMS/src/dashboard/admin/index.php");
                        break;
                    case 'Doctor':
                        header("Location: /MedMS/src/dashboard/doctor/index.php");
                        break;
                    case 'Nurse':
                        header("Location: /MedMS/src/dashboard/nurse/index.php");
                        break;
                    case 'Teacher':
                        header("Location: /MedMS/src/dashboard/teacher/index.php");
                        break;
                    case 'Student':
                        header("Location: /MedMS/src/dashboard/student/index.php");
                        break;
                    default:
                        header("Location: /MedMS/index.php");
                }
                exit;
            } else {
                $error = "Invalid School ID or password";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MedMS</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/MedMS/styles/global.css">
    <link rel="stylesheet" href="/MedMS/styles/auth.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card login-card o-hidden border-0 shadow-lg">
                    <div class="card-header">
                        <div class="text-center mb-3">
                            <img src="/MedMS/assets/img/logo.png" alt="MedMS Logo" height="70">
                        </div>
                        <h1 class="h4 text-gray-900 mb-2 text-center">Welcome Back!</h1>
                        <p class="mb-0 text-center">MedMS - Medical Management System</p>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>" class="user">
                            <div class="mb-4">
                                <label for="school_id" class="form-label">School ID</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-id-card"></i>
                                    </span>
                                    <input type="text" class="form-control" id="school_id" name="school_id" 
                                           placeholder="Enter School ID" required 
                                           value="<?= isset($_POST['school_id']) ? htmlspecialchars($_POST['school_id']) : '' ?>">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Enter Password" required>
                                    <span class="input-group-text password-toggle" style="cursor: pointer;">
                                        <i class="fas fa-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mb-4 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        Remember Me
                                    </label>
                                </div>
                                <a href="#" class="forgot-password">Forgot Password?</a>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                Login
                            </button>
                            
                            <div class="text-center">
                                <div class="divider">
                                    <span>OR</span>
                                </div>
                                
                                <p>Don't have an account? <a href="signup.php" class="font-weight-bold">Create Account</a></p>
                                <a href="/MedMS/index.php" class="btn btn-link mt-2">
                                    <i class="fas fa-arrow-left me-1"></i> Back to Home
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password visibility toggle
        const toggleButton = document.querySelector('.password-toggle');
        toggleButton.addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                passwordField.type = 'password';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            }
        });
    });
    </script>
</body>
</html>
