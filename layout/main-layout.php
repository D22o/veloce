<?php

function renderDashboardHeader(String $title, String $activePage, String $role = 'user') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once dirname(__DIR__) . '/backend/autoload.php';

    // --- AUTH GUARD: CHECK BASIC ASSIGNMENTS ---
    if (!isset($_SESSION['user_id']) || !isset($_COOKIE['veloce_auth_token'])) {
        session_destroy();
        clear_auth_cookie();
        header("Location: ". APP_URL . "/auth/login?error=please_login_first");
        exit();
    }

    // --- AUTH GUARD: CRYPTOGRAPHIC FINGERPRINT VERIFICATION ---
    $auth = verify_auth_cookie();
    if (!$auth) {
        clear_auth_cookie();
        session_destroy();
        header("Location: " . APP_URL . "/auth/login?error=invalid_auth_token");
        exit();
    }

    // --- AUTH GUARD: ROLE-BASED ACCESS CONTROL (RBAC) ---
    if ($_SESSION['user_role'] !== $role) {
        if ($_SESSION['user_role'] === 'admin') {
            header("Location: ". APP_URL . "/admin/admin-dashboard");
        } else {
            header("Location: ". APP_URL . "/user/user-dashboard");
        }
        exit();
    }

    // --- INITIALIZE PATH PARAMETERS & INTERFACE DETAILS ---
    $isAdmin = ($role === 'admin');
    
    // Grab initials cleanly from the active user's session data
    $fullName = $_SESSION['user_name'] ?? ($isAdmin ? 'Admin Console' : 'Guest Driver');
    $nameParts = explode(' ', trim($fullName));
    $initials = ($isAdmin) ? 'A' : strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1));
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veloce | <?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/png">
    <link rel="stylesheet" href="../assets/css/css-variables.css">
    <link rel="stylesheet" href="../assets/css/layout.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
    <link rel="stylesheet" href="../assets/css/media-queries.css">
    <link rel="stylesheet" href="../assets/css/modal-designs.css">
</head>
<body>

    <aside class="dash-sidebar" id="dashboardSidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-car-side"></i> VELOCE
        </div>
        <nav class="sidebar-menu">
            <?php if ($isAdmin): ?>
                <a href="admin-dashboard" class="menu-item <?php echo $activePage === 'dashboard' ? 'active' : ''; ?>"><i class="fa-solid fa-chart-pie"></i> Metrics Overview</a>
                <a href="manage-fleet" class="menu-item <?php echo $activePage === 'fleet' ? 'active' : ''; ?>"><i class="fa-solid fa-car"></i> Fleet Inventory</a>
                <a href="manage-bookings" class="menu-item <?php echo $activePage === 'bookings' ? 'active' : ''; ?>"><i class="fa-solid fa-calendar-check"></i> Booking Approvals</a>
                <a href="verify-users" class="menu-item <?php echo $activePage === 'users' ? 'active' : ''; ?>"><i class="fa-solid fa-user-check"></i> User Verifications</a>
            <?php else: ?>
                <a href="user-dashboard" class="menu-item <?php echo $activePage === 'dashboard' ? 'active' : ''; ?>"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
                <a href="fleet-listings" class="menu-item <?php echo $activePage === 'listings' ? 'active' : ''; ?>"><i class="fa-solid fa-car-rear"></i> Explore Fleet</a>
                <a href="my-bookings" class="menu-item <?php echo $activePage === 'bookings' ? 'active' : ''; ?>"><i class="fa-solid fa-calendar-days"></i> Booking Status</a>
                <a href="profile-verification" class="menu-item <?php echo $activePage === 'verification' ? 'active' : ''; ?>"><i class="fa-solid fa-user-shield"></i> Verification Profile</a>
            <?php endif; ?>
        </nav>
        <div class="sidebar-footer">
            <!-- Modified: Calls the JS trigger function instead of immediate redirection -->
            <a href="#" onclick="openLogoutModal(event)" class="menu-item logout-btn"><i class="fa-solid fa-right-from-bracket"></i> End Session</a>
        </div>
    </aside>

    <div class="dash-main-workspace">
        <header class="dash-topbar">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button class="menu-hamburger" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
                <div class="page-context-title">
                    <h1><?php echo htmlspecialchars($title); ?></h1>
                </div>
            </div>
            <div class="user-profile-badge">
                <div class="avatar-placeholder"><?php echo htmlspecialchars($initials); ?></div>
                <div class="profile-meta-info">
                    <span class="name"><?php echo htmlspecialchars($fullName); ?></span>
                    <span class="role-tag"><?php echo htmlspecialchars($role); ?> access</span>
                </div>
            </div>
        </header>
        <main class="dash-content-view">
    <?php
}

function renderDashboardFooter() {
    ?>
        </main>
    </div>

    <!-- Modal HTML structure rendered nicely at the bottom of the DOM -->
    <div id="logoutModal" class="logout-modal-overlay" onclick="closeLogoutModalOnOverlay(event)">
        <div class="logout-modal-card">
            <div class="logout-modal-title">
                <i class="fa-solid fa-circle-exclamation" style="color: #ff4a4a; margin-right: 8px;"></i>
                Confirm Session Close
            </div>
            <p class="logout-modal-desc">
                Are you sure you want to end your current driving session? You will need to log back in to access the active fleet options.
            </p>
            <form action="<?= APP_URL ?>/api/auth/logout" class="logout-modal-buttons" method="POST">
                <button type="button" class="logout-modal-btn logout-modal-cancel" onclick="closeLogoutModal()">Stay Logged In</button>
                <button type="submit" class="logout-modal-btn logout-modal-confirm">End Session</button>
            </form>
        </div>
    </div>

    <script src="../assets/js/layout.js"></script>
    </body>
    </html>
    <?php
}