<?php
require_once '../layout/main-layout.php';

// Render the admin layout template wrapper
renderDashboardHeader('System Administration', 'dashboard', 'admin');

$bookingService = new \Backend\Services\BookingService();

// Load aggregated administrative metrics
$metrics = $bookingService->getAdminDashboard();

$grossRevenue    = $metrics['gross_revenue'];
$utilizationRate = $metrics['fleet_utilization'];
$pendingClaims   = $metrics['pending_claims'];
$operationalLogs = $metrics['operational_log'];

?>

<!-- Metrics Summary Cards Grid -->
<div class="metrics-grid">
    <!-- Gross Revenue -->
    <div class="metric-card">
        <div class="metric-info">
            <h3>Gross Revenue</h3>
            <div class="value">₱<?= number_format($grossRevenue, 2); ?></div>
        </div>
        <div class="metric-icon" style="color: #2ed573; background: rgba(46, 213, 115, 0.1);">
            <i class="fa-solid fa-money-bill-trend-up"></i>
        </div>
    </div>

    <!-- Dynamic Fleet Utilization Rate -->
    <div class="metric-card">
        <div class="metric-info">
            <h3>Fleet Utilization</h3>
            <div class="value"><?= $utilizationRate; ?>%</div>
        </div>
        <div class="metric-icon"><i class="fa-solid fa-car"></i></div>
    </div>

    <!-- Pending Document Verification Audits -->
    <div class="metric-card">
        <div class="metric-info">
            <h3>Pending Claims</h3>
            <div class="value"><?= str_pad((string)$pendingClaims, 2, '0', STR_PAD_LEFT); ?></div>
        </div>
        <div class="metric-icon" style="color: #ffa502; background: rgba(255, 165, 2, 0.1);">
            <i class="fa-solid fa-hourglass-half"></i>
        </div>
    </div>
</div>

<!-- Global Operational Activity Log Table -->
<div class="data-table-container">
    <div class="table-header-block" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2>Global Operational Log</h2>
        <a href="<?= APP_URL ?>/admin/audit-verifications" style="color: #1e6fff; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
            Manage Audits <i class="fa-solid fa-arrow-right"></i>
        </a>
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
            <?php if (!empty($operationalLogs)): ?>
                <?php foreach ($operationalLogs as $log): ?>
                    <?php 
                        $status = $log['booking_status'];
                        
                        // Map DB ENUM to CSS badge styles
                        $badgeClass = match ($status) {
                            'Active', 'Completed' => 'active',
                            'Approved', 'Pending' => 'pending',
                            'Cancelled'           => 'rejected',
                            default               => 'inactive'
                        };
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($log['client_name']); ?></strong></td>
                        <td><?= htmlspecialchars($log['vehicle_name']); ?> (<?= htmlspecialchars($log['plate_number']); ?>)</td>
                        <td><?= (int)$log['total_days']; ?> <?= ((int)$log['total_days'] === 1) ? 'Day' : 'Days'; ?> (<?= date('M d', strtotime($log['pickup_date'])); ?> - <?= date('M d', strtotime($log['return_date'])); ?>)</td>
                        <td>
                            <span class="status-badge <?= $badgeClass; ?>">
                                <?= htmlspecialchars($status); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center; color: #8da2bb; padding: 2rem;">
                        No operational activity recorded yet.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
renderDashboardFooter(); 
?>