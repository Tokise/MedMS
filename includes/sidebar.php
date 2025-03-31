<?php
// Determine which sidebar class to use based on user role
$sidebarClass = '';
if (isset($role)) {
    switch ($role) {
        case 'Admin':
            $sidebarClass = 'admin-sidebar';
            break;
        case 'Doctor':
            $sidebarClass = 'doctor-sidebar';
            break;
        case 'Nurse':
            $sidebarClass = 'nurse-sidebar';
            break;
        case 'Teacher':
            $sidebarClass = 'teacher-sidebar';
            break;
        case 'Student':
            $sidebarClass = 'student-sidebar';
            break;
        default:
            $sidebarClass = '';
    }
}
?>

<div class="sidebar <?= $sidebarClass ?>">
    <div class="sidebar-heading p-3 text-center">
        <img src="/MedMS/assets/img/logo.png" alt="MedMS Logo" class="img-fluid" style="max-width: 50px;">
        <h5 class="mt-2 mb-0">MedMS</h5>
        <small>Medical Management System</small>
    </div>
    
    <hr class="sidebar-divider">
    
    <ul class="nav flex-column">
        <?php if ($role === 'Admin'): ?>
            <!-- Admin Menu -->
            <li class="nav-item" data-intro="View your dashboard overview">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" 
                   href="/MedMS/src/dashboard/admin/index.php">
                    <i class="fas fa-fw fa-tachometer-alt me-2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item" data-intro="Manage all system users here">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'users') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/user/index.php">
                    <i class="fas fa-fw fa-users me-2"></i>
                    <span>User Management</span>
                </a>
            </li>
            <li class="nav-item" data-intro="Monitor medical staff schedules and availability">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'staff') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/staff/index.php">
                    <i class="fas fa-fw fa-user-md me-2"></i>
                    <span>Staff Availability</span>
                </a>
            </li>
            <li class="nav-item" data-intro="Generate and view system reports">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'reports') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/reports/index.php">
                    <i class="fas fa-fw fa-chart-bar me-2"></i>
                    <span>Reports & Statistics</span>
                </a>
            </li>
            <li class="nav-item" data-intro="Configure system-wide settings">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'settings') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/settings/index.php">
                    <i class="fas fa-fw fa-cog me-2"></i>
                    <span>System Settings</span>
                </a>
            </li>
            
        <?php elseif ($role === 'Doctor'): ?>
            <!-- Doctor Menu -->
            <li class="nav-item" data-intro="View your dashboard overview">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" 
                   href="/MedMS/src/dashboard/doctor/index.php">
                    <i class="fas fa-fw fa-tachometer-alt me-2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item" data-intro="Create and manage prescriptions">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'prescription') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/prescription/index.php">
                    <i class="fas fa-fw fa-prescription me-2"></i>
                    <span>Prescriptions</span>
                </a>
            </li>
            <li class="nav-item" data-intro="Search for students and view their medical records">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'search') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/search/index.php">
                    <i class="fas fa-fw fa-search me-2"></i>
                    <span>Patient Search</span>
                </a>
            </li>
            <li class="nav-item" data-intro="Manage your consultations and appointments">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'consultation') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/consultation/index.php">
                    <i class="fas fa-fw fa-stethoscope me-2"></i>
                    <span>Consultations</span>
                </a>
            </li>
            <li class="nav-item" data-intro="Configure system-wide settings">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'settings') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/settings/index.php">
                    <i class="fas fa-fw fa-cog me-2"></i>
                    <span>System Settings</span>
                </a>
            </li>
      
            
        <?php elseif ($role === 'Nurse'): ?>
            <!-- Nurse Menu -->
            <li class="nav-item" data-intro="View your dashboard overview">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" 
                   href="/MedMS/src/dashboard/nurse/index.php">
                    <i class="fas fa-fw fa-tachometer-alt me-2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item" data-intro="Record vital signs and assessments">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'vitals') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/vitals/index.php">
                    <i class="fas fa-fw fa-heartbeat me-2"></i>
                    <span>Vital Signs</span>
                </a>
            </li>
            <li class="nav-item" data-intro="Search for students and view their records">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'search') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/search/index.php">
                    <i class="fas fa-fw fa-search me-2"></i>
                    <span>Patient Search</span>
                </a>
            </li>
            <li class="nav-item" data-intro="Manage appointments and walk-ins">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'walkin') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/walkin/index.php">
                    <i class="fas fa-fw fa-calendar-check me-2"></i>
                    <span>Walk-ins</span>
                </a>
            </li>
            <li class="nav-item" data-intro="Configure system-wide settings">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'settings') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/settings/index.php">
                    <i class="fas fa-fw fa-cog me-2"></i>
                    <span>System Settings</span>
                </a>
            </li>
        
            
        <?php elseif ($role === 'Teacher'): ?>
            <!-- Teacher Menu -->
            <li class="nav-item" data-intro="View your dashboard overview">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" 
                   href="/MedMS/src/dashboard/teacher/index.php">
                    <i class="fas fa-fw fa-tachometer-alt me-2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item" data-intro="View your medical history">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'history') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/history/index.php">
                    <i class="fas fa-fw fa-history me-2"></i>
                    <span>Medical History</span>
                </a>
            </li>
            <li class="nav-item" data-intro="View your medications">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'medication') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/medication/index.php">
                    <i class="fas fa-fw fa-pills me-2"></i>
                    <span>Medications</span>
                </a>
            </li>
            <li class="nav-item" data-intro="View your prescriptions">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'prescription') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/prescription/index.php">
                    <i class="fas fa-fw fa-prescription me-2"></i>
                    <span>Prescriptions</span>
                </a>
            </li>
            <li class="nav-item" data-intro="Access AI-based medical consultation">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'ai') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/ai/index.php">
                    <i class="fas fa-fw fa-robot me-2"></i>
                    <span>AI Consultation</span>
                </a>
            </li>
            <li class="nav-item" data-intro="Configure system-wide settings">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'settings') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/settings/index.php">
                    <i class="fas fa-fw fa-cog me-2"></i>
                    <span>System Settings</span>
                </a>
            </li>
            
        <?php elseif ($role === 'Student'): ?>
            <!-- Student Menu -->
            <li class="nav-item" data-intro="View your dashboard overview">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" 
                   href="/MedMS/src/dashboard/student/index.php">
                    <i class="fas fa-fw fa-tachometer-alt me-2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item" data-intro="View your medical history">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'history') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/history/index.php">
                    <i class="fas fa-fw fa-history me-2"></i>
                    <span>Medical History</span>
                </a>
            </li>
            <li class="nav-item" data-intro="View your medications">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'medication') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/medication/index.php">
                    <i class="fas fa-fw fa-pills me-2"></i>
                    <span>Medications</span>
                </a>
            </li>
            <li class="nav-item" data-intro="View your prescriptions">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'prescription') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/prescription/index.php">
                    <i class="fas fa-fw fa-prescription me-2"></i>
                    <span>Prescriptions</span>
                </a>
            </li>
            <li class="nav-item" data-intro="Access AI-based medical consultation">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'ai') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/ai/index.php">
                    <i class="fas fa-fw fa-robot me-2"></i>
                    <span>AI Consultation</span>
                </a>
            </li>
            <li class="nav-item" data-intro="Configure system-wide settings">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'settings') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/settings/index.php">
                    <i class="fas fa-fw fa-cog me-2"></i>
                    <span>System Settings</span>
                </a>
            </li>
        <?php endif; ?>
        
        <!-- My Profile - Common for all users -->
        <li class="nav-item" data-intro="View and update your profile">
            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'profile') !== false ? 'active' : '' ?>" 
               href="/MedMS/src/modules/profile/index.php">
                <i class="fas fa-fw fa-user me-2"></i>
                <span>My Profile</span>
            </a>
        </li>
    </ul>
    
    <hr class="sidebar-divider d-none d-md-block">
    
    <!-- Sidebar Toggle (Visible Only on Mobile) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle">
            <i class="fas fa-angle-left"></i>
        </button>
    </div>
</div>

<!-- Demo Tutorial Modal moved to header.php for automatic display after login -->


