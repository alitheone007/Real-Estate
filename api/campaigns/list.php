<?php
require_once '../../includes/init.php';
use App\Services\CampaignService;
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

$filters = [];
if (!empty($_GET['role'])) $filters['role'] = $_GET['role'];
if (!empty($_GET['status'])) $filters['status'] = $_GET['status'];
if (!empty($_GET['property_id'])) $filters['property_id'] = $_GET['property_id'];

try {
    $service = new CampaignService();
    $campaigns = $service->listCampaigns($filters);
    echo json_encode(['success' => true, 'campaigns' => $campaigns]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 