<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
require_once __DIR__ . '/../../backend/autoload.php';
// Security Check: Kick them out if they don't have an active authorized reset session
if (!isset($_SESSION['authorized_reset_email'])) {
    $_SESSION['auth_error'] = "Unauthorized access. Please complete verification first.";
    header("Location: " . APP_URL . "/auth/login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veloce | Assign New Key</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/css-variables.css">
    <link rel="stylesheet" href="../../assets/css/auth.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
</head>
<body>

<div class="auth-container">
    <div class="form-heading">
        <h2>Establish New Password</h2>
        <p>Re-configure credentials for your driver profile access panel.</p>
    </div>

    <?php if (isset($_SESSION['config_error'])): ?>
        <div style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 0.8rem 1rem; border-radius: 8px; font-size: 0.9rem; margin-bottom: 1.5rem; font-weight: 500;">
            <?php echo $_SESSION['config_error']; unset($_SESSION['config_error']); ?>
        </div>
    <?php endif; ?>

    <form action="<?= APP_URL ?>/api/auth/reset-password" method="POST">
        <div class="input-group">
            <label for="ver-new-password"><i class="fa-solid fa-lock"></i>New Password</label>
            <div class="password-field-wrapper">
                <input type="password" id="ver-new-password" name="new_password" placeholder="••••••••" required>
                <i class="fa-solid fa-eye toggle-password" onclick="togglePasswordVisibility('ver-new-password', this)"></i>
            </div>
        </div>
        <br>
        <div class="input-group">
            <label for="ver-confirm-password"><i class="fa-solid fa-lock"></i>Confirm Password</label>
            <div class="password-field-wrapper">
                <input type="password" id="ver-confirm-password" name="confirm_password" placeholder="••••••••" required>
                <i class="fa-solid fa-eye toggle-password" onclick="togglePasswordVisibility('ver-confirm-password', this)"></i>
            </div>
        </div>
        <button type="submit" class="action-btn">Update Password Profile</button>
    </form>
</div>

    <script src="../../assets/js/auth.js"></script>
</body>
</html>