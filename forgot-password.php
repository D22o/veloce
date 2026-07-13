<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veloce | Recover Access</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: system-ui, -apple-system, sans-serif; }
        body { background-color: #060b13; color: #ffffff; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1.5rem; }
        .auth-container { background: #0c1524; border: 1px solid #1b2a47; width: 100%; max-width: 420px; border-radius: 12px; padding: 2.5rem 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
        .brand-header { display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 1.5rem; font-weight: 800; letter-spacing: 1.5px; margin-bottom: 2rem; }
        .brand-header i { color: #1e6fff; }
        .form-heading { text-align: center; margin-bottom: 1.5rem; }
        .form-heading h2 { font-size: 1.4rem; font-weight: 700; margin-bottom: 0.5rem; }
        .form-heading p { color: #8da2bb; font-size: 0.9rem; line-height: 1.4; }
        .input-group { position: relative; margin-bottom: 1.5rem; }
        .input-group i { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #8da2bb; font-size: 1rem; }
        .input-field { width: 100%; background: #060b13; border: 1px solid #1b2a47; color: #ffffff; padding: 0.85rem 1rem 0.85rem 2.75rem; border-radius: 8px; font-size: 0.95rem; outline: none; transition: border-color 0.2s ease; }
        .input-field:focus { border-color: #1e6fff; }
        .action-btn { width: 100%; background: #1e6fff; color: #ffffff; border: none; padding: 0.85rem; border-radius: 8px; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .action-btn:hover { background: #1456cc; }
        .footer-links { text-align: center; margin-top: 1.5rem; font-size: 0.9rem; }
        .footer-links a { color: #8da2bb; text-decoration: none; transition: color 0.2s ease; font-weight: 500; }
        .footer-links a:hover { color: #ffffff; }
    </style>
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

    <form action="backend/controller/auth/forgot-process" method="POST">
        <div class="input-group">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" name="email" class="input-field" placeholder="name@domain.com" required autocomplete="email">
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