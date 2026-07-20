<?php
require_once '../layout/main-layout.php';

renderDashboardHeader('My Booking Operations', 'bookings', 'user');

$userId = $_SESSION['user_id'];

$bookingService = new \Backend\Services\BookingService;

$myBookings = $bookingService->getAllBookingsByUserId($userId);
?>

<?php if (isset($_SESSION['booking_success'])): ?>
    <div style="background: rgba(46, 213, 115, 0.1); border: 1px solid #2ed573; color: #2ed573; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
        <i class="fa-solid fa-circle-check"></i> <?php echo $_SESSION['booking_success']; unset($_SESSION['booking_success']); ?>
    </div>
<?php endif; ?>

<div class="data-table-container">
    <div class="table-header-block">
        <h2>Live Allocation Pipeline</h2>
    </div>
    <table class="veloce-table">
        <thead>
            <tr>
                <th>Booking Tracking ID</th>
                <th>Asset Profile</th>
                <th>Timeline Window</th>
                <th>Invoice Investment</th>
                <th>Live Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($myBookings)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #5f758e; padding: 3rem 1rem;">
                        <i class="fa-solid fa-receipt fa-2x" style="display:block; margin-bottom:0.5rem; color:#1b2a47;"></i>
                        No dynamic allocations recorded for your profile yet.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($myBookings as $row): ?>
                    <tr>
                        <td style="font-family: monospace; font-size: 0.95rem; color: #8da2bb;">
                            #VEL-<?php echo date('Y', strtotime($row['created_at'])); ?>-<?php echo str_pad($row['booking_id'], 4, '0', STR_PAD_LEFT); ?>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?></strong><br>
                            <span style="font-size: 0.75rem; color: #5f758e; text-transform: uppercase;"><?php echo htmlspecialchars($row['plate_number']); ?> • <?php echo $row['type']; ?></span>
                        </td>
                        <td>
                            <span style="color: #e2eafc; font-size: 0.9rem;">
                                <?php echo date('M d', strtotime($row['pickup_date'])); ?> - <?php echo date('M d, Y', strtotime($row['return_date'])); ?>
                            </span><br>
                            <span style="font-size: 0.75rem; color: #8da2bb;"><?php echo $row['total_days']; ?> Day Allocation</span>
                        </td>
                        <td><strong style="color: #fff;">₱<?php echo number_format($row['total_price'], 2); ?></strong></td>
                        <td>
                            <?php
                            $status = $row['booking_status'];
                            $badgeClass = 'pending'; // Default fallback
                            $displayLabel = $status;

                            if ($status === 'Approved') { $badgeClass = 'active'; $displayLabel = 'Approved / Dispatched'; }
                            elseif ($status === 'Cancelled') { $badgeClass = 'unpaid'; $displayLabel = 'Cancelled / Declined'; }
                            elseif ($status === 'Completed') { $badgeClass = 'active'; $displayLabel = 'Completed'; }
                            ?>
                            <span class="status-badge <?php echo $badgeClass; ?>"><?php echo $displayLabel; ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php renderDashboardFooter(); ?>