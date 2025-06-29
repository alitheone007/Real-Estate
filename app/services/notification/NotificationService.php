<?php

namespace App\Services\Notification;

use App\Core\Database;
use App\Core\Exceptions\DatabaseException;
use PDO;

class NotificationService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Create a new notification
     */
    public function createNotification($data)
    {
        try {
            $sql = "INSERT INTO notifications (
                        user_id, title, message, type, action_url
                    ) VALUES (
                        :user_id, :title, :message, :type, :action_url
                    )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'user_id' => $data['user_id'],
                'title' => $data['title'],
                'message' => $data['message'],
                'type' => $data['type'] ?? 'info',
                'action_url' => $data['action_url'] ?? null
            ]);

            $notificationId = $this->db->lastInsertId();

            // Send push notification if user has subscriptions
            $this->sendPushNotification($data['user_id'], $data);

            // Send WhatsApp notification if enabled
            if ($data['send_whatsapp'] ?? false) {
                $this->sendWhatsAppNotification($data['user_id'], $data);
            }

            return $notificationId;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to create notification: " . $e->getMessage());
        }
    }

    /**
     * Get user notifications
     */
    public function getUserNotifications($userId, $limit = 50, $offset = 0)
    {
        try {
            $sql = "SELECT * FROM notifications 
                    WHERE user_id = :user_id 
                    ORDER BY sent_at DESC 
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to get user notifications: " . $e->getMessage());
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId)
    {
        try {
            $sql = "UPDATE notifications SET 
                        is_read = TRUE,
                        read_at = CURRENT_TIMESTAMP
                    WHERE id = :notification_id AND user_id = :user_id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'notification_id' => $notificationId,
                'user_id' => $userId
            ]);

            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to mark notification as read: " . $e->getMessage());
        }
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead($userId)
    {
        try {
            $sql = "UPDATE notifications SET 
                        is_read = TRUE,
                        read_at = CURRENT_TIMESTAMP
                    WHERE user_id = :user_id AND is_read = FALSE";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);

            return $stmt->rowCount();
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to mark all notifications as read: " . $e->getMessage());
        }
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount($userId)
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM notifications 
                    WHERE user_id = :user_id AND is_read = FALSE";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);

            return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to get unread count: " . $e->getMessage());
        }
    }

    /**
     * Delete notification
     */
    public function deleteNotification($notificationId, $userId)
    {
        try {
            $sql = "DELETE FROM notifications 
                    WHERE id = :notification_id AND user_id = :user_id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'notification_id' => $notificationId,
                'user_id' => $userId
            ]);

            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to delete notification: " . $e->getMessage());
        }
    }

    /**
     * Register push subscription
     */
    public function registerPushSubscription($userId, $subscriptionData)
    {
        try {
            $sql = "INSERT INTO push_subscriptions (
                        user_id, endpoint, p256dh_key, auth_token, device_info
                    ) VALUES (
                        :user_id, :endpoint, :p256dh_key, :auth_token, :device_info
                    ) ON DUPLICATE KEY UPDATE
                        p256dh_key = VALUES(p256dh_key),
                        auth_token = VALUES(auth_token),
                        device_info = VALUES(device_info),
                        is_active = TRUE";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'user_id' => $userId,
                'endpoint' => $subscriptionData['endpoint'],
                'p256dh_key' => $subscriptionData['keys']['p256dh'],
                'auth_token' => $subscriptionData['keys']['auth'],
                'device_info' => json_encode($subscriptionData['device_info'] ?? [])
            ]);

            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to register push subscription: " . $e->getMessage());
        }
    }

    /**
     * Unregister push subscription
     */
    public function unregisterPushSubscription($userId, $endpoint)
    {
        try {
            $sql = "UPDATE push_subscriptions SET 
                        is_active = FALSE 
                    WHERE user_id = :user_id AND endpoint = :endpoint";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'user_id' => $userId,
                'endpoint' => $endpoint
            ]);

            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to unregister push subscription: " . $e->getMessage());
        }
    }

    /**
     * Get user push subscriptions
     */
    public function getUserPushSubscriptions($userId)
    {
        try {
            $sql = "SELECT * FROM push_subscriptions 
                    WHERE user_id = :user_id AND is_active = TRUE";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to get push subscriptions: " . $e->getMessage());
        }
    }

    /**
     * Send push notification
     */
    private function sendPushNotification($userId, $data)
    {
        try {
            $subscriptions = $this->getUserPushSubscriptions($userId);
            
            foreach ($subscriptions as $subscription) {
                $this->sendWebPushNotification($subscription, $data);
            }
        } catch (\Exception $e) {
            error_log("Failed to send push notification: " . $e->getMessage());
        }
    }

    /**
     * Send web push notification
     */
    private function sendWebPushNotification($subscription, $data)
    {
        // This would integrate with a web push service like Firebase Cloud Messaging
        // For now, just log the attempt
        error_log("Web push notification to {$subscription['endpoint']}: {$data['title']}");
    }

    /**
     * WhatsApp Integration
     */
    public function registerWhatsAppIntegration($userId, $phoneNumber)
    {
        try {
            $sql = "INSERT INTO whatsapp_integrations (
                        user_id, phone_number
                    ) VALUES (
                        :user_id, :phone_number
                    ) ON DUPLICATE KEY UPDATE
                        phone_number = VALUES(phone_number),
                        opt_in_date = CURRENT_TIMESTAMP";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'user_id' => $userId,
                'phone_number' => $phoneNumber
            ]);

            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to register WhatsApp integration: " . $e->getMessage());
        }
    }

    /**
     * Verify WhatsApp number
     */
    public function verifyWhatsAppNumber($userId, $verificationCode)
    {
        try {
            // This would integrate with WhatsApp Business API
            // For now, just mark as verified
            $sql = "UPDATE whatsapp_integrations SET 
                        is_verified = TRUE,
                        last_sync = CURRENT_TIMESTAMP
                    WHERE user_id = :user_id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);

            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to verify WhatsApp number: " . $e->getMessage());
        }
    }

    /**
     * Send WhatsApp notification
     */
    private function sendWhatsAppNotification($userId, $data)
    {
        try {
            $sql = "SELECT * FROM whatsapp_integrations 
                    WHERE user_id = :user_id AND is_verified = TRUE";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            $integration = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($integration) {
                // This would integrate with WhatsApp Business API
                // For now, just log the attempt
                error_log("WhatsApp notification to {$integration['phone_number']}: {$data['message']}");
            }
        } catch (\Exception $e) {
            error_log("Failed to send WhatsApp notification: " . $e->getMessage());
        }
    }

    /**
     * Get WhatsApp integration status
     */
    public function getWhatsAppStatus($userId)
    {
        try {
            $sql = "SELECT * FROM whatsapp_integrations WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to get WhatsApp status: " . $e->getMessage());
        }
    }

    /**
     * User Activity Tracking
     */
    public function trackUserActivity($userId, $data)
    {
        try {
            $sql = "INSERT INTO user_activity (
                        user_id, last_login, last_active, session_duration,
                        pages_visited, actions_performed
                    ) VALUES (
                        :user_id, :last_login, :last_active, :session_duration,
                        :pages_visited, :actions_performed
                    ) ON DUPLICATE KEY UPDATE
                        last_login = VALUES(last_login),
                        last_active = VALUES(last_active),
                        session_duration = VALUES(session_duration),
                        pages_visited = VALUES(pages_visited),
                        actions_performed = VALUES(actions_performed)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'user_id' => $userId,
                'last_login' => $data['last_login'] ?? null,
                'last_active' => $data['last_active'] ?? date('Y-m-d H:i:s'),
                'session_duration' => $data['session_duration'] ?? 0,
                'pages_visited' => $data['pages_visited'] ?? 0,
                'actions_performed' => $data['actions_performed'] ?? 0
            ]);

            // Update user's last_active timestamp
            $this->updateUserLastActive($userId);

            return true;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to track user activity: " . $e->getMessage());
        }
    }

    /**
     * Update user last active
     */
    private function updateUserLastActive($userId)
    {
        try {
            $sql = "UPDATE users SET last_active = CURRENT_TIMESTAMP WHERE id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
        } catch (\PDOException $e) {
            error_log("Failed to update user last active: " . $e->getMessage());
        }
    }

    /**
     * Check for inactive users and send notifications
     */
    public function checkInactiveUsers()
    {
        try {
            // Get users inactive for 30 days
            $sql = "SELECT u.id, u.name, u.email, ua.last_active
                    FROM users u
                    LEFT JOIN user_activity ua ON u.id = ua.user_id
                    WHERE u.is_active = TRUE 
                    AND (ua.last_active IS NULL OR ua.last_active < DATE_SUB(NOW(), INTERVAL 30 DAY))
                    AND u.id NOT IN (
                        SELECT user_id FROM inactivity_notifications 
                        WHERE notification_type = '30_days' 
                        AND sent_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
                    )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $inactiveUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($inactiveUsers as $user) {
                $this->sendInactivityNotification($user['id'], '30_days');
            }

            return count($inactiveUsers);
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to check inactive users: " . $e->getMessage());
        }
    }

    /**
     * Send inactivity notification
     */
    private function sendInactivityNotification($userId, $type)
    {
        try {
            // Create notification record
            $sql = "INSERT INTO inactivity_notifications (
                        user_id, notification_type, notification_method
                    ) VALUES (
                        :user_id, :type, 'email'
                    )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'user_id' => $userId,
                'type' => $type
            ]);

            // Send email notification
            $this->sendInactivityEmail($userId, $type);

            // Send WhatsApp notification if enabled
            $this->sendInactivityWhatsApp($userId, $type);

        } catch (\PDOException $e) {
            error_log("Failed to send inactivity notification: " . $e->getMessage());
        }
    }

    /**
     * Send inactivity email
     */
    private function sendInactivityEmail($userId, $type)
    {
        // This would integrate with your email service
        // For now, just log the attempt
        error_log("Inactivity email notification to user {$userId} for type {$type}");
    }

    /**
     * Send inactivity WhatsApp
     */
    private function sendInactivityWhatsApp($userId, $type)
    {
        try {
            $sql = "SELECT wi.* FROM whatsapp_integrations wi
                    JOIN users u ON wi.user_id = u.id
                    WHERE wi.user_id = :user_id AND wi.is_verified = TRUE";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            $integration = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($integration) {
                // This would integrate with WhatsApp Business API
                error_log("Inactivity WhatsApp notification to {$integration['phone_number']} for type {$type}");
            }
        } catch (\Exception $e) {
            error_log("Failed to send inactivity WhatsApp: " . $e->getMessage());
        }
    }

    /**
     * Get notification preferences
     */
    public function getNotificationPreferences($userId)
    {
        try {
            $sql = "SELECT notification_preferences FROM users WHERE id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && $result['notification_preferences']) {
                return json_decode($result['notification_preferences'], true);
            }

            return $this->getDefaultPreferences();
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to get notification preferences: " . $e->getMessage());
        }
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences($userId, $preferences)
    {
        try {
            $sql = "UPDATE users SET 
                        notification_preferences = :preferences
                    WHERE id = :user_id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'user_id' => $userId,
                'preferences' => json_encode($preferences)
            ]);

            return true;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to update notification preferences: " . $e->getMessage());
        }
    }

    /**
     * Get default notification preferences
     */
    private function getDefaultPreferences()
    {
        return [
            'email' => [
                'marketing' => true,
                'property_updates' => true,
                'lead_notifications' => true,
                'system_notifications' => true
            ],
            'push' => [
                'marketing' => false,
                'property_updates' => true,
                'lead_notifications' => true,
                'system_notifications' => true
            ],
            'whatsapp' => [
                'marketing' => false,
                'property_updates' => false,
                'lead_notifications' => true,
                'system_notifications' => false
            ]
        ];
    }
} 