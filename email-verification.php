<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check: If there is no registration session, kick them back to registration page
if (!isset($_SESSION['temp_reg_data'])) {
    header("Location: register");
    exit();
}

$displayEmail = $_SESSION['temp_reg_data']['user_email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veloce | Security Check</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: system-ui, -apple-system, sans-serif; }
        body { background-color: #060b13; color: #ffffff; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1.5rem; }
        
        .auth-container { background: #0c1524; border: 1px solid #1b2a47; width: 100%; max-width: 420px; border-radius: 12px; padding: 2.5rem 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.3); text-align: center; }
        .shield-icon { width: 56px; height: 56px; background: rgba(30, 111, 255, 0.1); color: #1e6fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 1.5rem; }
        
        .form-heading h2 { font-size: 1.4rem; font-weight: 700; margin-bottom: 0.5rem; }
        .form-heading p { color: #8da2bb; font-size: 0.9rem; line-height: 1.4; margin-bottom: 2rem; }
        .target-email { color: #ffffff; font-weight: 600; display: block; margin-top: 4px; word-break: break-all; }
        
        .otp-input-wrapper { display: flex; justify-content: center; gap: 0.75rem; margin-bottom: 2rem; }
        .otp-box { width: 56px; height: 56px; background: #060b13; border: 1px solid #1b2a47; border-radius: 8px; text-align: center; font-size: 1.5rem; font-weight: 700; color: #ffffff; outline: none; transition: border-color 0.2s ease; }
        .otp-box:focus { border-color: #1e6fff; }
        
        /* Remove arrows/spinners on inputs */
        .otp-box::-webkit-outer-spin-button, .otp-box::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        
        .action-btn { width: 100%; background: #1e6fff; color: #ffffff; border: none; padding: 0.85rem; border-radius: 8px; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s ease; }
        .action-btn:hover { background: #1456cc; }
        
        .resend-container { margin-top: 1.5rem; font-size: 0.85rem; color: #8da2bb; }
        .resend-link { color: #1e6fff; text-decoration: none; font-weight: 600; }
        .resend-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="shield-icon">
        <i class="fa-solid fa-envelope-shield"></i>
    </div>
    
    <div class="form-heading">
        <h2>Enter Verification Code</h2>
        <p>We've dispatched a 4-digit code to your inbox at: <span class="target-email"><?php echo htmlspecialchars($displayEmail); ?></span></p>
    </div>

    <?php if (isset($_SESSION['otp_error'])): ?>
        <div style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 0.8rem 1rem; border-radius: 8px; font-size: 0.9rem; margin-bottom: 1.5rem; text-align: left; font-weight: 500;">
            <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> 
            <?php 
                echo $_SESSION['otp_error']; 
                unset($_SESSION['otp_error']); 
            ?>
        </div>
    <?php endif; ?>

    <form action="backend/controller/verify/verify-process" method="POST">
        <div class="otp-input-wrapper">
            <input type="number" class="otp-box" name="otp[]" maxlength="1" oninput="moveFocus(this, 1)" onkeydown="handleBackspace(this, 0)" required>
            <input type="number" class="otp-box" name="otp[]" maxlength="1" oninput="moveFocus(this, 2)" onkeydown="handleBackspace(this, 1)" required>
            <input type="number" class="otp-box" name="otp[]" maxlength="1" oninput="moveFocus(this, 3)" onkeydown="handleBackspace(this, 2)" required>
            <input type="number" class="otp-box" name="otp[]" maxlength="1" oninput="moveFocus(this, 4)" onkeydown="handleBackspace(this, 3)" required>
        </div>
        
        <button type="submit" class="action-btn">Verify and Continue</button>
    </form>

    <div class="resend-container">
        Didn't receive the code? <a href="backend/controller/register-process" onclick="event.preventDefault(); document.getElementById('resend-form').submit();" class="resend-link">Resend OTP</a>
    </div>

    <form id="resend-form" action="backend/controller/register-process" method="POST" style="display:none;">
        <input type="hidden" name="first_name" value="<?php echo htmlspecialchars(explode(' ', $_SESSION['temp_reg_data']['user_name'])[0]); ?>">
        <input type="hidden" name="last_name" value="<?php echo htmlspecialchars(explode(' ', $_SESSION['temp_reg_data']['user_name'])[1] ?? ''); ?>">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['temp_reg_data']['user_email']); ?>">
        <input type="hidden" name="password" value="<?php echo htmlspecialchars($_SESSION['temp_reg_data']['user_pass']); ?>">
        <input type="hidden" name="confirm_password" value="<?php echo htmlspecialchars($_SESSION['temp_reg_data']['user_pass']); ?>">
    </form>
</div>

<script>
    const inputs = document.querySelectorAll('.otp-box');

    // Autofocus shifts cleanly forwards
    function moveFocus(current, nextIndex) {
        if (current.value.length >= 1 && nextIndex < inputs.length) {
            inputs[nextIndex].focus();
        }
    }

    // Safely shift focus backward when hitting backspace on empty frames
    function handleBackspace(current, prevIndex) {
        if (event.key === "Backspace" && current.value.length === 0 && prevIndex >= 0) {
            inputs[prevIndex].focus();
        }
    }
</script>
</body>
</html>