<?php

namespace Backend\Controllers;

use Backend\Models\StatusModel;
use Backend\Models\UserModel;
use Exception;

class StatusController
{
    private StatusModel $statusModel;
    private UserModel $userModel;

    public function __construct()
    {
        $this->statusModel = new StatusModel();
        $this->userModel = new UserModel();
    }

    public function approveAction(int $bookingId): bool
    {
        $booking = $this->statusModel->getBookingById($bookingId);
        if (!$booking) {
            return false;
        }
        return $this->statusModel->approveBooking($bookingId, (int)$booking['car_id']);
    }

    public function declineAction(int $bookingId): bool
    {
        $booking = $this->statusModel->getBookingById($bookingId);
        if (!$booking) {
            return false;
        }
        return $this->statusModel->declineBooking($bookingId);
    }

    public function completeAction(int $bookingId): bool
    {
        $booking = $this->statusModel->getBookingById($bookingId);
        if (!$booking) {
            return false;
        }
        return $this->statusModel->completeBooking($bookingId, (int)$booking['car_id']);
    }
    public function approveUserVerification(int $userId, string $status)
    {
        return $this->userModel->updateUserVerificationStatus($userId, $status);
    }
    public function rejectUserVerification(int $userId, string $status)
    {
        return $this->userModel->updateUserVerificationStatus($userId, $status);
    }
}