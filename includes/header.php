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
    <link rel="stylesheet" href="/MedMS/styles/variables.css">
    <link rel="stylesheet" href="/MedMS/styles/global.css">
    <link rel="stylesheet" href="/MedMS/styles/dashboard.css">
    
   
    <!-- jQuery (needed for some Bootstrap features) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle JS (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Intro.js for guided tour -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/6.0.0/intro.min.js"></script>
</head>
<body class="dark-theme">
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-md fixed-top">
        <div class="container-fluid">
            <?php if (isset($_SESSION['user_id'])): ?>
                <button class="btn btn-link d-md-none rounded-circle mr-3" id="sidebarToggleTop">
                    <i class="fa fa-bars"></i>
                </button>
            <?php endif; ?>
            
            <a class="navbar-brand" href="/MedMS/index.php">
               <img src="/MedMS/assets/img/logo.png" alt="MedMS Logo" class="img-fluid" style="max-width: 50px;">MedMS
            </a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Topbar Search -->
                    <?php if (in_array($role, ['Doctor', 'Nurse', 'Admin'])): ?>
                        <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search for..."
                                    aria-label="Search" aria-describedby="basic-addon2">
                                <button class="btn btn-primary" type="button" id="search-button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Navbar toggler for mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1" data-intro="View your notifications here">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <span class="badge badge-counter">3+</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">Alerts Center</h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small">Today</div>
                                        <span class="fw-bold">New prescription available</span>
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small" href="#">Show All Alerts</a>
                            </div>
                        </li>
                        
                        <!-- Messages -->
                        <li class="nav-item dropdown no-arrow mx-1" data-intro="Check your messages here">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <span class="badge badge-counter">7</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow"
                                aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">Message Center</h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60" alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Message preview goes here...</div>
                                        <div class="small">Sender · Time</div>
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small" href="#">Read More Messages</a>
                            </div>
                        </li>
                        
                        <!-- Theme Toggle -->
                        <li class="nav-item mx-1" data-intro="Toggle between light and dark mode">
                            <a class="nav-link" href="#" id="themeToggle">
                                <i class="fas fa-moon"></i>
                            </a>
                        </li>
                        
                        <!-- User Information -->
                        <li class="nav-item dropdown no-arrow" data-intro="Access your profile settings and logout here">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-none d-lg-inline small me-2"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></span>
                                <img class="profile-img" src="<?= !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'https://via.placeholder.com/150' ?>" alt="Profile">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="/MedMS/src/modules/profile/index.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#" id="startTutorial">
                                    <i class="fas fa-question-circle fa-sm fa-fw mr-2"></i>
                                    Take Tour
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="/MedMS/auth/logout.php">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/MedMS/auth/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/MedMS/auth/signup.php">Sign Up</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Wrapper -->
    <div class="wrapper d-flex">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php include_once __DIR__ . '/sidebar.php'; ?>
        <?php endif; ?>
        
        <!-- Main Content -->
        <div class="main-content <?= !isset($_SESSION['user_id']) ? 'w-100' : '' ?>" data-intro="This is the main content area where you'll interact with the system">
            <div class="container-fluid">
                
            <?php if ($showTutorial): ?>
            <!-- Tutorial Modal - Will be shown automatically on first login -->
            <div class="modal fade" id="tutorialModal" tabindex="-1" aria-labelledby="tutorialModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="tutorialModalLabel">Welcome to MedMS!</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Would you like to take a quick tour of the system?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Skip Tour</button>
                            <button type="button" class="btn btn-primary" id="startTutorialFromModal">Start Tour</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function(element) {
        return new bootstrap.Dropdown(element);
    });
    
    // Show tutorial modal if needed
    <?php if ($showTutorial): ?>
    var tutorialModal = new bootstrap.Modal(document.getElementById('tutorialModal'));
    tutorialModal.show();
    <?php endif; ?>
    
    // Toggle sidebar on mobile
    if (document.getElementById('sidebarToggleTop')) {
        document.getElementById('sidebarToggleTop').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('toggled');
        });
    }
    
    // Start tutorial when button is clicked
    if (document.getElementById('startTutorial')) {
        document.getElementById('startTutorial').addEventListener('click', function(e) {
            e.preventDefault();
            introJs().start();
        });
    }
    
    // Start tutorial from modal when button is clicked
    if (document.getElementById('startTutorialFromModal')) {
        document.getElementById('startTutorialFromModal').addEventListener('click', function() {
            var tutorialModal = bootstrap.Modal.getInstance(document.getElementById('tutorialModal'));
            tutorialModal.hide();
            setTimeout(function() {
                introJs().start();
            }, 500);
        });
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
</script>
</body>
</html>
