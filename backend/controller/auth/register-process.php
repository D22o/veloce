<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pull in system configurations
require_once dirname(__DIR__) . '/../config/config.php';
require_once dirname(__DIR__) . '/../config/database.php';
require_once dirname(__DIR__) . '/../model/UserModel.php';

// Import PHPMailer core libraries into namespace mapping
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once dirname(__DIR__) . '/../config/PHPMailer/src/Exception.php';
require_once dirname(__DIR__) . '/../config/PHPMailer/src/PHPMailer.php';
require_once dirname(__DIR__) . '/../config/PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../register");
    exit();
}

$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if (empty($firstName) || empty($lastName) || !$email || empty($password)) {
    $_SESSION['reg_error'] = "Please fill out all required form fields accurately.";
    header("Location: ../../../register");
    exit();
}

if ($password !== $confirmPassword) {
    $_SESSION['reg_error'] = "Password confirmation mismatch. Fields must match.";
    header("Location: ../../../register");
    exit();
}

$database = new Database();
$db = $database->connect();
$userModel = new UserModel($db);

if ($userModel->findUserByEmail($email)) {
    $_SESSION['reg_error'] = "This email address is already bound to an active driver profile.";
    header("Location: ../../../register");
    exit();
}

$otpCode = random_int(1000, 9999);

$_SESSION['temp_reg_data'] = [
    'user_name'  => $firstName . ' ' . $lastName,
    'user_email' => $email,
    'user_pass'  => $password,
    'otp_code'   => $otpCode,
    'expires_at' => time() + 600
];

// --- INITIALIZE PHPMAILER ROUTING INSTANCE ---
$mail = new PHPMailer(true);

try {
    // Server validation variables
    $mail->isSMTP();
    $mail->Host       = getenv('SMTP_HOST');
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USER');
    $mail->Password   = getenv('SMTP_PASS');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = intval(getenv('SMTP_PORT'));

    // Identity mappings
    $mail->setFrom(getenv('SMTP_USER'), 'Veloce Security');
    $mail->addAddress($email, $firstName . ' ' . $lastName);

    // Document Composition Content
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
    
    // Smooth transition handover to verification route page
    header("Location: ../../../email-verification");
    exit();

} catch (Exception $e) {
    // If things fail, let's catch the exact technical reason for logging purposes
    error_log("PHPMailer Engine Failure: " . $mail->ErrorInfo);
    $_SESSION['reg_error'] = "Mailer Network Issue: Unable to securely transmit validation token.";
    header("Location: ../../../register");
    exit();
}