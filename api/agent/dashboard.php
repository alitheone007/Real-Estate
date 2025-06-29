<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

require_once '../includes/init.php';

try {
    // For testing purposes, we'll use a hardcoded agent ID
    // In production, this would come from the authenticated user's session
    $agent_id = 5;

    // Get agent's properties with lead counts
    $properties_query = "
        SELECT 
            p.id,
            p.title,
            p.price,
            p.status,
            COUNT(l.id) as leads_count,
            p.created_at
        FROM properties p
        LEFT JOIN leads l ON p.id = l.property_id
        WHERE p.agent_id = ?
        GROUP BY p.id
        ORDER BY p.created_at DESC
        LIMIT 5
    ";
    $properties_stmt = $pdo->prepare($properties_query);
    $properties_stmt->execute([$agent_id]);
    $properties = $properties_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get agent's latest leads
    $leads_query = "
        SELECT 
            l.id,
            l.name,
            l.email,
            l.phone,
            l.status,
            p.title as property_title,
            l.created_at
        FROM leads l
        JOIN properties p ON l.property_id = p.id
        WHERE p.agent_id = ?
        ORDER BY l.created_at DESC
        LIMIT 5
    ";
    $leads_stmt = $pdo->prepare($leads_query);
    $leads_stmt->execute([$agent_id]);
    $leads = $leads_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get agent's statistics
    $stats_query = "
        SELECT 
            COUNT(DISTINCT p.id) as total_properties,
            COUNT(DISTINCT CASE WHEN l.status = 'active' THEN l.id END) as active_leads,
            COUNT(DISTINCT CASE WHEN l.status = 'converted' THEN l.id END) as converted_leads,
            COALESCE(SUM(CASE WHEN l.status = 'converted' THEN p.price * 0.03 ELSE 0 END), 0) as total_commission
        FROM properties p
        LEFT JOIN leads l ON p.id = l.property_id
        WHERE p.agent_id = ?
    ";
    $stats_stmt = $pdo->prepare($stats_query);
    $stats_stmt->execute([$agent_id]);
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'properties' => $properties,
        'leads' => $leads,
        'stats' => $stats
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'A database error occurred',
        'error' => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred',
        'error' => $e->getMessage()
    ]);
} 