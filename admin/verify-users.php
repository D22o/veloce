<?php
require_once '../includes/layout.php';

// Render layout wrapper tracking the active 'users' link
renderDashboardHeader('User Document Verification', 'users', 'admin');
?>

<div style="margin-bottom: 2rem;">
    <p style="color: #8da2bb; margin: 0;">Review uploaded identification documentation and manage security access tiers for platform drivers.</p>
</div>

<!-- Pending Verifications Grid/Table -->
<div class="data-table-container">
    <div class="table-header-block">
        <h2>Awaiting Identity Audits</h2>
    </div>
    <table class="veloce-table">
        <thead>
            <tr>
                <th>Registrant Info</th>
                <th>Document Details</th>
                <th>Submission Date</th>
                <th style="text-align: right;">System Adjustments</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Marc Alexis</strong><br>
                    <span style="font-size: 0.8rem; color: #8da2bb;">marc.alexis@email.com</span>
                </td>
                <td>
                    <a href="#" style="color: #1e6fff; text-decoration: none; font-weight: 600;">
                        <i class="fa-solid fa-file-image"></i> view_drivers_license.jpg
                    </a>
                </td>
                <td>July 03, 2026</td>
                <td style="text-align: right;">
                    <button style="background: #2ed573; color: #fff; border: none; padding: 0.4rem 0.8rem; border-radius: 4px; font-weight: 600; cursor: pointer; margin-right: 5px;">
                        <i class="fa-solid fa-user-check"></i> Grant Verification
                    </button>
                    <button style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 0.4rem 0.8rem; border-radius: 4px; font-weight: 600; cursor: pointer;">
                        <i class="fa-solid fa-user-slash"></i> Reject
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php 
renderDashboardFooter(); 
?>