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
    <ul class="nav flex-column">
        <?php if ($role === 'Admin'): ?>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" 
                   href="/MedMS/src/dashboard/admin/index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item" title="User Management">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'users') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/user/index.php">
                    <i class="fas fa-fw fa-users"></i>
                    <span>User Management</span>
                </a>
            </li>
            <li class="nav-item" title="Staff Availability">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'staff') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/staff/index.php">
                    <i class="fas fa-fw fa-user-md"></i>
                    <span>Staff Availability</span>
                </a>
            </li>
            <li class="nav-item" title="Reports & Statistics">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'reports') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/reports/index.php">
                    <i class="fas fa-fw fa-chart-bar"></i>
                    <span>Reports & Statistics</span>
                </a>
            </li>
   
            
        <?php elseif ($role === 'Doctor'): ?>
            <!-- Doctor Menu -->
            <li class="nav-item" title="Dashboard">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" 
                   href="/MedMS/src/dashboard/doctor/index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item" title="Prescriptions">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'prescription') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/prescription/index.php">
                    <i class="fas fa-fw fa-prescription"></i>
                    <span>Prescriptions</span>
                </a>
            </li>
            <li class="nav-item" title="Patient Search">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'search') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/search/index.php">
                    <i class="fas fa-fw fa-search"></i>
                    <span>Patient Search</span>
                </a>
            </li>
            <li class="nav-item" title="Consultations">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'consultation') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/consultation/index.php">
                    <i class="fas fa-fw fa-stethoscope"></i>
                    <span>Consultations</span>
                </a>
            </li>
       
      
            
        <?php elseif ($role === 'Nurse'): ?>
            <!-- Nurse Menu -->
            <li class="nav-item" title="Dashboard">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" 
                   href="/MedMS/src/dashboard/nurse/index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item" title="Vital Signs">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'vitals') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/vitals/index.php">
                    <i class="fas fa-fw fa-heartbeat"></i>
                    <span>Vital Signs</span>
                </a>
            </li>
            <li class="nav-item" title="Patient Search">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'search') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/search/index.php">
                    <i class="fas fa-fw fa-search"></i>
                    <span>Patient Search</span>
                </a>
            </li>
            <li class="nav-item" title="Walk-ins">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'walkin') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/walkin/index.php">
                    <i class="fas fa-fw fa-calendar-check"></i>
                    <span>Walk-ins</span>
                </a>
            </li>
      
        
            
        <?php elseif ($role === 'Teacher'): ?>
            <!-- Teacher Menu -->
            <li class="nav-item" title="Dashboard">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" 
                   href="/MedMS/src/dashboard/teacher/index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item" title="Medical History">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'history') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/history/index.php">
                    <i class="fas fa-fw fa-history"></i>
                    <span>Medical History</span>
                </a>
            </li>
            <li class="nav-item" title="Medications">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'medication') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/medication/index.php">
                    <i class="fas fa-fw fa-pills"></i>
                    <span>Medications</span>
                </a>
            </li>
            <li class="nav-item" title="Prescriptions">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'prescription') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/prescription/index.php">
                    <i class="fas fa-fw fa-prescription"></i>
                    <span>Prescriptions</span>
                </a>
            </li>
            <li class="nav-item" title="AI Consultation">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'ai') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/ai/index.php">
                    <i class="fas fa-fw fa-robot"></i>
                    <span>AI Consultation</span>
                </a>
            </li>
        
            
        <?php elseif ($role === 'Student'): ?>
            <!-- Student Menu -->
            <li class="nav-item" title="Dashboard">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" 
                   href="/MedMS/src/dashboard/student/index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item" title="Medical History">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'history') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/history/index.php">
                    <i class="fas fa-fw fa-history"></i>
                    <span>Medical History</span>
                </a>
            </li>
            <li class="nav-item" title="Medications">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'medication') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/medication/index.php">
                    <i class="fas fa-fw fa-pills"></i>
                    <span>Medications</span>
                </a>
            </li>
            <li class="nav-item" title="Prescriptions">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'prescription') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/prescription/index.php">
                    <i class="fas fa-fw fa-prescription"></i>
                    <span>Prescriptions</span>
                </a>
            </li>
            <li class="nav-item" title="AI Consultation">
                <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'ai') !== false ? 'active' : '' ?>" 
                   href="/MedMS/src/modules/ai/index.php">
                    <i class="fas fa-fw fa-robot"></i>
                    <span>AI Consultation</span>
                </a>
            </li>
          
        <?php endif; ?>
        
       
    </ul>
    
    
</div>

<!-- Demo Tutorial Modal moved to header.php for automatic display after login -->


