<?php
/**
 * Backend Authorization Engine
 */

// Initialize secure session layers
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/../config/database.php';
require_once dirname(__DIR__) . '/../model/UserModel.php';

// Enforce clean POST submission pipelines
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../login");
    exit();
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

// Basic state validation check
if (!$email || empty($password)) {
    $_SESSION['auth_error'] = "Malformed input parameters. Please check your entries.";
    header("Location: ../../../login");
    exit();
}

// Instantiate structural data instances
$database = new Database();
$db = $database->connect();
$userModel = new UserModel($db);

// Audit record against system constraints
$user = $userModel->findUserByEmail($email);

if ($user && password_verify($password, $user['user_pass'])) {
    
    // Regenerate session id to neutralize session fixation vulnerabilities
    session_regenerate_id(true);

    // Populate operational memory attributes
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_name'] = $user['user_name'];
    $_SESSION['user_role'] = $user['user_role']; // 'user' or 'admin'

    // --- SECURE AUTHENTICATION TOKEN IN COOKIE ---
    // Generate a random cryptographically strong state verification token
    $authToken = bin2hex(random_bytes(32));
    
    // Store token fingerprint locally inside the active session for internal cross-checking
    $_SESSION['auth_token_fingerprint'] = hash('sha256', $authToken);

    // Calculate expiration horizon (Extend if "Remember device" was checked)
    $cookieDuration = isset($_POST['remember_me']) ? (86400 * 30) : 0; // 30 Days vs Session-end
    $expiryTime = $cookieDuration > 0 ? (time() + $cookieDuration) : 0;

    /**
     * Bake the cookie with strict browser parameters:
     * - HttpOnly: Blocks Cross-Site Scripting (XSS) from reading the token via JavaScript
     * - Secure: Restricts token transmission strictly to HTTPS environments
     * - SameSite=Strict: Suppresses Cross-Site Request Forgery (CSRF) vectors entirely
     */
    setcookie(
        'token',
        $authToken,
        [
            'expires' => $expiryTime,
            'path' => '/',
            'domain' => '', // Automatically respects current domain execution limits
            'secure' => true, // Enforced on server deployments
            'httponly' => true,
            'samesite' => 'Strict'
        ]
    );

    // --- ASSIGN DOCK CHANNELS BASED ON ROLES ---
    if ($user['user_role'] === 'admin') {
        header("Location: ../../../admin/admin-dashboard");
    } else {
        header("Location: ../../../user/user-dashboard");
    }
    exit();

} else {
    // Flag failure validation context to local interface frame
    $_SESSION['auth_error'] = "Invalid verification email or matching security password.";
    header("Location: ../../../login");
    exit();
}