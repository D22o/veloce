<?php
require_once '../includes/layout.php';

// Render the admin variant layout template wrapper
renderDashboardHeader('System Administration', 'dashboard', 'admin');
?>

<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-info">
            <h3>Gross Revenue</h3>
            <div class="value">₱342,000</div>
        </div>
        <div class="metric-icon" style="color: #2ed573; background: rgba(46, 213, 115, 0.1);"><i class="fa-solid fa-money-bill-trend-up"></i></div>
    </div>
    <div class="metric-card">
        <div class="metric-info">
            <h3>Fleet Utilization</h3>
            <div class="value">82%</div>
        </div>
        <div class="metric-icon"><i class="fa-solid fa-car"></i></div>
    </div>
    <div class="metric-card">
        <div class="metric-info">
            <h3>Pending Claims</h3>
            <div class="value">04</div>
        </div>
        <div class="metric-icon" style="color: #ffa502; background: rgba(255, 165, 2, 0.1);"><i class="fa-solid fa-hourglass-half"></i></div>
    </div>
</div>

<div class="data-table-container">
    <div class="table-header-block">
        <h2>Global Operational Log</h2>
    </div>
    <table class="veloce-table">
        <thead>
            <tr>
                <th>Client Member</th>
                <th>Allocated Asset</th>
                <th>Duration Schedule</th>
                <th>State Verification</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Kenneth Esmeña</td>
                <td>Apex Touring GT</td>
                <td>1 Day (Rental)</td>
                <td><span class="status-badge active">Cleared</span></td>
            </tr>
            <tr>
                <td>Marc Alexis</td>
                <td>Stealth Matrix 4x4</td>
                <td>3 Days (Reserved)</td>
                <td><span class="status-badge pending">Pending Approval</span></td>
            </tr>
        </tbody>
    </table>
</div>

<?php 
renderDashboardFooter(); 
?>