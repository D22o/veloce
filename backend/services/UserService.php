<?php

namespace Backend\Services;

use Backend\Controllers\UserController;
use Exception;
use Throwable;

class UserService
{
    private UserController $userController;
    public function __construct()
    {
        $this->userController = new UserController();
    }
        public function handleRequest(string $action, array $data)
    {
        switch ($action) {

            case 'user/update-profile':
                $this->validateProfileData($data);
                break;

            case 'user/update-password':
                $this->validateUpdatePassData($data);
                break;

            case 'user/verify-profile':
                $this->validateDocumentSubmittion($data);
                break;

            default:
                $this->failRedirect("invalid+action", "auth/login");
        }
    }
    private function validateProfileData(array $data)
    {
        $userId = $_SESSION['user_id'];
        $firstName = trim($data['first_name'] ?? '');
        $lastName = trim($data['last_name'] ?? '');
        $phoneNumber = $data['phone_number'];

        if (empty($firstName) || empty($lastName)) {
            $_SESSION['profile_error'] = "First name and Last name parameters cannot be empty.";
            $this->failRedirect($_SESSION['profile_error'], "user/profile-verification");
            exit;
        }
        $fullName = $firstName . ' ' . $lastName;
        $data = [
            'fullname' => $fullName,
            'phone_number' => $phoneNumber
        ];
        
        $this->userController->updateUserProfile($userId, $data);

        $_SESSION['user_name'] = $fullName;
        $_SESSION['phone_number'] = $data['phone_number'];
        $_SESSION['profile_success'] = "Profile metrics modified successfully.";
        $this->successRedirect($_SESSION['profile_success'], "user/profile-verification");
    }
    private function validateUpdatePassData(array $data)
    {
        $email = $_SESSION['user_email'];
        $currentPassword = $data['current_password'] ?? '';
        $newPassword = $data['new_password'] ?? '';
        $confirmNewPassword = $data['confirm_new_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmNewPassword)) {
            $_SESSION['profile_error'] = "All password parameters must be assigned validation attributes.";
            $this->failRedirect($_SESSION['profile_error'], "user/profile-verification");
            exit;
        }

        if ($newPassword !== $confirmNewPassword) {
            $_SESSION['profile_error'] = "The structural match of validation check targets failed.";
            $this->failRedirect($_SESSION['profile_error'], "user/profile-verification");
            exit;
        }

        if (strlen($newPassword) < 8) {
            $_SESSION['profile_error'] = "The secure key length must contain at least 8 alphanumeric nodes.";
            $this->failRedirect($_SESSION['profile_error'], "user/profile-verification");
            exit;
        }
        $user = $this->userController->findUserByEmail($email);
        if ($user || password_verify($currentPassword, $user['user_pass']))
        {
            try {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $this->userController->updateUserPassword($email, $hashedPassword);
                 $_SESSION['profile_success'] = "Access Token configuration cycled successfully.";
                $this->successRedirect($_SESSION['profile_success'], "user/profile-verification");
                exit;
            } catch (Throwable $t) {
                $_SESSION['profile_error'] = "Database Transaction Failure: Verification loop disrupted.";
                $this->failRedirect($_SESSION['profile_error'], "user/profile-verification");
                exit;
            }
        } else {
            $_SESSION['profile_error'] = "Authentication state mismatch: Current secret key is invalid.";
            $this->failRedirect($_SESSION['profile_error'], "user/profile-verification");
            exit;
        }
    }
    private function validateDocumentSubmittion(array $data)
    {
        $userId = $_SESSION['user_id'];
        $documentType = trim($data['document_type'] ?? '');
        $documentNumber = trim($data['document_number'] ?? '');
        $fileAsset = $_FILES['verification_document'] ?? null;

        if (empty($documentType) || empty($documentNumber) || !$fileAsset || $fileAsset['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['profile_error'] = "Incomplete submission variables or system asset upload failure.";
            $this->failRedirect($_SESSION['profile_error'], "user/profile-verification");
            exit;
        }

        // Validate size specifications (Max limit: 5 Megabytes)
        if ($fileAsset['size'] > 5 * 1024 * 1024) {
            $_SESSION['profile_error'] = "Asset threshold anomaly: File limit exceeded (Max 5MB).";
            $this->failRedirect($_SESSION['profile_error'], "user/profile-verification");
            exit;
        }

        // MIME type structure isolation checks
        $allowedMimeTypes = ['image/jpeg', 'image/png'];
        $fileMime = mime_content_type($fileAsset['tmp_name']);
        if (!in_array($fileMime, $allowedMimeTypes)) {
            $_SESSION['profile_error'] = "Invalid file payload layout formatting. Only JPG and PNG extensions permitted.";
            $this->failRedirect($_SESSION['profile_error'], "user/profile-verification");
            exit;
        }

        // Determine clean localized target destination paths
        $extension = ($fileMime === 'image/jpeg') ? '.jpg' : '.png';
        $uniqueFileName = 'doc_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . $extension;
        
        // Relative paths to safely traverse internal system trees
        $uploadDirectory = dirname(__DIR__, 2) . '/data/documents/';
        
        // Ensure file tree paths exist structurally before staging assets
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }

        $targetFilePath = $uploadDirectory . $uniqueFileName;
        $datatoInsert = [
            'doc_type' => $documentType, 
            'doc_num' => $documentNumber, 
            'file_path' => $uniqueFileName
        ];

        if (move_uploaded_file($fileAsset['tmp_name'], $targetFilePath)) {
            try {
                // 1. Capture the return value of the operation
                $success = $this->userController->updateUserVerificationRecord($userId, $datatoInsert);
                
                // 2. Explicitly verify it completed successfully
                if ($success) {
                    $_SESSION['verification_status'] = 'pending';
                    $_SESSION['profile_success'] = "Regulatory document asset records queued for automated administrative audit.";
                    $this->successRedirect($_SESSION['profile_success'], "user/profile-verification");
                    exit;
                } else {
                    // If it executed but updated 0 rows (e.g. user_id doesn't match)
                    // Delete physical file to avoid leaving stranded orphaned assets
                    if (file_exists($targetFilePath)) { unlink($targetFilePath); }
                    
                    $_SESSION['profile_error'] = "No database record was modified. Please check if your profile exists.";
                    $this->failRedirect($_SESSION['profile_error'], "user/profile-verification");
                    exit;
                }
            } catch (Exception $e) {
                // Delete physical file if DB insertion failed
                if (file_exists($targetFilePath)) { unlink($targetFilePath); }

                // This catch will now execute correctly if there's an SQL error
                $_SESSION['profile_error'] = "Staging database index recording process failed: " . $e->getMessage();
                $this->failRedirect($_SESSION['profile_error'], "user/profile-verification");
                exit;
            }
        } else {
            $_SESSION['profile_error'] = "Filesystem transport fault during deployment staging.";
            $this->failRedirect($_SESSION['profile_error'], "user/profile-verification");
            exit;
        }
    }
    public function getPendingVerificationRequest()
    {
        return $this->userController->getPendingVerificationRequest();
    }
    private function failRedirect(string $errorMessage, string $redirectPath)
    {
        $_SESSION['profile_error'] = $errorMessage;
        header("Location: " . APP_URL . "/" . $redirectPath);
        exit();
    }
    private function successRedirect(string $successMessage, string $redirectPath)
    {
        $_SESSION['profile_success'] = $successMessage;
        header("Location: " . APP_URL . "/" . $redirectPath);
        exit();
    }
}