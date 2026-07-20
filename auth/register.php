<?php
    session_start();
    require_once __DIR__ . '/../backend/autoload.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veloce | Client Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/css-variables.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
    <link rel="stylesheet" href="../assets/css/media-queries.css">
</head>
<body>

    <div class="split-auth-container">
        <div class="auth-banner-side">
            <div class="banner-overlay"></div>
            <div class="banner-content">
                <a href="../index" class="back-home-link"><i class="fa-solid fa-arrow-left"></i> Back to Main Showcase</a>
                <div class="brand-showcase">
                    <div class="logo"><i class="fa-solid fa-car-side"></i> VELOCE</div>
                    <h2>Your Premium Key Awaits.</h2>
                    <p>Join an exclusive collective of drivers. Create your encrypted dashboard profile to unlock instant verification.</p>
                </div>
                <div class="banner-footer">
                    <p>&copy; 2026 VELOCE. End-to-end data safety protocol.</p>
                </div>
            </div>
        </div>
        <div class="auth-form-side">
            <div class="form-box-wrapper">
                <div class="form-header">
                    <h2>Registration Portal</h2>
                    <p>Provide your basic driver metadata details.</p>
                </div>
                <?php if (isset($_SESSION['reg_error'])): ?>
                    <div style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 0.8rem 1rem; border-radius: 8px; font-size: 0.9rem; margin-bottom: 1.5rem; font-weight: 500;">
                        <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> 
                        <?php echo $_SESSION['reg_error']; unset($_SESSION['reg_error']); ?>
                    </div>
                <?php endif; ?>
                <form id="form-register" class="auth-action-form" action="<?= APP_URL ?>/api/auth/register" method="POST">
                    <div class="input-grid">
                        <div class="input-group">
                            <label for="reg-first"><i class="fa-solid fa-user"></i> First Name</label>
                            <input type="text" id="reg-first" name="first_name" placeholder="John" required>
                        </div>
                        <div class="input-group">
                            <label for="reg-last">Last Name</label>
                            <input type="text" id="reg-last" name="last_name" placeholder="Doe" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="reg-email"><i class="fa-solid fa-envelope"></i> Email Address</label>
                        <input type="email" id="reg-email" name="email" placeholder="your-email@email.com" required>
                    </div>

                    <div class="input-grid">
                        <div class="input-group">
                            <label for="reg-password"><i class="fa-solid fa-lock"></i> Password</label>
                            <div class="password-field-wrapper">
                                <input type="password" id="reg-password" name="password" placeholder="••••••••" required>
                                <i class="fa-solid fa-eye toggle-password" onclick="togglePasswordVisibility('reg-password', this)"></i>
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="reg-confirm-password">Confirm Password</label>
                            <div class="password-field-wrapper">
                                <input type="password" id="reg-confirm-password" name="confirm_password" placeholder="••••••••" required>
                                <i class="fa-solid fa-eye toggle-password" onclick="togglePasswordVisibility('reg-confirm-password', this)"></i>
                            </div>
                        </div>
                    </div>

                    <div class="input-group">
                        <label class="remember-me alignment-fix">
                            <input type="checkbox" required> <span>I accept the platform Terms of Service & Security Policies</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </form>

                <div class="auth-form-footer">
                    <p>Already registered here? <a href="login" class="switch-auth-link">Sign In</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/auth.js"></script>
</body>
</html>