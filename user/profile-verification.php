<?php
require_once '../includes/layout.php';
renderDashboardHeader('Verification Profile', 'verification', 'user');
?>

<div style="background: #0c1524; border: 1px solid #1b2a47; border-radius: 12px; padding: 2rem; max-width: 600px;">
    <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 2rem;">
        <div style="width: 60px; height: 60px; background: rgba(46, 213, 115, 0.1); color: #2ed573; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
            <i class="fa-solid fa-id-card"></i>
        </div>
        <div>
            <h2 style="color: #fff; margin-bottom: 0.25rem;">Identity Trust Mapping</h2>
            <p style="color: #8da2bb; font-size: 0.9rem;">Your account status is currently verified for core asset access.</p>
        </div>
    </div>
    
    <div style="background: #060b13; border: 1px solid #1b2a47; padding: 1rem 1.25rem; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
        <span style="color: #8da2bb; font-size: 0.9rem; font-weight: 600;">Driver's License Status</span>
        <span class="status-badge active">Verified</span>
    </div>
</div>

<?php renderDashboardFooter(); ?>