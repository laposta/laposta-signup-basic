<?php

/**
 * Primary runtime autoloader for the plugin classes plus the bundled Laposta API v2 library.
 *
 * WordPress boots the plugin through laposta-signup-basic.php, which includes this file.
 * In Composer-managed installs, the root project autoloader may also know this namespace.
 */

spl_autoload_register(function ($class) {
    // Configuration for different namespaces and their base directories
    $namespaces = [
        'Laposta\\SignupBasic\\' => __DIR__ . '/src/',
    ];

    // Check each namespace configuration
    foreach ($namespaces as $prefix => $baseDir) {
        // If the class starts with this namespace prefix
        if (strpos($class, $prefix) === 0) {
            // Get the relative class path by removing the namespace prefix
            $relativeClass = substr($class, strlen($prefix));

            // Build the full file path by converting namespace separators to directory separators
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

            // Load the file if it exists
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

/**
 * Conditionally include the Laposta API autoloader
 *
 * This API library is only loaded when PHP 8.0 or higher is in use,
 * to ensure compatibility with its internal structure.
 */
if (PHP_VERSION_ID >= 80000) {
    require_once __DIR__ . '/includes/laposta-api-php-2/autoload.php';
}
