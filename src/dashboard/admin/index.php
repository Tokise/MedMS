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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/MedMS/styles/variables.css">
    <link rel="stylesheet" href="/MedMS/styles/admin-dashboard.css">
</head>
<body class="dark-theme">
    <?php include_once '../../../includes/header.php'; ?>
    
    <div class="container">
        <div class="dashboard-layout">
            <main class="dashboard-main">
                <div class="dashboard-header">
                    <h1 class="dashboard-title">Admin Dashboard</h1>
                    <div class="dashboard-actions">
                        <div class="action-buttons">
                            <button type="button" class="btn" id="startTutorial">
                                <i class="fas fa-question-circle"></i> Help
                            </button>
                            <button type="button" class="btn">
                                <i class="fas fa-file-export"></i> Export
                            </button>
                            <button type="button" class="btn">
                                <i class="fas fa-calendar"></i> This Week
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="stats-grid">
                    <div class="stats-card total-users">
                        <div class="card-body">
                            <div class="stats-content">
                                <h6>Total Users</h6>
                                <h3><?= $totalUsers ?></h3>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stats-card total-doctors">
                        <div class="card-body">
                            <div class="stats-content">
                                <h6>Doctors</h6>
                                <h3><?= $totalDoctors ?></h3>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-user-md"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stats-card total-nurses">
                        <div class="card-body">
                            <div class="stats-content">
                                <h6>Nurses</h6>
                                <h3><?= $totalNurses ?></h3>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stats-card total-teachers">
                        <div class="card-body">
                            <div class="stats-content">
                                <h6>Teachers</h6>
                                <h3><?= $totalTeachers ?></h3>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stats-card total-students">
                        <div class="card-body">
                            <div class="stats-content">
                                <h6>Students</h6>
                                <h3><?= $totalStudents ?></h3>
                            </div>
                            <div class="stats-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="charts-section">
                    <div class="chart-wrapper">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-chart-area"></i>
                                <span>Appointments Over Time</span>
                            </div>
                            <div class="card-body">
                                <canvas id="appointmentsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chart-wrapper">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-chart-pie"></i>
                                <span>User Distribution</span>
                            </div>
                            <div class="card-body">
                                <canvas id="userDistributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="system-logs">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-history"></i>
                            <span>Recent System Logs</span>
                        </div>
                        <div class="card-body">
                            <div class="table-container">
                                <table>
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
            </main>
        </div>
    </div>
    
    <div class="modal" id="tutorialModal">
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
                    <button type="button" class="btn" data-bs-dismiss="modal">Skip Tour</button>
                    <button type="button" class="btn" id="startTutorialFromModal">Start Tour</button>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once '../../../includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intro.js"></script>
    <script src="/MedMS/js/admin-dashboard.js"></script>
</body>
</html>
