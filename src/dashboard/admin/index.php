<?php
session_start();
require_once '../../../config/db.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'Admin') {
    header("Location: /MedMS/auth/login.php");
    exit;
}

// Get current user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get counts for dashboard stats
$totalUsersQuery = "SELECT COUNT(*) as count FROM users";
$totalUsers = $conn->query($totalUsersQuery)->fetch_assoc()['count'];

$totalDoctorsQuery = "SELECT COUNT(*) as count FROM users u 
                     JOIN roles r ON u.role_id = r.role_id 
                     WHERE r.role_name = 'Doctor'";
$totalDoctors = $conn->query($totalDoctorsQuery)->fetch_assoc()['count'];

$totalNursesQuery = "SELECT COUNT(*) as count FROM users u 
                    JOIN roles r ON u.role_id = r.role_id 
                    WHERE r.role_name = 'Nurse'";
$totalNurses = $conn->query($totalNursesQuery)->fetch_assoc()['count'];

$totalTeachersQuery = "SELECT COUNT(*) as count FROM users u 
                      JOIN roles r ON u.role_id = r.role_id 
                      WHERE r.role_name = 'Teacher'";
$totalTeachers = $conn->query($totalTeachersQuery)->fetch_assoc()['count'];

$totalStudentsQuery = "SELECT COUNT(*) as count FROM users u 
                      JOIN roles r ON u.role_id = r.role_id 
                      WHERE r.role_name = 'Student'";
$totalStudents = $conn->query($totalStudentsQuery)->fetch_assoc()['count'];

// Get recent system logs
$logsQuery = "SELECT sl.*, u.username, u.first_name, u.last_name 
             FROM system_logs sl
             JOIN users u ON sl.user_id = u.user_id
             ORDER BY sl.created_at DESC LIMIT 10";
$logs = $conn->query($logsQuery)->fetch_all(MYSQLI_ASSOC);

// Pass the role to be used in the sidebar
$role = 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MedMS</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/MedMS/styles/variables.css">
    <link rel="stylesheet" href="/MedMS/styles/global.css">
    <link rel="stylesheet" href="/MedMS/styles/dashboard.css">
    <!-- Intro.js for guided tour -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/6.0.0/introjs.min.css">
</head>
<body class="dark-theme">
    <?php include_once '../../../includes/header.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Dashboard Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2" data-intro="Welcome to your dashboard! This is where you can see an overview of the system.">Admin Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="startTutorial">
                                <i class="fas fa-question-circle"></i> Help
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-file-export"></i> Export
                            </button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                            <i class="fas fa-calendar"></i> This Week
                        </button>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row mb-4" data-intro="These cards show key statistics about your school's medical system">
                    <div class="col-xl-2 col-md-4 mb-4">
                        <div class="card stats-card total-users">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="text-muted">Total Users</h6>
                                        <h3 class="fw-bold mt-2"><?= $totalUsers ?></h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-4 mb-4">
                        <div class="card stats-card total-doctors">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="text-muted">Doctors</h6>
                                        <h3 class="fw-bold mt-2"><?= $totalDoctors ?></h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-md fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-4 mb-4">
                        <div class="card stats-card total-nurses">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="text-muted">Nurses</h6>
                                        <h3 class="fw-bold mt-2"><?= $totalNurses ?></h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-heartbeat fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-4 mb-4">
                        <div class="card stats-card total-teachers">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="text-muted">Teachers</h6>
                                        <h3 class="fw-bold mt-2"><?= $totalTeachers ?></h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-4 mb-4">
                        <div class="card stats-card total-students">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="text-muted">Students</h6>
                                        <h3 class="fw-bold mt-2"><?= $totalStudents ?></h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-graduate fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Charts Row -->
                <div class="row mb-4" data-intro="These charts provide visual representations of your data">
                    <div class="col-xl-8 col-lg-7">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-area me-1"></i>
                                Appointments Over Time
                            </div>
                            <div class="card-body">
                                <canvas id="appointmentsChart" width="100%" height="40"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-4 col-lg-5">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-pie me-1"></i>
                                User Distribution
                            </div>
                            <div class="card-body">
                                <canvas id="userDistributionChart" width="100%" height="50"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity & Logs Row -->
                <div class="row" data-intro="Here you can monitor recent system activity">
                    <div class="col-lg-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-history me-1"></i>
                                Recent System Logs
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Action</th>
                                                <th>Description</th>
                                                <th>IP Address</th>
                                                <th>Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($logs as $log): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($log['first_name'] . ' ' . $log['last_name']) ?></td>
                                                    <td><?= htmlspecialchars($log['action']) ?></td>
                                                    <td><?= htmlspecialchars($log['description']) ?></td>
                                                    <td><?= htmlspecialchars($log['ip_address']) ?></td>
                                                    <td><?= date('M d, Y g:i A', strtotime($log['created_at'])) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Tutorial Modal -->
    <div class="modal fade" id="tutorialModal" tabindex="-1" aria-labelledby="tutorialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tutorialModalLabel">Welcome to Admin Dashboard</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Welcome to the Admin Dashboard</h6>
                    <p>As an administrator, you can:</p>
                    <ul>
                        <li>Monitor student and staff statistics</li>
                        <li>Check medical staff availability</li>
                        <li>Generate reports</li>
                        <li>Manage system users</li>
                        <li>Configure system settings</li>
                    </ul>
                    <p>Would you like to take a quick tour of the system?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Skip Tour</button>
                    <button type="button" class="btn btn-primary" id="startTutorialFromModal">Start Tour</button>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once '../../../includes/footer.php'; ?>
    
    <!-- Custom JS for Charts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts
        // Appointments Chart
        var appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
        var appointmentsChart = new Chart(appointmentsCtx, {
            type: 'line',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                datasets: [{
                    label: 'Appointments',
                    data: [65, 59, 80, 81, 56, 55, 40],
                    backgroundColor: 'rgba(96, 165, 250, 0.2)',
                    borderColor: 'rgba(96, 165, 250, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // User Distribution Chart
        var userDistributionCtx = document.getElementById('userDistributionChart').getContext('2d');
        var userDistributionChart = new Chart(userDistributionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Students', 'Teachers', 'Doctors', 'Nurses', 'Admin'],
                datasets: [{
                    data: [<?= $totalStudents ?>, <?= $totalTeachers ?>, <?= $totalDoctors ?>, <?= $totalNurses ?>, 1],
                    backgroundColor: [
                        '#a78bfa', // Student - Purple
                        '#facc15', // Teacher - Yellow
                        '#4ade80', // Doctor - Green
                        '#60a5fa', // Nurse - Blue
                        '#f87171'  // Admin - Red
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
        
        // Display the tutorial modal if it's the user's first login
        <?php if (isset($_SESSION['show_tutorial']) && $_SESSION['show_tutorial']): ?>
            var tutorialModal = new bootstrap.Modal(document.getElementById('tutorialModal'));
            tutorialModal.show();
            <?php $_SESSION['show_tutorial'] = false; ?>
        <?php endif; ?>
    });
    </script>
</body>
</html>
