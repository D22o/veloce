<?php
    session_start();
    require_once __DIR__ . '/../backend/autoload.php';
    // echo password_hash("bonsurdev@gmail.com", PASSWORD_BCRYPT);
    // echo password_verify("bonsurdev@gmail.com", "$2y$10\$iooR5kFAIvDN.44j8MX6kuQZOOh/kKM8P44qoDkK6BxtT/avqoLZG");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veloce | Sign In</title>
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
                    <h2>Experience Chilled Luxury & Performance.</h2>
                    <p>Access your secure booking profile, review elite configurations, and track active premium rentals natively.</p>
                </div>
                <div class="banner-footer">
                    <p>&copy; 2026 VELOCE. Security assured via token verification.</p>
                </div>
            </div>
        </div>
        <div class="auth-form-side">
            <div class="form-box-wrapper">
                <div class="form-header">
                    <h2>Welcome Back</h2>
                    <p>Enter your authorization credentials below.</p>
                </div>
                <?php if (isset($_SESSION['auth_error'])): ?>
                    <div style="background: rgba(255, 74, 74, 0.1); border: 1px solid #ff4a4a; color: #ff4a4a; padding: 0.8rem 1rem; border-radius: 8px; font-size: 0.9rem; margin-bottom: 1.5rem; font-weight: 500;">
                        <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> 
                        <?php 
                            echo $_SESSION['auth_error']; 
                            unset($_SESSION['auth_error']); // Flush after reading
                        ?>
                    </div>
                <?php endif; ?>
                <form id="form-login" class="auth-action-form" action="<?= APP_URL ?>/api/auth/login"  method="POST">
                    <div class="input-group">
                        <label for="login-email"><i class="fa-solid fa-envelope"></i> Email Address</label>
                        <input type="email" id="login-email" name="email" placeholder="name@domain.com" required>
                    </div>

                    <div class="input-group">
                        <label for="login-password"><i class="fa-solid fa-lock"></i> Password</label>
                        <div class="password-field-wrapper">
                            <input type="password" id="login-password" name="password" placeholder="••••••••" required>
                            <i class="fa-solid fa-eye toggle-password" onclick="togglePasswordVisibility('login-password', this)"></i>
                        </div>
                    </div>

                    <div class="form-actions">
                        <label class="remember-me">
                            <input type="checkbox" name="remember_me"> <span>Remember device</span>
                        </label>
                        <a href="forgot-password" class="forgot-pass">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">login</button>
                </form>

                <div class="auth-form-footer">
                    <p>New to the platform? <a href="register" class="switch-auth-link">Create an account</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/auth.js"></script>
</body>
</html>