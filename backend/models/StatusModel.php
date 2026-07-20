<?php

namespace Backend\Models;

use Backend\Config\Database;
use Exception;
use PDO;
use PDOException;

class StatusModel
{
    private PDO $conn;
    private string $bookingsTable = 'bookings';
    private string $carsTable = 'cars';

    public function __construct()
    {
        // Assumes database connect returns a valid PDO instance
        $this->conn = Database::connect();
    }

    /**
     * Retrieves specific booking information safely
     */
    public function getBookingById(int $bookingId): ?array
    {
        $query = "SELECT car_id, booking_status FROM " . $this->bookingsTable . " WHERE booking_id = :id LIMIT 1";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $bookingId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Failed to fetch booking metadata: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Approves a booking: Marks booking as 'Approved' and 'Paid', flips vehicle state to 'Rented'
     */
    public function approveBooking(int $bookingId, int $carId): bool
    {
        try {
            $this->conn->beginTransaction();

            $stmt1 = $this->conn->prepare("UPDATE " . $this->bookingsTable . " SET booking_status = 'Approved', payment_status = 'Paid' WHERE booking_id = :id");
            $stmt1->execute([':id' => $bookingId]);

            $stmt2 = $this->conn->prepare("UPDATE " . $this->carsTable . " SET status = 'Rented' WHERE car_id = :car_id");
            $stmt2->execute([':car_id' => $carId]);

            return $this->conn->commit();
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Failed to approve booking context: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Declines a booking: Marks booking as 'Cancelled'
     */
    public function declineBooking(int $bookingId): bool
    {
        $query = "UPDATE " . $this->bookingsTable . " SET booking_status = 'Cancelled' WHERE booking_id = :id";
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([':id' => $bookingId]);
        } catch (PDOException $e) {
            error_log("Failed to decline booking transaction: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Completes a booking: Marks booking as 'Completed', releases vehicle back to 'Available' pool
     */
    public function completeBooking(int $bookingId, int $carId): bool
    {
        try {
            $this->conn->beginTransaction();

            $stmt1 = $this->conn->prepare("UPDATE " . $this->bookingsTable . " SET booking_status = 'Completed' WHERE booking_id = :id");
            $stmt1->execute([':id' => $bookingId]);

            $stmt2 = $this->conn->prepare("UPDATE " . $this->carsTable . " SET status = 'Available' WHERE car_id = :car_id");
            $stmt2->execute([':car_id' => $carId]);

            return $this->conn->commit();
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Failed to complete booking context: " . $e->getMessage());
            return false;
        }
    }
}