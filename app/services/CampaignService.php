<?php
namespace App\Services;

use App\Core\Database;
use PDO;

class CampaignService {
    private $db;

    public function __construct() {
        $this->db = DatabaseConfig::getInstance()->getConnection();
    }

    // Create or update a campaign
    public function saveCampaign($data) {
        if (isset($data['id'])) {
            // Update
            $sql = "UPDATE campaigns SET title = :title, description = :description, discount_type = :discount_type, discount_value = :discount_value, start_date = :start_date, end_date = :end_date, creator_role = :creator_role, status = :status WHERE id = :id";
        } else {
            // Insert
            $sql = "INSERT INTO campaigns (title, description, discount_type, discount_value, start_date, end_date, created_by, creator_role, status) VALUES (:title, :description, :discount_type, :discount_value, :start_date, :end_date, :created_by, :creator_role, :status)";
        }
        $stmt = $this->db->prepare($sql);
        $params = [
            ':title' => $data['title'],
            ':description' => $data['description'] ?? null,
            ':discount_type' => $data['discount_type'] ?? 'percentage',
            ':discount_value' => $data['discount_value'] ?? 0,
            ':start_date' => $data['start_date'] ?? null,
            ':end_date' => $data['end_date'] ?? null,
            ':creator_role' => $data['creator_role'] ?? 'influencer',
            ':status' => $data['status'] ?? 'active',
        ];
        if (isset($data['id'])) {
            $params[':id'] = $data['id'];
        } else {
            $params[':created_by'] = $data['created_by'];
        }
        $stmt->execute($params);
        return isset($data['id']) ? $data['id'] : $this->db->lastInsertId();
    }

    // Assign campaign to properties
    public function assignProperties($campaign_id, $property_ids) {
        $sql = "INSERT IGNORE INTO campaign_properties (campaign_id, property_id) VALUES (:campaign_id, :property_id)";
        $stmt = $this->db->prepare($sql);
        foreach ($property_ids as $pid) {
            $stmt->execute([':campaign_id' => $campaign_id, ':property_id' => $pid]);
        }
        return true;
    }

    // List campaigns (optionally filter by role, status, property, etc.)
    public function listCampaigns($filters = []) {
        $where = [];
        $params = [];
        if (!empty($filters['role'])) {
            $where[] = 'creator_role = :role';
            $params[':role'] = $filters['role'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['property_id'])) {
            $where[] = 'id IN (SELECT campaign_id FROM campaign_properties WHERE property_id = :property_id)';
            $params[':property_id'] = $filters['property_id'];
        }
        $sql = 'SELECT * FROM campaigns';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get campaign by ID
    public function getCampaign($id) {
        $stmt = $this->db->prepare('SELECT * FROM campaigns WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get properties for a campaign
    public function getCampaignProperties($campaign_id) {
        $stmt = $this->db->prepare('SELECT property_id FROM campaign_properties WHERE campaign_id = ?');
        $stmt->execute([$campaign_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Log campaign event
    public function logEvent($data) {
        $sql = "INSERT INTO campaign_events (campaign_id, influencer_id, builder_id, user_id, property_id, event_type) VALUES (:campaign_id, :influencer_id, :builder_id, :user_id, :property_id, :event_type)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':campaign_id' => $data['campaign_id'],
            ':influencer_id' => $data['influencer_id'] ?? null,
            ':builder_id' => $data['builder_id'] ?? null,
            ':user_id' => $data['user_id'] ?? null,
            ':property_id' => $data['property_id'] ?? null,
            ':event_type' => $data['event_type'] ?? 'click',
        ]);
        return $this->db->lastInsertId();
    }

    // List campaign events
    public function listEvents($filters = []) {
        $where = [];
        $params = [];
        if (!empty($filters['campaign_id'])) {
            $where[] = 'campaign_id = :campaign_id';
            $params[':campaign_id'] = $filters['campaign_id'];
        }
        if (!empty($filters['user_id'])) {
            $where[] = 'user_id = :user_id';
            $params[':user_id'] = $filters['user_id'];
        }
        if (!empty($filters['property_id'])) {
            $where[] = 'property_id = :property_id';
            $params[':property_id'] = $filters['property_id'];
        }
        $sql = 'SELECT * FROM campaign_events';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 