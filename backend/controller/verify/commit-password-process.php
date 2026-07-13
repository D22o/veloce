<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once dirname(__DIR__) . '/../config/database.php';
require_once dirname(__DIR__) . '/../model/UserModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../reset-password");
    exit();
}

if (!isset($_SESSION['recovery_context']) || $_SESSION['recovery_context']['verified'] !== true) {
    header("Location: ../../../forgot-password");
    exit();
}

$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if (empty($password) || strlen($password) < 8) {
    $_SESSION['config_error'] = "Passwords must be at least 8 characters long.";
    header("Location: ../../../reset-password");
    exit();
}

if ($password !== $confirmPassword) {
    $_SESSION['config_error'] = "Password configuration mismatch.";
    header("Location: ../../../reset-password");
    exit();
}

$database = new Database();
$db = $database->connect();
$userModel = new UserModel($db);

$email = $_SESSION['recovery_context']['email'];
$success = $userModel->updatePasswordByEmail($email, $password);

if ($success) {
    unset($_SESSION['recovery_context']); // Remove data footprint completely
    $_SESSION['auth_success'] = "Password modified successfully! Access updated.";
    header("Location: ../../../login");
} else {
    $_SESSION['config_error'] = "System failure adjusting authentication records.";
    header("Location: ../../../reset-password");
}
exit();