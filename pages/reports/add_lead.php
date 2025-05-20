<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $campaign_id = $_POST['campaign_id'];
    $influencer_id = $_POST['influencer_id'];
    $lead_name = trim($_POST['lead_name']);
    $contact_info = trim($_POST['contact_info']);
    $submitted_at = date("Y-m-d H:i:s");

    // Avoid duplicate leads for the same influencer + name + campaign
    $check = "SELECT * FROM leads WHERE lead_name = ? AND influencer_id = ? AND campaign_id = ?";
    $stmt = $conn->prepare($check);
    $stmt->bind_param("sii", $lead_name, $influencer_id, $campaign_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "❌ This lead already exists for the influencer in this campaign.";
    } else {
        // Insert the lead
        $insert = "INSERT INTO leads (campaign_id, influencer_id, lead_name, contact_info, submitted_at)
                   VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("iisss", $campaign_id, $influencer_id, $lead_name, $contact_info, $submitted_at);

        if ($stmt->execute()) {
            $success = "✅ Lead successfully added!";
        } else {
            $error = "❌ DB Insert Failed: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Lead</title>
    <link rel="stylesheet" href="/assets/css/tailwind.min.css">
    <link rel="stylesheet" href="/assets/css/custom.css">

</head>
<body class="bg-gray-100">

<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-semibold text-blue-700 mb-4">Add New Lead</h2>

    <?php if (isset($success)): ?>
        <div class="bg-green-100 text-green-800 px-4 py-2 mb-4 rounded"><?= $success ?></div>
    <?php elseif (isset($error)): ?>
        <div class="bg-red-100 text-red-800 px-4 py-2 mb-4 rounded"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block font-semibold mb-1">Lead Name</label>
            <input type="text" name="lead_name" required class="w-full border px-4 py-2 rounded" />
        </div>
        <div>
            <label class="block font-semibold mb-1">Contact Info</label>
            <input type="text" name="contact_info" required class="w-full border px-4 py-2 rounded" />
        </div>
        <div>
            <label class="block font-semibold mb-1">Influencer</label>
            <select name="influencer_id" required class="w-full border px-4 py-2 rounded">
                <option value="">Select Influencer</option>
                <?php
                $res = $conn->query("SELECT id, name FROM influencers WHERE category = 'Real Estate'");
                while ($row = $res->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>
        </div>
        <div>
            <label class="block font-semibold mb-1">Campaign</label>
            <select name="campaign_id" required class="w-full border px-4 py-2 rounded">
                <option value="">Select Campaign</option>
                <?php
                $res = $conn->query("SELECT id, campaign_name FROM campaigns");
                while ($row = $res->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['campaign_name']}</option>";
                }
                ?>
            </select>
        </div>
<div class="mt-4">
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
        ➕ Save Lead
    </button>
    <a href="view_campaigns.php" class="ml-4 text-gray-600 hover:underline">⬅ Back</a>
</div>
        
    </form>
<a href="view_campaigns.php" class="ml-4 text-gray-600 hover:underline">⬅ Back</a>
</div>
</body>
</html>
