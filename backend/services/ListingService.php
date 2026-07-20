<?php

namespace Backend\Services;

use Backend\Controllers\ListingController;
use Exception;

class ListingService
{
    private ListingController $listingController;

    public function __construct() 
    {
        $this->listingController = new ListingController();
    }

    public function handleRequest(string $action, array $data): void
    {
        switch ($action) {
            case 'listing/create-listing':
                $this->validateCreateAction($data);
                break;

            case 'listing/update-listing':
                $this->validateUpdateAction($data);
                break;

            case 'listing/delete-listing':
                $this->validateDeleteAction($data);
                break;

            default:
                throw new Exception("Action not recognized: " . $action);
        }
    }

    /**
     * Sanitizes raw entry data for dynamic validation
     */
    private function validateListingData(array $data): array
    {
        $cleanData = [
            'brand'            => trim($data['brand'] ?? ''),
            'model'            => trim($data['model'] ?? ''),
            'type'             => $data['type'] ?? 'Sedan',
            'transmission'     => $data['transmission'] ?? 'Automatic',
            'fuel_type'        => $data['fuel_type'] ?? 'Petrol',
            'seating_capacity' => filter_var($data['seating_capacity'] ?? null, FILTER_VALIDATE_INT),
            'price_per_day'    => filter_var($data['price_per_day'] ?? null, FILTER_VALIDATE_FLOAT),
            'plate_number'     => strtoupper(trim($data['plate_number'] ?? '')),
            'status'           => $data['status'] ?? 'Available'
        ];

        // Safely extract from $_FILES, falling back cleanly to null if empty
        $uploadedFile = $_FILES['car_image'] ?? null;
        $fallbackImage = $data['existing_image'] ?? 'default-car.png';

        // Set the cleaned image string to whatever our corrected parser returns
        $cleanData['car_image'] = imageParser($uploadedFile, $fallbackImage);

        return $cleanData;
    }

    private function validateCreateAction(array $data): void
    {
        $cleanedData = $this->validateListingData($data);
        
        if ($this->listingController->createListing($cleanedData)) {
            $_SESSION['fleet_success'] = "Asset successfully indexed into active catalog.";
        } else {
            $_SESSION['fleet_error'] = "Plate identifier collision or database rejection.";
        }
        $this->redirect("admin/manage-fleet");
    }

    private function validateUpdateAction(array $data): void
    {
        $carId = filter_var($data['car_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$carId) {
            $_SESSION['fleet_error'] = "Target asset context identifier is missing.";
            $this->redirect("admin/manage-fleet");
        }

        $cleanedData = $this->validateListingData($data);

        if ($this->listingController->updateListing($carId, $cleanedData)) {
            $_SESSION['fleet_success'] = "Asset changes written successfully.";
        } else {
            $_SESSION['fleet_error'] = "Failed writing fleet changes.";
        }
        $this->redirect("admin/manage-fleet");
    }

    public function validateDeleteAction(array $data): void
    {
        // Accept the deleted target car ID securely from POST/GET arrays
        $carId = filter_var($data['id'] ?? $_GET['id'] ?? null, FILTER_VALIDATE_INT);
        
        if ($carId) {
            try {
                if ($this->listingController->deleteListing($carId)) {
                    $_SESSION['fleet_success'] = "Asset unlisted from operational catalog.";
                } else {
                    $_SESSION['fleet_error'] = "Asset removal rejected.";
                }
            } catch (\PDOException $e) {
                $_SESSION['fleet_error'] = "Cannot remove asset. It is linked to active booking records.";
            }
        } else {
            $_SESSION['fleet_error'] = "Missing asset target reference identifier.";
        }
        $this->redirect("admin/manage-fleet");
    }
    public function searchAvailableFleet(string $search, string $type) 
    {
        return $this->listingController->searchAvailableFleet($search, $type);
    }
    public function getTotalCount()
    {
        return $this->listingController->getTotalCount();
    }
    public function readPaginated(int $limit, int $offset)
    {
        $limit = filter_var($limit, FILTER_VALIDATE_INT);
        $offset = filter_var($offset, FILTER_VALIDATE_INT);
        return $this->listingController->readPaginated($limit, $offset);
    }

    private function redirect(string $redirectPath): void
    {
        header("Location: " . APP_URL . "/" . $redirectPath);
        exit();
    }
}