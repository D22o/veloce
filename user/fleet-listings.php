<?php
require_once '../includes/layout.php';
renderDashboardHeader('Available Fleet Listings', 'listings', 'user');
?>

<div class="table-header-block" style="margin-bottom: 1.5rem; padding: 0;">
    <p style="color: #8da2bb;">Find and secure your premium vehicle allocation.</p>
</div>

<!-- Simple inline search layout component -->
<div style="background: #0c1524; padding: 1rem; border: 1px solid #1b2a47; border-radius: 8px; margin-bottom: 2rem; display: flex; gap: 1rem;">
    <input type="text" placeholder="Search by model, brand, or performance tier..." style="flex: 1; background: #060b13; border: 1px solid #1b2a47; padding: 0.75rem 1rem; border-radius: 6px; color: #fff; outline: none;">
    <button style="background: #1e6fff; color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 6px; font-weight: 600; cursor: pointer;">Search</button>
</div>

<!-- Example vehicle item grid system -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
    <div style="background: #0c1524; border: 1px solid #1b2a47; border-radius: 12px; overflow: hidden;">
        <div style="height: 180px; background: #121f35; display: flex; align-items: center; justify-content: center; color: #8da2bb;"><i class="fa-solid fa-car-side fa-3x"></i></div>
        <div style="padding: 1.5rem;">
            <h3 style="color: #fff; margin-bottom: 0.5rem;">Apex Touring GT</h3>
            <p style="color: #8da2bb; font-size: 0.9rem; margin-bottom: 1rem;">Twin-turbo V6 • Comfort Adaptive Trim</p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #1e6fff; font-weight: 700;">₱12,500 / day</span>
                <button style="background: #1e6fff; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600; cursor: pointer;">Book Now</button>
            </div>
        </div>
    </div>
</div>

<?php renderDashboardFooter(); ?>