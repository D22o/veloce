<?php
require_once '../layout/main-layout.php';

// Render layout wrapper tracking the active 'users' link
renderDashboardHeader('User Document Verification', 'users', 'admin');

$userService = new \Backend\Services\UserService;

$pendingRequests = $userService->getPendingVerificationRequest();

?>

<style>
    /* Status banner styling */
    .alert-banner {
        padding: 0.8rem 1rem; 
        border-radius: 8px; 
        font-size: 0.9rem; 
        margin-bottom: 1.5rem; 
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .alert-success { background: rgba(46, 213, 115, 0.1); border: 1px solid #2ed573; color: #2ed573; }
    .alert-danger { background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; }

    /* Modal System Overlay styling */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(4, 7, 13, 0.85);
        backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease;
    }
    .modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }
    .modal-viewport-card {
        background: #0c1524;
        border: 1px solid #1b2a47;
        width: 90%;
        max-width: 700px;
        border-radius: 12px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        overflow: hidden;
        transform: translateY(-20px);
        transition: transform 0.25s ease;
    }
    .modal-overlay.active .modal-viewport-card {
        transform: translateY(0);
    }
    .modal-header {
        background: #090f1a;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #1b2a47;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-body {
        padding: 1.5rem;
        max-height: 75vh;
        overflow-y: auto;
    }
    .preview-frame {
        width: 100%;
        border-radius: 6px;
        border: 1px solid #1b2a47;
        background: #060b13;
        display: block;
        margin-top: 1rem;
    }
</style>

<div style="margin-bottom: 2rem;">
    <p style="color: #8da2bb; margin: 0;">Review uploaded identification documentation and manage security access tiers for platform drivers.</p>
</div>

<!-- Dynamic Session Alerts Notification Trackers -->
<?php if (isset($_SESSION['audit_success'])): ?>
    <div class="alert-banner alert-success">
        <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($_SESSION['audit_success']); unset($_SESSION['audit_success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['audit_error'])): ?>
    <div class="alert-banner alert-danger">
        <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($_SESSION['audit_error']); unset($_SESSION['audit_error']); ?>
    </div>
<?php endif; ?>

<!-- Pending Verifications Grid/Table -->
<div class="data-table-container">
    <div class="table-header-block">
        <h2>Awaiting Identity Audits <?php echo htmlspecialchars($_SESSION['verification_status'] ?? ''); ?></h2>
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
            <?php if (empty($pendingRequests)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; color: #8da2bb; padding: 2rem;">
                        <i class="fa-solid fa-folder-open" style="font-size: 1.5rem; margin-bottom: 0.5rem; display: block;"></i>
                        No identity documents currently awaiting administrative audit.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pendingRequests as $row): ?>
                    <?php 
                        // Clean file isolation name for UI aesthetics
                        $fileName = basename($row['file_path']); 
                        $formattedDate = date("F d, Y", strtotime($row['created_at']));
                    ?>
                    <tr>
                        <td>
                            <strong style="color: #fff;"><?php echo htmlspecialchars($row['user_name']); ?></strong><br>
                            <span style="font-size: 0.8rem; color: #8da2bb;"><?php echo htmlspecialchars($row['user_email']); ?></span>
                        </td>
                        <td>
                            <div style="margin-bottom: 4px; font-size: 0.85rem; color: #fff; font-weight: 600;">
                                <span class="status-badge" style="background: rgba(30, 111, 255, 0.1); color: #1e6fff; border: 1px solid rgba(30, 111, 255, 0.2); padding: 2px 6px; font-size: 0.75rem;">
                                    <?php echo htmlspecialchars($row['document_type']); ?>
                                </span> 
                                <code style="color: #8da2bb; font-family: monospace; margin-left: 5px;"><?php echo htmlspecialchars($row['document_number']); ?></code>
                            </div>
                            
                            <!-- Trigger dynamic document viewer modal via parameters -->
                            <a href="#" onclick="openDocumentModal(event, '<?php echo addslashes(htmlspecialchars($row['user_name'])); ?>', '<?php echo addslashes(htmlspecialchars($row['document_type'] . ' (' . $row['document_number'] . ')')); ?>', '<?php echo htmlspecialchars($row['file_path']); ?>')" style="color: #1e6fff; text-decoration: none; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fa-solid fa-file-image"></i> <?php echo htmlspecialchars($fileName); ?>
                            </a>
                        </td>
                        <td><?php echo $formattedDate; ?></td>
                        <td style="text-align: right;">
                            <!-- Form handling verification decision updates -->
                            <form action="<?= APP_URL ?>/api/status/approve-user" method="POST" style="display: inline-block;">
                                <input type="hidden" name="verification_id" value="<?php echo $row['user_id']; ?>">
                                <input type="hidden" name="action_decision" value="approved">
                                <button type="submit" style="background: #2ed573; color: #fff; border: none; padding: 0.45rem 0.8rem; border-radius: 6px; font-weight: 600; cursor: pointer; margin-right: 5px; font-size: 0.85rem;">
                                    <i class="fa-solid fa-user-check"></i> Grant Verification
                                </button>
                            </form>

                            <form action="<?= APP_URL ?>/api/status/reject-user" method="POST" style="display: inline-block;">
                                <input type="hidden" name="verification_id" value="<?php echo $row['user_id']; ?>">
                                <input type="hidden" name="action_decision" value="rejected">
                                <button type="submit" style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 0.45rem 0.8rem; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.85rem;">
                                    <i class="fa-solid fa-user-slash"></i> Reject
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Identity Verification Modal Asset Viewer Overlay -->
<div id="documentViewerModal" class="modal-overlay" onclick="closeDocumentModal(event)">
    <div class="modal-viewport-card" onclick="event.stopPropagation()">
        <header class="modal-header">
            <div>
                <h3 id="modal-registrant" style="color: #fff; margin: 0; font-size: 1.1rem;">Document Audit</h3>
                <p id="modal-doc-meta" style="color: #8da2bb; margin: 4px 0 0 0; font-size: 0.8rem;"></p>
            </div>
            <button onclick="closeDocumentModal(null)" style="background: none; border: none; color: #8da2bb; font-size: 1.25rem; cursor: pointer;"><i class="fa-solid fa-xmark"></i></button>
        </header>
        <div class="modal-body">
            <span style="color: #8da2bb; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Asset File Verification Snapshot:</span>
            <img id="modal-preview-img" src="" alt="Verification Target Document Source" class="preview-frame">
        </div>
    </div>
</div>

<script>
    function openDocumentModal(event, registrantName, documentDetails, assetSourcePath) {
        if(event) event.preventDefault();
        
        document.getElementById('modal-registrant').innerText = "Audit: " + registrantName;
        document.getElementById('modal-doc-meta').innerText = documentDetails;
        
        const previewImg = document.getElementById('modal-preview-img');
        previewImg.src = '<?= APP_URL ?>/data/documents/' + assetSourcePath;
        
        document.getElementById('documentViewerModal').classList.add('active');
    }

    function closeDocumentModal(event) {
        if(event && event.target !== document.getElementById('documentViewerModal')) return;
        document.getElementById('documentViewerModal').classList.remove('active');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === "Escape") {
            closeDocumentModal(null);
        }
    });
</script>

<?php 
renderDashboardFooter(); 
?>