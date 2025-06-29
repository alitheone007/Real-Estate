<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../includes/init.php';

use App\Services\Feedback\FeedbackService;

try {
    $feedbackService = new FeedbackService();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $data = $_POST;
        }
        $user_id = isset($data['user_id']) ? intval($data['user_id']) : null;
        $name = isset($data['name']) ? $data['name'] : '';
        $email = isset($data['email']) ? $data['email'] : '';
        $subject = isset($data['subject']) ? $data['subject'] : '';
        $message = isset($data['message']) ? $data['message'] : '';
        $category = isset($data['category']) ? $data['category'] : 'general';
        $priority = isset($data['priority']) ? $data['priority'] : 'medium';
        try {
            $stmt = $db->prepare('INSERT INTO feedback_tickets (user_id, name, email, subject, message, category, priority) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$user_id, $name, $email, $subject, $message, $category, $priority]);
            echo json_encode(['success' => true, 'message' => 'Ticket created']);
            if ($user_id) {
                log_action("[FeedbackTicket] user_id={$user_id}, subject={$subject}");
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to create ticket', 'error' => $e->getMessage()]);
        }
        exit();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
        try {
            if ($user_id) {
                $stmt = $db->prepare('SELECT * FROM feedback_tickets WHERE user_id = ? ORDER BY created_at DESC');
                $stmt->execute([$user_id]);
            } else {
                $stmt = $db->query('SELECT * FROM feedback_tickets ORDER BY created_at DESC');
            }
            $tickets = $stmt->fetchAll();
            echo json_encode(['success' => true, 'tickets' => $tickets]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to fetch tickets', 'error' => $e->getMessage()]);
        }
        exit();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['ticket_id'])) {
            throw new Exception('Ticket ID is required');
        }

        $ticketId = $input['ticket_id'];
        $action = $input['action'] ?? '';

        switch ($action) {
            case 'add_response':
                if (empty($input['message'])) {
                    throw new Exception('Message is required');
                }
                
                $responseData = [
                    'user_id' => $_SESSION['user_id'] ?? null,
                    'message' => $input['message'],
                    'is_internal' => $input['is_internal'] ?? false
                ];
                
                $responseId = $feedbackService->addResponse($ticketId, $responseData);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Response added successfully',
                    'response_id' => $responseId
                ]);
                break;

            case 'update_status':
                if (empty($input['status'])) {
                    throw new Exception('Status is required');
                }
                
                $resolutionNotes = $input['resolution_notes'] ?? null;
                $feedbackService->updateTicketStatus($ticketId, $input['status'], $resolutionNotes);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Ticket status updated successfully'
                ]);
                break;

            case 'assign':
                if (empty($input['user_id'])) {
                    throw new Exception('User ID is required');
                }
                
                $feedbackService->assignTicket($ticketId, $input['user_id']);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Ticket assigned successfully'
                ]);
                break;

            default:
                throw new Exception('Invalid action');
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