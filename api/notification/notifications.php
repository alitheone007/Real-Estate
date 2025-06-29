<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../includes/init.php';

use App\Services\Notification\NotificationService;

try {
    $notificationService = new NotificationService();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Invalid JSON input');
        }

        $action = $input['action'] ?? '';

        switch ($action) {
            case 'create':
                // Create a new notification
                if (empty($input['user_id']) || empty($input['title']) || empty($input['message'])) {
                    throw new Exception('User ID, title, and message are required');
                }
                
                $notificationData = [
                    'user_id' => $input['user_id'],
                    'title' => $input['title'],
                    'message' => $input['message'],
                    'type' => $input['type'] ?? 'info',
                    'action_url' => $input['action_url'] ?? null,
                    'send_whatsapp' => $input['send_whatsapp'] ?? false
                ];
                
                $notificationId = $notificationService->createNotification($notificationData);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Notification created successfully',
                    'notification_id' => $notificationId
                ]);
                break;

            case 'register_push':
                // Register push subscription
                if (empty($input['user_id']) || empty($input['subscription'])) {
                    throw new Exception('User ID and subscription data are required');
                }
                
                $subscriptionId = $notificationService->registerPushSubscription($input['user_id'], $input['subscription']);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Push subscription registered successfully',
                    'subscription_id' => $subscriptionId
                ]);
                break;

            case 'unregister_push':
                // Unregister push subscription
                if (empty($input['user_id']) || empty($input['endpoint'])) {
                    throw new Exception('User ID and endpoint are required');
                }
                
                $result = $notificationService->unregisterPushSubscription($input['user_id'], $input['endpoint']);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Push subscription unregistered successfully'
                ]);
                break;

            case 'register_whatsapp':
                // Register WhatsApp integration
                if (empty($input['user_id']) || empty($input['phone_number'])) {
                    throw new Exception('User ID and phone number are required');
                }
                
                $integrationId = $notificationService->registerWhatsAppIntegration($input['user_id'], $input['phone_number']);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'WhatsApp integration registered successfully',
                    'integration_id' => $integrationId
                ]);
                break;

            case 'verify_whatsapp':
                // Verify WhatsApp number
                if (empty($input['user_id']) || empty($input['verification_code'])) {
                    throw new Exception('User ID and verification code are required');
                }
                
                $result = $notificationService->verifyWhatsAppNumber($input['user_id'], $input['verification_code']);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'WhatsApp number verified successfully'
                ]);
                break;

            case 'track_activity':
                // Track user activity
                if (empty($input['user_id'])) {
                    throw new Exception('User ID is required');
                }
                
                $activityData = [
                    'last_login' => $input['last_login'] ?? null,
                    'last_active' => $input['last_active'] ?? date('Y-m-d H:i:s'),
                    'session_duration' => $input['session_duration'] ?? 0,
                    'pages_visited' => $input['pages_visited'] ?? 0,
                    'actions_performed' => $input['actions_performed'] ?? 0
                ];
                
                $result = $notificationService->trackUserActivity($input['user_id'], $activityData);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Activity tracked successfully'
                ]);
                break;

            default:
                throw new Exception('Invalid action');
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'user_notifications':
                // Get user notifications
                $userId = $_GET['user_id'] ?? null;
                if (!$userId && isset($_SESSION['user_id'])) {
                    $userId = $_SESSION['user_id'];
                }
                
                if (!$userId) {
                    throw new Exception('User ID is required');
                }
                
                $limit = $_GET['limit'] ?? 50;
                $offset = $_GET['offset'] ?? 0;
                
                $notifications = $notificationService->getUserNotifications($userId, $limit, $offset);
                
                echo json_encode([
                    'success' => true,
                    'data' => $notifications
                ]);
                break;

            case 'unread_count':
                // Get unread notification count
                $userId = $_GET['user_id'] ?? null;
                if (!$userId && isset($_SESSION['user_id'])) {
                    $userId = $_SESSION['user_id'];
                }
                
                if (!$userId) {
                    throw new Exception('User ID is required');
                }
                
                $count = $notificationService->getUnreadCount($userId);
                
                echo json_encode([
                    'success' => true,
                    'count' => $count
                ]);
                break;

            case 'push_subscriptions':
                // Get user push subscriptions
                $userId = $_GET['user_id'] ?? null;
                if (!$userId && isset($_SESSION['user_id'])) {
                    $userId = $_SESSION['user_id'];
                }
                
                if (!$userId) {
                    throw new Exception('User ID is required');
                }
                
                $subscriptions = $notificationService->getUserPushSubscriptions($userId);
                
                echo json_encode([
                    'success' => true,
                    'data' => $subscriptions
                ]);
                break;

            case 'whatsapp_status':
                // Get WhatsApp integration status
                $userId = $_GET['user_id'] ?? null;
                if (!$userId && isset($_SESSION['user_id'])) {
                    $userId = $_SESSION['user_id'];
                }
                
                if (!$userId) {
                    throw new Exception('User ID is required');
                }
                
                $status = $notificationService->getWhatsAppStatus($userId);
                
                echo json_encode([
                    'success' => true,
                    'data' => $status
                ]);
                break;

            case 'notification_preferences':
                // Get notification preferences
                $userId = $_GET['user_id'] ?? null;
                if (!$userId && isset($_SESSION['user_id'])) {
                    $userId = $_SESSION['user_id'];
                }
                
                if (!$userId) {
                    throw new Exception('User ID is required');
                }
                
                $preferences = $notificationService->getNotificationPreferences($userId);
                
                echo json_encode([
                    'success' => true,
                    'data' => $preferences
                ]);
                break;

            default:
                throw new Exception('Invalid action');
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Invalid JSON input');
        }

        $action = $input['action'] ?? '';

        switch ($action) {
            case 'mark_read':
                // Mark notification as read
                if (empty($input['notification_id'])) {
                    throw new Exception('Notification ID is required');
                }
                
                $userId = $input['user_id'] ?? null;
                if (!$userId && isset($_SESSION['user_id'])) {
                    $userId = $_SESSION['user_id'];
                }
                
                if (!$userId) {
                    throw new Exception('User ID is required');
                }
                
                $result = $notificationService->markAsRead($input['notification_id'], $userId);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Notification marked as read'
                ]);
                break;

            case 'mark_all_read':
                // Mark all notifications as read
                $userId = $input['user_id'] ?? null;
                if (!$userId && isset($_SESSION['user_id'])) {
                    $userId = $_SESSION['user_id'];
                }
                
                if (!$userId) {
                    throw new Exception('User ID is required');
                }
                
                $count = $notificationService->markAllAsRead($userId);
                
                echo json_encode([
                    'success' => true,
                    'message' => "Marked {$count} notifications as read"
                ]);
                break;

            case 'update_preferences':
                // Update notification preferences
                if (empty($input['preferences'])) {
                    throw new Exception('Preferences are required');
                }
                
                $userId = $input['user_id'] ?? null;
                if (!$userId && isset($_SESSION['user_id'])) {
                    $userId = $_SESSION['user_id'];
                }
                
                if (!$userId) {
                    throw new Exception('User ID is required');
                }
                
                $result = $notificationService->updateNotificationPreferences($userId, $input['preferences']);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Notification preferences updated successfully'
                ]);
                break;

            default:
                throw new Exception('Invalid action');
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Invalid JSON input');
        }

        if (empty($input['notification_id'])) {
            throw new Exception('Notification ID is required');
        }
        
        $userId = $input['user_id'] ?? null;
        if (!$userId && isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
        }
        
        if (!$userId) {
            throw new Exception('User ID is required');
        }
        
        $result = $notificationService->deleteNotification($input['notification_id'], $userId);
        
        echo json_encode([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);

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