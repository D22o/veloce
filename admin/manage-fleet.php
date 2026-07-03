<?php
require_once '../includes/layout.php';
renderDashboardHeader('Fleet Inventory Management', 'fleet', 'admin');
?>

<!-- Action Bar to post a new car -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <p style="color: #8da2bb; margin: 0;">Add, modify, or remove performance assets from the user catalog.</p>
    <button style="background: #1e6fff; color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 6px; font-weight: 600; cursor: pointer;">
        <i class="fa-solid fa-plus" style="margin-right: 5px;"></i> Post New Car
    </button>
</div>

<!-- Fleet Control Table -->
<div class="data-table-container">
    <div class="table-header-block">
        <h2>Active Listings Inventory</h2>
    </div>
    <table class="veloce-table">
        <thead>
            <tr>
                <th>Vehicle Profile</th>
                <th>Rate Profile</th>
                <th>Status Mapping</th>
                <th style="text-align: right;">System Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Apex Touring GT</strong><br><span style="font-size: 0.8rem; color: #8da2bb;">V6 Twin-Turbo • Premium Class</span></td>
                <td>₱12,500 / day</td>
                <td><span class="status-badge active">Available</span></td>
                <td style="text-align: right;">
                    <button style="background: transparent; border: 1px solid #1b2a47; color: #e2eafc; padding: 0.4rem 0.8rem; border-radius: 4px; font-weight: 600; cursor: pointer; margin-right: 5px;"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                    <button style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 0.4rem 0.8rem; border-radius: 4px; font-weight: 600; cursor: pointer;"><i class="fa-solid fa-trash"></i> Delete</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php renderDashboardFooter(); ?>