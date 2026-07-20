<?php
require_once '../layout/main-layout.php';

renderDashboardHeader('Booking Approvals Hub', 'bookings', 'admin');

$bookingService = new \Backend\Services\BookingService;

$allBookings = $bookingService->getAllBookings();
?>

<!-- Status Alerts Channels -->
<?php if (isset($_SESSION['booking_admin_success'])): ?>
    <div style="background: rgba(46, 213, 115, 0.1); border: 1px solid #2ed573; color: #2ed573; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
        <i class="fa-solid fa-circle-check"></i> <?php echo $_SESSION['booking_admin_success']; unset($_SESSION['booking_admin_success']); ?>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['booking_admin_error'])): ?>
    <div style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
        <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $_SESSION['booking_admin_error']; unset($_SESSION['booking_admin_error']); ?>
    </div>
<?php endif; ?>

<div class="data-table-container">
    <div class="table-header-block">
        <h2>Incoming Client Requests Pipeline</h2>
    </div>
    <table class="veloce-table">
        <thead>
            <tr>
                <th>Client Record</th>
                <th>Target Car Asset</th>
                <th>Timeline Window</th>
                <th>Gross Price</th>
                <th>Current Pipeline State</th>
                <th style="text-align: right;">Operational Decisions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($allBookings)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #5f758e; padding: 3rem 1rem;">No rental traffic requests processed within database layers.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($allBookings as $row): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($row['user_name']); ?></strong><br>
                            <span style="font-size: 0.75rem; color: #8da2bb;"><?php echo htmlspecialchars($row['user_email']); ?></span>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?></strong><br>
                            <span style="font-size: 0.75rem; color: #5f758e; text-transform: uppercase;"><?php echo htmlspecialchars($row['plate_number']); ?></span>
                        </td>
                        <td>
                            <span style="color: #e2eafc;"><?php echo date('M d', strtotime($row['pickup_date'])); ?> - <?php echo date('M d, Y', strtotime($row['return_date'])); ?></span><br>
                            <span style="font-size: 0.75rem; color: #8da2bb;"><?php echo $row['total_days']; ?> Days</span>
                        </td>
                        <td><strong style="color: #2ed573;">₱<?php echo number_format($row['total_price'], 2); ?></strong></td>
                        <td>
                            <?php
                            $status = $row['booking_status'];
                            $badge = 'pending';
                            if ($status === 'Approved') $badge = 'active';
                            if ($status === 'Cancelled') $badge = 'unpaid';
                            if ($status === 'Completed') $badge = 'active';
                            ?>
                            <span class="status-badge <?php echo $badge; ?>"><?php echo $status; ?></span>
                        </td>
                        <td style="text-align: right;">
                            <?php if ($status === 'Pending'): ?>
                                <!-- Upgraded to modern trigger buttons that hook safely into the JS-driven modal engine -->
                                <button onclick="openStatusConfirmModal(<?php echo $row['booking_id']; ?>, 'approve', '<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['user_name'], ENT_QUOTES); ?>')" style="background: #2ed573; border: none; color: #060b13; padding: 0.4rem 0.8rem; border-radius: 4px; font-weight: 700; cursor: pointer; font-size: 0.8rem; margin-right: 4px;"><i class="fa-solid fa-check"></i> Approve</button>
                                <button onclick="openStatusConfirmModal(<?php echo $row['booking_id']; ?>, 'decline', '<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['user_name'], ENT_QUOTES); ?>')" style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 0.4rem 0.8rem; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 0.8rem;"><i class="fa-solid fa-xmark"></i> Decline</button>
                            <?php elseif ($status === 'Approved'): ?>
                                <button onclick="openStatusConfirmModal(<?php echo $row['booking_id']; ?>, 'complete', '<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['user_name'], ENT_QUOTES); ?>')" style="background: #1e6fff; border: none; color: #fff; padding: 0.4rem 0.8rem; border-radius: 4px; font-weight: 700; cursor: pointer; font-size: 0.8rem;"><i class="fa-solid fa-flag-checkered"></i> Mark Completed</button>
                            <?php else: ?>
                                <span style="font-size: 0.8rem; color: #5f758e; font-style: italic;">No actions remaining</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ========================================== -->
<!-- ACTION DECISION CONFIRMATION MODAL -->
<!-- ========================================== -->
<div id="statusConfirmModal" class="system-modal-overlay">
    <div class="system-modal-box">
        <div id="modalActionIcon" class="modal-warn-icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3 id="modalTitle">Confirm Action</h3>
        <p id="modalDescription">Are you sure you want to proceed with this state modification?</p>
        <span class="modal-subtext" id="modalSubtext">This action directly impacts active booking states.</span>
        
        <!-- Post Submission form to target core-handler router -->
        <form id="statusActionForm" action="<?= APP_URL ?>/api/status/approve-status" method="POST">
            <!-- Router Routing Directives -->
            <!-- <input type="hidden" name="action" id="formActionPath" value=""> -->
            <input type="hidden" name="id" id="formBookingId" value="">

            <div class="modal-actions-wrapper">
                <button type="button" class="btn-cancel" onclick="closeStatusConfirmModal()">Abort</button>
                <button type="submit" class="btn-confirm" id="btnSubmitAction">Execute</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL OVERLAY CUSTOM COMPONENT STYLES -->
<!-- ========================================== -->
<style>
.system-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(4, 7, 13, 0.85);
    backdrop-filter: blur(4px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.system-modal-box {
    background: #0b121f;
    border: 1px solid #1c2e4a;
    border-radius: 8px;
    width: 100%;
    max-width: 440px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    animation: modalFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.modal-warn-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.system-modal-box h3 {
    color: #e2eafc;
    margin-bottom: 0.5rem;
    font-size: 1.25rem;
}

.system-modal-box p {
    color: #8da2bb;
    font-size: 0.95rem;
    line-height: 1.4;
    margin-bottom: 0.5rem;
}

.modal-subtext {
    display: block;
    color: #5f758e;
    font-size: 0.75rem;
    margin-bottom: 1.5rem;
}

.modal-actions-wrapper {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.btn-cancel {
    background: transparent;
    border: 1px solid #1b2a47;
    color: #8da2bb;
    padding: 0.6rem 1.2rem;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    flex: 1;
    transition: background 0.2s;
}

.btn-cancel:hover {
    background: rgba(27, 42, 71, 0.3);
}

.btn-confirm {
    border: none;
    color: #060b13;
    padding: 0.6rem 1.2rem;
    border-radius: 4px;
    font-weight: 700;
    cursor: pointer;
    flex: 1;
    transition: opacity 0.2s;
}

.btn-confirm:hover {
    opacity: 0.95;
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>

<!-- ========================================== -->
<!-- INTERACTION CONTROLLER SCRIPT -->
<!-- ========================================== -->
<script>
function openStatusConfirmModal(bookingId, actionType, carName, clientName) {
    const modal = document.getElementById('statusConfirmModal');
    const formAction = document.getElementById('statusActionForm');
    const formId = document.getElementById('formBookingId');
    
    const iconDiv = document.getElementById('modalActionIcon');
    const titleText = document.getElementById('modalTitle');
    const descText = document.getElementById('modalDescription');
    const subtextText = document.getElementById('modalSubtext');
    const submitBtn = document.getElementById('btnSubmitAction');

    // Assign hidden input variables securely
    formId.value = bookingId;

    // Dynamically adjust modal presentation based on the specific action type selected
    if (actionType === 'approve') {
        formAction.action = '<?= APP_URL ?>/api/status/approve-status';
        
        iconDiv.innerHTML = '<i class="fa-solid fa-circle-check"></i>';
        iconDiv.style.color = '#2ed573';
        titleText.textContent = "Approve Rental Reservation";
        descText.innerHTML = `Are you sure you want to approve the reservation for <strong>${clientName}</strong> requesting the <strong>${carName}</strong>?`;
        subtextText.textContent = "This will capture the transaction payment and mark the target asset as 'Rented'.";
        
        submitBtn.style.background = '#2ed573';
        submitBtn.style.color = '#060b13';
        submitBtn.textContent = "Approve & Dispatch";

    } else if (actionType === 'decline') {
        formAction.action = '<?= APP_URL ?>/api/status/decline-status';

        iconDiv.innerHTML = '<i class="fa-solid fa-circle-xmark"></i>';
        iconDiv.style.color = '#ff4a4a';
        titleText.textContent = "Decline Rental Request";
        descText.innerHTML = `Are you sure you want to reject the reservation submission from <strong>${clientName}</strong>?`;
        subtextText.textContent = "Declining releases any hold on the target asset for this time block.";
        
        submitBtn.style.background = '#ff4a4a';
        submitBtn.style.color = '#ffffff';
        submitBtn.textContent = "Decline Reservation";

    } else if (actionType === 'complete') {
        formAction.action = '<?= APP_URL ?>/api/status/complete-status';

        iconDiv.innerHTML = '<i class="fa-solid fa-flag-checkered"></i>';
        iconDiv.style.color = '#1e6fff';
        titleText.textContent = "Mark Rental Completed";
        descText.innerHTML = `Confirm safe return process for the <strong>${carName}</strong> managed by <strong>${clientName}</strong>?`;
        subtextText.textContent = "Executing this process transitions the vehicle back to 'Available' pool status.";
        
        submitBtn.style.background = '#1e6fff';
        submitBtn.style.color = '#ffffff';
        submitBtn.textContent = "Complete Return";
    }

    modal.style.display = 'flex';
}

function closeStatusConfirmModal() {
    document.getElementById('statusConfirmModal').style.display = 'none';
}

// Background blur clicking dismiss protection 
window.onclick = function(event) {
    const modal = document.getElementById('statusConfirmModal');
    if (event.target === modal) {
        closeStatusConfirmModal();
    }
}
</script>

<?php renderDashboardFooter(); ?>