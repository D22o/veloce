<?php
require_once '../layout/main-layout.php';

renderDashboardHeader('Account Profile & Verification', 'verification', 'user');

if (isset($_SESSION['user_id'])) {
    $userModel = new \Backend\Models\UserModel;
    $_SESSION['verification_status'] = $userModel->getUserVerificationStatus($_SESSION['user_id']);
}

// Pull details directly from active session memory safely
$fullName = $_SESSION['user_name'] ?? 'Driver Profile';
$emailAddress = $_SESSION['user_email'] ?? 'driver@veloce.com';
$phoneNumber = $_SESSION['phone_number'];

// Generate dynamic avatar initials matching layout engine
$nameParts = explode(' ', trim($fullName));
$initials = strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1));
?>

<style>
    .profile-grid-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    @media (min-width: 992px) {
        .profile-grid-container {
            grid-template-columns: 320px 1fr;
        }
    }

    /* Meta Details Card (Left side) */
    .meta-identity-card {
        background: #0c1524;
        border: 1px solid #1b2a47;
        border-radius: 12px;
        padding: 2rem 1.5rem;
        text-align: center;
        height: fit-content;
    }

    .large-profile-avatar {
        width: 90px;
        height: 90px;
        background: linear-gradient(135deg, #1e6fff, #1456cc);
        color: #ffffff;
        font-size: 2.2rem;
        font-weight: 800;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        box-shadow: 0 0 20px rgba(30, 111, 255, 0.2);
        border: 3px solid #121f35;
    }

    .verification-pill-box {
        background: rgba(46, 213, 115, 0.1);
        border: 1px solid rgba(46, 213, 115, 0.2);
        padding: 0.5rem 1rem;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 1rem 0 2rem;
    }

    .verification-pill-box i { color: #2ed573;font-size: 0.9rem; }
    .verification-pill-box span { color: #2ed573; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }

    .identity-bulletin-list {
        border-top: 1px solid #1b2a47;
        padding-top: 1.5rem;
        text-align: left;
    }

    .bulletin-node {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .bulletin-node:last-child { margin-bottom: 0; }
    .bulletin-node label { color: #8da2bb; font-size: 0.85rem; font-weight: 500; }
    .bulletin-node value { color: #ffffff; font-size: 0.9rem; font-weight: 600; }
    .bulletin-node .verified {
        background: rgba(46, 213, 115, 0.1);
        color: #2ed573;
        border: 1px solid rgba(46, 213, 115, 0.2);
    }
    .bulletin-node .pending {
        background: rgba(255, 184, 0, 0.1);
        color: #ffb800;
        border: 1px solid rgba(255, 184, 0, 0.2);
    }
    .bulletin-node .rejected {
        background: rgba(255, 74, 74, 0.1);
        color: #ff4a4a;
        border: 1px solid rgba(255, 74, 74, 0.2);
    }
    .bulletin-node .unverified {
        background: rgba(255, 74, 74, 0.1);
        color: #ff4a4a;
        border: 1px solid rgba(255, 74, 74, 0.2);
    }

    /* Interactive Management Panel (Right side) */
    .profile-management-panel {
        background: #0c1524;
        border: 1px solid #1b2a47;
        border-radius: 12px;
        overflow: hidden;
    }

    .panel-navigation-header {
        background: #090f1a;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #1b2a47;
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .nav-tab-btn {
        background: none;
        border: none;
        color: #8da2bb;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        padding-bottom: 4px;
        border-bottom: 2px solid transparent;
        transition: all 0.2s ease;
    }

    .nav-tab-btn.active, .nav-tab-btn:hover {
        color: #1e6fff;
        border-bottom-color: #1e6fff;
    }

    .panel-content-body { padding: 2rem 1.5rem; }
    .form-segment-view { display: none; }
    .form-segment-view.active { display: block; }

    .input-grid-wrapper {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    @media(min-width: 600px) {
        .input-grid-wrapper { grid-template-columns: repeat(2, 1fr); }
        .span-full-width { grid-column: span 2; }
    }

    .control-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .control-group label {
        color: #8da2bb;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .control-input-field {
        background: #060b13;
        border: 1px solid #1b2a47;
        color: #ffffff;
        padding: 0.8rem 1rem;
        border-radius: 8px;
        font-size: 0.95rem;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .control-input-field:focus { border-color: #1e6fff; }
    .control-input-field:disabled { background: #090f1a; color: #5f758e; cursor: not-allowed; }

    .commit-save-btn {
        background: #1e6fff;
        color: #ffffff;
        border: none;
        padding: 0.85rem 2rem;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .commit-save-btn:hover { background: #1456cc; }

    /* Custom Document Upload UI Elements */
    .file-dropzone-box {
        border: 2px dashed #1b2a47;
        background: #060b13;
        border-radius: 8px;
        padding: 2.5rem 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }
    
    .file-dropzone-box:hover {
        border-color: #1e6fff;
        background: rgba(30, 111, 255, 0.02);
    }

    .file-dropzone-box input[type="file"] {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .dropzone-icon {
        font-size: 2.5rem;
        color: #8da2bb;
        margin-bottom: 1rem;
    }
</style>

<div class="profile-grid-container">

    <aside class="meta-identity-card">
        <div class="large-profile-avatar"><?php echo htmlspecialchars($initials); ?></div>
        <h2 style="color: #fff; font-size: 1.3rem; font-weight: 700; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($fullName); ?></h2>
        <p style="color: #8da2bb; font-size: 0.85rem;"><?php echo htmlspecialchars($emailAddress); ?></p>

        <div class="verification-pill-box">
            <i class="fa-solid fa-circle-check"></i>
            <span>Email Verified</span>
        </div>
    
        <div class="identity-bulletin-list">
            <div class="bulletin-node">
                <label>Platform Access</label>
                <?php if ($_SESSION['verification_status'] === 'approved'): ?>
                    <span class="status-badge verified">Active Driver</span>
                <?php else: ?>
                    <span class="status-badge unverified">Inactive</span>
                <?php endif; ?>
            </div>
            <div class="bulletin-node">
                <label>License Verification</label>
                <value class="status-badge <?= htmlspecialchars($_SESSION['verification_status']) ?>" style="font-size: 0.7rem;"><?= htmlspecialchars($_SESSION['verification_status']) ?></value>
            </div>
            <div class="bulletin-node">
                <label>Onboard Date</label>
                <value>July 2026</value>
            </div>
        </div>
    </aside>

    <section class="profile-management-panel">
        <header class="panel-navigation-header">
            <button type="button" class="nav-tab-btn active" onclick="switchTab(event, 'general-settings')">General Settings</button>
            <button type="button" class="nav-tab-btn" onclick="switchTab(event, 'security-override')">Security & Password</button>
            <button type="button" class="nav-tab-btn" onclick="switchTab(event, 'document-verification')">Document Verification</button>
        </header>

        <div class="panel-content-body">
            
            <?php if (isset($_SESSION['profile_success'])): ?>
                <div style="background: rgba(46, 213, 115, 0.1); border: 1px solid #2ed573; color: #2ed573; padding: 0.8rem 1rem; border-radius: 8px; font-size: 0.9rem; margin-bottom: 1.5rem; font-weight: 500;">
                    <i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> <?php echo $_SESSION['profile_success']; unset($_SESSION['profile_success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['profile_error'])): ?>
                <div style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 0.8rem 1rem; border-radius: 8px; font-size: 0.9rem; margin-bottom: 1.5rem; font-weight: 500;">
                    <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> <?php echo $_SESSION['profile_error']; unset($_SESSION['profile_error']); ?>
                </div>
            <?php endif; ?>

            <!-- General Settings View -->
            <form id="general-settings" class="form-segment-view active" action="<?= APP_URL ?>/api/user/update-profile" method="POST">
                <div class="input-grid-wrapper">
                    <div class="control-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="control-input-field" value="<?php echo htmlspecialchars($nameParts[0] ?? ''); ?>" required>
                    </div>
                    <div class="control-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="control-input-field" value="<?php echo htmlspecialchars($nameParts[1] ?? ''); ?>" required>
                    </div>
                    <div class="control-group">
                        <label>Email Address</label>
                        <input type="email" class="control-input-field" value="<?php echo htmlspecialchars($emailAddress); ?>" disabled>
                        <span style="color: #5f758e; font-size: 0.75rem; font-weight: 500;"><i class="fa-solid fa-lock" style="margin-right:4px;"></i> Email changes require custom support clearance authorization.</span>
                    </div>
                    <div class="control-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="tel" name="phone_number" class="control-input-field" placeholder="+63- 10 Digit Phone Number" value="<?php echo htmlspecialchars($phoneNumber ?? ''); ?>">
                    </div>
                </div>
                <button type="submit" class="commit-save-btn"><i class="fa-solid fa-floppy-disk"></i> Save Modifications</button>
            </form>

            <!-- Security View -->
            <form id="security-override" class="form-segment-view" action="<?= APP_URL ?>/api/user/update-password" method="POST">
                <div class="input-grid-wrapper">
                    <div class="control-group span-full-width">
                        <label>Current Password</label>
                        <input type="password" name="current_password" class="control-input-field" placeholder="••••••••" required>
                    </div>
                    <div class="control-group">
                        <label>New Secure Password</label>
                        <input type="password" name="new_password" class="control-input-field" placeholder="••••••••" required>
                    </div>
                    <div class="control-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_new_password" class="control-input-field" placeholder="••••••••" required>
                    </div>
                </div>
                <button type="submit" class="commit-save-btn" style="background: #e67e22;"><i class="fa-solid fa-shield-keyhole"></i> Cycle Access Token</button>
            </form>

            <!-- Document Verification View (New Added Tab) -->
            <form id="document-verification" class="form-segment-view" action="<?= APP_URL ?>/api/user/verify-profile" method="POST" enctype="multipart/form-data">
                <?php if (isset($_SESSION['verification_status']) && $_SESSION['verification_status'] === 'approved'): ?>
                    <div style="background: rgba(46, 213, 115, 0.1); border: 1px solid #2ed573; color: #2ed573; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
                        <i class="fa-solid fa-circle-check"></i> Your account is fully verified. No further action is required.
                    </div>
                <?php elseif (isset($_SESSION['verification_status']) && $_SESSION['verification_status'] === 'pending'): ?>
                    <div style="background: rgba(255, 184, 0, 0.1); border: 1px solid #ffb800; color: #ffb800; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
                        <i class="fa-solid fa-hourglass-half"></i> Your verification documents are currently under review. Please allow up to 48 hours for processing.
                    </div>
                <?php elseif (isset($_SESSION['verification_status']) && $_SESSION['verification_status'] === 'rejected'): ?>
                    <div style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Your verification documents were rejected. Please review the requirements and resubmit.
                    </div>
                <?php elseif (isset($_SESSION['verification_status']) && $_SESSION['verification_status'] === 'unverified'): ?>
                    <div style="margin-bottom: 1.5rem;">
                        <h3 style="color: #fff; margin: 0 0 0.5rem 0; font-size: 1.1rem;">Upload Active Driving Credentials</h3>
                        <p style="color: #8da2bb; font-size: 0.85rem; margin: 0;">In order to unlock system ride matching capabilities, you must upload a clear photographic record of your regulatory Driver's License.</p>
                    </div>

                    <div class="input-grid-wrapper">
                        <div class="control-group">
                            <label>Document Type Identification</label>
                            <select name="document_type" class="control-input-field" style="height: 47px;" required>
                                <option value="drivers_license">Driver's License</option>
                                <option value="passport">International Passport</option>
                                <option value="national_id">National Identification Card</option>
                            </select>
                        </div>
                        <div class="control-group">
                            <label>Document / License Number</label>
                            <input type="text" name="document_number" class="control-input-field" placeholder="e.g. N01-12-345678" required>
                        </div>

                        <div class="control-group span-full-width">
                            <label>File Upload Asset</label>
                            <div class="file-dropzone-box">
                                <i class="fa-solid fa-cloud-arrow-up dropzone-icon"></i>
                                <h4 style="color: #fff; margin: 0 0 0.25rem 0; font-size: 0.95rem;">Select image asset or drop here</h4>
                                <p style="color: #5f758e; font-size: 0.8rem; margin: 0;" id="file-chosen-text">Supports JPG, PNG formats (Max size: 5MB)</p>
                                <input type="file" name="verification_document" accept="image/jpeg, image/png" onchange="displayFileName(this)" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="commit-save-btn" style="background: #1e6fff;"><i class="fa-solid fa-upload"></i> Process Document Submission</button>
                <?php endif; ?>
            </form>

        </div>
    </section>
</div>

<script>
    function switchTab(event, targetTabId) {
        // Disengage all structural viewing segments
        document.querySelectorAll('.form-segment-view').forEach(view => view.classList.remove('active'));
        document.querySelectorAll('.nav-tab-btn').forEach(tab => tab.classList.remove('active'));

        // Engage targeted elements
        document.getElementById(targetTabId).classList.add('active');
        event.currentTarget.classList.add('active');
    }

    // Interactive name update script for file dropzone interface
    function displayFileName(inputElement) {
        const statusText = document.getElementById('file-chosen-text');
        if (inputElement.files && inputElement.files.length > 0) {
            statusText.innerText = "Target Asset: " + inputElement.files[0].name;
            statusText.style.color = "#2ed573";
        } else {
            statusText.innerText = "Supports JPG, PNG formats (Max size: 5MB)";
            statusText.style.color = "#5f758e";
        }
    }
</script>

<?php renderDashboardFooter(); ?>