<?php
require_once '../../includes/init.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    $ticket_id = isset($data['ticket_id']) ? intval($data['ticket_id']) : null;
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : null;
    $message = isset($data['message']) ? $data['message'] : '';
    $is_internal = isset($data['is_internal']) ? (bool)$data['is_internal'] : false;
    if (!$ticket_id || !$message) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ticket_id and message are required']);
        exit();
    }
    try {
        $stmt = $db->prepare('INSERT INTO ticket_responses (ticket_id, user_id, message, is_internal) VALUES (?, ?, ?, ?)');
        $stmt->execute([$ticket_id, $user_id, $message, $is_internal]);
        $responseId = $db->lastInsertId();
        log_action("[FeedbackResponse] user_id={$user_id}, ticket_id={$ticket_id}, response_id={$responseId}");
        echo json_encode(['success' => true, 'message' => 'Response added']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add response', 'error' => $e->getMessage()]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $ticket_id = isset($_GET['ticket_id']) ? intval($_GET['ticket_id']) : null;
    if (!$ticket_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ticket_id is required']);
        exit();
    }
    try {
        $stmt = $db->prepare('SELECT * FROM ticket_responses WHERE ticket_id = ? ORDER BY created_at ASC');
        $stmt->execute([$ticket_id]);
        $responses = $stmt->fetchAll();
        echo json_encode(['success' => true, 'responses' => $responses]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch responses', 'error' => $e->getMessage()]);
    }
    exit();
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method Not Allowed']); 