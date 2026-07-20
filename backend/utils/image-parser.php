<?php

function imageParser(?array $image_data, string $fallbackName): string
{
    // Check if a file was actually uploaded without errors
    if (isset($image_data) && $image_data['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $image_data['tmp_name'];
        $fileName = $image_data['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            // Generate unique target filename
            $newImageName = 'car_' . time() . '_' . random_int(1000, 9999) . '.' . $fileExtension;
            
            // Standardize output directory path to matching uploads folder
            $uploadFileDir = dirname(__DIR__, 2) . '/data/fleet/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newImageName)) {
                return $newImageName; // Return new image name upon successful upload
            }
        }
    }
    
    return $fallbackName; // Fallback to existing or default image if no new upload occurs
}