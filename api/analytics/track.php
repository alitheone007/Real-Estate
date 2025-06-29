<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../includes/init.php';

use App\Services\Analytics\AnalyticsService;

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

$user_id = isset($data['user_id']) ? intval($data['user_id']) : null;
$ip_address = isset($data['ip_address']) ? $data['ip_address'] : '';
$country_code = isset($data['country_code']) ? $data['country_code'] : '';
$city = isset($data['city']) ? $data['city'] : '';
$region = isset($data['region']) ? $data['region'] : '';
$timezone = isset($data['timezone']) ? $data['timezone'] : '';
$user_agent = isset($data['user_agent']) ? $data['user_agent'] : '';
$page_visited = isset($data['page_visited']) ? $data['page_visited'] : '';
$session_duration = isset($data['session_duration']) ? intval($data['session_duration']) : 0;
$click_count = isset($data['click_count']) ? intval($data['click_count']) : 1;

try {
    $stmt = $db->prepare('INSERT INTO user_analytics (user_id, ip_address, country_code, city, region, timezone, user_agent, page_visited, session_duration, click_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$user_id, $ip_address, $country_code, $city, $region, $timezone, $user_agent, $page_visited, $session_duration, $click_count]);
    echo json_encode(['success' => true, 'message' => 'Analytics logged']);
    log_action("[Analytics] user_id={$user_id}, page_visited={$page_visited}, event=track");
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to log analytics', 'error' => $e->getMessage()]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get analytics dashboard data
    $filters = [];
    
    if (isset($_GET['date_from'])) {
        $filters['date_from'] = $_GET['date_from'];
    }
    
    if (isset($_GET['date_to'])) {
        $filters['date_to'] = $_GET['date_to'];
    }

    $analytics = $analyticsService->getDashboardAnalytics($filters);

    echo json_encode([
        'success' => true,
        'data' => $analytics
    ]);

} else {
    throw new Exception('Method not allowed');
} 