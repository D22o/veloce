<?php
require_once '../includes/layout.php';
renderDashboardHeader('My Booking Operations', 'bookings', 'user');
?>

<div class="data-table-container">
    <div class="table-header-block">
        <h2>Live Allocation Pipeline</h2>
    </div>
    <table class="veloce-table">
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Asset Profile</th>
                <th>Timeline Window</th>
                <th>Live Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>#VEL-2026-0891</td>
                <td><strong>Apex Touring GT</strong></td>
                <td>Jul 04 - Jul 05, 2026</td>
                <td><span class="status-badge active">Active / Dispatched</span></td>
            </tr>
        </tbody>
    </table>
</div>

<?php renderDashboardFooter(); ?>