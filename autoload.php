<?php

/**
 * PSR-4 compatible autoloader for the Laposta API library.
 *
 * This autoloader maps namespace prefixes to directory structures following
 * the PSR-4 standard (https://www.php-fig.org/psr/psr-4/).
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
            // Get the relative class path by remo`ving the namespace prefix
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