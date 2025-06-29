<?php
// components/ui/header.php - Content for the top bar header within the main content area

// Get the current page to display in the header
$current_page = isset($current_page) ? $current_page : 'dashboard';
$page_name = ucfirst(str_replace('-', ' ', $current_page));
?>

<!-- Top Bar Header within Main Content -->
<header class="w-full bg-gray-800/50 backdrop-blur p-4 flex items-center justify-between sticky top-0 z-10 border-b border-gray-700">
    <div>
        <!-- Dynamic content based on page can go here -->
        <h1 class="text-xl font-semibold text-white"><?php echo htmlspecialchars($page_name); ?></h1>
    </div>
    <div class="flex items-center space-x-4">
        <!-- User Avatar/Menu -->
        <div class="text-white">User Name</div>
        <!-- Settings Icon, Notifications, etc. -->
        <button class="p-2 rounded-md hover:bg-gray-700 transition-colors duration-200 text-white">
             <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                 <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.78 1.35a2 2 0 0 0 .72 2.73l.05.03a2 2 0 0 1 1 1.74v.44a2 2 0 0 0 2 2h.18a2 2 0 0 1 1.73 1l.25.43a2 2 0 0 1 0 2l-.08.15a2 2 0 0 0 .73 2.73l1.35.78a2 2 0 0 0 2.73-.72l.03-.05a2 2 0 0 1 1.74-1h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.78-1.35a2 2 0 0 0-.72-2.73l-.05-.03a2 2 0 0 1-1-1.74v-.44a2 2 0 0 0-2-2h-.18a2 2 0 0 1-1.73-1l-.25-.43a2 2 0 0 1 0-2l.08-.15a2 2 0 0 0-.73-2.73l-1.35-.78a2 2 0 0 0-2.73.72l-.03.05a2 2 0 0 1-1.74 1z"/>
                 <circle cx="12" cy="12" r="3"/>
             </svg>
        </button>
    </div>
</header>
