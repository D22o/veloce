<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all execution session variables
$_SESSION = array();

// Obliterate the session cookie entry 
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Obliterate the secure 'token' auth cookie
setcookie('token', '', time() - 3600, '/');

// Terminate storage context completely
session_destroy();

// Redirect clean away to landing panel
header("Location: ../../login");
exit();