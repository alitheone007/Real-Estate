<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../includes/init.php';

use App\Services\Timezone\TimezoneService;

try {
    $timezoneService = new TimezoneService();
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $countryId = $_GET['country_id'] ?? null;
        $ip = $_GET['ip'] ?? null;
        
        if ($countryId) {
            // Get status for specific country
            $status = $timezoneService->getMarketplaceStatus($countryId);
        } elseif ($ip) {
            // Get status based on IP address
            $status = $timezoneService->getMarketplaceStatusByIp($ip);
        } else {
            // Get IP from request
            $clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $status = $timezoneService->getMarketplaceStatusByIp($clientIp);
        }

        echo json_encode([
            'success' => true,
            'data' => $status
        ]);

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Invalid JSON input');
        }

        if (isset($input['action'])) {
            switch ($input['action']) {
                case 'update_all':
                    // Update marketplace status for all countries
                    $result = $timezoneService->updateAllMarketplaceStatus();
                    echo json_encode([
                        'success' => true,
                        'message' => 'Marketplace status updated for all countries'
                    ]);
                    break;

                case 'update_country':
                    // Update status for specific country
                    if (!isset($input['country_id'])) {
                        throw new Exception('Country ID is required');
                    }
                    $result = $timezoneService->updateMarketplaceStatus($input['country_id']);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Marketplace status updated for country'
                    ]);
                    break;

                case 'update_operational_hours':
                    // Update operational hours for a country
                    if (!isset($input['country_id']) || !isset($input['operational_hours'])) {
                        throw new Exception('Country ID and operational hours are required');
                    }
                    $result = $timezoneService->updateOperationalHours($input['country_id'], $input['operational_hours']);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Operational hours updated successfully'
                    ]);
                    break;

                default:
                    throw new Exception('Invalid action');
            }
        } else {
            throw new Exception('Action is required');
        }

    } else {
        throw new Exception('Method not allowed');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 