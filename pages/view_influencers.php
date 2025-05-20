<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/db.php');  // ✅ Universal DB file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Real Estate Influencers</title>
    <link rel="stylesheet" href="/assets/css/tailwind.min.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="max-w-7xl mx-auto p-6">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h1 class="text-3xl font-bold text-blue-700">👥 Real Estate Influencers</h1>
            <p class="text-gray-600 mt-1">List of all influencers registered under the Real Estate category.</p>
        </div>

        <!-- Influencer Table -->
        <div class="overflow-x-auto bg-white rounded-xl shadow-md p-4">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Phone</th>
                        <th class="px-4 py-2">Followers</th>
                        <th class="px-4 py-2">Platform</th>
                        <th class="px-4 py-2">Joined On</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM influencers WHERE category = 'Real Estate' ORDER BY registered_at DESC");
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td class='px-4 py-3 font-medium text-gray-800'>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['phone']) . "</td>";
                            echo "<td class='px-4 py-3'>" . number_format($row['followers_count']) . "</td>";
                            echo "<td class='px-4 py-3'>" . htmlspecialchars($row['platform']) . "</td>";
                            echo "<td class='px-4 py-3'>" . date("d M Y", strtotime($row['registered_at'])) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='px-4 py-4 text-center text-gray-500'>No influencers found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Back Button -->
        <div class="mt-6">
            <a href="index.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow">
                ⬅️ Back to Dashboard
            </a>
        </div>

        <!-- Footer -->
        <footer class="text-center text-gray-500 mt-8 text-sm">
            &copy; <?php echo date("Y"); ?> Bilion Sales & Services – Real Estate Microservice.
        </footer>
    </div>

</body>
</html>
