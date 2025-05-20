<?php

session_start();

// Load configurations properly
$config     = include __DIR__ . '/../app/config/app.config.php';
$dbConfig   = include __DIR__ . '/../config/db.config.php';

// Set error reporting
if (!empty($config['app_env']) && $config['app_env'] === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set($config['timezone'] ?? 'UTC');

// Database connection
try {
    $conn = new mysqli(
        $dbConfig['host'],
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['database'], // explicitly set the DB name here
        isset($dbConfig['port']) ? (int)$dbConfig['port'] : 3306
    );

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

} catch (Exception $e) {
    error_log($e->getMessage());
    die("A database error occurred. Please try again later.");
}

// Optional: autoloader if needed
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../app/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
