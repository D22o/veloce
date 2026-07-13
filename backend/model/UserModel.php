<?php
/**
 * User Data Model
 */
class UserModel {
    private PDO $db;
    private string $table = 'users';

    public function __construct( PDO $databaseConnection) {
        $this->db = $databaseConnection;
    }

    /**
     * Finds a single user record utilizing their unique email profile
     */
    public function findUserByEmail(string $email) {
        $query = "SELECT user_id, user_name, user_email, user_pass, user_role FROM " . $this->table . " WHERE user_email = :email LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Database Lookup Exception: " . $e->getMessage());
            return false;
        }
    }

    public function registerUser( String $name, String $email, String $password, String $role = 'user') {
        $query = "INSERT INTO " . $this->table . " (user_name, user_email, user_pass, user_role) VALUES (:name, :email, :pass, :role)";
        try {
            $stmt = $this->db->prepare($query);
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':pass', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Registration Database Error: " . $e->getMessage());
            return false;
        }
    }

    public function updatePasswordByEmail(String $email, String $newPassword) {
        $query = "UPDATE " . $this->table . " SET user_pass = :pass WHERE user_email = :email";
        try {
            $stmt = $this->db->prepare($query);
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt->bindParam(':pass', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Password Reset Model Failure: " . $e->getMessage());
            return false;
        }
    }
}