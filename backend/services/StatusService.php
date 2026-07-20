<?php

namespace Backend\Services;

use Backend\Controllers\StatusController;
use Exception;

class StatusService
{
    private StatusController $statusController;

    public function __construct()
    {
        // Session starting guard
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->statusController = new StatusController();
    }

    public function handleRequest(string $action, array $data): void
    {
        // Enforce route authorization security guard
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            $this->redirect("auth/login");
        }

        switch ($action) {
            case 'status/approve-status':
                $this->validateApproveAction($data);
                break;

            case 'status/decline-status':
                $this->validateDeclineAction($data);
                break;

            case 'status/complete-status':
                $this->validateCompleteAction($data);
                break;

            case 'status/approve-user':
                $this->validateAdminApprovedUser($data);
                break;

            case 'status/reject-user':
                $this->validateAdminRejectUser($data);
                break;

            default:
                throw new Exception("Action not recognized: " . $action);
        }
    }

    private function validateApproveAction(array $data): void
    {
        $bookingId = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);
        
        if (!$bookingId) {
            $_SESSION['booking_admin_error'] = "Invalid or missing tracking identifier.";
            $this->redirect("admin/manage-bookings");
        }

        if ($this->statusController->approveAction($bookingId)) {
            $_SESSION['booking_admin_success'] = "Allocation pipeline updated: Approved and dispatched.";
        } else {
            $_SESSION['booking_admin_error'] = "Status change failed: Target transaction missing or rejected.";
        }
        $this->redirect("admin/manage-bookings");
    }

    private function validateDeclineAction(array $data): void
    {
        $bookingId = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);
        
        if (!$bookingId) {
            $_SESSION['booking_admin_error'] = "Invalid or missing tracking identifier.";
            $this->redirect("admin/manage-bookings");
        }

        if ($this->statusController->declineAction($bookingId)) {
            $_SESSION['booking_admin_success'] = "Reservation allocation declined.";
        } else {
            $_SESSION['booking_admin_error'] = "Status change failed: Target transaction missing or rejected.";
        }
        $this->redirect("admin/manage-bookings");
    }

    private function validateCompleteAction(array $data): void
    {
        $bookingId = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);
        
        if (!$bookingId) {
            $_SESSION['booking_admin_error'] = "Invalid or missing tracking identifier.";
            $this->redirect("admin/manage-bookings");
        }

        if ($this->statusController->completeAction($bookingId)) {
            $_SESSION['booking_admin_success'] = "Asset safely returned to base tracking matrix.";
        } else {
            $_SESSION['booking_admin_error'] = "Status change failed: Target transaction missing or rejected.";
        }
        $this->redirect("admin/manage-bookings");
    }
    public function validateAdminApprovedUser(array $data)
    {
        $verificationId = intval($data['verification_id'] ?? 0);
        $actionDecision = $data['action_decision'] ?? '';

        // Validate inputs
        if ($verificationId <= 0 || !in_array($actionDecision, ['approved', 'rejected'])) {
            $_SESSION['audit_error'] = "Invalid operational parameters received.";
            $this->redirect("admin/verify-users");
            exit;
        }

        $isSuccess = $this->statusController->approveUserVerification($verificationId, $actionDecision);
        if ($isSuccess) {
            $_SESSION['verification_status'] = $actionDecision;
            $_SESSION['audit_success'] = "Driver credential request was successfully status-shifted to: " . strtoupper($actionDecision);
            $this->redirect("admin/verify-users");
            exit;
        } else {
            $_SESSION['audit_error'] = "Target request tracking node was not found.";
            $this->redirect("admin/verify-users");
            exit;
        }
    }
    public function validateAdminRejectUser(array $data)
    {
        $verificationId = intval($data['verification_id'] ?? 0);
        $actionDecision = $data['action_decision'] ?? '';

        // Validate inputs
        if ($verificationId <= 0 || !in_array($actionDecision, ['approved', 'rejected'])) {
            $_SESSION['audit_error'] = "Invalid operational parameters received.";
            $this->redirect("admin/verify-users");
            exit;
        }

        $isSuccess = $this->statusController->rejectUserVerification($verificationId, $actionDecision);
        if ($isSuccess) {
            $_SESSION['verification_status'] = $actionDecision;
            $_SESSION['audit_success'] = "Driver credential request was successfully status-shifted to: " . strtoupper($actionDecision);
            $this->redirect("admin/verify-users");
            exit;
        } else {
            $_SESSION['audit_error'] = "Target request tracking node was not found.";
            $this->redirect("admin/verify-users");
            exit;
        }
    }
    private function redirect(string $redirectPath): void
    {
        header("Location: " . APP_URL . "/" . $redirectPath);
        exit();
    }
}