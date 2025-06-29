<?php
// Real-Estate/app/config/services.config.php

// Database Configuration
require_once __DIR__ . '/database/db.config.php';
require_once __DIR__ . '/../core/Database.php';

// Service Class Definitions
class ServiceRegistry {
    private static $services = [];
    private static $instance = null;

    // Singleton Pattern
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Service Configuration
    private function __construct() {
        // Database Service
        self::$services['database'] = new Database();

        // Property Services
        self::$services['propertyService'] = [
            'class' => 'PropertyService',
            'path' => __DIR__ . '/../services/property/PropertyService.php',
            'config' => [
                'image_upload_path' => '/assets/uploads/properties/',
                'max_file_size' => 5242880, // 5MB
                'allowed_types' => ['jpg', 'jpeg', 'png']
            ]
        ];

        // Lead Services
        self::$services['leadService'] = [
            'class' => 'LeadService',
            'path' => __DIR__ . '/../services/leads/LeadService.php',
            'config' => [
                'max_leads_per_page' => 50,
                'lead_statuses' => [
                    'new',
                    'contacted',
                    'qualified',
                    'proposal',
                    'negotiation',
                    'closed_won',
                    'closed_lost'
                ]
            ]
        ];

        // Influencer Services
        self::$services['influencerService'] = [
            'class' => 'InfluencerService',
            'path' => __DIR__ . '/../services/influencer/InfluencerService.php',
            'config' => [
                'commission_rate' => 0.05,
                'payment_methods' => ['bank_transfer', 'paypal', 'stripe'],
                'verification_required' => true
            ]
        ];

        // Authentication Service
        self::$services['authService'] = [
            'class' => 'AuthService',
            'path' => __DIR__ . '/../components/auth/AuthService.php',
            'config' => [
                'session_timeout' => 3600, // 1 hour
                'max_login_attempts' => 5,
                'lockout_time' => 900, // 15 minutes
                'password_policy' => [
                    'min_length' => 8,
                    'require_uppercase' => true,
                    'require_number' => true,
                    'require_special_char' => true
                ]
            ]
        ];

        // Dashboard Service
        self::$services['dashboardService'] = [
            'class' => 'DashboardService',
            'path' => __DIR__ . '/../components/dashboard/DashboardService.php',
            'config' => [
                'cache_timeout' => 300, // 5 minutes
                'stats_refresh_interval' => 1800, // 30 minutes
                'chart_defaults' => [
                    'type' => 'line',
                    'period' => 'monthly'
                ]
            ]
        ];

        // Chart Service
        self::$services['chartService'] = [
            'class' => 'ChartService',
            'path' => __DIR__ . '/../components/charts/ChartService.php',
            'config' => [
                'default_chart_type' => 'bar',
                'color_scheme' => [
                    'primary' => '#4e73df',
                    'success' => '#1cc88a',
                    'info' => '#36b9cc',
                    'warning' => '#f6c23e',
                    'danger' => '#e74a3b'
                ]
            ]
        ];

        // Pipeline Service
        self::$services['pipelineService'] = [
            'class' => 'PipelineService',
            'path' => __DIR__ . '/../components/leads-pipeline/PipelineService.php',
            'config' => [
                'stages' => [
                    'new' => ['order' => 1, 'color' => '#36b9cc'],
                    'contacted' => ['order' => 2, 'color' => '#4e73df'],
                    'qualified' => ['order' => 3, 'color' => '#1cc88a'],
                    'proposal' => ['order' => 4, 'color' => '#f6c23e'],
                    'negotiation' => ['order' => 5, 'color' => '#e74a3b'],
                    'closed' => ['order' => 6, 'color' => '#858796']
                ],
                'drag_drop_enabled' => true
            ]
        ];

        // Email Service
        self::$services['emailService'] = [
            'class' => 'EmailService',
            'path' => __DIR__ . '/../services/EmailService.php',
            'config' => [
                'smtp_host' => 'smtp.example.com',
                'smtp_port' => 587,
                'smtp_secure' => 'tls',
                'from_email' => 'noreply@yourdomain.com',
                'from_name' => 'Real Estate CRM'
            ]
        ];
    }

    // Get Service Instance
    public static function getService($serviceName) {
        $registry = self::getInstance();
        
        if (!isset(self::$services[$serviceName])) {
            throw new Exception("Service {$serviceName} not found");
        }

        $service = self::$services[$serviceName];

        // If service is already instantiated, return it
        if (is_object($service)) {
            return $service;
        }

        // Load service class file
        require_once $service['path'];

        // Instantiate service with config
        $className = $service['class'];
        self::$services[$serviceName] = new $className($service['config']);

        return self::$services[$serviceName];
    }

    // Get Service Configuration
    public static function getServiceConfig($serviceName) {
        $registry = self::getInstance();
        
        if (!isset(self::$services[$serviceName])) {
            throw new Exception("Service {$serviceName} not found");
        }

        return self::$services[$serviceName]['config'] ?? null;
    }
}

// Global Constants
define('UPLOAD_PATH', __DIR__ . '/../../public/uploads/');
define('TEMP_PATH', __DIR__ . '/../../temp/');
define('LOG_PATH', __DIR__ . '/../../logs/');

// Error Reporting
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Timezone Setting
date_default_timezone_set('UTC');

// Usage Example:
// $database = ServiceRegistry::getService('database');
// $leadService = ServiceRegistry::getService('leadService');
// $propertyService = ServiceRegistry::getService('propertyService');
?>
