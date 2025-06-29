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

if (empty($data['campaign_id']) || empty($data['property_ids']) || !is_array($data['property_ids'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'campaign_id and property_ids are required']);
    exit();
}

try {
    $service = new CampaignService();
    $service->assignProperties($data['campaign_id'], $data['property_ids']);
    echo json_encode(['success' => true, 'message' => 'Properties assigned to campaign']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 