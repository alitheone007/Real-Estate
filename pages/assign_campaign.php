<?php
include("../db.php"); // Central DB path

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $influencer_id = trim($_POST['influencer_id']);
    $campaign_name = trim($_POST['campaign_name']);
    $assigned_by = "admin"; // Replace with session if needed
    $created_at = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO campaigns (influencer_id, campaign_name, assigned_by, created_at) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("isss", $influencer_id, $campaign_name, $assigned_by, $created_at);
        if ($stmt->execute()) {
            $success = "✅ Campaign successfully assigned!";
        } else {
            $error = "❌ Execution failed: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "❌ Preparation failed: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Campaign</title>
    <link rel="stylesheet" href="/assets/css/tailwind.min.css">
    <link rel="stylesheet" href="/assets/css/custom.css">

</head>
<body class="bg-gray-100 text-gray-800">

<div class="max-w-2xl mx-auto p-6">
    <div class="bg-white shadow-md rounded-xl p-6">
        <h1 class="text-2xl font-bold text-blue-700 mb-4">📢 Assign Campaign to Influencer</h1>

        <?php if ($success): ?>
            <div class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">
                <?= $success ?>
            </div>
        <?php elseif ($error): ?>
            <div class="mb-4 bg-red-100 text-red-800 px-4 py-2 rounded">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label for="campaign_name" class="block font-semibold mb-1">Campaign Name</label>
                <input type="text" name="campaign_name" id="campaign_name" required
                       class="w-full px-4 py-2 border rounded-md focus:ring focus:outline-none">
            </div>

            <div>
                <label for="influencer_id" class="block font-semibold mb-1">Select Influencer</label>
                <select name="influencer_id" id="influencer_id" required
                        class="w-full px-4 py-2 border rounded-md focus:ring focus:outline-none">
                    <option value="">-- Choose Influencer --</option>
                    <?php
                    $res = $conn->query("SELECT id, name FROM influencers WHERE category = 'Real Estate'");
                    while ($row = $res->fetch_assoc()) {
                        $safe_name = htmlspecialchars($row['name']);
                        echo "<option value='{$row['id']}'>{$safe_name}</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
                ✅ Assign Campaign
            </button>
        </form>

        <div class="mt-6">
            <a href="index.php" class="text-blue-600 hover:underline">⬅️ Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>
