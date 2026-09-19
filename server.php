<?php

/**
 * Development server router.
 *
 * PHP's built-in server serves existing files directly. All other requests
 * must be forwarded to Laravel's public front controller so named routes such
 * as /launching are handled by the application.
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
