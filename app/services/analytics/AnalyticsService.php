<?php

namespace App\Services\Analytics;

use App\Core\Database;
use App\Core\Exceptions\DatabaseException;
use PDO;

class AnalyticsService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Track user activity and IP analytics
     */
    public function trackActivity($data)
    {
        try {
            $sql = "INSERT INTO user_analytics (
                user_id, ip_address, country_code, city, region, timezone,
                user_agent, page_visited, click_count, referrer, device_type,
                browser, os
            ) VALUES (
                :user_id, :ip_address, :country_code, :city, :region, :timezone,
                :user_agent, :page_visited, :click_count, :referrer, :device_type,
                :browser, :os
            )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'user_id' => $data['user_id'] ?? null,
                'ip_address' => $data['ip_address'],
                'country_code' => $data['country_code'] ?? null,
                'city' => $data['city'] ?? null,
                'region' => $data['region'] ?? null,
                'timezone' => $data['timezone'] ?? null,
                'user_agent' => $data['user_agent'],
                'page_visited' => $data['page_visited'],
                'click_count' => $data['click_count'] ?? 1,
                'referrer' => $data['referrer'] ?? null,
                'device_type' => $data['device_type'] ?? 'desktop',
                'browser' => $data['browser'] ?? null,
                'os' => $data['os'] ?? null
            ]);

            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to track activity: " . $e->getMessage());
        }
    }

    /**
     * Get IP geolocation data
     */
    public function getIpLocation($ip)
    {
        try {
            // Check cache first
            $sql = "SELECT * FROM ip_locations WHERE ip_address = :ip AND cached_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['ip' => $ip]);
            $cached = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cached) {
                return $cached;
            }

            // Fetch from external API
            $location = $this->fetchIpLocation($ip);
            
            if ($location) {
                $this->cacheIpLocation($ip, $location);
                return $location;
            }

            return null;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to get IP location: " . $e->getMessage());
        }
    }

    /**
     * Cache IP location data
     */
    private function cacheIpLocation($ip, $location)
    {
        try {
            $sql = "INSERT INTO ip_locations (
                ip_address, country_code, country_name, city, region, timezone,
                latitude, longitude, isp
            ) VALUES (
                :ip, :country_code, :country_name, :city, :region, :timezone,
                :latitude, :longitude, :isp
            ) ON DUPLICATE KEY UPDATE
                country_code = VALUES(country_code),
                country_name = VALUES(country_name),
                city = VALUES(city),
                region = VALUES(region),
                timezone = VALUES(timezone),
                latitude = VALUES(latitude),
                longitude = VALUES(longitude),
                isp = VALUES(isp),
                cached_at = CURRENT_TIMESTAMP";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'ip' => $ip,
                'country_code' => $location['country_code'] ?? null,
                'country_name' => $location['country_name'] ?? null,
                'city' => $location['city'] ?? null,
                'region' => $location['region'] ?? null,
                'timezone' => $location['timezone'] ?? null,
                'latitude' => $location['latitude'] ?? null,
                'longitude' => $location['longitude'] ?? null,
                'isp' => $location['isp'] ?? null
            ]);
        } catch (\PDOException $e) {
            // Log error but don't throw - caching failure shouldn't break the app
            error_log("Failed to cache IP location: " . $e->getMessage());
        }
    }

    /**
     * Fetch IP location from external API
     */
    private function fetchIpLocation($ip)
    {
        // Use ipapi.co for geolocation (free tier available)
        $url = "http://ip-api.com/json/{$ip}";
        
        try {
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            
            if ($data && $data['status'] === 'success') {
                return [
                    'country_code' => $data['countryCode'],
                    'country_name' => $data['country'],
                    'city' => $data['city'],
                    'region' => $data['regionName'],
                    'timezone' => $data['timezone'],
                    'latitude' => $data['lat'],
                    'longitude' => $data['lon'],
                    'isp' => $data['isp']
                ];
            }
        } catch (\Exception $e) {
            error_log("Failed to fetch IP location: " . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Track property view
     */
    public function trackPropertyView($propertyId, $userId = null, $ip = null, $duration = 0)
    {
        try {
            $sql = "INSERT INTO property_views (property_id, user_id, ip_address, session_duration) 
                    VALUES (:property_id, :user_id, :ip_address, :duration)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'property_id' => $propertyId,
                'user_id' => $userId,
                'ip_address' => $ip,
                'duration' => $duration
            ]);

            // Update property view count
            $this->updatePropertyViewCount($propertyId);
            
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to track property view: " . $e->getMessage());
        }
    }

    /**
     * Update property view count
     */
    private function updatePropertyViewCount($propertyId)
    {
        try {
            $sql = "UPDATE properties SET 
                    views_count = views_count + 1,
                    last_viewed = CURRENT_TIMESTAMP 
                    WHERE id = :property_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['property_id' => $propertyId]);
        } catch (\PDOException $e) {
            error_log("Failed to update property view count: " . $e->getMessage());
        }
    }

    /**
     * Get analytics dashboard data
     */
    public function getDashboardAnalytics($filters = [])
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

            // Total page views
            $sql = "SELECT COUNT(*) as total_views FROM user_analytics WHERE {$where}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $totalViews = $stmt->fetch(PDO::FETCH_ASSOC)['total_views'];

            // Unique visitors
            $sql = "SELECT COUNT(DISTINCT ip_address) as unique_visitors FROM user_analytics WHERE {$where}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $uniqueVisitors = $stmt->fetch(PDO::FETCH_ASSOC)['unique_visitors'];

            // Top pages
            $sql = "SELECT page_visited, COUNT(*) as views 
                    FROM user_analytics 
                    WHERE {$where} 
                    GROUP BY page_visited 
                    ORDER BY views DESC 
                    LIMIT 10";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $topPages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Device breakdown
            $sql = "SELECT device_type, COUNT(*) as count 
                    FROM user_analytics 
                    WHERE {$where} 
                    GROUP BY device_type";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $deviceBreakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Country breakdown
            $sql = "SELECT country_code, COUNT(*) as count 
                    FROM user_analytics 
                    WHERE {$where} AND country_code IS NOT NULL 
                    GROUP BY country_code 
                    ORDER BY count DESC 
                    LIMIT 10";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $countryBreakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'total_views' => $totalViews,
                'unique_visitors' => $uniqueVisitors,
                'top_pages' => $topPages,
                'device_breakdown' => $deviceBreakdown,
                'country_breakdown' => $countryBreakdown
            ];
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to get analytics: " . $e->getMessage());
        }
    }

    /**
     * Get user activity summary
     */
    public function getUserActivitySummary($userId)
    {
        try {
            $sql = "SELECT 
                        last_login,
                        last_active,
                        session_duration,
                        pages_visited,
                        actions_performed
                    FROM user_activity 
                    WHERE user_id = :user_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to get user activity: " . $e->getMessage());
        }
    }

    /**
     * Update user activity
     */
    public function updateUserActivity($userId, $data)
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
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to update user activity: " . $e->getMessage());
        }
    }
} 