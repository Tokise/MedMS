<?php
session_start();
require_once '../../../config/db.php';

// Check if user is logged in and has nurse role
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'Nurse') {
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
$totalVisitsQuery = "SELECT COUNT(*) as count FROM clinic_visits";
$totalVisits = $conn->query($totalVisitsQuery)->fetch_assoc()['count'];

$todayVisitsQuery = "SELECT COUNT(*) as count FROM clinic_visits WHERE DATE(visit_date) = CURDATE()";
$todayVisits = $conn->query($todayVisitsQuery)->fetch_assoc()['count'];

$currentVisitsQuery = "SELECT COUNT(*) as count FROM clinic_visits 
                    WHERE DATE(visit_date) = CURDATE() AND status = 'in-progress'";
$currentVisits = $conn->query($currentVisitsQuery)->fetch_assoc()['count'];

// Get today's clinic visits
$todayQuery = "SELECT cv.*, u.first_name, u.last_name, u.school_id, u.profile_image
              FROM clinic_visits cv
              JOIN users u ON cv.patient_id = u.user_id
              WHERE DATE(cv.visit_date) = CURDATE()
              ORDER BY cv.visit_date DESC";
$todayVisitsList = $conn->query($todayQuery)->fetch_all(MYSQLI_ASSOC);

// Get inventory alerts (items with low stock)
$inventoryAlertsQuery = "SELECT * FROM medical_supplies 
                       WHERE current_quantity <= reorder_level
                       ORDER BY current_quantity ASC
                       LIMIT 5";
$inventoryAlerts = $conn->query($inventoryAlertsQuery)->fetch_all(MYSQLI_ASSOC);

// Pass the role to be used in the sidebar
$role = 'Nurse';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nurse Dashboard - MedMS</title>
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
                    <h1 class="h2" data-intro="Welcome to your dashboard! This is where you can see an overview of clinic activities.">Nurse Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="startTutorial">
                                <i class="fas fa-question-circle"></i> Help
                            </button>
                            <a href="/MedMS/src/modules/clinic_visit/create.php" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> New Visit
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Welcome Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card welcome-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <img src="<?= !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'https://via.placeholder.com/150' ?>" class="rounded-circle nurse-profile-img" alt="Nurse Profile" width="80" height="80">
                                    </div>
                                    <div class="col">
                                        <h4>Welcome, <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h4>
                                        <p class="text-muted mb-0"><?= date('l, F j, Y') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row mb-4" data-intro="These cards show key statistics about clinic visits and inventory">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card total-patients">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="text-muted">Total Visits</h6>
                                        <h3 class="fw-bold mt-2"><?= $totalVisits ?></h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-hospital-user fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card total-appointments">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="text-muted">Today's Visits</h6>
                                        <h3 class="fw-bold mt-2"><?= $todayVisits ?></h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-day fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card total-prescriptions">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="text-muted">Current Visits</h6>
                                        <h3 class="fw-bold mt-2"><?= $currentVisits ?></h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-procedures fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stats-card inventory-alerts">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="text-muted">Inventory Alerts</h6>
                                        <h3 class="fw-bold mt-2"><?= count($inventoryAlerts) ?></h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Two-column layout for today's visits and inventory alerts -->
                <div class="row mb-4">
                    <!-- Today's Clinic Visits -->
                    <div class="col-lg-8 mb-4">
                        <div class="card" data-intro="Here you can see all visits to the clinic today">
                            <div class="card-header">
                                <i class="fas fa-clipboard-list me-1"></i>
                                Today's Clinic Visits (<?= count($todayVisitsList) ?>)
                            </div>
                            <div class="card-body">
                                <?php if (count($todayVisitsList) > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Time</th>
                                                    <th>ID</th>
                                                    <th>Patient</th>
                                                    <th>Complaint</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($todayVisitsList as $visit): ?>
                                                    <tr>
                                                        <td><?= date('h:i A', strtotime($visit['visit_date'])) ?></td>
                                                        <td><?= htmlspecialchars($visit['school_id']) ?></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <img src="<?= !empty($visit['profile_image']) ? htmlspecialchars($visit['profile_image']) : 'https://via.placeholder.com/40' ?>" class="rounded-circle me-2" width="40" height="40" alt="Patient">
                                                                <?= htmlspecialchars($visit['first_name'] . ' ' . $visit['last_name']) ?>
                                                            </div>
                                                        </td>
                                                        <td><?= htmlspecialchars($visit['chief_complaint']) ?></td>
                                                        <td>
                                                            <?php
                                                            switch ($visit['status']) {
                                                                case 'waiting':
                                                                    echo '<span class="badge bg-warning">Waiting</span>';
                                                                    break;
                                                                case 'in-progress':
                                                                    echo '<span class="badge bg-info">In Progress</span>';
                                                                    break;
                                                                case 'completed':
                                                                    echo '<span class="badge bg-success">Completed</span>';
                                                                    break;
                                                                case 'referred':
                                                                    echo '<span class="badge bg-primary">Referred</span>';
                                                                    break;
                                                                default:
                                                                    echo '<span class="badge bg-secondary">Unknown</span>';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <a href="/MedMS/src/modules/clinic_visit/view.php?id=<?= $visit['visit_id'] ?>" class="btn btn-sm btn-info">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="/MedMS/src/modules/clinic_visit/process.php?id=<?= $visit['visit_id'] ?>" class="btn btn-sm btn-success">
                                                                <i class="fas fa-heartbeat"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No clinic visits recorded for today.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Inventory Alerts and Quick Actions -->
                    <div class="col-lg-4">
                        <!-- Inventory Alerts -->
                        <div class="card mb-4" data-intro="This section shows medical supplies that need restocking">
                            <div class="card-header">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Inventory Alerts
                            </div>
                            <div class="card-body">
                                <?php if (count($inventoryAlerts) > 0): ?>
                                    <ul class="list-group">
                                        <?php foreach ($inventoryAlerts as $item): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="fw-bold"><?= htmlspecialchars($item['item_name']) ?></span>
                                                    <div class="text-muted">
                                                        Stock: <span class="text-danger"><?= $item['current_quantity'] ?></span> / <?= $item['reorder_level'] ?>
                                                    </div>
                                                </div>
                                                <a href="/MedMS/src/modules/inventory/update.php?id=<?= $item['item_id'] ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-plus"></i> Restock
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle me-2"></i>
                                        All inventory items are adequately stocked.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="card" data-intro="Quick access to common tasks">
                            <div class="card-header">
                                <i class="fas fa-tasks me-1"></i>
                                Quick Actions
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="/MedMS/src/modules/clinic_visit/create.php" class="list-group-item list-group-item-action">
                                        <i class="fas fa-plus-circle me-2"></i> Record New Visit
                                    </a>
                                    <a href="/MedMS/src/modules/health_record/search.php" class="list-group-item list-group-item-action">
                                        <i class="fas fa-search me-2"></i> Search Health Records
                                    </a>
                                    <a href="/MedMS/src/modules/medical_notes/create.php" class="list-group-item list-group-item-action">
                                        <i class="fas fa-notes-medical me-2"></i> Add Medical Notes
                                    </a>
                                    <a href="/MedMS/src/modules/inventory/index.php" class="list-group-item list-group-item-action">
                                        <i class="fas fa-boxes me-2"></i> Manage Inventory
                                    </a>
                                    <a href="/MedMS/src/modules/reports/daily.php" class="list-group-item list-group-item-action">
                                        <i class="fas fa-chart-line me-2"></i> Generate Daily Report
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Visit Statistics and Recent Activities -->
                <div class="row mb-4" data-intro="These charts show visit trends and recent activities">
                    <!-- Visit Statistics Chart -->
                    <div class="col-xl-8 col-lg-7">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-line me-1"></i>
                                Weekly Visit Statistics
                            </div>
                            <div class="card-body">
                                <canvas id="visitsStatsChart" width="100%" height="40"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activities -->
                    <div class="col-xl-4 col-lg-5">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-history me-1"></i>
                                Recent Activities
                            </div>
                            <div class="card-body">
                                <ul class="timeline">
                                    <li class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <h5 class="timeline-title">Inventory updated</h5>
                                            <p class="timeline-text">Restocked Paracetamol tablets</p>
                                            <p class="timeline-date">Today, 10:45 AM</p>
                                        </div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <h5 class="timeline-title">Patient visit completed</h5>
                                            <p class="timeline-text">Sarah Johnson (ID: S12345)</p>
                                            <p class="timeline-date">Today, 9:15 AM</p>
                                        </div>
                                    </li>
                                    <li class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-content">
                                            <h5 class="timeline-title">Daily report generated</h5>
                                            <p class="timeline-text">For May 16, 2023</p>
                                            <p class="timeline-date">Yesterday, 5:00 PM</p>
                                        </div>
                                    </li>
                                </ul>
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
                    <h5 class="modal-title" id="tutorialModalLabel">Welcome to Nurse Dashboard</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Welcome to the Nurse Dashboard</h6>
                    <p>As a healthcare provider, you can:</p>
                    <ul>
                        <li>Record and manage clinic visits</li>
                        <li>Track inventory and medical supplies</li>
                        <li>Access patient health records</li>
                        <li>Generate daily and weekly reports</li>
                        <li>Manage medication distribution</li>
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
        // Visit Stats Chart
        var visitsStatsCtx = document.getElementById('visitsStatsChart').getContext('2d');
        var visitsStatsChart = new Chart(visitsStatsCtx, {
            type: 'line',
            data: {
                labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                datasets: [{
                    label: 'Clinic Visits',
                    data: [15, 12, 18, 14, 20, 8, 5],
                    backgroundColor: 'rgba(74, 222, 128, 0.2)',
                    borderColor: 'rgba(74, 222, 128, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Referred Cases',
                    data: [3, 5, 4, 2, 6, 1, 0],
                    backgroundColor: 'rgba(251, 146, 60, 0.2)',
                    borderColor: 'rgba(251, 146, 60, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        }
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
