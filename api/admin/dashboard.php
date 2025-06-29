<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/init.php';

try {
    // Get total users by role
    $userStats = $db->query("
        SELECT role, COUNT(*) as count 
        FROM users 
        GROUP BY role
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Get property statistics
    $propertyStats = $db->query("
        SELECT 
            COUNT(*) as total_properties,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_properties,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_properties,
            AVG(price) as average_price
        FROM properties
    ")->fetch(PDO::FETCH_ASSOC);

    // Get lead statistics
    $leadStats = $db->query("
        SELECT 
            COUNT(*) as total_leads,
            SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_leads,
            SUM(CASE WHEN status = 'contacted' THEN 1 ELSE 0 END) as contacted_leads,
            SUM(CASE WHEN status = 'qualified' THEN 1 ELSE 0 END) as qualified_leads
        FROM leads
    ")->fetch(PDO::FETCH_ASSOC);

    // Get recent leads
    $recentLeads = $db->query("
        SELECT l.*, p.title as property_title 
        FROM leads l 
        LEFT JOIN properties p ON l.property_id = p.id 
        ORDER BY l.created_at DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Get recent properties
    $recentProperties = $db->query("
        SELECT p.*, u.name as builder_name 
        FROM properties p 
        LEFT JOIN users u ON p.builder_id = u.id 
        ORDER BY p.created_at DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Get analytics data for the last 7 days
    $analytics = $db->query("
        SELECT * FROM analytics 
        ORDER BY date DESC 
        LIMIT 7
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'userStats' => $userStats,
            'propertyStats' => $propertyStats,
            'leadStats' => $leadStats,
            'recentLeads' => $recentLeads,
            'recentProperties' => $recentProperties,
            'analytics' => $analytics
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'A database error occurred',
        'error' => $e->getMessage()
    ]);
} 