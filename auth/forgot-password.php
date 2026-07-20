<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/../backend/autoload.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veloce | Recover Access</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/css-variables.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
    <link rel="stylesheet" href="../assets/css/media-queries.css">
</head>
<body>

<div class="auth-container">
    <div class="brand-header">
        <i class="fa-solid fa-car-side"></i> VELOCE
    </div>
    
    <div class="form-heading">
        <h2>Reset Password</h2>
        <p>Enter the email address linked to your driver profile, and we'll transmit a secure OTP code.</p>
    </div>

    <?php if (isset($_SESSION['reset_error'])): ?>
        <div style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 0.8rem 1rem; border-radius: 8px; font-size: 0.9rem; margin-bottom: 1.5rem; font-weight: 500;">
            <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> 
            <?php echo $_SESSION['reset_error']; unset($_SESSION['reset_error']); ?>
        </div>
    <?php endif; ?>

    <form action="<?= APP_URL ?>/api/auth/forgot-password" method="POST">
        <div class="input-group">
            <label for="fp-email"><i class="fa-solid fa-envelope"></i> Email Address</label>
            <input type="email" id="fp-email" name="email" placeholder="name@domain.com" required>
        </div>
        
        <button type="submit" class="action-btn">
            Send Verification Code <i class="fa-solid fa-arrow-right"></i>
        </button>
    </form>

    <div class="footer-links">
        <a href="login"><i class="fa-solid fa-chevron-left" style="font-size: 0.8rem; margin-right: 4px;"></i> Return to Sign In</a>
    </div>
</div>

</body>
</html>