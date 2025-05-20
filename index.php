<?php
// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// index.php - Main entry point for the application

// Include necessary configuration files
// Assuming db.config.php is needed application-wide
// require_once __DIR__ . '~/html/Real-Estate/config/db.config.php';
// Note: You might want to include other configs here as needed

// Basic routing: Determine which page to load based on URL parameter
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard'; // Default to dashboard

// Define allowed pages to prevent including arbitrary files
$allowed_pages = [
    'dashboard' => 'pages/admin/dashboard.php',
    'client-portal' => 'pages/client/client-portal.php', // We'll create this later
    'influencer-management' => 'pages/influencers/influencer-management.php', // Based on view_influencers.php, create later
    // Add other pages as you create them, e.g., 'add-lead' => 'pages/reports/add_lead.php',
    // '404' => 'pages/404.php', // Add a 404 page later
];

// Check if the requested page is allowed and the file exists
$page_path = '';
if (isset($allowed_pages[$page]) && file_exists(__DIR__ . '/' . $allowed_pages[$page])) {
    $page_path = __DIR__ . '/' . $allowed_pages[$page];
} else {
    // If page is not found or not allowed, show a 404 or default to dashboard
    // For now, let's default to dashboard and set a flag
    $page_path = __DIR__ . '/' . $allowed_pages['dashboard'];
    // In a real application, you might set $page_path to the 404 page
    // header("HTTP/1.0 404 Not Found");
    // $page_path = __DIR__ . '/pages/404.php';
}

// Include the document header (HTML doctype, head, body start, main layout start)
require_once __DIR__ . '/components/ui/document_header.php';

// Include the page content
if ($page_path && file_exists($page_path)) {
    // Pass the current page identifier to the included page script
    $current_page = $page;
    require_once $page_path;
} else {
    // Fallback error message if even the default page file is missing
    echo "<h1>Error: Page content could not be loaded.</h1>";
    // You might include a simple 404 message here if not using a dedicated 404 page file
}

// Include the document footer (closing layout divs, body, html)
require_once __DIR__ . '/components/ui/document_footer.php';

?>



