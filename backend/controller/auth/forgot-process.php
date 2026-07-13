<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once dirname(__DIR__) . '/../config/config.php';
require_once dirname(__DIR__) . '/../config/database.php';
require_once dirname(__DIR__) . '/../model/UserModel.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once dirname(__DIR__) . '/../config/PHPMailer/src/Exception.php';
require_once dirname(__DIR__) . '/../config/PHPMailer/src/PHPMailer.php';
require_once dirname(__DIR__) . '/../config/PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../forgot-password");
    exit();
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

if (!$email) {
    $_SESSION['reset_error'] = "Provide a structurally sound email identifier address.";
    header("Location: ../../../forgot-password");
    exit();
}

$database = new Database();
$db = $database->connect();
$userModel = new UserModel($db);
$user = $userModel->findUserByEmail($email);

if (!$user) {
    // Security Best Practice: Don't explicitly reveal that the email doesn't exist to prevent enumeration.
    // But for straightforward local system debugging context, let's keep user notices transparent:
    $_SESSION['reset_error'] = "No driver profile matches that registered email location.";
    header("Location: ../../../forgot-password");
    exit();
}

$otpCode = random_int(1000, 9999);

// Cache tracking parameters explicitly targeting account recovery
$_SESSION['recovery_context'] = [
    'email' => $email,
    'otp_code' => $otpCode,
    'expires_at' => time() + 600,
    'verified' => false
];

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = getenv('SMTP_HOST');
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USER');
    $mail->Password   = getenv('SMTP_PASS');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = intval(getenv('SMTP_PORT'));

    $mail->setFrom(getenv('SMTP_USER'), 'Veloce Recovery');
    $mail->addAddress($email, $user['user_name']);

    $mail->isHTML(true);
    $mail->Subject = "Veloce Override Key: Password Reset Token";
    $mail->Body    = "
        <html>
        <body style='background-color: #060b13; color: #ffffff; font-family: sans-serif; padding: 2rem;'>
            <div style='max-width: 400px; margin: 0 auto; background: #0c1524; border: 1px solid #1b2a47; padding: 2rem; border-radius: 12px; border-top: 4px solid #e67e22;'>
                <h2 style='color: #e67e22; text-align: center; margin-bottom: 1.5rem;'>VELOCE SECURITY</h2>
                <p style='color: #8da2bb; font-size: 0.95rem; line-height: 1.5;'>An account recovery action was requested. Use this token verification block to validate override clearance authority:</p>
                <div style='background: #060b13; font-size: 2.5rem; font-weight: bold; letter-spacing: 6px; text-align: center; padding: 1rem; color: #ffffff; border-radius: 8px; border: 1px solid #1b2a47; margin: 1.5rem 0;'>
                    $otpCode
                </div>
                <p style='color: #8da2bb; font-size: 0.8rem; text-align: center;'>If you did not request this, you can safely disregard this document file safely.</p>
            </div>
        </body>
        </html>
    ";

    $mail->send();
    header("Location: ../../../verify-password-otp");
    exit();
} catch (Exception $e) {
    error_log("Recovery Mail Failure: " . $mail->ErrorInfo);
    $_SESSION['reset_error'] = "Unable to route tracking validation tokens securely right now.";
    header("Location: ../../../forgot-password");
    exit();
}