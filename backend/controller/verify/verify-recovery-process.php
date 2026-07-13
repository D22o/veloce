<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['recovery_context'])) {
    header("Location: ../../../forgot-password");
    exit();
}

$submittedOtp = implode('', $_POST['otp'] ?? []);
$context = $_SESSION['recovery_context'];

if (time() > $context['expires_at']) {
    unset($_SESSION['recovery_context']);
    $_SESSION['reset_error'] = "The security code recovery window expired. Restart validation.";
    header("Location: ../../../forgot-password");
    exit();
}

if (intval($submittedOtp) === intval($context['otp_code'])) {
    $_SESSION['recovery_context']['verified'] = true; // Unlock clearance flag
    header("Location: ../../../reset-password");
} else {
    $_SESSION['otp_error'] = "Verification mismatch. Token override denied.";
    header("Location: ../../../verify-password-otp");
}
exit();