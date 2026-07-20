<?php

namespace Backend\Services;

use Backend\Controllers\BookingController;
use DateTime;
use Exception;

class BookingService
{
    private BookingController $bookingController;

    public function __construct()
    {
        $this->bookingController = new BookingController();
    }

    public function handleRequest(string $action, array $data): void
    {
        switch ($action) {
            case 'booking/create-booking':
                $this->cleanBookingData($data);
                break;

            default:
                throw new Exception("Action not recognized: " . $action);
        }
    }
    private function cleanBookingData(array $data): void
    {
        // Guard: Enforce session state authentication
        if (!isset($_SESSION['user_id'])) {
            $this->abort("Unauthorized access. Please log in first.", "user/fleet-listings");
        }

        // Sanitize and validate inputs
        $cleanedData = [
            'user_id'     => (int)$_SESSION['user_id'], // Retrieve securely from Session, never rely on $_POST
            'car_id'      => filter_var($data['car_id'] ?? null, FILTER_VALIDATE_INT),
            'pickup_date' => isset($data['pickup_date']) ? trim($data['pickup_date']) : null,
            'return_date' => isset($data['return_date']) ? trim($data['return_date']) : null,
        ];

        // 1. Structural Checks
        if (!$cleanedData['car_id'] || $cleanedData['car_id'] <= 0) {
            $this->abort("Invalid car selection. Please choose a valid vehicle.", "user/fleet-listings");
        }

        if (empty($cleanedData['pickup_date']) || empty($cleanedData['return_date'])) {
            $this->abort("Both Pick-up and Return dates are required.", "user/fleet-listings");
        }

        // 2. Date Format Validation (Object-Oriented native replacement)
        if (!isValidDateFormat($cleanedData['pickup_date']) || !isValidDateFormat($cleanedData['return_date'])) {
            $this->abort("Invalid date formatting. Please use the system calendar.", "user/fleet-listings");
        }

        $today      = new DateTime('today');
        $pickupDate = new DateTime($cleanedData['pickup_date']);
        $returnDate = new DateTime($cleanedData['return_date']);

        // 3. Logical Constraints
        if ($pickupDate < $today) {
            $this->abort("Your pick-up date cannot be in the past.", "user/fleet-listings");
        }

        if ($returnDate < $pickupDate) {
            $this->abort("Your return date cannot be scheduled before your pick-up date.", "user/fleet-listings");
        }

        // 4. Booking Limits and Calculation
        $interval  = $pickupDate->diff($returnDate);
        $totalDays = $interval->days === 0 ? 1 : $interval->days;

        if ($totalDays > 30) {
            $this->abort("Asset allocations cannot exceed a maximum limit of 30 days.", "user/fleet-listings");
        }

        // Try getting the vehicle rate safely
        $carInfo    = $this->bookingController->getCarPricePerDay($cleanedData['car_id']);
        $dailyPrice = floatval($carInfo['price_per_day'] ?? 0);

        if ($dailyPrice <= 0) {
            $this->abort("Could not retrieve active vehicle pricing. Try again.", "user/fleet-listings");
        }

        // --- MAP THE VALUES EXPECTED BY YOUR MODEL ---
        $cleanedData['total_days']  = $totalDays;
        $cleanedData['total_price'] = $dailyPrice * $totalDays;

        // 5. Execution Delegation
        $isOkay = $this->bookingController->createBooking($cleanedData);

        if ($isOkay) {
            $_SESSION['booking_success'] = "Booking Success!";
            $this->redirect("user/fleet-listings");
        } else {
            $this->abort("Booking Failed, Something Went Wrong", "user/fleet-listings");
        }
    }
    public function getAllBookings()
    {
        return $this->bookingController->getAllBookings();
    }
    public function getAllBookingsByUserId(int $userId)
    {
        return $this->bookingController->getAllBookingsByUserId($userId);
    }
    public function getDashboardData(): array
    {
        // Ensure user is authenticated
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            $this->redirect("auth/login");
            exit();
        }

        // Delegate business data aggregation to the Service layer
        return $this->bookingController->getUserDashboardMetrics($userId);
    }
    public function getAdminDashboard()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            $this->redirect("auth/login");
        }
        return $this->bookingController->getAdminMetrics();
    }
    private function abort(string $errorMessage, string $redirectPath): void
    {
        $_SESSION['booking_error'] = $errorMessage;
        $this->redirect($redirectPath);
    }
    private function redirect(string $redirectPath): void
    {
        header("Location: " . APP_URL . "/" . $redirectPath);
        exit();
    }

}