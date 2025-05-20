<?php
// pages/admin/dashboard.php - Content for the admin dashboard page

// Set a page title that will be used by document_header.php
$page_title = "Dashboard";

// --- PHP Logic for the page ---

// Simulate mock data for students - In a real app, this would come from the database
$students = [
    [
        "id" => "stu-1",
        "name" => "Alice Smith",
        "email" => "alice.s@example.com",
        "course" => "Web Development Bootcamp",
        "status" => "active",
        "paymentStatus" => "paid",
        "joinDate" => "2023-08-01",
    ],
    [
        "id" => "stu-2",
        "name" => "Bob Johnson",
        "email" => "bob.j@example.com",
        "course" => "Data Science Fundamentals",
        "status" => "pending",
        "paymentStatus" => "pending",
        "joinDate" => "2023-09-10",
    ],
    [
        "id" => "stu-3",
        "name" => "Charlie Brown",
        "email" => "charlie.b@example.com",
        "course" => "Digital Marketing Masterclass",
        "status" => "graduated",
        "paymentStatus" => "paid",
        "joinDate" => "2023-05-20",
    ],
     [
        "id" => "stu-4",
        "name" => "Diana Prince",
        "email" => "diana.p@example.com",
        "course" => "Web Development Bootcamp",
        "status" => "dropped",
        "paymentStatus" => "overdue",
        "joinDate" => "2023-07-15",
    ],
     [
        "id" => "stu-5",
        "name" => "Ethan Hunt",
        "email" => "ethan.h@example.com",
        "course" => "Data Science Fundamentals",
        "status" => "active",
        "paymentStatus" => "partial",
        "joinDate" => "2023-09-05",
    ],
    // Add more student data as needed
];

// Function to get status badge color class
function getStudentStatusColor($status) {
    switch ($status) {
        case "active":
            return "bg-green-500/10 text-green-500 border-green-500/20";
        case "pending":
            return "bg-yellow-500/10 text-yellow-500 border-yellow-500/20";
        case "graduated":
            return "bg-blue-500/10 text-blue-500 border-blue-500/20";
        case "dropped":
            return "bg-red-500/10 text-red-500 border-red-500/20";
        default:
            return "bg-gray-500/10 text-gray-500 border-gray-500/20";
    }
}

// Function to get payment status badge color class
function getPaymentStatusColor($status) {
    switch ($status) {
        case "paid":
            return "bg-green-500/10 text-green-500 border-green-500/20";
        case "pending":
            return "bg-yellow-500/10 text-yellow-500 border-yellow-500/20";
        case "overdue":
            return "bg-red-500/10 text-red-500 border-red-500/20";
        case "partial":
            return "bg-blue-500/10 text-blue-500 border-blue-500/20";
        default:
            return "bg-gray-500/10 text-gray-500 border-gray-500/20";
    }
}

// Note: In a real application, data fetching from the database would happen here
// using your Database.php and model/repository/service classes.
// Example: $leads = $leadService->getRecentLeads();

?>

<!-- HTML Structure for the Dashboard Page Content -->

<div class="flex items-center justify-between animate-fade-in mb-4">
    <div>
        <h1 class="text-2xl font-bold mb-1 edu-gradient-text">EduLeads Dashboard</h1>
        <p class="text-sm text-muted-foreground">
            Welcome back! Here's what's happening with your leads and enrollments.
        </p>
    </div>
    <!-- Using an anchor tag for navigation in PHP -->
    <a href="?page=add-lead" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2 shine-effect">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
            class="mr-2"
        >
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <line x1="19" x2="19" y1="8" y2="14" />
            <line x1="22" x2="16" y1="11" y2="11" />
        </svg>
        Add New Lead
    </a>
</div>

<!-- Stats Cards - Include the component file if it exists, otherwise use a placeholder -->
<?php
$stats_cards_path = __DIR__ . '/../../components/dashboard/StatsCards.php';
if (file_exists($stats_cards_path)) {
    require_once $stats_cards_path;
} else {
    echo '<div class="glass-card animate-fade-in hover-scale p-6"><h3 class="text-lg font-semibold mb-4">Stats Cards (Placeholder)</h3><p>Content for Stats Cards goes here.</p></div>';
}
?>


<!-- Leads Pipeline - Include the component file if it exists, otherwise use a placeholder -->
<?php
$pipeline_path = __DIR__ . '/../../components/leads-pipeline/PipelineView.php';
if (file_exists($pipeline_path)) {
    require_once $pipeline_path;
} else {
     echo '<div class="glass-card animate-fade-in hover-scale p-6"><h3 class="text-lg font-semibold mb-4">Leads Pipeline (Placeholder)</h3><p>Content for Leads Pipeline goes here.</p></div>';
}
?>

<!-- Charts and Activity -->
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Performance Chart - Include the component file if it exists, otherwise use a placeholder -->
    <!-- You will need to create this file and potentially use a JS library for charts -->
    <?php
    // Assuming charts are in components/charts
    $performance_chart_path = __DIR__ . '/../../components/charts/PerformanceChart.php';
    if (file_exists($performance_chart_path)) {
        require_once $performance_chart_path;
    } else {
        echo '
        <div class="glass-card animate-fade-in hover-scale p-6">
             <h3 class="text-lg font-semibold mb-4">Performance Chart (Placeholder)</h3>
             <div class="h-48 w-full bg-muted/30 rounded-md flex items-center justify-center text-muted-foreground">
                 Chart will go here
             </div>
        </div>';
    }
    ?>

    <!-- Recent Activity - Include the component file if it exists, otherwise use a placeholder -->
    <?php
    // Assuming activity is in components/dashboard or similar
    $recent_activity_path = __DIR__ . '/../../components/dashboard/RecentActivity.php'; // Adjust path if needed
     if (file_exists($recent_activity_path)) {
         require_once $recent_activity_path;
     } else {
         echo '
          <div class="glass-card animate-fade-in hover-scale p-6 lg:col-span-3">
              <h3 class="text-lg font-semibold mb-4">Recent Activity (Placeholder)</h3>
               <div class="h-48 w-full bg-muted/30 rounded-md flex items-center justify-center text-muted-foreground">
                  Activity feed will go here
              </div>
          </div>';
     }
    ?>
</div>

<!-- Recent Students -->
<div class="glass-card animate-fade-in hover-scale">
    <div class="border-b border-border/30 bg-muted/10 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold flex items-center gap-2">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="18"
                      height="18"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      class="text-edu-teal-500"
                    >
                      <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                      <circle cx="9" cy="7" r="4" />
                      <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                      <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    Recent Students
                </h2>
                <p class="text-sm text-muted-foreground mt-1">
                    Latest student enrollments and information
                </p>
            </div>
            <!-- Using an anchor tag for navigation in PHP -->
            <a href="#" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-8 px-3 shine-effect">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="14"
                  height="14"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  class="mr-1"
                >
                  <rect width="18" height="18" x="3" y="3" rx="2" />
                  <path d="M3 9h18" />
                  <path d="M9 21V9" />
                </svg>
                View All
            </a>
        </div>
    </div>
    <div class="p-4">
        <div class="rounded-lg border border-border/50 overflow-hidden">
            <div class="overflow-auto">
                <table class="w-full min-w-[600px]">
                    <thead>
                        <tr class="bg-muted/30">
                            <th class="text-xs font-medium text-left p-3 pl-4">Student</th>
                            <th class="text-xs font-medium text-left p-3">Course</th>
                            <th class="text-xs font-medium text-left p-3">Status</th>
                            <th class="text-xs font-medium text-left p-3">Payment</th>
                            <th class="text-xs font-medium text-right p-3 pr-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/30">
                        <?php foreach ($students as $index => $student): ?>
                            <tr
                                class="hover:bg-muted/20 transition-colors duration-200 animate-fade-in"
                                style="animation-delay: <?php echo $index * 100; ?>ms"
                            >
                                <td class="p-3 pl-4">
                                    <div class="flex items-center gap-3">
                                        <!-- Avatar Placeholder - In a real app, generate or fetch the image -->
                                        <div class="h-9 w-9 rounded-full bg-gradient-to-br from-primary/20 to-accent/20 flex items-center justify-center text-sm font-medium border-2 border-white/10">
                                            <?php echo htmlspecialchars(substr($student['name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="font-medium"><?php echo htmlspecialchars($student['name']); ?></div>
                                            <div class="text-xs text-muted-foreground"><?php echo htmlspecialchars($student['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3">
                                    <div class="font-medium text-sm"><?php echo htmlspecialchars($student['course']); ?></div>
                                </td>
                                <td class="p-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border <?php echo getStudentStatusColor($student['status']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($student['status'])); ?>
                                    </span>
                                </td>
                                <td class="p-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border <?php echo getPaymentStatusColor($student['paymentStatus']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($student['paymentStatus'])); ?>
                                    </span>
                                </td>
                                <td class="p-3 pr-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <!-- Details Button -->
                                        <!-- Using a button tag for interaction -->
                                        <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 px-2 py-1 gap-1">
                                            <svg
                                              xmlns="http://www.w3.org/2000/svg"
                                              width="14"
                                              height="14"
                                              viewBox="0 0 24 24"
                                              fill="none"
                                              stroke="currentColor"
                                              strokeWidth="2"
                                              strokeLinecap="round"
                                              strokeLinejoin="round"
                                            >
                                              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                              <path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4Z" />
                                            </svg>
                                            Details
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="flex justify-between border-t border-border/30 p-4 bg-muted/10">
        <!-- Pagination Buttons -->
        <!-- Using button tags for interaction -->
        <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 shine-effect">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
                class="mr-1"
              >
                <path d="m15 18-6-6 6-6" />
              </svg>
              Previous
        </button>
        <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 shine-effect">
              Next
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
                class="ml-1"
              >
                <path d="m9 18 6-6-6-6" />
              </svg>
        </button>
    </div>
</div>
