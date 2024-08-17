<?php
session_start(); // Start the session

error_reporting(0);
ini_set('display_errors', '0');

// Check if the user is logged in by verifying session variable
if (!isset($_SESSION['username'])) {
    // Redirect to the index page if not logged in
    header("Location: index.php");
    exit;
}

include('includes/db.php');
include('includes/header.php');
require_once '../htdocs/assets/parsedown/Parsedown.php'; // Include Parsedown

$parsedown = new Parsedown(); // Instantiate Parsedown

$page_id = $_GET['id'] ?? 0; // Get the page ID from the URL or default to 0
$page_title = "Pages";

// Fetch the page content
$stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
$stmt->execute([$page_id]);
$page = $stmt->fetch();

// Check if the page exists
if (!$page) {
    echo "<div class='alert alert-danger'>Page not found.</div>";
    include('includes/footer.php');
    exit;
}

// Check if the page is associated with a plugin
$pluginStmt = $pdo->prepare("SELECT * FROM plugins WHERE page_id = ?");
$pluginStmt->execute([$page_id]);
$plugin = $pluginStmt->fetch();

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($page['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/custom.css"> <!-- Include custom CSS if needed -->
</head>
<body>
    <main class="container my-5">
        <div class="content-wrapper mb-5"> <!-- Added wrapper div with margin-bottom -->
            <?php
            if ($plugin && $plugin['is_enabled']) {
                $pluginDir = $plugin['directory'];
                $pluginFile = "plugins/$pluginDir/$pluginDir.php"; // Ensure this matches your plugin file structure

                if (file_exists($pluginFile)) {
                    require_once $pluginFile;

                    // Determine the function name to call
                    $pluginFunction = $plugin['function_name'] ?? 'display_plugin_content';
                    if (function_exists($pluginFunction)) {
                        // Pass the PDO connection to the plugin function
                        call_user_func($pluginFunction, $pdo);
                    } else {
                        echo "<div class='alert alert-warning'>Function $pluginFunction does not exist in the plugin.</div>";
                    }
                } else {
                    echo "<div class='alert alert-warning'>Plugin file $pluginFile does not exist.</div>";
                }
            } else {
                // Display page content if no plugin is associated or plugin is disabled
                ?>
                <div class="bg-light text-dark p-4 rounded shadow-sm mx-auto" style="max-width: 800px;">
                    <h1><?php echo htmlspecialchars($page['title']); ?></h1>
                    <div>
                        <?php echo $parsedown->text($page['content']); // Render Markdown content ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </main>

    <?php include('includes/footer.php'); ?>
</body>
</html>
