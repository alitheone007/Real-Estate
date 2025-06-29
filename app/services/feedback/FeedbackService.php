<?php

namespace App\Services\Feedback;

use App\Core\Database;
use App\Core\Exceptions\DatabaseException;
use PDO;

class FeedbackService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create a new feedback ticket
     */
    public function createTicket($data)
    {
        try {
            $sql = "INSERT INTO feedback_tickets (
                        user_id, name, email, subject, message, category, 
                        priority, status
                    ) VALUES (
                        :user_id, :name, :email, :subject, :message, :category,
                        :priority, :status
                    )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'user_id' => $data['user_id'] ?? null,
                'name' => $data['name'],
                'email' => $data['email'],
                'subject' => $data['subject'],
                'message' => $data['message'],
                'category' => $data['category'] ?? 'general',
                'priority' => $data['priority'] ?? 'medium',
                'status' => 'open'
            ]);

            $ticketId = $this->db->lastInsertId();

            // Send notification to admin
            $this->notifyAdmin($ticketId, $data);

            return $ticketId;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to create feedback ticket: " . $e->getMessage());
        }
    }

    /**
     * Get all tickets with filters
     */
    public function getTickets($filters = [])
    {
        try {
            $where = "1=1";
            $params = [];

            if (!empty($filters['status'])) {
                $where .= " AND status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['priority'])) {
                $where .= " AND priority = :priority";
                $params['priority'] = $filters['priority'];
            }

            if (!empty($filters['category'])) {
                $where .= " AND category = :category";
                $params['category'] = $filters['category'];
            }

            if (!empty($filters['user_id'])) {
                $where .= " AND user_id = :user_id";
                $params['user_id'] = $filters['user_id'];
            }

            if (!empty($filters['assigned_to'])) {
                $where .= " AND assigned_to = :assigned_to";
                $params['assigned_to'] = $filters['assigned_to'];
            }

            $sql = "SELECT 
                        ft.*,
                        u.name as user_name,
                        u.email as user_email,
                        a.name as assigned_to_name
                    FROM feedback_tickets ft
                    LEFT JOIN users u ON ft.user_id = u.id
                    LEFT JOIN users a ON ft.assigned_to = a.id
                    WHERE {$where}
                    ORDER BY 
                        CASE ft.priority 
                            WHEN 'urgent' THEN 1 
                            WHEN 'high' THEN 2 
                            WHEN 'medium' THEN 3 
                            WHEN 'low' THEN 4 
                        END,
                        ft.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to get tickets: " . $e->getMessage());
        }
    }

    /**
     * Get ticket by ID with responses
     */
    public function getTicket($ticketId)
    {
        try {
            // Get ticket details
            $sql = "SELECT 
                        ft.*,
                        u.name as user_name,
                        u.email as user_email,
                        a.name as assigned_to_name
                    FROM feedback_tickets ft
                    LEFT JOIN users u ON ft.user_id = u.id
                    LEFT JOIN users a ON ft.assigned_to = a.id
                    WHERE ft.id = :ticket_id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['ticket_id' => $ticketId]);
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$ticket) {
                return null;
            }

            // Get responses
            $sql = "SELECT 
                        tr.*,
                        u.name as user_name,
                        u.email as user_email
                    FROM ticket_responses tr
                    LEFT JOIN users u ON tr.user_id = u.id
                    WHERE tr.ticket_id = :ticket_id
                    ORDER BY tr.created_at ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['ticket_id' => $ticketId]);
            $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $ticket['responses'] = $responses;

            return $ticket;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to get ticket: " . $e->getMessage());
        }
    }

    /**
     * Add response to ticket
     */
    public function addResponse($ticketId, $data)
    {
        try {
            $sql = "INSERT INTO ticket_responses (
                        ticket_id, user_id, message, is_internal
                    ) VALUES (
                        :ticket_id, :user_id, :message, :is_internal
                    )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'ticket_id' => $ticketId,
                'user_id' => $data['user_id'] ?? null,
                'message' => $data['message'],
                'is_internal' => $data['is_internal'] ?? false
            ]);

            $responseId = $this->db->lastInsertId();

            // Update ticket status if it's an admin response
            if ($data['user_id'] && !$data['is_internal']) {
                $this->updateTicketStatus($ticketId, 'in_progress');
            }

            // Notify user if it's an admin response
            if ($data['user_id'] && !$data['is_internal']) {
                $this->notifyUser($ticketId, $data['message']);
            }

            return $responseId;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to add response: " . $e->getMessage());
        }
    }

    /**
     * Update ticket status
     */
    public function updateTicketStatus($ticketId, $status, $resolutionNotes = null)
    {
        try {
            $sql = "UPDATE feedback_tickets SET 
                        status = :status,
                        resolution_notes = :resolution_notes,
                        resolved_at = CASE WHEN :status = 'resolved' THEN CURRENT_TIMESTAMP ELSE NULL END,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = :ticket_id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'ticket_id' => $ticketId,
                'status' => $status,
                'resolution_notes' => $resolutionNotes
            ]);

            return true;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to update ticket status: " . $e->getMessage());
        }
    }

    /**
     * Assign ticket to user
     */
    public function assignTicket($ticketId, $userId)
    {
        try {
            $sql = "UPDATE feedback_tickets SET 
                        assigned_to = :user_id,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = :ticket_id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'ticket_id' => $ticketId,
                'user_id' => $userId
            ]);

            return true;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to assign ticket: " . $e->getMessage());
        }
    }

    /**
     * Get ticket statistics
     */
    public function getTicketStats($filters = [])
    {
        try {
            $where = "1=1";
            $params = [];

            if (!empty($filters['date_from'])) {
                $where .= " AND created_at >= :date_from";
                $params['date_from'] = $filters['date_from'];
            }

            if (!empty($filters['date_to'])) {
                $where .= " AND created_at <= :date_to";
                $params['date_to'] = $filters['date_to'];
            }

            // Total tickets
            $sql = "SELECT COUNT(*) as total FROM feedback_tickets WHERE {$where}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Status breakdown
            $sql = "SELECT status, COUNT(*) as count 
                    FROM feedback_tickets 
                    WHERE {$where} 
                    GROUP BY status";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $statusBreakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Priority breakdown
            $sql = "SELECT priority, COUNT(*) as count 
                    FROM feedback_tickets 
                    WHERE {$where} 
                    GROUP BY priority";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $priorityBreakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Category breakdown
            $sql = "SELECT category, COUNT(*) as count 
                    FROM feedback_tickets 
                    WHERE {$where} 
                    GROUP BY category";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $categoryBreakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Average resolution time
            $sql = "SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_resolution_hours
                    FROM feedback_tickets 
                    WHERE {$where} AND status = 'resolved' AND resolved_at IS NOT NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $avgResolution = $stmt->fetch(PDO::FETCH_ASSOC)['avg_resolution_hours'];

            return [
                'total' => $total,
                'status_breakdown' => $statusBreakdown,
                'priority_breakdown' => $priorityBreakdown,
                'category_breakdown' => $categoryBreakdown,
                'avg_resolution_hours' => round($avgResolution, 2)
            ];
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to get ticket stats: " . $e->getMessage());
        }
    }

    /**
     * Search tickets
     */
    public function searchTickets($query, $filters = [])
    {
        try {
            $where = "1=1";
            $params = ['query' => "%{$query}%"];

            if (!empty($filters['status'])) {
                $where .= " AND status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['priority'])) {
                $where .= " AND priority = :priority";
                $params['priority'] = $filters['priority'];
            }

            $sql = "SELECT 
                        ft.*,
                        u.name as user_name,
                        u.email as user_email
                    FROM feedback_tickets ft
                    LEFT JOIN users u ON ft.user_id = u.id
                    WHERE {$where} AND (
                        ft.subject LIKE :query OR 
                        ft.message LIKE :query OR
                        u.name LIKE :query OR
                        u.email LIKE :query
                    )
                    ORDER BY ft.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to search tickets: " . $e->getMessage());
        }
    }

    /**
     * Add attachment to ticket
     */
    public function addAttachment($ticketId, $responseId, $fileData)
    {
        try {
            $sql = "INSERT INTO ticket_attachments (
                        ticket_id, response_id, file_name, file_path, 
                        file_size, mime_type
                    ) VALUES (
                        :ticket_id, :response_id, :file_name, :file_path,
                        :file_size, :mime_type
                    )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'ticket_id' => $ticketId,
                'response_id' => $responseId,
                'file_name' => $fileData['name'],
                'file_path' => $fileData['path'],
                'file_size' => $fileData['size'],
                'mime_type' => $fileData['mime_type']
            ]);

            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to add attachment: " . $e->getMessage());
        }
    }

    /**
     * Get ticket attachments
     */
    public function getAttachments($ticketId)
    {
        try {
            $sql = "SELECT * FROM ticket_attachments 
                    WHERE ticket_id = :ticket_id 
                    ORDER BY created_at ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['ticket_id' => $ticketId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to get attachments: " . $e->getMessage());
        }
    }

    /**
     * Notify admin about new ticket
     */
    private function notifyAdmin($ticketId, $data)
    {
        // This would integrate with your notification system
        // For now, just log it
        error_log("New feedback ticket #{$ticketId} from {$data['email']}: {$data['subject']}");
    }

    /**
     * Notify user about ticket response
     */
    private function notifyUser($ticketId, $message)
    {
        // This would integrate with your notification system
        // For now, just log it
        error_log("Response added to ticket #{$ticketId}: " . substr($message, 0, 100) . "...");
    }

    /**
     * Get tickets by user
     */
    public function getUserTickets($userId)
    {
        try {
            $sql = "SELECT * FROM feedback_tickets 
                    WHERE user_id = :user_id 
                    ORDER BY created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to get user tickets: " . $e->getMessage());
        }
    }

    /**
     * Get assigned tickets
     */
    public function getAssignedTickets($userId)
    {
        try {
            $sql = "SELECT 
                        ft.*,
                        u.name as user_name,
                        u.email as user_email
                    FROM feedback_tickets ft
                    LEFT JOIN users u ON ft.user_id = u.id
                    WHERE ft.assigned_to = :user_id
                    ORDER BY 
                        CASE ft.priority 
                            WHEN 'urgent' THEN 1 
                            WHEN 'high' THEN 2 
                            WHEN 'medium' THEN 3 
                            WHEN 'low' THEN 4 
                        END,
                        ft.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to get assigned tickets: " . $e->getMessage());
        }
    }
} 