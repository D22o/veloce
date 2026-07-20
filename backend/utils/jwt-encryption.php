<?php

/**
 * Gets the secure cryptographic signing key
 */
function get_token_secret_key(): string 
{
    return getenv('JWT_SECRET') ?: ($_ENV['JWT_SECRET']);
}

/**
 * Packages a payload, signs it, and sets an HttpOnly secure cookie
 */
function set_auth_cookie(array $payload, int $expiry_seconds = 86400): bool 
{
    $cookie_name = 'veloce_auth_token';
    $secret = get_token_secret_key();
    
    // 1. Inject an expiration timestamp into the payload
    $payload['exp'] = time() + $expiry_seconds;
    
    // 2. Serialize and Base64-encode the payload
    $serialized_payload = base64_encode(json_encode($payload));
    
    // 3. Generate a cryptographic HMAC signature (prevents client-side tampering)
    $signature = hash_hmac('sha256', $serialized_payload, $secret);
    
    // 4. Combine encoded payload and signature
    $token = $serialized_payload . '.' . $signature;

    // 5. Send cookie with strict security flags
    return setcookie(
        $cookie_name,
        $token,
        [
            'expires'  => time() + $expiry_seconds,
            'path'     => '/',
            'domain'   => '', 
            'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', 
            'httponly' => true, // Prevents XSS scripts from reading the token
            'samesite' => 'Strict' // Prevents CSRF attacks
        ]
    );
}

/**
 * Decodes, verifies, and returns the cookie payload. Returns null if invalid.
 */
function verify_auth_cookie(): ?array 
{
    $cookie_name = 'veloce_auth_token';

    if (!isset($_COOKIE[$cookie_name])) {
        return null;
    }

    $token = $_COOKIE[$cookie_name];
    $parts = explode('.', $token);

    if (count($parts) !== 2) {
        return null;
    }

    list($serialized_payload, $received_signature) = $parts;
    $secret = get_token_secret_key();

    // 1. Verify integrity using a timing-attack safe comparison
    $expected_signature = hash_hmac('sha256', $serialized_payload, $secret);
    if (!hash_equals($expected_signature, $received_signature)) {
        return null; // Tampered token!
    }

    // 2. Decode payload
    $payload = json_decode(base64_decode($serialized_payload), true);

    // 3. Check expiration
    if (!isset($payload['exp']) || time() > $payload['exp']) {
        clear_auth_cookie(); // Wipe expired cookie
        return null;
    }

    return $payload;
}

/**
 * Clears and deletes the secure authentication cookie
 */
function clear_auth_cookie(): bool 
{
    $cookie_name = 'veloce_auth_token';

    if (isset($_COOKIE[$cookie_name])) {
        unset($_COOKIE[$cookie_name]);
    }
    
    return setcookie(
        $cookie_name,
        '',
        [
            'expires'  => time() - 3600, // Force expiry in the past
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Strict'
        ]
    );
}