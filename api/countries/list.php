<?php
ob_start();

// Allow requests from your React development server
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle OPTIONS requests (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include the database initialization file - provides $db
require_once __DIR__ . '/../../includes/init.php';

header('Content-Type: application/json');

// Explicitly check if PDO connection is available after including init.php
if (!isset($db) || !$db) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection not available after initialization.']);
    exit();
}

// Fetch countries from the database using PDO
try {
    $sql = "SELECT id, name, code, flag_icon FROM countries ORDER BY id ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($countries) {
        http_response_code(200);
        echo json_encode($countries);
    } else {
        // No countries found, which is not necessarily an error, but inform the frontend
        http_response_code(200); // Still 200 OK, just an empty array
        echo json_encode([]);
    }

} catch (PDOException $e) {
    // Log the database error
    error_log("Database error fetching countries: " . $e->getMessage());
    // Return a generic message to the frontend
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'A database error occurred while fetching countries.']);
} catch (Exception $e) {
    // Catch any other exceptions
    error_log("An unexpected error occurred while fetching countries: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
}

// PDO statements and connection are often managed automatically or closed explicitly if needed
?> 