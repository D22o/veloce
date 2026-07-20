<?php

namespace Backend\Models;

use Backend\Config\Database;
use PDO;
use PDOException;

class BookingModel
{
    private PDO $conn;
    private string $table = 'bookings';
    private string $listingTable = 'cars';
    private string $usersTable = 'users';

    public function __construct()
    {
        $this->conn = Database::connect();
    }

    public function insertBooking(array $data): ?int
    {
        $query = "INSERT INTO " . $this->table . " (user_id, car_id, pickup_date, return_date, total_days, total_price, booking_status, payment_status) 
                       VALUES (:user_id, :car_id, :pickup_date, :return_date, :total_days, :total_price, 'Pending', 'Unpaid')";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':car_id', $data['car_id'], PDO::PARAM_INT);
            $stmt->bindParam(':pickup_date', $data['pickup_date'], PDO::PARAM_STR);
            $stmt->bindParam(':return_date', $data['return_date'], PDO::PARAM_STR);
            $stmt->bindParam(':total_days', $data['total_days'], PDO::PARAM_STR);
            $stmt->bindParam(':total_price', $data['total_price'], PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Booking insertion failed: " . $e->getMessage());
            return false;
        }
    }
    public function getAllBookings()
    {
        $query = "SELECT b.*, c.brand, c.model, c.plate_number, u.user_name, u.user_email 
          FROM " . $this->table . " b
          INNER JOIN " . $this->listingTable . " c ON b.car_id = c.car_id
          INNER JOIN " . $this->usersTable . " u ON b.user_id = u.user_id
          ORDER BY b.booking_id DESC";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Booking Retrieval failed: " . $e->getMessage());
            return [];
        }
    }
    public function getAllBookingsByUserId(int $userId)
    {
        $query = "SELECT b.*, c.brand, c.model, c.plate_number, c.type 
          FROM bookings b 
          INNER JOIN cars c ON b.car_id = c.car_id 
          WHERE b.user_id = :user_id 
          ORDER BY b.booking_id DESC";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Booking Retrieval failed: " . $e->getMessage());
            return [];
        }
    }
    public function getActiveBookingsCount(int $userId): int
    {
        $query = "SELECT COUNT(*) FROM " . $this->table . " 
                  WHERE user_id = :user_id 
                  AND booking_status IN ('Active', 'Approved')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }
    public function getCompletedBookingsCount(int $userId): int
    {
        $query = "SELECT COUNT(*) FROM " . $this->table . " 
                  WHERE user_id = :user_id AND booking_status = 'Completed'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }
    public function getTotalSpentAmount(int $userId): float
    {
        $query = "SELECT SUM(total_price) FROM " . $this->table . " 
                  WHERE user_id = :user_id 
                  AND booking_status = 'Completed'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $total = $stmt->fetchColumn();
        return $total ? (float) $total : 0.00;
    }
    public function getRecentUserBookings(int $userId, int $limit = 5): array
    {
        $query = "SELECT 
                    b.booking_id,
                    b.pickup_date,
                    b.return_date,
                    b.total_days,
                    b.total_price,
                    b.booking_status,
                    b.payment_status,
                    CONCAT(c.brand, ' ', c.model) AS car_name,
                    c.plate_number
                  FROM " . $this->table . " b
                  INNER JOIN cars c ON b.car_id = c.car_id
                  WHERE b.user_id = :user_id
                  ORDER BY b.created_at DESC
                  LIMIT :limit_val";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit_val', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
    /**
     * Total system revenue from paid / non-cancelled bookings
     */
    public function getGrossRevenue(): float
    {
        $query = "SELECT SUM(total_price) FROM " . $this->table . " 
                  WHERE booking_status IN ('Completed', 'Active', 'Approved')
                  OR payment_status = 'Paid'";
        
        $stmt = $this->conn->query($query);
        $total = $stmt->fetchColumn();

        return $total ? (float) $total : 0.00;
    }

    /**
     * Fleet utilization calculated using the `cars` table status
     */
    public function getFleetMetrics(): array
    {
        // Total Cars
        $stmtTotal = $this->conn->query("SELECT COUNT(*) FROM " . $this->listingTable );
        $totalCars = (int) $stmtTotal->fetchColumn();

        // Rented Cars
        $stmtRented = $this->conn->query("SELECT COUNT(*) FROM " . $this->listingTable . " WHERE status = 'Rented'");
        $rentedCars = (int) $stmtRented->fetchColumn();

        return [
            'total_cars'  => $totalCars,
            'rented_cars' => $rentedCars
        ];
    }

    /**
     * Count of pending document verification submissions in `user_verifications`
     */
    public function getPendingVerificationsCount(): int
    {
        $query = "SELECT COUNT(*) FROM user_verifications WHERE status = 'pending'";
        $stmt  = $this->conn->query($query);
        
        return (int) $stmt->fetchColumn();
    }

    /**
     * Global Operational Log joining `bookings`, `users`, and `cars`
     */
    public function getGlobalOperationalLog(int $limit = 10): array
    {
        $query = "SELECT 
                    b.booking_id,
                    u.user_name AS client_name,
                    u.user_email AS client_email,
                    CONCAT(c.brand, ' ', c.model) AS vehicle_name,
                    c.plate_number,
                    b.pickup_date,
                    b.return_date,
                    b.total_days,
                    b.booking_status,
                    b.payment_status
                  FROM bookings b
                  INNER JOIN " . $this->usersTable . " u ON b.user_id = u.user_id
                  INNER JOIN " . $this->listingTable . " c ON b.car_id = c.car_id
                  ORDER BY b.created_at DESC
                  LIMIT :limit_val";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit_val', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}