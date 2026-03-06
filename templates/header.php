<?php
// templates/header.php
require_once 'includes/config.php';
if (!is_logged_in()) {
    redirect('index.php');
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Dashboard'; ?> - FeeFlow</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar (Desktop) -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <i class="fas fa-wallet" style="color: var(--primary);"></i> FeeFlow
            </div>
            <nav style="display: flex; flex-direction: column; height: calc(100vh - 100px); justify-content: space-between;">
                <div>
                    <a href="dashboard.php" class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i> Home
                    </a>
                    <a href="classes.php" class="nav-link <?php echo $current_page == 'classes.php' ? 'active' : ''; ?>">
                        <i class="fas fa-school"></i> Classes
                    </a>
                    <a href="fee_categories.php" class="nav-link <?php echo $current_page == 'fee_categories.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tags"></i> Fee Categories
                    </a>
                    <a href="students.php" class="nav-link <?php echo $current_page == 'students.php' ? 'active' : ''; ?>">
                        <i class="fas fa-user-graduate"></i> Students
                    </a>
                    <a href="collect_fee.php" class="nav-link <?php echo $current_page == 'collect_fee.php' ? 'active' : ''; ?>">
                        <i class="fas fa-hand-holding-dollar"></i> Collect Fee
                    </a>
                    <a href="all_receipts.php" class="nav-link <?php echo $current_page == 'all_receipts.php' ? 'active' : ''; ?>">
                        <i class="fas fa-receipt"></i> All Receipts
                    </a>
                    <a href="reports.php" class="nav-link <?php echo $current_page == 'reports.php' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-line"></i> Analytics
                    </a>
                    <a href="profile.php" class="nav-link <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">
                        <i class="fas fa-sliders-h"></i> Settings
                    </a>
                </div>
                <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem; margin-bottom: 1rem;">
                    <a href="logout.php" class="nav-link" style="color: #fca5a5;">
                        <i class="fas fa-power-off"></i> Logout
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Mobile Nav -->
        <nav class="mobile-nav">
            <a href="dashboard.php" class="mobile-nav-item <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="students.php" class="mobile-nav-item <?php echo $current_page == 'students.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-graduate"></i>
                <span>Students</span>
            </a>
            <a href="collect_fee.php" class="mobile-nav-item <?php echo $current_page == 'collect_fee.php' ? 'active' : ''; ?>">
                <i class="fas fa-circle-plus" style="font-size: 2rem; color: var(--primary);"></i>
            </a>
            <a href="reports.php" class="mobile-nav-item <?php echo $current_page == 'reports.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span>Reports</span>
            </a>
            <a href="profile.php" class="mobile-nav-item <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-gear"></i>
                <span>Settings</span>
            </a>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header-bar">
                <h2 class="fade-in"><?php echo $page_title ?? 'Dashboard'; ?></h2>
                <div class="user-profile glass-card" style="padding: 0.5rem 1rem; display: flex; align-items: center; gap: 0.75rem;">
                    <strong><?php echo $_SESSION['institute_name']; ?></strong>
                </div>
            </header>
            <div class="content-body fade-in">
