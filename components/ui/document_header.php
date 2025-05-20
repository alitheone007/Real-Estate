<?php
// components/ui/document_header.php - Handles the document head and start of the main layout

// Define a default title or get it from a variable set in the page script
$page_title = isset($page_title) ? $page_title : "Loading..."; // Default title

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> | EduLeads Pro</title>
    <!-- Link to Tailwind CSS -->
    <!-- Ensure this path is correct based on your assets directory -->
    <link href="../Real-Estate/assets/css/tailwind.min.css" rel="stylesheet">
     <!-- Link to Dashboard specific CSS -->
     <link href="../Real-Estate/assets/css/dashboard.css" rel="stylesheet">
    <!-- Link to your custom CSS if you have one -->
    <link href="../Real-Estate/assets/css/components/custom.css" rel="stylesheet">
    <!-- Add any other head elements here (favicons, other meta tags, etc.) -->
    <style>
        /* Basic styles for gradient text, etc.
           You might want to move these to custom.css */
        .edu-gradient-text {
            background: linear-gradient(to right, #3b82f6, #06b6d4); /* Tailwind blue-500 to cyan-500 */
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
        }
        .glass-card {
            background-color: rgba(255, 255, 255, 0.08); /* Slightly visible white */
            backdrop-filter: blur(10px); /* Blur effect */
            border: 1px solid rgba(255, 255, 255, 0.1); /* Light border */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
         .shine-effect {
            position: relative;
            overflow: hidden;
        }
        .shine-effect::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 300%;
            height: 100%;
            background: linear-gradient(105deg, transparent 0%, rgba(255, 255, 255, 0.8) 40%, rgba(255, 255, 255, 0.8) 60%, transparent 100%);
            transition: all 1s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
        }
        .shine-effect:hover::before {
            left: 0%;
        }
         .hover-scale:hover {
             transform: scale(1.02);
             transition: transform 0.3s ease-in-out;
         }
         .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
            opacity: 0;
         }
         @keyframes fadeIn {
            to {
                opacity: 1;
            }
         }
         /* Add padding to body to prevent content from being hidden under a fixed header if you implement one */
         /* body { padding-top: height_of_your_fixed_header; } */

        /* Basic styling for the sidebar links visible in the screenshot */
        /* This is a fallback and should be overridden by Tailwind classes in sidebar.php */
        .sidebar a {
            display: block;
            padding: 10px;
            margin-bottom: 5px;
            color: #fff; /* Example color */
            text-decoration: none;
        }
         .sidebar a:hover {
             background-color: #333; /* Example hover */
         }
    </style>
</head>
<body class="bg-background text-foreground antialiased">

    <!-- Main layout container: Flex container for Sidebar and Main Content -->
    <div class="flex min-h-screen bg-background">
        <?php
            // Include the Sidebar component - assuming it's a fixed part of the layout
            // CORRECTED PATH: components/ui/sidebar.php
            require_once __DIR__ . '/sidebar.php';
        ?>
        <!-- Main content area wrapper -->
        <div class="flex-1 flex flex-col">
            <?php
                // Include the Header component (the top bar within the main content area)
                // Ensure this path is correct: components/ui/header.php
                require_once __DIR__ . '/header.php'; // This is the *content* header component
            ?>
            <!-- Main page content area -->
            <main class="flex-1 p-6">
                <div class="max-w-7xl mx-auto space-y-6">
                    <!-- Page specific content will be included here by index.php -->
