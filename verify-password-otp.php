<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['recovery_context'])) { header("Location: forgot-password"); exit(); }
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
        .shield-icon { width: 56px; height: 56px; background: rgba(230, 126, 34, 0.1); color: #e67e22; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 1.5rem; }
        .form-heading h2 { font-size: 1.4rem; font-weight: 700; margin-bottom: 0.5rem; }
        .form-heading p { color: #8da2bb; font-size: 0.9rem; line-height: 1.4; margin-bottom: 2rem; }
        .otp-input-wrapper { display: flex; justify-content: center; gap: 0.75rem; margin-bottom: 2rem; }
        .otp-box { width: 56px; height: 56px; background: #060b13; border: 1px solid #1b2a47; border-radius: 8px; text-align: center; font-size: 1.5rem; font-weight: 700; color: #ffffff; outline: none; transition: border-color 0.2s ease; }
        .otp-box:focus { border-color: #e67e22; }
        .otp-box::-webkit-outer-spin-button, .otp-box::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .action-btn { width: 100%; background: #e67e22; color: #ffffff; border: none; padding: 0.85rem; border-radius: 8px; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s ease; }
        .action-btn:hover { background: #d35400; }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="shield-icon"><i class="fa-solid fa-key-skeleton"></i></div>
    <div class="form-heading">
        <h2>Enter Recovery Token</h2>
        <p>Verify recovery parameters for: <span style="color:#fff; font-weight:600;"><?php echo htmlspecialchars($_SESSION['recovery_context']['email']); ?></span></p>
    </div>

    <?php if (isset($_SESSION['otp_error'])): ?>
        <div style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 0.8rem 1rem; border-radius: 8px; font-size: 0.9rem; margin-bottom: 1.5rem; text-align: left; font-weight: 500;">
            <?php echo $_SESSION['otp_error']; unset($_SESSION['otp_error']); ?>
        </div>
    <?php endif; ?>

    <form action="backend/controller/verify/verify-recovery-process" method="POST">
        <div class="otp-input-wrapper">
            <input type="number" class="otp-box" name="otp[]" maxlength="1" oninput="moveFocus(this, 1)" onkeydown="handleBackspace(this, 0)" required>
            <input type="number" class="otp-box" name="otp[]" maxlength="1" oninput="moveFocus(this, 2)" onkeydown="handleBackspace(this, 1)" required>
            <input type="number" class="otp-box" name="otp[]" maxlength="1" oninput="moveFocus(this, 3)" onkeydown="handleBackspace(this, 2)" required>
            <input type="number" class="otp-box" name="otp[]" maxlength="1" oninput="moveFocus(this, 4)" onkeydown="handleBackspace(this, 3)" required>
        </div>
        <button type="submit" class="action-btn">Authorize Override</button>
    </form>
</div>

<script>
    const inputs = document.querySelectorAll('.otp-box');
    function moveFocus(current, idx) { if (current.value.length >= 1 && idx < inputs.length) inputs[idx].focus(); }
    function handleBackspace(current, idx) { if (event.key === "Backspace" && current.value.length === 0 && idx >= 0) inputs[idx].focus(); }
</script>
</body>
</html>