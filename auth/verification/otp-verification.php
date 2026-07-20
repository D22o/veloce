<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../backend/autoload.php';

$context = null;
if (isset($_SESSION['temp_reg_data'])) {
    $context = 'register';
    $temp_data = $_SESSION['temp_reg_data'];
} elseif (isset($_SESSION['temp_reset_data'])) {
    $context = 'reset';
    $temp_data = $_SESSION['temp_reset_data'];
}

// If no active validation flow is happening, reject access
if (!$context) {
    header("Location: " . APP_URL . "/auth/login?error=Unauthorized access. Please initiate a valid registration or password reset flow.");
    exit();
}

$temp_email = $temp_data['user_email'] ?? 'amazing-hacker@harvard.edu-now-what?';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veloce | Security Check</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/css-variables.css">
    <link rel="stylesheet" href="../../assets/css/auth.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
</head>
<body>

    <div class="auth-container">
        <div class="shield-icon">
            <i class="fa-solid fa-envelope-shield"></i>
        </div>
        
        <div class="form-heading">
            <h2>Enter Verification Code</h2>
            <p>We've dispatched a 4-digit code to your inbox at: <span class="target-email"><?php echo htmlspecialchars($temp_email); ?></span></p>
        </div>

        <?php if (isset($_SESSION['auth_error'])): ?>
            <div style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 0.8rem 1rem; border-radius: 8px; font-size: 0.9rem; margin-bottom: 1.5rem; text-align: left; font-weight: 500;">
                <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> 
                <?php 
                    echo $_SESSION['auth_error']; 
                    unset($_SESSION['auth_error']); 
                ?>
            </div>
        <?php endif; ?>

        <form action="<?= APP_URL ?>/api/auth/verify-otp" method="POST">
            <div class="otp-input-wrapper">
                <input type="number" class="otp-box" name="otp[]" maxlength="1" oninput="moveFocus(this, 1)" onkeydown="handleBackspace(this, 0)" required>
                <input type="number" class="otp-box" name="otp[]" maxlength="1" oninput="moveFocus(this, 2)" onkeydown="handleBackspace(this, 1)" required>
                <input type="number" class="otp-box" name="otp[]" maxlength="1" oninput="moveFocus(this, 3)" onkeydown="handleBackspace(this, 2)" required>
                <input type="number" class="otp-box" name="otp[]" maxlength="1" oninput="moveFocus(this, 4)" onkeydown="handleBackspace(this, 3)" required>
            </div>
            
            <button type="submit" class="action-btn">Verify and Continue</button>
        </form>

        <div class="resend-container" style="margin-top: 1.5rem; font-size: 0.9rem; color: #8da2bb;">
            Didn't receive the code? <a href="#" onclick="event.preventDefault(); document.getElementById('resend-form').submit();" class="resend-link" style="color: #1e6fff; text-decoration: none; font-weight: 600;">Resend OTP</a>
        </div>

        <?php if ($context === 'register'): ?>
            <?php
                $name_parts = explode(' ', $temp_data['user_name']);
                $first_name = $name_parts[0] ?? '';
                $last_name = $name_parts[1] ?? '';
            ?>
            <form id="resend-form" action="<?= APP_URL ?>/api/auth/register" method="POST" style="display:none;">
                <input type="hidden" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>">
                <input type="hidden" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($temp_email); ?>">
                <input type="hidden" name="password" value="PRE_SECURED_HASH">
                <input type="hidden" name="confirm_password" value="PRE_SECURED_HASH">
            </form>
        <?php else: ?>
            <form id="resend-form" action="<?= APP_URL ?>/api/auth/forgot-password" method="POST" style="display:none;">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($temp_email); ?>">
            </form>
        <?php endif; ?>
    </div>

    <script src="../../assets/js/auth.js"></script>
</body>
</html>