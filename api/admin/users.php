<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering to catch any stray output
ob_start();

// Allow requests from your React development server
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); // Add PUT and DELETE methods
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle OPTIONS requests (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Use absolute path to init.php
require_once __DIR__ . '/../../includes/init.php';

header('Content-Type: application/json');

// Check the request method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Existing GET logic to fetch all users
        try {
            // Test database connection first
            if (!isset($db) || !($db instanceof PDO)) {
                throw new Exception('Database connection not available');
            }

            // Fetch all users
            $users = $db->query("
                SELECT u.id, u.name, u.email, u.role, u.created_at, u.country_id, c.name as country_name
                FROM users u
                LEFT JOIN countries c ON u.country_id = c.id
                ORDER BY u.created_at DESC
            ")->fetchAll(PDO::FETCH_ASSOC);

            // Clear any output buffer before sending JSON
            ob_clean();
            
            echo json_encode([
                'success' => true,
                'data' => $users
            ]);
        } catch (Exception $e) {
            // Clear any output buffer before sending error JSON
            ob_clean();
            
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ]);
        }
        break;

    case 'PUT':
        // Handle updating a user (e.g., changing role)
        try {
            // Test database connection first
            if (!isset($db) || !($db instanceof PDO)) {
                throw new Exception('Database connection not available');
            }

            // Get the PUT data
            $data = json_decode(file_get_contents('php://input'), true);

            // Validate input data
            $userId = $data['id'] ?? null;
            $newRole = $data['role'] ?? null;

            if (empty($userId) || empty($newRole)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required fields (id, role) for update.'
                ]);
                exit();
            }

            // Validate role against allowed ENUM values (basic check)
            $allowedRoles = ['admin', 'agent', 'builder', 'client', 'influencer'];
            if (!in_array($newRole, $allowedRoles)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid user role specified for update.'
                ]);
                exit();
            }

            // Prepare and execute the update query
            $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$newRole, $userId]);

            // Check if the update was successful (at least one row affected)
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User role updated successfully.'
                ]);
            } else {
                 // User ID not found or role was the same
                 // Consider if you need a different status code here
                http_response_code(404); // Not Found or Conflict
                echo json_encode([
                    'success' => false,
                    'message' => 'User not found or role was already the same.'
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update user role',
                'error' => $e->getMessage()
            ]);
        }
        break;

    case 'POST':
        // Handle creating a new user (admin add user)
        try {
            if (!isset($db) || !($db instanceof PDO)) {
                throw new Exception('Database connection not available');
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $name = $data['name'] ?? null;
            $email = $data['email'] ?? null;
            $password = $data['password'] ?? null;
            $role = $data['role'] ?? null;
            $country_id = $data['country_id'] ?? null;
            $phone = ($role === 'agent') ? ($data['phone'] ?? null) : null;
            $license = ($role === 'agent') ? ($data['license'] ?? null) : null;
            if (empty($name) || empty($email) || empty($password) || empty($role) || empty($country_id)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing required fields (name, email, password, role, country_id).']);
                exit();
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
                exit();
            }
            $allowedRoles = ['admin', 'agent', 'builder', 'client', 'influencer'];
            if (!in_array($role, $allowedRoles)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid user role specified.']);
                exit();
            }
            // Check if email already exists
            $stmt = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'Email already exists.']);
                exit();
            }
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_fields = 'name, email, password, role, country_id';
            $insert_placeholders = '?, ?, ?, ?, ?';
            $insert_values = [$name, $email, $hashed_password, $role, $country_id];
            if ($role === 'agent') {
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
            $sql = "INSERT INTO users ({$insert_fields}) VALUES ({$insert_placeholders})";
            $stmt = $db->prepare($sql);
            if ($stmt->execute($insert_values)) {
                $user_id = $db->lastInsertId();
                http_response_code(201);
                echo json_encode(['success' => true, 'message' => 'User created successfully!', 'user_id' => $user_id]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error creating user.']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'A database error occurred during user creation.', 'error' => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        // Handle deleting a user (admin delete user)
        try {
            if (!isset($db) || !($db instanceof PDO)) {
                throw new Exception('Database connection not available');
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = $data['id'] ?? null;
            if (empty($userId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing user id for deletion.']);
                exit();
            }
            // Prevent deleting admin users
            $stmt = $db->prepare('SELECT role FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'User not found.']);
                exit();
            }
            if ($user['role'] === 'admin') {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Admin user cannot be deleted.']);
                exit();
            }
            $stmt = $db->prepare('DELETE FROM users WHERE id = ?');
            if ($stmt->execute([$userId])) {
                echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error deleting user.']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'A database error occurred during user deletion.', 'error' => $e->getMessage()]);
        }
        break;

    default:
        // Method not allowed
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method Not Allowed.'
        ]);
        break;
}

// Note: ob_start() and ob_clean() might be needed depending on error handling flow
// Ensure no stray output before JSON encoding

?> 