<?php

namespace App\Services\Timezone;

use App\Core\Database;
use App\Core\Exceptions\DatabaseException;
use DateTime;
use DateTimeZone;
use PDO;

class TimezoneService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get marketplace status for a specific country
     */
    public function getMarketplaceStatus($countryId)
    {
        try {
            $sql = "SELECT 
                        ms.*,
                        c.name as country_name,
                        c.timezone as country_timezone,
                        c.currency_code,
                        c.currency_symbol
                    FROM marketplace_status ms
                    JOIN countries c ON ms.country_id = c.id
                    WHERE ms.country_id = :country_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['country_id' => $countryId]);
            $status = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$status) {
                // Create default status if not exists
                $status = $this->createDefaultMarketplaceStatus($countryId);
            }

            return $status;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to get marketplace status: " . $e->getMessage());
        }
    }

    /**
     * Get marketplace status by IP address
     */
    public function getMarketplaceStatusByIp($ip)
    {
        try {
            // Get country from IP
            $countryCode = $this->getCountryFromIp($ip);
            
            if (!$countryCode) {
                return $this->getDefaultMarketplaceStatus();
            }

            $sql = "SELECT 
                        ms.*,
                        c.name as country_name,
                        c.timezone as country_timezone,
                        c.currency_code,
                        c.currency_symbol
                    FROM marketplace_status ms
                    JOIN countries c ON ms.country_id = c.id
                    WHERE c.code = :country_code";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['country_code' => $countryCode]);
            $status = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$status) {
                return $this->getDefaultMarketplaceStatus();
            }

            return $status;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to get marketplace status by IP: " . $e->getMessage());
        }
    }

    /**
     * Update marketplace status for all countries
     */
    public function updateAllMarketplaceStatus()
    {
        try {
            $sql = "SELECT id, timezone FROM countries WHERE is_active = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($countries as $country) {
                $this->updateMarketplaceStatus($country['id']);
            }

            return true;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to update all marketplace status: " . $e->getMessage());
        }
    }

    /**
     * Update marketplace status for a specific country
     */
    public function updateMarketplaceStatus($countryId)
    {
        try {
            // Get country timezone
            $sql = "SELECT timezone FROM countries WHERE id = :country_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['country_id' => $countryId]);
            $country = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$country || !$country['timezone']) {
                return false;
            }

            // Get operational hours
            $sql = "SELECT * FROM country_operational_hours WHERE country_id = :country_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['country_id' => $countryId]);
            $operationalHours = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$operationalHours) {
                return false;
            }

            // Get current time in country timezone
            $timezone = new DateTimeZone($country['timezone']);
            $currentTime = new DateTime('now', $timezone);
            $currentTimeLocal = $currentTime->format('H:i:s');
            $currentDayOfWeek = $currentTime->format('N'); // 1 (Monday) to 7 (Sunday)

            // Check if marketplace is operational
            $isOperational = $this->isOperationalNow($operationalHours, $currentTimeLocal, $currentDayOfWeek);
            
            // Determine status and message
            if ($isOperational) {
                $status = 'operational';
                $message = 'Marketplace is currently operational';
                $nextOperationalTime = null;
            } else {
                $status = 'non-operational';
                $nextOperationalTime = $this->getNextOperationalTime($operationalHours, $currentTime, $timezone);
                $message = 'Marketplace is currently closed. Opens at ' . $operationalHours['operational_start'];
            }

            // Update or insert marketplace status
            $sql = "INSERT INTO marketplace_status (
                        country_id, current_status, current_time_local, 
                        next_operational_time, status_message, last_updated
                    ) VALUES (
                        :country_id, :status, :current_time, 
                        :next_operational_time, :message, CURRENT_TIMESTAMP
                    ) ON DUPLICATE KEY UPDATE
                        current_status = VALUES(current_status),
                        current_time_local = VALUES(current_time_local),
                        next_operational_time = VALUES(next_operational_time),
                        status_message = VALUES(status_message),
                        last_updated = CURRENT_TIMESTAMP";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'country_id' => $countryId,
                'status' => $status,
                'current_time' => $currentTimeLocal,
                'next_operational_time' => $nextOperationalTime,
                'message' => $message
            ]);

            return true;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to update marketplace status: " . $e->getMessage());
        }
    }

    /**
     * Check if marketplace is operational now
     */
    private function isOperationalNow($operationalHours, $currentTime, $dayOfWeek)
    {
        // Check if it's weekend and weekend operations are disabled
        if ($dayOfWeek >= 6 && !$operationalHours['weekend_operational']) {
            return false;
        }

        // Check if current time is within operational hours
        $startTime = $operationalHours['operational_start'];
        $endTime = $operationalHours['operational_end'];

        return $currentTime >= $startTime && $currentTime <= $endTime;
    }

    /**
     * Get next operational time
     */
    private function getNextOperationalTime($operationalHours, $currentTime, $timezone)
    {
        $nextTime = clone $currentTime;
        
        // If current time is after operational hours, move to next day
        if ($currentTime->format('H:i:s') > $operationalHours['operational_end']) {
            $nextTime->add(new \DateInterval('P1D'));
        }

        // Set to operational start time
        $nextTime->setTime(
            (int)substr($operationalHours['operational_start'], 0, 2),
            (int)substr($operationalHours['operational_start'], 3, 2),
            (int)substr($operationalHours['operational_start'], 6, 2)
        );

        return $nextTime->format('Y-m-d H:i:s');
    }

    /**
     * Get country from IP address
     */
    private function getCountryFromIp($ip)
    {
        try {
            // Check cache first
            $sql = "SELECT country_code FROM ip_locations 
                    WHERE ip_address = :ip AND cached_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['ip' => $ip]);
            $cached = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cached) {
                return $cached['country_code'];
            }

            // Fetch from external API
            $url = "http://ip-api.com/json/{$ip}";
            $response = file_get_contents($url);
            $data = json_decode($response, true);

            if ($data && $data['status'] === 'success') {
                return $data['countryCode'];
            }

            return null;
        } catch (\Exception $e) {
            error_log("Failed to get country from IP: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create default marketplace status
     */
    private function createDefaultMarketplaceStatus($countryId)
    {
        try {
            $sql = "INSERT INTO marketplace_status (
                        country_id, current_status, current_time_local, 
                        next_operational_time, status_message
                    ) VALUES (
                        :country_id, 'operational', '12:00:00', 
                        NULL, 'Marketplace is currently operational'
                    )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['country_id' => $countryId]);

            return $this->getMarketplaceStatus($countryId);
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to create default marketplace status: " . $e->getMessage());
        }
    }

    /**
     * Get default marketplace status
     */
    private function getDefaultMarketplaceStatus()
    {
        return [
            'current_status' => 'operational',
            'current_time_local' => '12:00:00',
            'next_operational_time' => null,
            'status_message' => 'Marketplace is currently operational',
            'country_name' => 'Unknown',
            'country_timezone' => 'UTC',
            'currency_code' => 'USD',
            'currency_symbol' => '$'
        ];
    }

    /**
     * Get timezone offset for a country
     */
    public function getTimezoneOffset($countryCode)
    {
        try {
            $sql = "SELECT timezone FROM countries WHERE code = :country_code";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['country_code' => $countryCode]);
            $country = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$country || !$country['timezone']) {
                return 0;
            }

            $timezone = new DateTimeZone($country['timezone']);
            $dateTime = new DateTime('now', $timezone);
            
            return $dateTime->getOffset();
        } catch (\Exception $e) {
            error_log("Failed to get timezone offset: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all operational hours
     */
    public function getAllOperationalHours()
    {
        try {
            $sql = "SELECT 
                        coh.*,
                        c.name as country_name,
                        c.code as country_code,
                        c.timezone
                    FROM country_operational_hours coh
                    JOIN countries c ON coh.country_id = c.id
                    ORDER BY c.name";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to get operational hours: " . $e->getMessage());
        }
    }

    /**
     * Update operational hours for a country
     */
    public function updateOperationalHours($countryId, $data)
    {
        try {
            $sql = "INSERT INTO country_operational_hours (
                        country_id, timezone, operational_start, operational_end,
                        is_operational, weekend_operational, holiday_operational
                    ) VALUES (
                        :country_id, :timezone, :operational_start, :operational_end,
                        :is_operational, :weekend_operational, :holiday_operational
                    ) ON DUPLICATE KEY UPDATE
                        timezone = VALUES(timezone),
                        operational_start = VALUES(operational_start),
                        operational_end = VALUES(operational_end),
                        is_operational = VALUES(is_operational),
                        weekend_operational = VALUES(weekend_operational),
                        holiday_operational = VALUES(holiday_operational)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'country_id' => $countryId,
                'timezone' => $data['timezone'],
                'operational_start' => $data['operational_start'],
                'operational_end' => $data['operational_end'],
                'is_operational' => $data['is_operational'] ?? true,
                'weekend_operational' => $data['weekend_operational'] ?? false,
                'holiday_operational' => $data['holiday_operational'] ?? true
            ]);

            // Update marketplace status after changing operational hours
            $this->updateMarketplaceStatus($countryId);

            return true;
        } catch (\PDOException $e) {
            throw new DatabaseException("Failed to update operational hours: " . $e->getMessage());
        }
    }
} 