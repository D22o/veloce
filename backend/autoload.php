<?php

/**
 * ------------------------------------------------------
 * Load Environment Variables
 * ------------------------------------------------------
 */

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile, false, INI_SCANNER_TYPED);
    foreach ($env as $key => $value) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

define('APP_URL', getenv('APP_URL') ?: 'http://localhost/test');

/**
 * ------------------------------------------------------
 * Load Global Helper Functions
 * ------------------------------------------------------
 */

foreach (glob(__DIR__ . '/utils/*.php') as $helper) {
    require_once $helper;
}

/**
 * ------------------------------------------------------
 * PSR-4 Style Autoloader
 * Namespace: Backend\
 * ------------------------------------------------------
 */

spl_autoload_register(function (string $class) {

    $prefix = 'Backend\\';

    // Only autoload classes inside the Backend namespace
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    // Remove "Backend\"
    $relativeClass = substr($class, strlen($prefix));

    // Convert namespace to directory
    $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass);

    // Keep your folders lowercase
    $parts = explode(DIRECTORY_SEPARATOR, $relativePath);

    if (!empty($parts)) {
        $parts[0] = strtolower($parts[0]);
    }

    $file = __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});