<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/../config/database.php';
require_once dirname(__DIR__) . '/../model/UserModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../email-verification");
    exit();
}

// Combine the 4 split OTP input boxes into a single string
$submittedOtp = implode('', $_POST['otp'] ?? []);

if (!isset($_SESSION['temp_reg_data'])) {
    $_SESSION['otp_error'] = "Session expired. Please restart the registration process.";
    header("Location: ../../../register");
    exit();
}

$tempData = $_SESSION['temp_reg_data'];

if (time() > $tempData['expires_at']) {
    unset($_SESSION['temp_reg_data']);
    $_SESSION['reg_error'] = "The verification code has expired. Please try again.";
    header("Location: ../../../register");
    exit();
}

if (intval($submittedOtp) === intval($tempData['otp_code'])) {
    $database = new Database();
    $db = $database->connect();
    $userModel = new UserModel($db);

    // Commit user record permanently to database
    $success = $userModel->registerUser($tempData['user_name'], $tempData['user_email'], $tempData['user_pass']);

    if ($success) {
        // Clear out temporary data cache
        unset($_SESSION['temp_reg_data']);
        $_SESSION['auth_success'] = "Account provisioned successfully! You can now log in.";
        header("Location: ../../../login");
    } else {
        $_SESSION['otp_error'] = "System failure processing account creation. Contact support.";
        header("Location: ../../../email-verification");
    }
} else {
    $_SESSION['otp_error'] = "Invalid verification code. Access verification denied.";
    header("Location: ../../../email-verification");
}
exit();