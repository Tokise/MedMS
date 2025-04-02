<?php

require_once __DIR__ . '/../config/db.php';

// Check if user is logged in, if not and trying to access protected page, redirect to login
$currentPage = basename($_SERVER['PHP_SELF']);
$publicPages = ['login.php', 'signup.php', 'index.php'];

if (!isset($_SESSION['user_id']) && !in_array($currentPage, $publicPages) && $currentPage !== 'index.php') {
    header("Location: /MedMS/auth/login.php");
    exit;
}

// Get user data if logged in
$user = null;
$role = null;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userQuery = "SELECT u.*, r.role_name FROM users u 
                  JOIN roles r ON u.role_id = r.role_id 
                  WHERE u.user_id = ?";
    
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $role = $user['role_name'];
}

// Check if tutorial should be shown (first time login)
$showTutorial = isset($_SESSION['show_tutorial']) && $_SESSION['show_tutorial'] === true;
if ($showTutorial) {
    // Reset the tutorial flag after showing it once
    $_SESSION['show_tutorial'] = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedMS - Medical Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/MedMS/styles/components.css">
    <!-- Add IntroJS CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/6.0.0/introjs.min.css">
    <!-- jQuery UI for better interactions -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    
   
    <!-- jQuery (needed for some Bootstrap features) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle JS (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Intro.js for guided tour -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/6.0.0/intro.min.js"></script>
</head>
<body class="dark-theme">
    <nav class="header-nav">
        <div class="header-container">
            <a class="brand" href="/MedMS/index.php">
                <img src="/MedMS/assets/img/logo.png" alt="MedMS Logo" class="brand-logo">
                <div class="brand-text">
                    <span class="brand-name gradient-text">MedMS</span>
                    <span class="brand-subtitle">Medical Management System</span>
                </div>
            </a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="nav-actions">
                    <div class="nav-item dropdown">
                        <button class="nav-link" id="notificationDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="badge">3</span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="notificationDropdown">
                            <a class="dropdown-item" href="#"><i class="fas fa-info-circle"></i> New Message</a>
                            <a class="dropdown-item" href="#"><i class="fas fa-calendar"></i> Appointment Update</a>
                            <a class="dropdown-item" href="#"><i class="fas fa-bell"></i> System Alert</a>
                        </div>
                    </div>
                    
                    <div class="nav-item dropdown">
                        <button class="nav-link" id="messageDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-envelope"></i>
                            <span class="badge">7</span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="messageDropdown">
                            <a class="dropdown-item" href="#"><i class="fas fa-envelope"></i> New Message</a>
                            <a class="dropdown-item" href="#"><i class="fas fa-inbox"></i> Inbox</a>
                            <a class="dropdown-item" href="#"><i class="fas fa-paper-plane"></i> Sent</a>
                        </div>
                    </div>
                    
                    <div class="nav-item">
                        <button class="nav-link" id="themeToggle">
                            <i class="fas fa-moon"></i>
                        </button>
                    </div>
                    
                    <div class="nav-item dropdown">
                        <button class="profile-toggle" id="userDropdown" data-bs-toggle="dropdown">
                            <img src="<?= !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'https://via.placeholder.com/150' ?>" 
                                 alt="Profile" class="profile-img">
                            <span class="profile-name"><?= htmlspecialchars($user['first_name']) ?></span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="/MedMS/src/modules/profile/index.php">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                            <a class="dropdown-item" href="/MedMS/src/modules/settings/index.php">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                            <a class="dropdown-item" href="/MedMS/demo/index.php" onclick="startDemoTour(event)">
                                <i class="fas fa-desktop"></i> Try Demo
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="/MedMS/auth/logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="nav-actions">
                    <a href="/MedMS/auth/login.php" class="nav-link">Login</a>
                    <a href="/MedMS/auth/signup.php" class="nav-link">Sign Up</a>
                    <a href="/MedMS/demo/index.php" class="demo-btn">
                        <i class="fas fa-desktop"></i> Try Demo
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <?php if ($showTutorial): ?>
    <div class="tutorial-modal" id="tutorialModal">
        <div class="tutorial-content">
            <div class="tutorial-header">
                <h2>Welcome to MedMS!</h2>
                <button type="button" class="close-btn" onclick="closeTutorial()">&times;</button>
            </div>
            <div class="tutorial-body">
                <p>Would you like to take a quick tour of the system?</p>
            </div>
            <div class="tutorial-footer">
                <button class="btn btn-secondary" onclick="closeTutorial()">Skip Tour</button>
                <button class="btn btn-primary" onclick="startTutorial()">Start Tour</button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Wrapper -->
    <div class="wrapper d-flex">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php include_once __DIR__ . '/sidebar.php'; ?>
        <?php endif; ?>
        
    </div>

   
    <!-- Intro.js Initialization Script -->

<script type="text/javascript">
function closeTutorial() {
    document.getElementById('tutorialModal').style.display = 'none';
    sessionStorage.setItem('tutorial_dismissed', 'true');
}

function startTutorial() {
    document.getElementById('tutorialModal').style.display = 'none';
    startDemoTour();
}

// Add event listeners when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tutorial modal
    const tutorialModal = document.getElementById('tutorialModal');
    const closeBtn = tutorialModal ? tutorialModal.querySelector('.close-btn') : null;
    const skipBtn = tutorialModal ? tutorialModal.querySelector('.btn-secondary') : null;
    const startBtn = tutorialModal ? tutorialModal.querySelector('.btn-primary') : null;

    if (closeBtn) {
        closeBtn.addEventListener('click', closeTutorial);
    }
    if (skipBtn) {
        skipBtn.addEventListener('click', closeTutorial);
    }
    if (startBtn) {
        startBtn.addEventListener('click', startTutorial);
    }
    
    // Toggle between light and dark theme
    if (document.getElementById('themeToggle')) {
        document.getElementById('themeToggle').addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('dark-theme');
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-moon')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });
    }
});

// Update the startDemoTour function
function startDemoTour(event) {
    if (event) {
        event.preventDefault();
    }
    
    // Remove any existing intro.js elements
    const existingOverlay = document.querySelector('.introjs-overlay');
    const existingTooltip = document.querySelector('.introjs-tooltipReferenceLayer');
    if (existingOverlay) existingOverlay.remove();
    if (existingTooltip) existingTooltip.remove();
    
    // Store demo state in session
    sessionStorage.setItem('demo_active', 'true');
    
    // Get current page type and start appropriate tour
    const currentPath = window.location.pathname;
    if (currentPath.includes('/dashboard/student/')) {
        startStudentDashboardTour();
    } else if (currentPath.includes('/dashboard/doctor/')) {
        startDoctorDashboardTour();
    } else if (currentPath.includes('/dashboard/admin/')) {
        startAdminDashboardTour();
    } else {
        startGeneralTour();
    }
}

function startGeneralTour() {
    // Remove any existing intro overlay first
    const existingOverlay = document.querySelector('.introjs-overlay');
    if (existingOverlay) {
        existingOverlay.remove();
    }

}
</script>

<!-- Add custom styles for intro.js -->
<style>
.introjs-custom-highlight {
    background-color: rgba(255,255,255,0.1) !important;
    border-radius: 4px !important;
}

.introjs-tooltip {
    background-color: var(--primary-color);
    color: var(--text-color);
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.5);
}

.introjs-button {
    background-color: var(--accent-color) !important;
    color: white !important;
    border: none !important;
    text-shadow: none !important;
    padding: 8px 16px !important;
    border-radius: 4px !important;
}

.introjs-button:hover {
    background-color: var(--accent-hover-color) !important;
}

.introjs-overlay {
    opacity: 0.85 !important;
    background-color: var(--bg-primary) !important;
}

.tutorial-modal {
    display: flex;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.85);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}

.tutorial-content {
    background: var(--background-color);
    padding: 20px;
    border-radius: 8px;
    max-width: 500px;
    width: 90%;
}

.tutorial-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: var(--text-color);
}

.tutorial-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

.introjs-tooltip {
    min-width: 300px;
}

.introjs-button {
    cursor: pointer !important;
}
</style>
</body>
</html>
