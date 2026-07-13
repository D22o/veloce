<?php
/**
 * Core Configuration and Environment Loader
 */

// Define the root file-system directory of your project dynamically
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2) . '/');
}

/**
 * Parses a standard .env file and loads values into $_ENV and getenv()
 */
function loadEnv( String $path): bool {
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Strip out trailing inline comments
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        // Split by the first equals sign
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remove enveloping quotes if present
            $value = trim($value, '"\'');

            if (!array_key_exists($name, $_ENV)) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
            }
        }
    }
    return true;
}

// Automatically load your system configuration parameters immediately
loadEnv(ROOT_PATH . '.env');