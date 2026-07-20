<?php

namespace Backend\Controllers;

require_once dirname(__DIR__) . '/config/PHPMailer/src/Exception.php';
require_once dirname(__DIR__) . '/config/PHPMailer/src/PHPMailer.php';
require_once dirname(__DIR__) . '/config/PHPMailer/src/SMTP.php';

use Backend\Models\UserModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class AuthController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function loginController(string $email, string $password)
    {
        // 1. Fetch user from Model
        $user = $this->userModel->findUserByEmail($email);

        // 2. Perform credential verify checks
        if ($user && password_verify($password, $user['user_pass'])) {

            // Check if the user is verified
            $isVerified = $this->userModel->getUserVerificationStatus($user['user_id'] ?? null);

            // Prevent Session Fixation
            session_regenerate_id(true);

            // 3. Set Session Payload
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['user_email'] = $user['user_email'];
            $_SESSION['phone_number'] = $user['phone'];
            $_SESSION['user_role'] = $user['user_role'];
            $_SESSION['verification_status'] = $isVerified;

            $cookie_payload = [
                'user_id'   => $user['user_id'],
                'user_role' => $user['user_role']
            ];
            
            // Sets the secure auth cookie for 24 hours
            set_auth_cookie($cookie_payload, 86400);

            // 4. Role Based Routing redirect
            if ($user['user_role'] === 'admin') {
                header("Location: " . APP_URL . "/admin/admin-dashboard");
            } else {
                header("Location: " . APP_URL . "/user/user-dashboard");
            }
            exit();
        }

        // Return error if authentication fails
        $_SESSION['auth_error'] = "Invalid email or password.";
        header("Location: " . APP_URL . "/auth/login");
        exit;
    }

    public function registerController(array $cleanData)
    {
        // 1. Check if user already exists
        $existingUser = $this->userModel->findUserByEmail($cleanData['email']);
        if ($existingUser) {
            $_SESSION['auth_error'] = "This email is already registered.";
            header("Location: " . APP_URL . "/auth/register");
            exit();
        }

        // 2. Generate secure 4-digit verification token
        $otpCode = random_int(1000, 9999);

        // 3. Stash pending registration data temporarily in Session Memory
        $_SESSION['temp_reg_data'] = [
            'user_name'  => $cleanData['username'],
            'user_email' => $cleanData['email'],
            'user_pass'  => password_hash($cleanData['password'], PASSWORD_BCRYPT), // Pre-hash for DB write later
            'otp_code'   => $otpCode,
            'expires_at' => time() + 600 // 10 minutes expiry window
        ];

        $mail = new PHPMailer(true);

        try {
            // SMTP Configurations
            $mail->isSMTP();
            $mail->Host       = getenv('SMTP_HOST') ?: $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SMTP_USER') ?: $_ENV['SMTP_USER'];
            $mail->Password   = getenv('SMTP_PASS') ?: $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = intval(getenv('SMTP_PORT') ?: $_ENV['SMTP_PORT']);

            // Identity Configurations
            $mail->setFrom($mail->Username, 'Veloce Security');
            $mail->addAddress($cleanData['email'], $cleanData['username']);

            // Compose Document Template
            $mail->isHTML(true);
            $mail->Subject = "Veloce Access Key: Verify Your Driver Profile";
            $mail->Body    = "
                <html>
                <body style='background-color: #060b13; color: #ffffff; font-family: sans-serif; padding: 2rem;'>
                    <div style='max-width: 400px; margin: 0 auto; background: #0c1524; border: 1px solid #1b2a47; padding: 2rem; border-radius: 12px; border-top: 4px solid #1e6fff;'>
                        <h2 style='color: #1e6fff; text-align: center; margin-bottom: 1.5rem;'>VELOCE</h2>
                        <p style='color: #8da2bb; font-size: 0.95rem; line-height: 1.5;'>Your security verification token is displayed below. Use this code to authorize your platform onboarding registration:</p>
                        <div style='background: #060b13; font-size: 2.5rem; font-weight: bold; letter-spacing: 6px; text-align: center; padding: 1rem; color: #ffffff; border-radius: 8px; border: 1px solid #1b2a47; margin: 1.5rem 0;'>
                            $otpCode
                        </div>
                        <p style='color: #8da2bb; font-size: 0.8rem; text-align: center;'>This access ticket window expires automatically in 10 minutes.</p>
                    </div>
                </body>
                </html>
            ";

            $mail->send();

            // Redirect to OTP verification view using extension-less routing base
            header("Location: " . APP_URL . "/auth/verification/otp-verification");
            exit();

        } catch (Exception $e) {
            error_log("PHPMailer Engine Failure: " . $mail->ErrorInfo);
            unset($_SESSION['temp_reg_data']); // Clear temporary storage on network failure
            $_SESSION['auth_error'] = "Mailer Network Issue: Unable to securely transmit validation token.";
            header("Location: " . APP_URL . "/auth/register");
            exit();
        }
    }
    public function forgotPasswordController(string $email)
    {
        // 1. Verify user exists in the system
        $user = $this->userModel->findUserByEmail($email);
        if (!$user) {
            $_SESSION['auth_error'] = "We couldn't find a driver profile associated with that email.";
            header("Location: " . APP_URL . "/auth/forgot-password");
            exit();
        }

        $otpCode = random_int(1000, 9999);

        // Clear registration session if it exists to avoid flow collision
        unset($_SESSION['temp_reg_data']);

        // 2. Set up temporary recovery session data
        $_SESSION['temp_reset_data'] = [
            'user_email' => $email,
            'otp_code'   => $otpCode,
            'expires_at' => time() + 600 // 10 minutes
        ];

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = getenv('SMTP_HOST') ?: $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SMTP_USER') ?: $_ENV['SMTP_USER'];
            $mail->Password   = getenv('SMTP_PASS') ?: $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = intval(getenv('SMTP_PORT') ?: $_ENV['SMTP_PORT']);

            $mail->setFrom($mail->Username, 'Veloce Security');
            $mail->addAddress($email, $user['user_name']);

            $mail->isHTML(true);
            $mail->Subject = "Veloce Access Recovery: Reset Your Password";
            $mail->Body    = "
                <html>
                <body style='background-color: #060b13; color: #ffffff; font-family: sans-serif; padding: 2rem;'>
                    <div style='max-width: 400px; margin: 0 auto; background: #0c1524; border: 1px solid #1b2a47; padding: 2rem; border-radius: 12px; border-top: 4px solid #1e6fff;'>
                        <h2 style='color: #1e6fff; text-align: center; margin-bottom: 1.5rem;'>VELOCE</h2>
                        <p style='color: #8da2bb; font-size: 0.95rem; line-height: 1.5;'>Use the secure OTP code below to finalize your driver profile password recovery request:</p>
                        <div style='background: #060b13; font-size: 2.5rem; font-weight: bold; letter-spacing: 6px; text-align: center; padding: 1rem; color: #ffffff; border-radius: 8px; border: 1px solid #1b2a47; margin: 1.5rem 0;'>
                            $otpCode
                        </div>
                        <p style='color: #8da2bb; font-size: 0.8rem; text-align: center;'>This access ticket window expires automatically in 10 minutes.</p>
                    </div>
                </body>
                </html>
            ";

            $mail->send();

            header("Location: " . APP_URL . "/auth/verification/otp-verification");
            exit();

        } catch (Exception $e) {
            error_log("PHPMailer Reset Failure: " . $mail->ErrorInfo);
            unset($_SESSION['temp_reset_data']);
            $_SESSION['auth_error'] = "Unable to securely transmit recovery code. Please try again.";
            header("Location: " . APP_URL . "/auth/forgot-password");
            exit();
        }
    }
    public function resetPasswordController(string $plainPassword)
    {
        if (!isset($_SESSION['authorized_reset_email'])) {
            $_SESSION['auth_error'] = "Unauthorized transaction context. Please try again.";
            header("Location: " . APP_URL . "/auth/login");
            exit();
        }

        $email = $_SESSION['authorized_reset_email'];

        // 1. Encrypt and secure password using strong bcrypt
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

        // 2. Commit modification change directly to your database layer
        // (Assumes updatePassword() exists in your UserModel class)
        $updated = $this->userModel->updateUserPassword($email, $hashedPassword);

        if ($updated) {
            // Clear recovery credentials from active session
            unset($_SESSION['authorized_reset_email']);

            $_SESSION['auth_success'] = "Password successfully modified! Sign in with your new credentials.";
            header("Location: " . APP_URL . "/auth/login");
            exit();
        } else {
            $_SESSION['auth_error'] = "Could not update the database record. Please contact system administrators.";
            header("Location: " . APP_URL . "/auth/verification/reset-password");
            exit();
        }
    }
    public function verifyOtpController(int $submittedOtp)
    {
        // Context Checking
        if (isset($_SESSION['temp_reg_data'])) {
            $tempData = $_SESSION['temp_reg_data'];
            $context = 'register';
        } elseif (isset($_SESSION['temp_reset_data'])) {
            $tempData = $_SESSION['temp_reset_data'];
            $context = 'reset';
        } else {
            $_SESSION['auth_error'] = "Session expired. Please restart the process.";
            header("Location: " . APP_URL . "/auth/login");
            exit();
        }

        if (time() > $tempData['expires_at']) {
            unset($_SESSION['temp_reg_data'], $_SESSION['temp_reset_data']);
            $_SESSION['auth_error'] = "Your security code has expired. Please try again.";
            $redirect = ($context === 'register') ? 'auth/register' : 'auth/forgot-password';
            header("Location: " . APP_URL . "/" . $redirect);
            exit();
        }

        if ($submittedOtp !== $tempData['otp_code']) {
            $_SESSION['auth_error'] = "Incorrect verification token. Please try again.";
            header("Location: " . APP_URL . "/auth/verification/otp-verification");
            exit();
        }

        // --- OTP is CORRECT ---
        if ($context === 'register') {
            // Handle database creation for new user
            $created = $this->userModel->createUser([
                'username' => $tempData['user_name'],
                'email'    => $tempData['user_email'],
                'password' => $tempData['user_pass']
            ]);

            if ($created) {
                unset($_SESSION['temp_reg_data']);
                $_SESSION['auth_success'] = "Verification successful! Welcome to Veloce.";
                header("Location: " . APP_URL . "/auth/login");
                exit();
            }
            $_SESSION['auth_error'] = "Could not finalize your registration. Please try again.";
            header("Location: " . APP_URL . "/auth/register");
            exit();

        } else {
            // For Password Recovery, set an "authorized reset email" flag, 
            // clean up the recovery OTP block, and redirect them to type their new password.
            $_SESSION['authorized_reset_email'] = $tempData['user_email'];
            unset($_SESSION['temp_reset_data']);

            header("Location: " . APP_URL . "/auth/verification/reset-password");
            exit();
        }
    }
    public function logoutController()
    {
        clear_auth_cookie();
        session_start();
        session_unset();
        session_destroy();

        header("Location: " . APP_URL . "/auth/login");
        exit();
    }
}