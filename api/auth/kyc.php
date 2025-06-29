<?php
require_once '../../includes/init.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
    $document_type = isset($_POST['document_type']) ? $_POST['document_type'] : null;
    $file_name = isset($_FILES['file']['name']) ? $_FILES['file']['name'] : null;
    if (!$user_id || !$document_type || !$file_name) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'user_id, document_type, and file are required']);
        exit();
    }
    // For now, just log the file name (file upload handling can be expanded)
    try {
        $stmt = $db->prepare('INSERT INTO kyc_documents (user_id, document_type, file_name, status) VALUES (?, ?, ?, ?)');
        $stmt->execute([$user_id, $document_type, $file_name, 'pending']);
        echo json_encode(['success' => true, 'message' => 'KYC document submitted']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to submit KYC document', 'error' => $e->getMessage()]);
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
        $stmt = $db->prepare('SELECT * FROM kyc_documents WHERE user_id = ? ORDER BY submitted_at DESC');
        $stmt->execute([$user_id]);
        $kyc = $stmt->fetchAll();
        echo json_encode(['success' => true, 'kyc' => $kyc]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch KYC info', 'error' => $e->getMessage()]);
    }
    exit();
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method Not Allowed']); 