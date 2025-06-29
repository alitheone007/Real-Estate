<?php
// Define the project root path
define('PROJECT_ROOT', dirname(__DIR__));

session_start();

// Define a global error and exception handler to output JSON
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        // This error code is not included in error_reporting
        return false;
    }
    // Clear any previous output
    if (ob_get_contents()) {
        ob_clean();
    }
    header('Content-Type: application/json', true, 500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred',
        'error' => $message,
        'severity' => $severity,
        'file' => $file,
        'line' => $line
    ]);
    exit();
});

set_exception_handler(function ($exception) {
    // Clear any previous output
     if (ob_get_contents()) {
        ob_clean();
    }
    header('Content-Type: application/json', true, 500);
    echo json_encode([
        'success' => false,
        'message' => 'An uncaught exception occurred',
        'error' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
    exit();
});

// Ensure output buffering is on to prevent partial output before errors
ob_start();

// Load configurations properly
$config = include __DIR__ . '/../app/config/app.config.php';

// Load database configuration based on environment
if (!empty($config['app_env']) && $config['app_env'] === 'development') {
    $dbConfigPath = __DIR__ . '/../config/db.config.local.php';
} else {
    $dbConfigPath = __DIR__ . '/../config/db.config.php';
}

// Check if the database config file exists before including
if (!file_exists($dbConfigPath)) {
    // Use the JSON error handler for this critical error
    throw new Exception("Database configuration file not found for environment: " . (isset($config['app_env']) ? $config['app_env'] : 'production'));
}

$dbConfig = include $dbConfigPath;

// Set error reporting - display_errors should be off for API backend in production
// Our custom error handler will take care of displaying errors in a JSON format
error_reporting(E_ALL);
ini_set('display_errors', 0); // Ensure display_errors is off
ini_set('log_errors', 1); // Log errors
ini_set('error_log', __DIR__ . '/../logs/php_errors.log'); // Specify log file

// Set timezone
date_default_timezone_set(isset($config['timezone']) ? $config['timezone'] : 'UTC');

// Database connection
try {
    // Use PDO instead of mysqli for better error handling and consistency
    // Prepare DSN parts for compatibility
    $dbHost = isset($dbConfig['host']) ? $dbConfig['host'] : '';
    $dbName = isset($dbConfig['database']) ? $dbConfig['database'] : '';
    $dbCharset = isset($dbConfig['charset']) ? $dbConfig['charset'] : 'utf8mb4';
    $dbPort = isset($dbConfig['port']) ? (int)$dbConfig['port'] : 3306;

    // Construct DSN string using concatenation
    $dsn = 'mysql:host=' . $dbHost . ';dbname=' . $dbName . ';charset=' . $dbCharset . ';port=' . $dbPort;

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch results as associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false, // Use native prepared statements
    ];
    $db = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $options);

} catch (PDOException $e) {
    // This specific database connection error needs to be caught here before the global handler
    // Clear any previous output
     if (ob_get_contents()) {
        ob_clean();
    }
    header('Content-Type: application/json', true, 500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection error',
        'error' => $e->getMessage()
    ]);
    exit();
} catch (Exception $e) {
    // Catch any other exceptions
    error_log("An unexpected error occurred during init: " . $e->getMessage());
     if (ob_get_contents()) {
        ob_clean();
    }
    header('Content-Type: application/json', true, 500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred during initialization.',
        'error' => $e->getMessage()
    ]);
    exit();
}

// Provide the $db connection object to other scripts
// Optional: autoloader if needed
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../app/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Logging utility for log_file.txt
function log_action($message) {
    $logFile = PROJECT_ROOT . '/log_file.txt';
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[$timestamp] $message\n";
    file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
}
