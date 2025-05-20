<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Real Estate Campaigns</title>
  <link rel="stylesheet" href="/assets/css/tailwind.min.css">
  <link rel="stylesheet" href="/assets/css/custom.css">

</head>
<body class="bg-gray-100">
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center mb-6">📋 Real Estate Campaigns</h1>

    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php
          $stmt = $pdo->query("SELECT * FROM real_estate_campaigns ORDER BY start_date DESC");
          $count = 1;
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              echo "<tr>";
              echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>" . $count++ . "</td>";
              echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>" . htmlspecialchars($row['campaign_name']) . "</td>";
              echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>₹" . number_format($row['budget']) . "</td>";
              echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>" . $row['start_date'] . "</td>";
              echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>" . $row['end_date'] . "</td>";
              echo "<td class='px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold'>" . $row['status'] . "</td>";
              echo "</tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
