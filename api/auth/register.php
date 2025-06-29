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

// Include the database initialization file - provides $pdo
require_once __DIR__ . '/../../includes/init.php';

header('Content-Type: application/json');

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input data - ensure required fields are present
$name = $data['name'] ?? null;
$email = $data['email'] ?? null;
$password = $data['password'] ?? null;
$role = $data['role'] ?? null;
$country_id = $data['country_id'] ?? null;

// Include agent-specific fields if role is agent
$phone = ($role === 'agent') ? ($data['phone'] ?? null) : null;
$license = ($role === 'agent') ? ($data['license'] ?? null) : null;

// Basic validation for required fields across all roles
if (empty($name) || empty($email) || empty($password) || empty($role) || empty($country_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields (name, email, password, role, country_id).']);
    exit();
}

// Basic email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit();
}

// Check if email already exists using PDO
try {
    $sql = "SELECT id FROM users WHERE email = ? LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email already exists.']);
        exit();
    }
} catch (PDOException $e) {
    // Log the error but return a generic message to the frontend
    error_log("Database error checking email existence: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'A database error occurred.']);
    exit();
}

// Validate role against allowed ENUM values (basic check)
$allowedRoles = ['admin', 'agent', 'builder', 'client', 'influencer'];
if (!in_array($role, $allowedRoles)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid user role specified.']);
    exit();
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Determine which fields to insert based on role
$insert_fields = 'name, email, password, role, country_id';
$insert_placeholders = '?, ?, ?, ?, ?';
$insert_values = [$name, $email, $hashed_password, $role, $country_id];

if ($role === 'agent') {
    // Add agent-specific fields if they exist and are needed for initial insert
    // Note: phone and license were added to the users table in schema.sql
    if ($phone !== null) {
        $insert_fields .= ', phone';
        $insert_placeholders .= ', ?';
        $insert_values[] = $phone;
    }
    if ($license !== null) {
         $insert_fields .= ', license';
        $insert_placeholders .= ', ?';
        $insert_values[] = $license;
    }
}

// Insert the new user into the database using PDO
try {
    $sql = "INSERT INTO users ({$insert_fields}) VALUES ({$insert_placeholders})";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute($insert_values)) {
        $user_id = $pdo->lastInsertId();
        http_response_code(201); // Created
        echo json_encode(['success' => true, 'message' => 'User registered successfully!', 'user_id' => $user_id]);
    } else {
        // This else block might be redundant with PDO::ERRMODE_EXCEPTION, but good for clarity
        error_log("PDOStatement execute failed: " . implode(", ", $stmt->errorInfo()));
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error registering user.']);
    }
} catch (PDOException $e) {
    // Catching PDO exceptions during insert
    error_log("Database error inserting user: " . $e->getMessage());
    http_response_code(500);
     echo json_encode(['success' => false, 'message' => 'A database error occurred during registration.']);
}

// PDO statements and connection are often managed automatically or closed explicitly if needed
?> 