<?php
/**
 * Layout Render Engine for Veloce Dashboards with Embedded Security Guards
 */

function renderDashboardHeader(String $title, String $activePage, String $role = 'user') {
    // --- 1. START SECURE SESSION MANAGEMENT ---
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // --- 2. AUTH GUARD: CHECK BASIC ASSIGNMENTS ---
    // If user credentials or the cookie token are missing, reject entry immediately
    if (!isset($_SESSION['user_id']) || !isset($_COOKIE['token'])) {
        session_destroy();
        setcookie('token', '', time() - 3600, '/');
        header("Location: ../login"); 
        exit();
    }

    // --- 3. AUTH GUARD: CRYPTOGRAPHIC FINGERPRINT VERIFICATION ---
    // Cross-verify the browser cookie against the signature stashed in the session
    $tokenHash = hash('sha256', $_COOKIE['token']);
    if ($tokenHash !== $_SESSION['auth_token_fingerprint']) {
        session_destroy();
        setcookie('token', '', time() - 3600, '/');
        header("Location: ../login");
        exit();
    }

    // --- 4. AUTH GUARD: ROLE-BASED ACCESS CONTROL (RBAC) ---
    // Prevent standard users from accessing admin routes and vice-versa
    if ($_SESSION['user_role'] !== $role) {
        if ($_SESSION['user_role'] === 'admin') {
            header("Location: ../admin/admin-dashboard");
        } else {
            header("Location: ../user/user-dashboard");
        }
        exit();
    }

    // --- 5. INITIALIZE PATH PARAMETERS & INTERFACE DETAILS ---
    $isAdmin = ($role === 'admin');
    $homePath = '../index'; 
    
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
        <link rel="stylesheet" href="../assets/css/index.css"> 
        <style>
            /* --- DASHBOARD ARCHITECTURE BASE --- */
            :root {
                --sidebar-width: 260px;
                --topbar-height: 70px;
            }
            body {
                background-color: #060b13;
                margin: 0;
                display: flex;
                min-height: 100vh;
                overflow-x: hidden;
            }
            
            /* --- SIDEBAR CONFIG --- */
            .dash-sidebar {
                width: var(--sidebar-width);
                background-color: #0c1524;
                border-right: 1px solid #1b2a47;
                display: flex;
                flex-direction: column;
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                z-index: 100;
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .sidebar-brand {
                height: var(--topbar-height);
                display: flex;
                align-items: center;
                padding: 0 1.5rem;
                font-size: 1.25rem;
                font-weight: 800;
                letter-spacing: 1.5px;
                color: #ffffff;
                border-bottom: 1px solid #1b2a47;
                gap: 10px;
            }
            .sidebar-brand i { color: #1e6fff; }
            
            .sidebar-menu {
                flex: 1;
                padding: 1.5rem 0.75rem;
                display: flex;
                flex-direction: column;
                gap: 0.4rem;
            }
            .menu-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 0.8rem 1rem;
                color: #8da2bb;
                font-weight: 600;
                font-size: 0.95rem;
                border-radius: 8px;
                text-decoration: none;
                transition: all 0.2s ease;
            }
            .menu-item:hover, .menu-item.active {
                color: #ffffff;
                background-color: #121f35;
            }
            .menu-item.active {
                background-color: rgba(30, 111, 255, 0.15);
                color: #1e6fff;
                border-left: 3px solid #1e6fff;
                border-top-left-radius: 0;
                border-bottom-left-radius: 0;
            }
            .sidebar-footer {
                padding: 1rem;
                border-top: 1px solid #1b2a47;
            }
            .logout-btn {
                color: #ff4a4a;
                background: rgba(255, 74, 74, 0.05);
            }
            .logout-btn:hover {
                background: #ff4a4a;
                color: #ffffff;
            }

            /* --- WORKSPACE OVERVIEW --- */
            .dash-main-workspace {
                flex: 1;
                margin-left: var(--sidebar-width);
                display: flex;
                flex-direction: column;
                min-width: 0;
                transition: margin-left 0.3s ease;
            }

            /* --- TOP CONTEXT BAR --- */
            .dash-topbar {
                height: var(--topbar-height);
                background-color: rgba(6, 11, 19, 0.85);
                backdrop-filter: blur(12px);
                border-bottom: 1px solid #1b2a47;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 2rem;
                position: sticky;
                top: 0;
                z-index: 90;
            }
            .page-context-title h1 {
                font-size: 1.25rem;
                font-weight: 700;
                color: #ffffff;
            }
            .user-profile-badge {
                display: flex;
                align-items: center;
                gap: 12px;
                cursor: pointer;
            }
            .avatar-placeholder {
                width: 36px;
                height: 36px;
                background-color: #1e6fff;
                color: #ffffff;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 0.9rem;
            }
            .profile-meta-info {
                display: flex;
                flex-direction: column;
            }
            .profile-meta-info .name {
                font-size: 0.9rem;
                font-weight: 600;
                color: #e2eafc;
            }
            .profile-meta-info .role-tag {
                font-size: 0.75rem;
                color: #8da2bb;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .menu-hamburger {
                display: none;
                background: none;
                border: none;
                color: #ffffff;
                font-size: 1.25rem;
                cursor: pointer;
            }

            /* --- CONTENT CANVAS VIEW --- */
            .dash-content-view {
                padding: 2rem;
                flex: 1;
            }

            /* --- MODERN CORE CARD COMPONENTS --- */
            .metrics-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 1.5rem;
                margin-bottom: 2rem;
            }
            .metric-card {
                background: #0c1524;
                border: 1px solid #1b2a47;
                border-radius: 12px;
                padding: 1.5rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
                transition: transform 0.2s ease;
            }
            .metric-card:hover { transform: translateY(-2px); }
            .metric-info h3 { font-size: 0.85rem; color: #8da2bb; text-transform: uppercase; margin-bottom: 0.5rem; }
            .metric-info .value { font-size: 1.8rem; font-weight: 800; color: #ffffff; }
            .metric-icon { width: 48px; height: 48px; background: rgba(30, 111, 255, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #1e6fff; font-size: 1.3rem; }

            .data-table-container {
                background: #0c1524;
                border: 1px solid #1b2a47;
                border-radius: 12px;
                overflow: hidden;
            }
            .table-header-block { padding: 1.25rem 1.5rem; border-bottom: 1px solid #1b2a47; display: flex; justify-content: space-between; align-items: center; }
            .table-header-block h2 { font-size: 1.1rem; font-weight: 700; color: #ffffff; }
            
            .veloce-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem; }
            .veloce-table th { background: #090f1a; padding: 1rem 1.5rem; color: #8da2bb; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; }
            .veloce-table td { padding: 1rem 1.5rem; color: #e2eafc; border-bottom: 1px solid #1b2a47; }
            .veloce-table tr:last-child td { border-bottom: none; }
            
            .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; display: inline-block; }
            .status-badge.active { background: rgba(46, 213, 115, 0.15); color: #2ed573; }
            .status-badge.pending { background: rgba(ffa502, 0.15); color: #ffa502; }

            /* --- RESPONSIVE MEDIA HOOKS --- */
            @media (max-width: 992px) {
                .dash-sidebar { transform: translateX(-100%); }
                .dash-sidebar.open { transform: translateX(0); }
                .dash-main-workspace { margin-left: 0; }
                .menu-hamburger { display: block; }
            }
        </style>
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
            <a href="../backend/controller/logout" class="menu-item logout-btn"><i class="fa-solid fa-right-from-bracket"></i> End Session</a>
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

    <script>
        function toggleSidebar() {
            document.getElementById('dashboardSidebar').classList.toggle('open');
        }
    </script>
    </body>
    </html>
    <?php
}