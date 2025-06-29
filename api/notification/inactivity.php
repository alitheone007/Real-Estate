<?php
require_once '../../includes/init.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : null;
    $notification_type = isset($data['notification_type']) ? $data['notification_type'] : null;
    if (!$user_id || !$notification_type) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'user_id and notification_type are required']);
        exit();
    }
    try {
        $stmt = $db->prepare('INSERT INTO inactivity_notifications (user_id, notification_type) VALUES (?, ?)');
        $stmt->execute([$user_id, $notification_type]);
        echo json_encode(['success' => true, 'message' => 'Notification logged']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to log notification', 'error' => $e->getMessage()]);
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
        $stmt = $db->prepare('SELECT * FROM inactivity_notifications WHERE user_id = ? ORDER BY sent_at DESC');
        $stmt->execute([$user_id]);
        $notifications = $stmt->fetchAll();
        echo json_encode(['success' => true, 'notifications' => $notifications]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch notifications', 'error' => $e->getMessage()]);
    }
    exit();
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method Not Allowed']); 