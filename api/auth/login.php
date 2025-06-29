<?php

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

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);

// Temporary logging to check received data
error_log("Received login data: " . print_r($data, true));

// Validate input data
$email = $data['email'] ?? null;
$password = $data['password'] ?? null;
$role = $data['role'] ?? null;

if (empty($email) || empty($password) || empty($role)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields (email, password, role).']);
    exit();
}

// Basic email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit();
}

// Validate role against allowed ENUM values (basic check)
$allowedRoles = ['admin', 'agent', 'builder', 'client', 'influencer'];
if (!in_array($role, $allowedRoles)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid user role specified.']);
    exit();
}

// Fetch user from database using PDO
try {
    $sql = "SELECT u.id, u.name, u.email, u.password, u.role, u.country_id, c.name as country_name FROM users u LEFT JOIN countries c ON u.country_id = c.id WHERE u.email = ? LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        // User not found or password incorrect
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        exit();
    }

    // Check if the role matches
    if ($user['role'] !== $role) {
         http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access denied for this role.']);
        exit();
    }

    // Authentication successful
    // Remove password hash before sending to frontend
    unset($user['password']);

    // Store user data in session (optional, depending on how you manage sessions)
    // $_SESSION['user_id'] = $user['id'];
    // $_SESSION['user_role'] = $user['role'];

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Login successful!', 'user' => $user]);

} catch (PDOException $e) {
    // Log the error but return a generic message to the frontend
    error_log("Database error during login: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'A database error occurred during login.']);
} catch (Exception $e) {
    // Catch any other exceptions
    error_log("An unexpected error occurred during login: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
}

// PDO statements and connection are often managed automatically or closed explicitly if needed
?> 