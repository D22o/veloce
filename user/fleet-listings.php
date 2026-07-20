<?php
require_once dirname(__DIR__) . '/layout/main-layout.php';

renderDashboardHeader('Available Fleet Listings', 'listings', 'user');

$listingService = new \Backend\Services\ListingService();

$search = trim($_GET['search'] ?? '');
$type = trim($_GET['type'] ?? '');
$availableCars = $listingService->searchAvailableFleet($search, $type);

?>

<style>
    /* Filter Bar container aesthetics */
    .filter-bar-container { 
        background: #0c1524; 
        padding: 1.25rem; 
        border: 1px solid #1b2a47; 
        border-radius: 12px; 
        margin-bottom: 2rem; 
        display: flex; 
        gap: 1rem; 
        flex-wrap: wrap; 
        align-items: center; 
    }
    .search-input-field { 
        flex: 1; 
        min-width: 260px; 
        background: #060b13; 
        border: 1px solid #1b2a47; 
        padding: 0.75rem 1rem; 
        border-radius: 8px; 
        color: #fff; 
        outline: none; 
        transition: border 0.2s, box-shadow 0.2s; 
        font-size: 0.95rem; 
    }
    .search-input-field:focus { 
        border-color: #1e6fff; 
        box-shadow: 0 0 0 2px rgba(30, 111, 255, 0.2);
    }
    .filter-dropdown { 
        background: #060b13; 
        border: 1px solid #1b2a47; 
        padding: 0.75rem 1rem; 
        border-radius: 8px; 
        color: #fff; 
        outline: none; 
        cursor: pointer; 
        font-size: 0.95rem; 
        transition: border-color 0.2s;
    }
    .filter-dropdown:focus {
        border-color: #1e6fff;
    }
    
    /* Fleet Grid & Modern Card Designs */
    .fleet-display-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(310px, 1fr)); 
        gap: 1.75rem; 
    }
    .car-asset-card { 
        background: #0c1524; 
        border: 1px solid #1b2a47; 
        border-radius: 14px; 
        overflow: hidden; 
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.25s, box-shadow 0.25s; 
        display: flex; 
        flex-direction: column; 
    }
    .car-asset-card:hover { 
        transform: translateY(-5px); 
        border-color: #1e6fff; 
        box-shadow: 0 10px 20px rgba(30, 111, 255, 0.15);
    }
    .car-card-img { 
        height: 200px; 
        background-size: cover; 
        background-position: center; 
        background-color: #121f35; 
        position: relative; 
    }
    .car-card-img::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 40%;
        background: linear-gradient(to top, #0c1524, transparent);
    }
    
    .car-detail-pane { 
        padding: 1.5rem; 
        flex: 1; 
        display: flex; 
        flex-direction: column; 
        justify-content: space-between; 
    }
    .spec-pill-group { 
        display: flex; 
        flex-wrap: wrap; 
        gap: 0.5rem; 
        margin: 1rem 0 1.5rem 0; 
    }
    .spec-badge { 
        background: #060b13; 
        border: 1px solid #1b2a47; 
        color: #8da2bb; 
        font-size: 0.75rem; 
        padding: 0.4rem 0.7rem; 
        border-radius: 6px; 
        font-weight: 500; 
        display: inline-flex; 
        align-items: center; 
        gap: 6px; 
    }

    /* Interactive Booking Modal overlays */
    .booking-modal-overlay { 
        position: fixed; 
        top: 0; 
        left: 0; 
        right: 0; 
        bottom: 0; 
        background: rgba(3, 6, 11, 0.85); 
        backdrop-filter: blur(6px); 
        z-index: 1000; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        opacity: 0; 
        pointer-events: none; 
        transition: opacity 0.25s ease-in-out; 
    }
    .booking-modal-overlay.active { 
        opacity: 1; 
        pointer-events: auto; 
    }
    .booking-window { 
        background: #0c1524; 
        border: 1px solid #1b2a47; 
        border-radius: 16px; 
        width: 90%; 
        max-width: 460px; 
        padding: 2rem; 
        transform: scale(0.95);
        transition: transform 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 25px 50px rgba(0,0,0,0.5); 
    }
    .booking-modal-overlay.active .booking-window {
        transform: scale(1);
    }
    
    .invoice-preview-card { 
        background: #060b13; 
        border: 1px solid #1b2a47; 
        border-radius: 8px; 
        padding: 1.15rem; 
        margin-top: 1.5rem; 
        display: none; 
    }
</style>

<!-- Verification Status Guards -->
<?php if (isset($_SESSION['verification_status']) && $_SESSION['verification_status'] === 'pending'): ?>
    <div style="background: rgba(30, 111, 255, 0.1); border: 1px solid #1e6fff; color: #1e6fff; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
        <i class="fa-solid fa-circle-info" style="margin-right: 6px;"></i> Your account is currently under verification. Some features may be restricted until the process is complete.
    </div>
<?php elseif (isset($_SESSION['verification_status']) && $_SESSION['verification_status'] === 'unverified'): ?>
    <div style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
        <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> Your account is currently unverified. Please submit the required documents for verification to access full features.
    </div>
<?php elseif (isset($_SESSION['verification_status']) && $_SESSION['verification_status'] === 'rejected'): ?>
    <div style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
        <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> Your account verification was rejected. Please review your submitted documents and resubmit for verification.
    </div>
<?php else: ?>
    <div class="table-header-block" style="margin-bottom: 1.5rem; padding: 0;">
        <p style="color: #8da2bb; margin: 0;">Browse and configure your high-performance vehicle allocation reservation instantly.</p>
    </div>
<?php endif; ?>

<!-- Booking Process Execution Notifications -->
<?php if (isset($_SESSION['booking_error'])): ?>
    <div style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
        <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> <?php echo $_SESSION['booking_error']; unset($_SESSION['booking_error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['booking_success'])): ?>
    <div style="background: rgba(46, 213, 115, 0.1); border: 1px solid #2ed573; color: #2ed573; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
        <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> <?php echo $_SESSION['booking_success']; unset($_SESSION['booking_success']); ?>
    </div>
<?php endif; ?>

<!-- Filter & Search Form -->
<form method="GET" action="" class="filter-bar-container">
    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by manufacturer, engine variants, types..." class="search-input-field">
    
    <select name="type" class="filter-dropdown" onchange="this.form.submit()">
        <option value="">All Categories</option>
        <?php 
        $types = ['Sedan', 'SUV', 'Coupe', 'Hatchback', 'Van', 'Sports'];
        foreach ($types as $t) {
            $selected = ($type === $t) ? 'selected' : '';
            echo "<option value=\"$t\" $selected>$t</option>";
        }
        ?>
    </select>
    
    <button type="submit" style="background: #1e6fff; color: #fff; border: none; padding: 0.75rem 1.75rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-magnifying-glass"></i> Search
    </button>
</form>

<!-- Grid of Results -->
<div class="fleet-display-grid">
    <?php if (empty($availableCars)): ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; background: #0c1524; border: 1px solid #1b2a47; border-radius: 12px;">
            <i class="fa-solid fa-car-tunnel fa-3x" style="color: #243757; margin-bottom: 1rem;"></i>
            <h3 style="color: #fff; margin-bottom: 0.25rem;">No Matches Found</h3>
            <p style="color: #8da2bb; margin: 0; font-size: 0.9rem;">Try broadening your parameters or changing category selections.</p>
        </div>
    <?php else: ?>
        <?php foreach ($availableCars as $car): ?>
            <div class="car-asset-card">
                <!-- Fallback to root or dynamically resolved paths -->
                <div class="car-card-img" style="background-image: url('<?= APP_URL ?>/data/fleet/<?php echo $car['car_image']; ?>');"></div>
                <div class="car-detail-pane">
                    <div>
                        <h3 style="color: #fff; font-size: 1.2rem; margin: 0 0 0.35rem 0; font-weight: 700;">
                            <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>
                        </h3>
                        
                        <div class="spec-pill-group">
                            <span class="spec-badge"><i class="fa-solid fa-gears"></i> <?php echo htmlspecialchars($car['transmission']); ?></span>
                            <span class="spec-badge"><i class="fa-solid fa-gas-pump"></i> <?php echo htmlspecialchars($car['fuel_type']); ?></span>
                            <span class="spec-badge"><i class="fa-solid fa-users"></i> <?php echo htmlspecialchars($car['seating_capacity']); ?> Seats</span>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #1b2a47; padding-top: 1rem; margin-top: auto;">
                        <div>
                            <span style="color: #8da2bb; font-size: 0.75rem; text-transform: uppercase; font-weight: 700; display: block; margin-bottom: -2px;">Daily Rate</span>
                            <span style="color: #1e6fff; font-size: 1.25rem; font-weight: 800;">₱<?php echo number_format($car['price_per_day'], 2); ?></span>
                        </div>
                        <?php 
                        $isApproved = (isset($_SESSION['verification_status']) && $_SESSION['verification_status'] === 'approved');
                        ?>
                        <button 
                            onclick='triggerBookingModal(<?php echo json_encode($car); ?>)' 
                            style="background: <?php echo $isApproved ? '#1e6fff' : '#243757'; ?>; color: <?php echo $isApproved ? '#ffffff' : '#8da2bb'; ?>; border: none; padding: 0.65rem 1.3rem; border-radius: 8px; font-weight: 700; cursor: <?php echo $isApproved ? 'pointer' : 'not-allowed'; ?>;"
                            <?php echo !$isApproved ? 'disabled title="Account must be verified to book assets"' : ''; ?>
                        >
                            Book Asset
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Booking Modal Container -->
<div class="booking-modal-overlay" id="bookingOverlay" onclick="closeModalOnOverlay(event)">
    <div class="booking-window">
        <h3 id="modalVehicleName" style="color: #fff; font-size: 1.3rem; font-weight: 700; margin-bottom: 0.25rem;">Confirm Secure Allocation</h3>
        <p style="color: #8da2bb; font-size: 0.85rem; margin: 0 0 1.5rem 0;">Provide your operational window details below.</p>
        
        <!-- Updated Action to unified API Endpoint target -->
        <form action="<?= APP_URL ?>/api/booking/create-booking" method="POST">
            <input type="hidden" name="car_id" id="modalCarId">
            
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                    <label style="color: #8da2bb; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Pick-up Date</label>
                    <input type="date" name="pickup_date" id="inputPickup" class="search-input-field" style="width:100%; box-sizing: border-box;" required>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                    <label style="color: #8da2bb; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Return Date</label>
                    <input type="date" name="return_date" id="inputReturn" class="search-input-field" style="width:100%; box-sizing: border-box;" required>
                </div>
            </div>

            <!-- Dynamically calculated cost estimation card -->
            <div class="invoice-preview-card" id="invoiceCard">
                <div style="display: flex; justify-content: space-between; color: #8da2bb; font-size: 0.85rem; margin-bottom: 0.6rem;">
                    <span>Rental Duration:</span>
                    <span id="textDuration" style="color: #fff; font-weight: 600;">0 Days</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-top: 1px dashed #1b2a47; padding-top: 0.6rem; color: #fff; font-weight: 700;">
                    <span>Estimated Total:</span>
                    <span id="textTotal" style="color: #2ed573; font-size: 1.1rem;">₱0.00</span>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.75rem;">
                <button type="button" onclick="dismissBookingModal()" style="background: transparent; border: 1px solid #1b2a47; color: #8da2bb; padding: 0.65rem 1.25rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;">Cancel</button>
                <button type="submit" style="background: #2ed573; color: #060b13; border: none; padding: 0.65rem 1.25rem; border-radius: 8px; font-weight: 700; cursor: pointer; transition: opacity 0.2s;">Request Allocation</button>
            </div>
        </form>
    </div>
</div>

<script>
    const overlay = document.getElementById('bookingOverlay');
    const pickupInput = document.getElementById('inputPickup');
    const returnInput = document.getElementById('inputReturn');
    const invoiceCard = document.getElementById('invoiceCard');
    const textDuration = document.getElementById('textDuration');
    const textTotal = document.getElementById('textTotal');
    
    let activeCarRate = 0;

    // Set minimal date limits dynamically to current time today
    const todayStr = new Date().toISOString().split('T')[0];
    pickupInput.min = todayStr;
    returnInput.min = todayStr;

    function triggerBookingModal(car) {
        activeCarRate = parseFloat(car.price_per_day);
        document.getElementById('modalVehicleName').innerText = `${car.brand} ${car.model}`;
        document.getElementById('modalCarId').value = car.car_id;
        
        // Reset old cached inputs
        pickupInput.value = '';
        returnInput.value = '';
        invoiceCard.style.display = 'none';
        
        overlay.classList.add('active');
    }

    function dismissBookingModal() {
        overlay.classList.remove('active');
    }

    function closeModalOnOverlay(event) {
        if (event.target === overlay) {
            dismissBookingModal();
        }
    }

    function calculateLiveInvoice() {
        if (!pickupInput.value || !returnInput.value) return;
        
        const start = new Date(pickupInput.value);
        const end = new Date(returnInput.value);
        
        if (end < start) {
            invoiceCard.style.display = 'none';
            return;
        }
        
        const timeDiff = Math.abs(end - start);
        let days = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
        if (days === 0) days = 1; // Safeguard same-day rentals
        
        const overallPrice = days * activeCarRate;
        
        textDuration.innerText = `${days} ${days === 1 ? 'Day' : 'Days'}`;
        textTotal.innerText = `₱${overallPrice.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        invoiceCard.style.display = 'block';
    }

    pickupInput.addEventListener('change', () => {
        // Enforces return dates to restrict selections prior to picking date
        returnInput.min = pickupInput.value;
        if (returnInput.value && new Date(returnInput.value) < new Date(pickupInput.value)) {
            returnInput.value = pickupInput.value;
        }
        calculateLiveInvoice();
    });
    
    returnInput.addEventListener('change', calculateLiveInvoice);
</script>

<?php renderDashboardFooter(); ?>