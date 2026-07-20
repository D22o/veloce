<?php

namespace Backend\Models;

use Backend\Config\Database;
use PDO;
use PDOException;

class ListingModel 
{
    private PDO $conn;
    private string $table = 'cars';

    public function __construct() {
        $this->conn = Database::connect();
    }
    public function createListing(array $data): bool
    {
        $query = "INSERT INTO " . $this->table . " 
                  (brand, model, type, transmission, fuel_type, seating_capacity, price_per_day, plate_number, car_image, status) 
                  VALUES 
                  (:brand, :model, :type, :transmission, :fuel_type, :seating_capacity, :price_per_day, :plate_number, :car_image, :status)";

        try {
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(':brand', $data['brand'], PDO::PARAM_STR);
            $stmt->bindValue(':model', $data['model'], PDO::PARAM_STR);
            $stmt->bindValue(':type', $data['type'], PDO::PARAM_STR);
            $stmt->bindValue(':transmission', $data['transmission'], PDO::PARAM_STR);
            $stmt->bindValue(':fuel_type', $data['fuel_type'], PDO::PARAM_STR);
            $stmt->bindValue(':seating_capacity', $data['seating_capacity'], PDO::PARAM_INT);
            $stmt->bindValue(':price_per_day', $data['price_per_day'], PDO::PARAM_STR); // Treated as string/decimal
            $stmt->bindValue(':plate_number', $data['plate_number'], PDO::PARAM_STR);
            $stmt->bindValue(':car_image', $data['car_image'], PDO::PARAM_STR);
            $stmt->bindValue(':status', $data['status'], PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Failed to create vehicle listing: " . $e->getMessage());
            return false;
        }
    }
    public function updateListing(int $id, array $data): bool 
    {
        $query = "UPDATE " . $this->table . " SET 
                    brand = :brand, 
                    model = :model, 
                    type = :type, 
                    transmission = :transmission, 
                    fuel_type = :fuel_type, 
                    seating_capacity = :seating_capacity, 
                    price_per_day = :price_per_day, 
                    plate_number = :plate_number, 
                    car_image = :car_image, 
                    status = :status 
                  WHERE car_id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':brand', $data['brand'], PDO::PARAM_STR);
            $stmt->bindValue(':model', $data['model'], PDO::PARAM_STR);
            $stmt->bindValue(':type', $data['type'], PDO::PARAM_STR);
            $stmt->bindValue(':transmission', $data['transmission'], PDO::PARAM_STR);
            $stmt->bindValue(':fuel_type', $data['fuel_type'], PDO::PARAM_STR);
            $stmt->bindValue(':seating_capacity', $data['seating_capacity'], PDO::PARAM_INT);
            $stmt->bindValue(':price_per_day', $data['price_per_day'], PDO::PARAM_STR);
            $stmt->bindValue(':plate_number', $data['plate_number'], PDO::PARAM_STR);
            $stmt->bindValue(':car_image', $data['car_image'], PDO::PARAM_STR);
            $stmt->bindValue(':status', $data['status'], PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Failed to update vehicle listing (ID $id): " . $e->getMessage());
            return false;
        }
    }
    public function deleteListing(int $id): bool 
    {
        $query = "DELETE FROM " . $this->table . " WHERE car_id = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }
    public function getFleetPriceById(int $price)
    {
        $query = "SELECT price_per_day FROM " . $this->table . " WHERE car_id = :car_id AND status = 'Available'";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':car_id', $price, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Database Look Up Exception: " . $e->getMessage());
            return false;
        }
    }
    public function getTotalCount()
    {
        $query = "SELECT COUNT(*) FROM " . $this->table;
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Database Look Up Exception: " . $e->getMessage());
            return false;
        }
    }
    public function readPaginated(int $limit, int $offset) {
        $query = "SELECT * FROM " . $this->table . " ORDER BY car_id DESC LIMIT :limit OFFSET :offset";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Look Up Exception: " . $e->getMessage());
            return false;
        }
    }
    public function searchAvailableFleet( string $searchTerm, string $typeFilter) 
    {
        $query = "SELECT * FROM " . $this->table . " WHERE status = 'Available'";
        $params = [];

        if (!empty($searchTerm)) {
            $query .= " AND (brand LIKE :search OR model LIKE :search OR type LIKE :search)";
            $params[':search'] = '%' . $searchTerm . '%';
        }

        if (!empty($typeFilter)) {
            $query .= " AND type = :type";
            $params[':type'] = $typeFilter;
        }

        $query .= " ORDER BY price_per_day ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}