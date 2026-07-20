<?php

namespace Backend\Services;

use Backend\Controllers\AuthController;

class AuthService
{
    private $authController;

    public function __construct()
    {
        $this->authController = new AuthController();
    }

    public function handleRequest(string $action, array $rawData)
    {
        switch ($action) {
            case 'auth/login':
                $this->validateAndForwardLogin($rawData);
                break;

            case 'auth/register':
                $this->validateAndForwardRegister($rawData);
                break;

            case 'auth/forgot-password':
                $this->validateAndForwardForgotPassword($rawData);
                break;
            
            case 'auth/reset-password':
                $this->validateAndForwardResetPassword($rawData);
                break;

            case 'auth/verify-otp':
                $this->validateAndForwardOtp($rawData);
                break;
                
            case 'auth/logout':
                $this->authController->logoutController();
                break;

            default:
                $this->failRedirect("invalid+action", "auth/login");
        }
    }

    /**
     * Clean & Validate Login inputs
     */
    private function validateAndForwardLogin(array $data)
    {
        $email = isset($data['email']) ? filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL) : null;
        $password = isset($data['password']) ? trim($data['password']) : '';

        if (!$email) {
            $this->failRedirect("Please enter a valid email address.", "auth/login");
        }

        if (empty($password)) {
            $this->failRedirect("Password field cannot be empty.", "auth/login");
        }

        // Pass pristine, clean data straight to controller
        $this->authController->loginController($email, $password);
    }

    /**
     * Clean & Validate Registration inputs
     */
    private function validateAndForwardRegister(array $data)
    {
        $fname = isset($data['first_name']) ? htmlspecialchars(trim($data['first_name']), ENT_QUOTES, 'UTF-8') : '';
        $lname = isset($data['last_name']) ? htmlspecialchars(trim($data['last_name']), ENT_QUOTES, 'UTF-8') : '';
        $username = $fname . ' ' . $lname;
        $email = isset($data['email']) ? filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL) : null;
        $password = isset($data['password']) ? $data['password'] : '';
        $confirmPassword = isset($data['confirm_password']) ? $data['confirm_password'] : '';

        if (strlen($username) < 3) {
            $this->failRedirect("Username must be at least 3 characters long.", "auth/register");
        }

        if (!$email) {
            $this->failRedirect("Please provide a valid email address.", "auth/register");
        }

        if (strlen($password) < 8) {
            $this->failRedirect("Password must be at least 8 characters long.", "auth/register");
        }

        if ($password !== $confirmPassword) {
            $this->failRedirect("Passwords do not match.", "auth/register");
        }

        $cleanData = [
            'username' => $username,
            'email' => $email,
            'password' => $password
        ];

        // Pass cleaned data payload to controller
        $this->authController->registerController($cleanData);
    }
    private function validateAndForwardForgotPassword(array $data)
    {
        $email = isset($data['email']) ? filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL) : null;

        if (!$email) {
            $this->failRedirect("Please enter a valid email address.", "auth/forgot-password");
        }

        $this->authController->forgotPasswordController($email);
    }
    private function validateAndForwardResetPassword(array $data)
    {
        if (!isset($_SESSION['authorized_reset_email'])) {
            $this->failRedirect("Unauthorized session. Please request a new recovery OTP link.", "auth/login");
        }

        $password = $data['new_password'] ?? '';
        $confirmPassword = $data['confirm_password'] ?? '';

        if (empty($password) || strlen($password) < 8) {
            $this->failRedirect("Your password must contain at least 8 characters.", "auth/reset-password");
        }

        if ($password !== $confirmPassword) {
            $this->failRedirect("Your passwords do not match.", "auth/reset-password");
        }

        $this->authController->resetPasswordController($password);
    }
    private function validateAndForwardOtp(array $data)
    {
        // If otp is passed as an array (name="otp[]"), join the elements together
        $otpArray = $data['otp'] ?? null;
        $otpCombined = is_array($otpArray) ? implode('', $otpArray) : trim($otpArray ?? '');

        if (empty($otpCombined) || strlen($otpCombined) !== 4 || !is_numeric($otpCombined)) {
            $this->failRedirect("Please enter a valid 4-digit OTP security code.", "auth/verification/otp-verification");
        }

        // Forward the assembled integer to your Controller
        $this->authController->verifyOtpController(intval($otpCombined));
    }

    /**
     * Secure helper to cleanly break flow and pass error states back to UI
     */
    private function failRedirect(string $errorMessage, string $redirectPath)
    {
        $_SESSION['auth_error'] = $errorMessage;
        header("Location: " . APP_URL . "/" . $redirectPath);
        exit();
    }
}