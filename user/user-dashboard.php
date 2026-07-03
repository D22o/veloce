<?php
require_once '../includes/layout.php';

// Render the user layout template wrapper
renderDashboardHeader('Client Workspace', 'dashboard', 'user');
?>

<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-info">
            <h3>Active Bookings</h3>
            <div class="value">01</div>
        </div>
        <div class="metric-icon"><i class="fa-solid fa-key"></i></div>
    </div>
    <div class="metric-card">
        <div class="metric-info">
            <h3>Total Spent</h3>
            <div class="value">₱12,500</div>
        </div>
        <div class="metric-icon"><i class="fa-solid fa-wallet"></i></div>
    </div>
    <div class="metric-card">
        <div class="metric-info">
            <h3>Identity Status</h3>
            <div class="value" style="font-size: 1.2rem; margin-top: 0.5rem;"><span class="status-badge active">Verified</span></div>
        </div>
        <div class="metric-icon"><i class="fa-solid fa-shield-check"></i></div>
    </div>
</div>

<div class="data-table-container">
    <div class="table-header-block">
        <h2>Recent Allocation Orders</h2>
    </div>
    <table class="veloce-table">
        <thead>
            <tr>
                <th>Vehicle Config</th>
                <th>Allocation Date</th>
                <th>Billing</th>
                <th>Status Mapping</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Apex Touring GT</strong></td>
                <td>July 04, 2026</td>
                <td>₱12,500</td>
                <td><span class="status-badge active">Active</span></td>
            </tr>
        </tbody>
    </table>
</div>

<?php 
renderDashboardFooter(); 
?>