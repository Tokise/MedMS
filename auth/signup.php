<?php
session_start();
require_once '../config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: /MedMS/index.php");
    exit;
}

$error = '';
$success = '';

// Get available roles (exclude Admin)
$rolesQuery = "SELECT * FROM roles WHERE role_name != 'Admin' ORDER BY role_name";
$rolesResult = $conn->query($rolesQuery);
$roles = $rolesResult->fetch_all(MYSQLI_ASSOC);

// Process signup form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and validate
    $school_id = trim($_POST['school_id']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $role_id = $_POST['role_id'];
    
    // Basic validation
    if (empty($school_id) || empty($username) || empty($email) || empty($password) || empty($confirmPassword) || 
        empty($firstName) || empty($lastName) || empty($role_id)) {
        $error = "All fields are required";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Check if school_id already exists
        $checkSchoolId = "SELECT user_id FROM users WHERE school_id = ?";
        $stmt = $conn->prepare($checkSchoolId);
        $stmt->bind_param("s", $school_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "School ID already exists";
        } else {
            // Check if username already exists
            $checkUsername = "SELECT user_id FROM users WHERE username = ?";
            $stmt = $conn->prepare($checkUsername);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $error = "Username already exists";
            } else {
                // Check if email already exists
                $checkEmail = "SELECT user_id FROM users WHERE email = ?";
                $stmt = $conn->prepare($checkEmail);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    $error = "Email already exists";
                } else {
                    // Hash password
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert into users table
                    $insertQuery = "INSERT INTO users (role_id, school_id, username, password, email, first_name, last_name, created_at) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                    $stmt = $conn->prepare($insertQuery);
                    $stmt->bind_param("issssss", $role_id, $school_id, $username, $hashedPassword, $email, $firstName, $lastName);
                    
                    if ($stmt->execute()) {
                        $userId = $stmt->insert_id;
                        
                        // Get role name
                        $roleQuery = "SELECT role_name FROM roles WHERE role_id = ?";
                        $roleStmt = $conn->prepare($roleQuery);
                        $roleStmt->bind_param("i", $role_id);
                        $roleStmt->execute();
                        $roleResult = $roleStmt->get_result();
                        $role = $roleResult->fetch_assoc();
                        
                        // Create role-specific profile
                        if ($role['role_name'] === 'Student') {
                            $profileQuery = "INSERT INTO students (user_id) VALUES (?)";
                            $profileStmt = $conn->prepare($profileQuery);
                            $profileStmt->bind_param("i", $userId);
                            $profileStmt->execute();
                        } elseif ($role['role_name'] === 'Teacher') {
                            $profileQuery = "INSERT INTO teachers (user_id) VALUES (?)";
                            $profileStmt = $conn->prepare($profileQuery);
                            $profileStmt->bind_param("i", $userId);
                            $profileStmt->execute();
                        } elseif ($role['role_name'] === 'Doctor' || $role['role_name'] === 'Nurse') {
                            $profileQuery = "INSERT INTO medical_staff (user_id) VALUES (?)";
                            $profileStmt = $conn->prepare($profileQuery);
                            $profileStmt->bind_param("i", $userId);
                            $profileStmt->execute();
                        }
                        
                        // Add registration to logs
                        $logQuery = "INSERT INTO system_logs (user_id, action, description, ip_address, user_agent, created_at) 
                                   VALUES (?, ?, ?, ?, ?, NOW())";
                        $logStmt = $conn->prepare($logQuery);
                        $action = 'Registration';
                        $description = 'New user registration';
                        $ipAddress = $_SERVER['REMOTE_ADDR'];
                        $userAgent = $_SERVER['HTTP_USER_AGENT'];
                        $logStmt->bind_param("issss", $userId, $action, $description, $ipAddress, $userAgent);
                        $logStmt->execute();
                        
                        $success = "Account created successfully! You can now login.";
                        // Redirect to login after 3 seconds
                        header("refresh:3;url=login.php");
                    } else {
                        $error = "Error: " . $stmt->error;
                    }
                }
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
    <title>Sign Up - MedMS</title>
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
            <div class="col-xl-10 col-lg-12 col-md-12">
                <div class="card signup-card o-hidden border-0 shadow-lg">
                    <div class="card-header">
                        <div class="icon-container">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h1 class="h4 text-gray-900 mb-2">Create an Account</h1>
                        <p class="mb-0">MedMS - Medical Management System</p>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>
                        
                        <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>" class="user">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="school_id" class="form-label">School ID</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-id-card"></i>
                                        </span>
                                        <input type="text" class="form-control left-border-none" id="school_id" name="school_id" 
                                               placeholder="Enter School ID" required 
                                               value="<?= isset($_POST['school_id']) ? htmlspecialchars($_POST['school_id']) : '' ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="role_id" class="form-label">Role</label>
                                    <select class="form-select" id="role_id" name="role_id" required>
                                        <option value="" selected disabled>Select Role</option>
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?= $role['role_id'] ?>" <?= (isset($_POST['role_id']) && $_POST['role_id'] == $role['role_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($role['role_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           placeholder="First Name" required 
                                           value="<?= isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : '' ?>">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           placeholder="Last Name" required 
                                           value="<?= isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : '' ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       placeholder="Username" required 
                                       value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="Email Address" required 
                                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" 
                                               placeholder="Password" required>
                                        <span class="input-group-text password-toggle" style="cursor: pointer;">
                                            <i class="fas fa-eye-slash"></i>
                                        </span>
                                    </div>
                                    <div class="password-strength">
                                        <div class="password-strength-meter"></div>
                                    </div>
                                    <small class="form-text text-muted">
                                        Password must be at least 8 characters long
                                    </small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                               placeholder="Confirm Password" required>
                                        <span class="input-group-text password-toggle" style="cursor: pointer;">
                                            <i class="fas fa-eye-slash"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                Register Account
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <p>Already have an account? <a href="login.php" class="font-weight-bold">Login</a></p>
                            <a href="/MedMS/index.php" class="btn btn-link mt-2">
                                <i class="fas fa-arrow-left me-1"></i> Back to Home
                            </a>
                        </div>
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
        const toggleButtons = document.querySelectorAll('.password-toggle');
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const passwordField = this.previousElementSibling;
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
        
        // Password strength meter
        const passwordField = document.getElementById('password');
        const strengthMeter = document.querySelector('.password-strength-meter');
        
        passwordField.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Calculate password strength
            if (password.length >= 8) strength += 1;
            if (password.match(/[A-Z]/)) strength += 1;
            if (password.match(/[0-9]/)) strength += 1;
            if (password.match(/[^A-Za-z0-9]/)) strength += 1;
            
            // Update strength meter
            strengthMeter.className = 'password-strength-meter';
            if (password.length === 0) {
                strengthMeter.style.width = '0';
            } else {
                switch (strength) {
                    case 1:
                        strengthMeter.classList.add('weak');
                        break;
                    case 2:
                        strengthMeter.classList.add('medium');
                        break;
                    case 3:
                        strengthMeter.classList.add('strong');
                        break;
                    case 4:
                        strengthMeter.classList.add('very-strong');
                        break;
                }
            }
        });
        
        // Check password match
        const confirmPasswordField = document.getElementById('confirm_password');
        
        confirmPasswordField.addEventListener('input', function() {
            if (this.value !== passwordField.value) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    });
    </script>
</body>
</html>
