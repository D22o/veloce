<?php
require_once '../includes/layout.php';
renderDashboardHeader('Booking Approvals Hub', 'bookings', 'admin');
?>

<div class="data-table-container">
    <div class="table-header-block">
        <h2>Incoming Client Requests</h2>
    </div>
    <table class="veloce-table">
        <thead>
            <tr>
                <th>Client</th>
                <th>Target Car</th>
                <th>Timeline</th>
                <th style="text-align: right;">Operational Decisions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Kenneth Esmeña</strong><br><span style="font-size: 0.8rem; color: #2ed573;">ID Verified</span></td>
                <td>Apex Touring GT</td>
                <td>Jul 04 - Jul 05, 2026</td>
                <td style="text-align: right;">
                    <button style="background: #2ed573; color: #fff; border: none; padding: 0.4rem 0.8rem; border-radius: 4px; font-weight: 600; cursor: pointer; margin-right: 5px;"><i class="fa-solid fa-check"></i> Approve</button>
                    <button style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 0.4rem 0.8rem; border-radius: 4px; font-weight: 600; cursor: pointer;"><i class="fa-solid fa-xmark"></i> Decline</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php renderDashboardFooter(); ?>