<?php
require_once '../../includes/init.php';
use App\Services\CampaignService;
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

try {
    $service = new CampaignService();
    $id = $service->saveCampaign($data);
    echo json_encode(['success' => true, 'campaign_id' => $id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 