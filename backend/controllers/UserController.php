<?php

namespace Backend\Controllers;

use Backend\Models\UserModel;

class UserController
{
    private UserModel $userModel;
    public function __construct()
    {
        $this->userModel = new UserModel();
    }
    public function updateUserProfile(int $userId, array $data)
    {
        return $this->userModel->updateUserProfile($userId, $data);
    }
    public function findUserByEmail(string $email)
    {
        return $this->userModel->findUserByEmail($email);
    }
    public function updateUserPassword(string $userEmail, string $newPassword)
    {
        return $this->userModel->updateUserPassword($userEmail, $newPassword);
    }
    public function updateUserVerificationRecord(int $userId, array $data)
    {
        return $this->userModel->updateUserVerificationRecord($userId, $data);
    }
    public function getPendingVerificationRequest()
    {
        return $this->userModel->getPendingVerificationRequest();
    }
}