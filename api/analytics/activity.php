<?php
require_once '../../includes/init.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : null;
    $last_login = isset($data['last_login']) ? $data['last_login'] : null;
    $last_active = isset($data['last_active']) ? $data['last_active'] : null;
    $session_duration = isset($data['session_duration']) ? intval($data['session_duration']) : 0;
    $pages_visited = isset($data['pages_visited']) ? intval($data['pages_visited']) : 0;
    if (!$user_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'user_id is required']);
        exit();
    }
    try {
        $stmt = $db->prepare('INSERT INTO user_activity (user_id, last_login, last_active, session_duration, pages_visited) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$user_id, $last_login, $last_active, $session_duration, $pages_visited]);
        log_action("[UserActivity] user_id={$user_id}, activity=activity");
        echo json_encode(['success' => true, 'message' => 'Activity logged']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to log activity', 'error' => $e->getMessage()]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
    if (!$user_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'user_id is required']);
        exit();
    }
    try {
        $stmt = $db->prepare('SELECT * FROM user_activity WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$user_id]);
        $activity = $stmt->fetchAll();
        echo json_encode(['success' => true, 'activity' => $activity]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch activity', 'error' => $e->getMessage()]);
    }
    exit();
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method Not Allowed']); 