<?php

namespace Backend\Controllers;

use Backend\Models\BookingModel;
use Backend\Models\ListingModel;
use Backend\Models\UserModel;
class BookingController
{
    private BookingModel $bookingModel;
    private ListingModel $listingModel;
    private UserModel $userModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->listingModel = new ListingModel();
        $this->userModel = new UserModel();
    }

    public function createBooking(array $data)
    {
        $booking = $this->bookingModel->insertBooking($data);
        return $booking;
    }
    public function getCarPricePerDay(int $carId)
    {
        return $this->listingModel->getFleetPriceById($carId);
    }
    public function getAllBookings()
    {
        return $this->bookingModel->getAllBookings();
    }
    public function getAllBookingsByUserId(int $userId)
    {
        return $this->bookingModel->getAllBookingsByUserId($userId);
    }
    public function getUserDashboardMetrics(int $userId): array
    {
        // 1. Query database stats concurrently via Model
        $activeCount    = $this->bookingModel->getActiveBookingsCount($userId);
        $completedCount = $this->bookingModel->getCompletedBookingsCount($userId);
        $totalSpent     = $this->bookingModel->getTotalSpentAmount($userId);
        $recentOrders   = $this->bookingModel->getRecentUserBookings($userId, 5);
        
        // 2. Pull live verification status directly from DB (prevents stale session bugs)
        $identityStatus = $this->userModel->getUserVerificationStatus($userId) ?? 'unverified';

        // 3. Return consolidated data payload
        return [
            'active_bookings_count' => $activeCount,
            'completed_trips_count' => $completedCount,
            'total_spent_amount'    => $totalSpent,
            'verification_status'   => $identityStatus,
            'recent_orders'         => $recentOrders
        ];
    }
    public function getAdminMetrics(): array
    {
        $revenue = $this->bookingModel->getGrossRevenue();
        $fleet   = $this->bookingModel->getFleetMetrics();

        // Formula: (Rented Cars / Total Cars) * 100
        $utilization = ($fleet['total_cars'] > 0) 
            ? round(($fleet['rented_cars'] / $fleet['total_cars']) * 100) 
            : 0;

        $pendingClaims   = $this->bookingModel->getPendingVerificationsCount();
        $operationalLogs = $this->bookingModel->getGlobalOperationalLog(10);

        return [
            'gross_revenue'     => $revenue,
            'fleet_utilization' => $utilization,
            'total_cars'        => $fleet['total_cars'],
            'rented_cars'       => $fleet['rented_cars'],
            'pending_claims'    => $pendingClaims,
            'operational_log'   => $operationalLogs
        ];
    }
}