<?php
include('db.php');

// Get the current page ID from the URL or context
$currentPageId = $_GET['page_id'] ?? null; // Replace with your logic to get the current page ID

// Fetch enabled plugins assigned to the current page
$stmt = $pdo->prepare("SELECT * FROM plugins WHERE is_enabled = 1 AND (page_id IS NULL OR page_id = ?)");
$stmt->execute([$currentPageId]);
$plugins = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include each enabled plugin
foreach ($plugins as $plugin) {
    $pluginDir = $plugin['directory'];
    $pluginFile = "../plugins/$pluginDir/$pluginDir.php";
    $pluginJsonFile = "../plugins/$pluginDir/plugin.json";

    if (file_exists($pluginFile)) {
        // Load plugin metadata
        $metadata = [];
        if (file_exists($pluginJsonFile)) {
            $metadata = json_decode(file_get_contents($pluginJsonFile), true);
        }

        // Use metadata as needed, e.g., for logging or displaying information
        // Example: echo "Loading plugin: " . htmlspecialchars($metadata['name']);

        require_once $pluginFile;
    }
}
?>
