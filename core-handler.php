<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Load the centralized autoloader
require_once __DIR__ . '/backend/autoload.php';

// Grab the clean action parameter rewritten by your .htaccess ('auth/login', 'booking/create')
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Ensure this router only processes requests routed via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action) {

    // Clean the action path string
    $action = trim($action, '/');
    $segments = explode('/', $action);
    
    // Extract the primary controller segment ('auth', 'booking', 'listing')
    $module = strtolower($segments[0] ?? '');

    // Dynamic class mapping based on namespace conventions
    // Example: 'booking' -> 'Backend\Services\BookingService'
    $className = "Backend\\Services\\" . ucfirst($module) . "Service";

    if (class_exists($className)) {
        // Instantiate the matched service dynamically
        $serviceInstance = new $className();
        
        // Hand off the raw post data & the original clean action
        $serviceInstance->handleRequest($action, $_POST);
    } else {
        $_SESSION['router_error'] = "The requested controller action could not be resolved.";
        header("Location: " . APP_URL . "/error/404");
        exit();
    }

} else {
    header("Location: " . APP_URL);
    exit();
}