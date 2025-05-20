<?php
// components/ui/sidebar.php - Content for the application sidebar

// Get the current page from the variable set in index.php
$current_page = isset($current_page) ? $current_page : '';
?>

<!-- Sidebar -->
<aside class="w-64 bg-gray-900 text-white flex flex-col h-screen sticky top-0">
    <div class="p-6 text-center border-b border-gray-800">
        <h2 class="text-2xl font-bold edu-gradient-text">EduLeads Pro</h2>
    </div>
    <nav class="flex-1 p-6 space-y-2">
        <a href="?page=dashboard" class="flex items-center p-2 rounded-md <?php echo ($current_page === 'dashboard') ? 'bg-gray-700' : 'hover:bg-gray-700'; ?> transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" class="mr-3">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                <line x1="3" y1="9" x2="21" y2="9"/>
                <line x1="9" y1="21" x2="9" y2="9"/>
            </svg>
            Dashboard
        </a>
        <a href="?page=client-portal" class="flex items-center p-2 rounded-md <?php echo ($current_page === 'client-portal') ? 'bg-gray-700' : 'hover:bg-gray-700'; ?> transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" class="mr-3">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            Client Portal
        </a>
         <a href="?page=influencer-management" class="flex items-center p-2 rounded-md <?php echo ($current_page === 'influencer-management') ? 'bg-gray-700' : 'hover:bg-gray-700'; ?> transition-colors duration-200">
             <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                 <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                 <circle cx="8.5" cy="7" r="4"/>
                 <line x1="20" x2="20" y1="8" y2="14"/>
                 <line x1="23" x2="17" y1="11" y2="11"/>
             </svg>
             Influencers
         </a>
         <!-- Add other navigation links here -->
         <a href="#" class="flex items-center p-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" class="mr-3">
                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                <path d="m14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
            </svg>
            Campaigns
        </a>
         <a href="#" class="flex items-center p-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" class="mr-3">
                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <line x1="10" y1="9" x2="10" y2="9"/>
            </svg>
            Reports
        </a>
    </nav>
    <div class="p-6 border-t border-gray-800">
        <a href="#" class="flex items-center p-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" class="mr-3">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Logout
        </a>
    </div>
</aside>
