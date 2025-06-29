<?php
require_once '../../includes/init.php';
use App\Services\CampaignService;
header('Content-Type: application/json');

$service = new CampaignService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }
    if (empty($data['campaign_id']) || empty($data['event_type'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'campaign_id and event_type are required']);
        exit();
    }
    log_action("[CampaignEvent] campaign_id={$data['campaign_id']}, user_id=".($data['user_id']??'').", event_type={$data['event_type']}, property_id=".($data['property_id']??'')."");
    try {
        $service->logEvent($data);
        echo json_encode(['success' => true, 'message' => 'Campaign event logged']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to log campaign event', 'error' => $e->getMessage()]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $filters = [];
    if (!empty($_GET['campaign_id'])) $filters['campaign_id'] = $_GET['campaign_id'];
    if (!empty($_GET['influencer_id'])) $filters['influencer_id'] = $_GET['influencer_id'];
    if (!empty($_GET['builder_id'])) $filters['builder_id'] = $_GET['builder_id'];
    if (!empty($_GET['user_id'])) $filters['user_id'] = $_GET['user_id'];
    if (!empty($_GET['property_id'])) $filters['property_id'] = $_GET['property_id'];
    try {
        $events = $service->listEvents($filters);
        echo json_encode(['success' => true, 'events' => $events]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch campaign events', 'error' => $e->getMessage()]);
    }
    exit();
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method Not Allowed']); 