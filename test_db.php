<?php
// DB credentials
$db_host = 'f63845733780033.db.45733780.39d.hostedresource.net';
$db_port = 3311;
$db_user = 'f63845733780033';
$db_pass = 's{u4/2s}mG@';
$db_name = 'f63845733780033'; // Replace if your DB name is different

// Connect to MySQL
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Success
echo "<h2>✅ Database Connection Successful</h2>";

// List Tables
$result = $conn->query("SHOW TABLES");

if ($result->num_rows > 0) {
    echo "<h3>Tables in database `$db_name`:</h3><ul>";
    while ($row = $result->fetch_array()) {
        echo "<li>" . htmlspecialchars($row[0]) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No tables found in the database.</p>";
}

$conn->close();
?>
