<?php
require_once '../includes/layout.php';
renderDashboardHeader('Account Profile & Verification', 'verification', 'user');

// Pull details directly from active session memory safely
$fullName = $_SESSION['user_name'] ?? 'Driver Profile';
$emailAddress = $_SESSION['user_email'] ?? 'driver@veloce.com';

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

    .verification-pill-box i { color: #2ed573; font-size: 0.9rem; }
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
</style>

<div class="profile-grid-container">

    <aside class="meta-identity-card">
        <div class="large-profile-avatar"><?php echo htmlspecialchars($initials); ?></div>
        <h2 style="color: #fff; font-size: 1.3rem; font-weight: 700; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($fullName); ?></h2>
        <p style="color: #8da2bb; font-size: 0.85rem;"><?php echo htmlspecialchars($emailAddress); ?></p>

        <div class="verification-pill-box">
            <i class="fa-solid fa-badge-check"></i>
            <span>Identity Trusted</span>
        </div>

        <div class="identity-bulletin-list">
            <div class="bulletin-node">
                <label>Platform Access</label>
                <value style="color: #2ed573;">Active Driver</value>
            </div>
            <div class="bulletin-node">
                <label>License Verification</label>
                <value class="status-badge active" style="font-size: 0.7rem;">Verified</value>
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

            <form id="general-settings" class="form-segment-view active" action="../backend/controller/update-profile-metadata" method="POST">
                <div class="input-grid-wrapper">
                    <div class="control-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="control-input-field" value="<?php echo htmlspecialchars($nameParts[0] ?? ''); ?>" required>
                    </div>
                    <div class="control-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="control-input-field" value="<?php echo htmlspecialchars($nameParts[1] ?? ''); ?>" required>
                    </div>
                    <div class="control-group span-full-width">
                        <label>Email Address</label>
                        <input type="email" class="control-input-field" value="<?php echo htmlspecialchars($emailAddress); ?>" disabled>
                        <span style="color: #5f758e; font-size: 0.75rem; font-weight: 500;"><i class="fa-solid fa-lock" style="margin-right:4px;"></i> Email changes require custom support clearance authorization.</span>
                    </div>
                </div>
                <button type="submit" class="commit-save-btn"><i class="fa-solid fa-floppy-disk"></i> Save Modifications</button>
            </form>

            <form id="security-override" class="form-segment-view" action="../backend/controller/update-profile-security" method="POST">
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
</script>

<?php renderDashboardFooter(); ?>