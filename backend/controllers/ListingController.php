<?php 

namespace Backend\Controllers;

use Backend\Models\ListingModel;

class ListingController
{
    private ListingModel $listingModel;

    public function __construct() 
    {
        $this->listingModel = new ListingModel();
    }
    public function createListing(array $data)
    {
        return $this->listingModel->createListing($data);
    }
    public function updateListing(int $id, array $data)
    {
        return $this->listingModel->updateListing($id, $data);
    }
    public function deleteListing(int $id)
    {
        return $this->listingModel->deleteListing($id);
    }
    public function searchAvailableFleet(string $searchTerm, string $typeFilter) 
    {
        return $this->listingModel->searchAvailableFleet($searchTerm, $typeFilter);
    }
    public function getTotalCount()
    {
        return $this->listingModel->getTotalCount();
    }
    public function readPaginated(int $limit, int $offset)
    {
        return $this->listingModel->readPaginated($limit, $offset);
    }
}