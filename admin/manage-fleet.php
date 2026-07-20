<?php

require_once '../layout/main-layout.php';

renderDashboardHeader('Fleet Inventory Management', 'fleet', 'admin');

$listingService = new \Backend\Services\ListingService();

$limit = 5;
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$totalRows = $listingService->getTotalCount();
$totalPages = ceil($totalRows / $limit);
$cars = $listingService->readPaginated($limit, $offset);
?>

<style>
    /* Pagination Layout */
    .paginator-container { display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem; }
    .page-node { background: #0c1524; border: 1px solid #1b2a47; color: #8da2bb; padding: 0.5rem 0.85rem; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; transition: all 0.2s; }
    .page-node.active, .page-node:hover { background: #1e6fff; color: #fff; border-color: #1e6fff; }

    /* Operational Modals Backdrop Overlay Architecture */
    .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(3, 6, 11, 0.8); backdrop-filter: blur(4px); z-index: 1000; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: opacity 0.25s ease; }
    .modal-overlay.open { opacity: 1; pointer-events: auto; }
    .modal-window { background: #0c1524; border: 1px solid #1b2a47; border-radius: 12px; width: 100%; max-width: 640px; padding: 2rem; box-shadow: 0 20px 40px rgba(0,0,0,0.4); max-height: 90vh; overflow-y: auto; }
    
    .modal-fields-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin: 1.5rem 0; }
    .field-block { display: flex; flex-direction: column; gap: 0.4rem; }
    .field-block.span-2 { grid-column: span 2; }
    .field-block label { color: #8da2bb; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; }
    .modal-input { background: #060b13; border: 1px solid #1b2a47; color: #fff; padding: 0.75rem; border-radius: 6px; outline: none; font-size: 0.9rem; }
    .modal-input:focus { border-color: #1e6fff; }
    
    .flex-actions { display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; }
</style>

<!-- Action Header Bar -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <p style="color: #8da2bb; margin: 0;">Add, modify, or remove performance assets from the user catalog.</p>
    </div>
    <button onclick="openFleetModal()" style="background: #1e6fff; color: #fff; border: none; padding: 0.75rem 1.5rem; border-radius: 6px; font-weight: 600; cursor: pointer;">
        <i class="fa-solid fa-plus" style="margin-right: 5px;"></i> Post New Car
    </button>
</div>

<!-- Context Alert Channels -->
<?php if (isset($_SESSION['fleet_success'])): ?>
    <div style="background: rgba(46, 213, 115, 0.1); border: 1px solid #2ed573; color: #2ed573; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
        <i class="fa-solid fa-circle-check"></i> <?php echo $_SESSION['fleet_success']; unset($_SESSION['fleet_success']); ?>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['fleet_error'])): ?>
    <div style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
        <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $_SESSION['fleet_error']; unset($_SESSION['fleet_error']); ?>
    </div>
<?php endif; ?>

<!-- Fleet Control Table View -->
<div class="data-table-container">
    <div class="table-header-block">
        <h2>Active Listings Inventory (<?php echo $totalRows; ?> assets)</h2>
    </div>
    <table class="veloce-table">
        <thead>
            <tr>
                <th>Vehicle Profile</th>
                <th>Type / Variant</th>
                <th>Rate Profile</th>
                <th>Status Mapping</th>
                <th style="text-align: right;">System Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($cars)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #5f758e; padding: 2rem;">No operational vehicles registered in data engine.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($cars as $car): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 50px; height: 35px; background: #060b13; border: 1px solid #1b2a47; border-radius: 4px; background-image: url('../data/fleet/<?php echo $car['car_image']; ?>'); background-size: cover; background-position: center;"></div>
                                <div>
                                    <strong><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></strong><br>
                                    <span style="font-size: 0.75rem; color: #8da2bb; text-transform: uppercase;"><?php echo htmlspecialchars($car['plate_number']); ?></span>
                                </div>
                            </div>
                        </td>
                        <td><span style="color: #e2eafc; font-size: 0.9rem;"><?php echo $car['type']; ?></span><br><span style="font-size:0.75rem; color:#5f758e;"><?php echo $car['transmission'] . ' • ' . $car['fuel_type']; ?></span></td>
                        <td><strong>₱<?php echo number_format($car['price_per_day'], 2); ?></strong><span style="color: #8da2bb; font-size: 0.8rem;"> / day</span></td>
                        <td>
                            <?php 
                            $badgeMap = ['Available' => 'active', 'Rented' => 'pending', 'Maintenance' => 'unpaid'];
                            $badgeClass = $badgeMap[$car['status']] ?? 'pending';
                            ?>
                            <span class="status-badge <?php echo $badgeClass; ?>"><?php echo $car['status']; ?></span>
                        </td>
                        <td style="text-align: right;">
                            <button onclick='openFleetModal(<?php echo json_encode($car); ?>)' style="background: transparent; border: 1px solid #1b2a47; color: #e2eafc; padding: 0.4rem 0.8rem; border-radius: 4px; font-weight: 600; cursor: pointer; margin-right: 5px;"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                            <button onclick="openDeleteModal(<?php echo $car['car_id']; ?>, '<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model'], ENT_QUOTES); ?>')" style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 0.4rem 0.8rem; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 0.85rem;"><i class="fa-solid fa-trash"></i> Delete</button>                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination Layout Panel Footer -->
<?php if ($totalPages > 1): ?>
    <div class="paginator-container">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="page-node <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<!-- MODAL LIGHTBOX DIALOG COMPONENT -->
<div class="modal-overlay" id="fleetModalOverlay">
    <div class="modal-window">
        <h2 id="modalTitle" style="color: #fff; font-size: 1.3rem; font-weight: 700;">Post New Asset Listing</h2>
        <form id="fleetForm" action="<?= APP_URL ?>/api/listing/create-listing" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="car_id" id="formCarId">
            <input type="hidden" name="existing_image" id="formExistingImage">

            <div class="modal-fields-grid">
                <div class="field-block">
                    <label>Manufacturer Brand</label>
                    <input type="text" name="brand" id="formBrand" class="modal-input" placeholder="e.g., Porsche" required>
                </div>
                <div class="field-block">
                    <label>Model Designation</label>
                    <input type="text" name="model" id="formModel" class="modal-input" placeholder="e.g., 911 GT3" required>
                </div>
                <div class="field-block">
                    <label>Classification Type</label>
                    <select name="type" id="formType" class="modal-input">
                        <option>Sedan</option><option>SUV</option><option>Coupe</option>
                        <option>Hatchback</option><option>Van</option><option>Sports</option>
                    </select>
                </div>
                <div class="field-block">
                    <label>Transmission Profile</label>
                    <select name="transmission" id="formTransmission" class="modal-input">
                        <option>Automatic</option><option>Manual</option>
                    </select>
                </div>
                <div class="field-block">
                    <label>Fuel Configuration</label>
                    <select name="fuel_type" id="formFuel" class="modal-input">
                        <option>Petrol</option><option>Diesel</option><option>Electric</option><option>Hybrid</option>
                    </select>
                </div>
                <div class="field-block">
                    <label>Seating Capacity</label>
                    <input type="number" name="seating_capacity" id="formCapacity" class="modal-input" min="1" max="10" value="4" required>
                </div>
                <div class="field-block">
                    <label>Price Rate Profile (PHP / Day)</label>
                    <input type="number" name="price_per_day" id="formPrice" class="modal-input" min="0" step="0.01" placeholder="8500.00" required>
                </div>
                <div class="field-block">
                    <label>Plate Tracker Identifier</label>
                    <input type="text" name="plate_number" id="formPlate" class="modal-input" placeholder="e.g., ABC-1234" required>
                </div>
                <div class="field-block">
                    <label>Asset Display Photo File</label>
                    <input type="file" name="car_image" class="modal-input">
                </div>
                <div class="field-block">
                    <label>Fleet Allocation Status</label>
                    <select name="status" id="formStatus" class="modal-input">
                        <option>Available</option><option>Rented</option><option>Maintenance</option>
                    </select>
                </div>
            </div>

            <div class="flex-actions">
                <button type="button" onclick="closeFleetModal()" style="background: transparent; border: 1px solid #1b2a47; color: #8da2bb; padding: 0.7rem 1.5rem; border-radius: 6px; cursor: pointer; font-weight: 600;">Cancel</button>
                <button type="submit" style="background: #1e6fff; color: #fff; border: none; padding: 0.7rem 1.5rem; border-radius: 6px; cursor: pointer; font-weight: 600;">Commit Entry</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteConfirmationModal" class="system-modal-overlay">
    <div class="system-modal-box">
        <div class="modal-warn-icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3>Confirm Deletion Protocol</h3>
        <p>Are you sure you want to permanently unlist <span id="deleteCarName" style="color: #ff4a4a; font-weight: bold;">this asset</span> from the active operational fleet?</p>
        <span class="modal-subtext">This action cannot be undone if the asset isn't linked to active booking rows.</span>
        
        <!-- Submission Form targeting your Routing Engine -->
        <form id="deleteAssetForm" action="<?= APP_URL ?>/api/listing/delete-listing" method="POST">
            <!-- Router instructions -->
            <input type="hidden" name="action" value="listing/delete-listing">
            <input type="hidden" name="id" id="deleteCarId" value="">

            <div class="modal-actions-wrapper">
                <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Abort Process</button>
                <button type="submit" class="btn-confirm-delete">Confirm Removal</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('fleetModalOverlay');
    const form = document.getElementById('fleetForm');
    
    function openFleetModal(data = null) {
        if (data) {
            document.getElementById('modalTitle').innerText = "Modify Operational Asset Parameters";
            form.action = "<?= APP_URL ?>/api/listing/update-listing";
            document.getElementById('formCarId').value = data.car_id;
            document.getElementById('formExistingImage').value = data.car_image;
            document.getElementById('formBrand').value = data.brand;
            document.getElementById('formModel').value = data.model;
            document.getElementById('formType').value = data.type;
            document.getElementById('formTransmission').value = data.transmission;
            document.getElementById('formFuel').value = data.fuel_type;
            document.getElementById('formCapacity').value = data.seating_capacity;
            document.getElementById('formPrice').value = data.price_per_day;
            document.getElementById('formPlate').value = data.plate_number;
            document.getElementById('formStatus').value = data.status;
        } else {
            document.getElementById('modalTitle').innerText = "Post New Asset Listing";
            form.action = "<?= APP_URL ?>/api/listing/create-listing";
            form.reset();
            document.getElementById('formCarId').value = "";
            document.getElementById('formExistingImage').value = "default-car.png";
        }
        modal.classList.add('open');
    }

    function closeFleetModal() {
        modal.classList.remove('open');
    }
</script>

<?php renderDashboardFooter(); ?>