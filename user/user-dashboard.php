<?php
require_once '../layout/main-layout.php';

// Render layout header
renderDashboardHeader('Client Workspace', 'dashboard', 'user');

$userId = $_SESSION['user_id'] ?? null;

// Fetch dynamic user status
$verificationStatus = $_SESSION['verification_status'] ?? 'unverified';

$bookingService = new \Backend\Services\BookingService();

$dashboardData = $bookingService->getDashboardData();

$activeBookingsCount = $dashboardData['active_bookings_count'];
$completedTripsCount = $dashboardData['completed_trips_count'];
$totalSpentAmount    = $dashboardData['total_spent_amount'];
$verificationStatus  = $dashboardData['verification_status'];
$recentOrders        = $dashboardData['recent_orders'];

// Identity status mapping table
$statusConfig = [
    'approved'   => ['class' => 'status-badge active',    'label' => 'Verified',        'icon' => 'fa-shield-check'],
    'pending'    => ['class' => 'status-badge pending',   'label' => 'Under Review',    'icon' => 'fa-hourglass-half'],
    'rejected'   => ['class' => 'status-badge rejected',  'label' => 'Action Required', 'icon' => 'fa-triangle-exclamation'],
    'unverified' => ['class' => 'status-badge inactive',  'label' => 'Unverified',      'icon' => 'fa-shield-slash']
];

$currentIdentity = $statusConfig[$verificationStatus] ?? $statusConfig['unverified'];

?>

<style>
    .status-badge.active    { background: rgba(46, 213, 115, 0.15); color: #2ed573; border: 1px solid #2ed573; }
.status-badge.pending   { background: rgba(255, 184, 0, 0.15);  color: #ffb800; border: 1px solid #ffb800; }
.status-badge.rejected  { background: rgba(255, 74, 74, 0.15);  color: #ff4a4a; border: 1px solid #ff4a4a; }
.status-badge.inactive  { background: rgba(141, 162, 187, 0.15); color: #8da2bb; border: 1px solid #8da2bb; }
</style>

<!-- Metric Summary Cards Grid -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-info">
            <h3>Active Rentals</h3>
            <div class="value"><?= str_pad((string)$activeBookingsCount, 2, '0', STR_PAD_LEFT); ?></div>
        </div>
        <div class="metric-icon"><i class="fa-solid fa-key"></i></div>
    </div>

    <div class="metric-card">
        <div class="metric-info">
            <h3>Completed Journeys</h3>
            <div class="value"><?= str_pad((string)$completedTripsCount, 2, '0', STR_PAD_LEFT); ?></div>
        </div>
        <div class="metric-icon"><i class="fa-solid fa-route"></i></div>
    </div>

    <div class="metric-card">
        <div class="metric-info">
            <h3>Total Spent</h3>
            <div class="value">₱<?= number_format($totalSpentAmount, 2); ?></div>
        </div>
        <div class="metric-icon"><i class="fa-solid fa-wallet"></i></div>
    </div>

    <div class="metric-card">
        <div class="metric-info">
            <h3>Identity Status</h3>
            <div class="value" style="font-size: 1rem; margin-top: 0.5rem;">
                <span class="<?= $currentIdentity['class']; ?>">
                    <i class="fa-solid <?= $currentIdentity['icon']; ?>"></i> <?= $currentIdentity['label']; ?>
                </span>
            </div>
        </div>
        <div class="metric-icon"><i class="fa-solid fa-id-card"></i></div>
    </div>
</div>

<!-- Data Table Section -->
<div class="data-table-container">
    <div class="table-header-block" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2>Recent Allocation Orders</h2>
        <a href="<?= APP_URL ?>/user/my-bookings" style="color: #1e6fff; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
            View All Orders <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <table class="veloce-table">
        <thead>
            <tr>
                <th>Vehicle Config</th>
                <th>Rental Schedule</th>
                <th>Billing Total</th>
                <th>Status Mapping</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($recentOrders)): ?>
                <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($order['car_name'] ?? 'N/A'); ?></strong></td>
                        <td><?= date('M d, Y', strtotime($order['pickup_date'])); ?> - <?= date('M d, Y', strtotime($order['return_date'])); ?></td>
                        <td>₱<?= number_format($order['total_price'], 2); ?></td>
                        <td>
                            <span class="status-badge <?= strtolower($order['booking_status']); ?>">
                                <?= ucfirst($order['booking_status']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center; color: #8da2bb; padding: 2rem;">
                        No active or past bookings found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
renderDashboardFooter();
?>