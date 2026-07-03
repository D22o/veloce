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
        .target-email { color: #ffffff; font-weight: 600; display: block; margin-top: 4px; }
        
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
        <p>We've dispatched a 4-digit code to your inbox at: <span class="target-email">user@gmail.com</span></p>
    </div>

    <!-- Replace action route once backend processes verification status -->
    <form action="process-verification" method="POST">
        <div class="otp-input-wrapper">
            <input type="number" class="otp-box" name="otp[]" maxlength="1" oninput="moveFocus(this, 1)" onkeydown="handleBackspace(this, 0)" required>
            <input type="number" class="otp-box" name="otp[]" maxlength="1" oninput="moveFocus(this, 2)" onkeydown="handleBackspace(this, 1)" required>
            <input type="number" class="otp-box" name="otp[]" maxlength="1" oninput="moveFocus(this, 3)" onkeydown="handleBackspace(this, 2)" required>
            <input type="number" class="otp-box" name="otp[]" maxlength="1" oninput="moveFocus(this, 4)" onkeydown="handleBackspace(this, 3)" required>
        </div>
        
        <button type="submit" class="action-btn">Verify and Continue</button>
    </form>

    <div class="resend-container">
        Didn't receive the code? <a href="#" class="resend-link">Resend OTP</a>
    </div>
</div>

<script>
    // Autofocus behavior logic for discrete verification fields
    const inputs = document.querySelectorAll('.otp-box');

    function moveFocus(current, nextIndex) {
        if (current.value.length >= 1 && nextIndex < inputs.length) {
            inputs[nextIndex].focus();
        }
    }

    function handleBackspace(current, prevIndex) {
        if (event.key === "Backspace" && current.value.length === 0 && prevIndex >= 0) {
            inputs[prevIndex].focus();
        }
    }
</script>
</body>
</html>