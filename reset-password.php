<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// Ensure the user went through the OTP verification first
if (!isset($_SESSION['recovery_context']) || $_SESSION['recovery_context']['verified'] !== true) {
    header("Location: forgot-password");
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
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: system-ui, -apple-system, sans-serif; }
        body { background-color: #060b13; color: #ffffff; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1.5rem; }
        .auth-container { background: #0c1524; border: 1px solid #1b2a47; width: 100%; max-width: 420px; border-radius: 12px; padding: 2.5rem 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
        .form-heading { text-align: center; margin-bottom: 1.5rem; }
        .form-heading h2 { font-size: 1.4rem; font-weight: 700; margin-bottom: 0.5rem; }
        .form-heading p { color: #8da2bb; font-size: 0.9rem; }
        .input-group { margin-bottom: 1.25rem; }
        .input-group label { display: block; font-size: 0.85rem; color: #8da2bb; margin-bottom: 0.5rem; font-weight: 600; }
        .input-field { width: 100%; background: #060b13; border: 1px solid #1b2a47; color: #ffffff; padding: 0.85rem 1rem; border-radius: 8px; font-size: 0.95rem; outline: none; }
        .action-btn { width: 100%; background: #1e6fff; color: #ffffff; border: none; padding: 0.85rem; border-radius: 8px; font-size: 0.95rem; font-weight: 600; cursor: pointer; }
    </style>
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

    <form action="backend/controller/verify/commit-password-process" method="POST">
        <div class="input-group">
            <label>New Password</label>
            <input type="password" name="password" class="input-field" placeholder="••••••••" required>
        </div>
        <div class="input-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="input-field" placeholder="••••••••" required>
        </div>
        <button type="submit" class="action-btn">Update Password Profile</button>
    </form>
</div>

</body>
</html>