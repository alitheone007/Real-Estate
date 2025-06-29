<?php
require_once '../../includes/init.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : null;
    $phone_number = isset($data['phone_number']) ? $data['phone_number'] : '';
    $whatsapp_id = isset($data['whatsapp_id']) ? $data['whatsapp_id'] : '';
    $is_verified = isset($data['is_verified']) ? (bool)$data['is_verified'] : false;
    if (!$user_id || !$phone_number) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'user_id and phone_number are required']);
        exit();
    }
    try {
        $stmt = $db->prepare('INSERT INTO whatsapp_integrations (user_id, phone_number, whatsapp_id, is_verified) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE whatsapp_id = VALUES(whatsapp_id), is_verified = VALUES(is_verified), last_sync = CURRENT_TIMESTAMP');
        $stmt->execute([$user_id, $phone_number, $whatsapp_id, $is_verified]);
        echo json_encode(['success' => true, 'message' => 'WhatsApp integration updated']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update WhatsApp integration', 'error' => $e->getMessage()]);
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
        $stmt = $db->prepare('SELECT * FROM whatsapp_integrations WHERE user_id = ? ORDER BY last_sync DESC LIMIT 1');
        $stmt->execute([$user_id]);
        $info = $stmt->fetch();
        echo json_encode(['success' => true, 'whatsapp' => $info]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch WhatsApp info', 'error' => $e->getMessage()]);
    }
    exit();
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method Not Allowed']); 