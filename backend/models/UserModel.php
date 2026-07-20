<?php

namespace Backend\Models;

use Backend\Config\Database;
use PDO;
use PDOException;
class UserModel
{
    private PDO $conn;
    private $table = 'users';
    private $verificationTable = 'user_verifications';

    public function __construct()
    {
        $this->conn = Database::connect();
    }

    public function findUserById(int $userId)
    {
        $query = "SELECT user_id, user_name, user_email, user_pass, user_role FROM " . $this->table . " WHERE user_id = :user_id LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Database Lookup Exception: " . $e->getMessage());
            return false;
        }
    }
    public function findUserByEmail(string $userEmail)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE user_email = :email LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $userEmail, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Database Lookup Exception: " . $e->getMessage());
            return false;
        }
    }
    public function createUser(array $data)
    {
        $query = "INSERT INTO " . $this->table . " (user_name, user_email, user_pass) VALUES (:name, :email, :pass)";
        try {
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':name', $data['username'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
            $stmt->bindParam(':pass', $data['password'], PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Registration Database Error: " . $e->getMessage());
            return false;
        }
    }
    public function updateUserPassword(string $userEmail, string $newPassword)
    {
        $query = "UPDATE " . $this->table . " SET user_pass = :new_pass WHERE user_email = :user_email";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':new_pass', $newPassword, PDO::PARAM_STR);
            $stmt->bindParam(':user_email', $userEmail, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Update Exception: " . $e->getMessage());
            return false;
        }
    }
    public function getUserVerificationStatus(int $userId) 
    {
        $query = "SELECT status FROM " . $this->verificationTable . " WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['status'] ?? null;
        } catch (PDOException $e) {
            error_log("Verification Status Retrieval Error: " . $e->getMessage());
            return null;
        }
    }
    public function updateUserProfile(int $userId, array $data)
    {
        $query = "UPDATE " . $this->table . " SET user_name = :full_name, phone = :phone_number WHERE user_id = :id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':full_name', $data['fullname'], PDO::PARAM_STR);
            $stmt->bindParam(':phone_number', $data['phone_number'], PDO::PARAM_STR);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Profile Metadata Update Error: " . $e->getMessage());
            return false;
        }
    }
    public function updateUserVerificationRecord(int $userId, array $data)
    {
        $query = "UPDATE " . $this->verificationTable . " 
        SET document_type = :doc_type, 
            document_number = :doc_num, 
            file_path = :file_path, 
            status = 'pending'
        WHERE user_id = :user_id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':doc_type', $data['doc_type'], PDO::PARAM_STR);
            $stmt->bindParam(':doc_num', $data['doc_num'], PDO::PARAM_STR);
            $stmt->bindParam(':file_path', $data['file_path'], PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Verification Record Update Error: " . $e->getMessage());
            return false;
        }
    }
    public function getPendingVerificationRequest()
    {
        $query = "SELECT uv.*, u.user_name, u.user_email 
            FROM " . $this->verificationTable . " uv
            JOIN " . $this->table . " u ON uv.user_id = u.user_id
            WHERE uv.status = 'pending'
            ORDER BY uv.created_at ASC";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $_SESSION['audit_error'] = "Failed to load database logs: " . $e->getMessage();
            return [];
        }
    }
    public function updateUserVerificationStatus(int $userId, string $status)
    {
        $query = "UPDATE " . $this->verificationTable ." 
                SET status = :status 
                WHERE user_id = :user_id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Deletion Exception: " . $e->getMessage());
            return false;
        }
    }
    public function deleteUserById(int $userId)
    {
        $query = "DELETE FROM " . $this->table . " WHERE user_id = :user_id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Deletion Exception: " . $e->getMessage());
            return false;
        }
    }
}