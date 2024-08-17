<?php
session_start();
include('../includes/db.php');

$page_title = "Plugin Manager";
include('admin_header.php');

// Functions for managing plugin settings and database tables
function get_plugin_settings($pdo, $pluginDir) {
    $stmt = $pdo->prepare("SELECT setting_name, setting_value FROM plugin_settings WHERE plugin_directory = ?");
    $stmt->execute([$pluginDir]);
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}

function update_plugin_settings($pdo, $pluginDir, $newSettings) {
    foreach ($newSettings as $setting_name => $setting_value) {
        $stmt = $pdo->prepare("INSERT INTO plugin_settings (setting_name, setting_value, plugin_directory) VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        $stmt->execute([$setting_name, $setting_value, $pluginDir]);
    }
}

function delete_plugin_settings($pdo, $pluginDir) {
    $stmt = $pdo->prepare("DELETE FROM plugin_settings WHERE plugin_directory = ?");
    $stmt->execute([$pluginDir]);
}

function delete_plugin_tables($pdo, $pluginDir) {
    $tablesSettingName = $pluginDir . '_tables';
    $stmt = $pdo->prepare("SELECT setting_value FROM plugin_settings WHERE setting_name = ? AND plugin_directory = ?");
    $stmt->execute([$tablesSettingName, $pluginDir]);
    $tables = $stmt->fetchColumn();

    if ($tables) {
        $tables = explode(',', $tables);
        foreach ($tables as $table) {
            $pdo->exec("DROP TABLE IF EXISTS $table");
        }
    }
}

// Handle plugin installation and page association
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['install_plugin']) && isset($_FILES['plugin_zip'])) {
        $file = $_FILES['plugin_zip'];
        $uploadDir = '../plugins/';
        $uploadFile = $uploadDir . basename($file['name']);

        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
            $zip = new ZipArchive();
            if ($zip->open($uploadFile) === TRUE) {
                $zip->extractTo($uploadDir);
                $zip->close();

                $extractedDir = basename($uploadFile, '.zip');
                $pluginJsonFile = "$uploadDir/$extractedDir/plugin.json";

                if (file_exists($pluginJsonFile)) {
                    $metadata = json_decode(file_get_contents($pluginJsonFile), true);

                    $pageOptions = '';
                    $pagesStmt = $pdo->query("SELECT id, title FROM pages");
                    $pages = $pagesStmt->fetchAll();
                    foreach ($pages as $page) {
                        $pageOptions .= "<option value='{$page['id']}'>{$page['title']}</option>";
                    }

                    ?>
                    <!-- Display the plugin association form -->
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-light">
                            <h3 class="mb-0">Associate and Install Plugin</h3>
                        </div>
                        <div class="card-body">
                            <form action="" method="post">
                                <input type="hidden" name="plugin_directory" value="<?php echo htmlspecialchars($extractedDir); ?>">
                                <input type="hidden" name="plugin_name" value="<?php echo htmlspecialchars($metadata['name']); ?>">
                                <div class="mb-3">
                                    <label for="page_id" class="form-label">Assign to Page</label>
                                    <select id="page_id" name="page_id" class="form-select" required>
                                        <option value="">Select a page...</option>
                                        <?php echo $pageOptions; ?>
                                    </select>
                                </div>
                                <button type="submit" name="save_plugin" class="btn btn-success">Save Plugin</button>
                            </form>
                        </div>
                    </div>
                    <?php

                } else {
                    echo '<div class="alert alert-danger">Plugin metadata file (plugin.json) not found.</div>';
                }
            } else {
                echo '<div class="alert alert-danger">Failed to unzip the plugin file.</div>';
            }
        } else {
            echo '<div class="alert alert-danger">Failed to upload the plugin file.</div>';
        }
    } elseif (isset($_POST['save_plugin'])) {
        $pluginName = $_POST['plugin_name'];
        $pluginDir = $_POST['plugin_directory'];
        $pageId = $_POST['page_id'];

        $stmt = $pdo->prepare("INSERT INTO plugins (name, directory, page_id, is_enabled) VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE name = ?, page_id = ?");
        $stmt->execute([$pluginName, $pluginDir, $pageId, 1, $pluginName, $pageId]);

        header("Location: plugin_settings.php");
        exit;
    } elseif (isset($_POST['toggle_status'])) {
        $plugin_id = $_POST['plugin_id'];
        $current_status = $_POST['current_status'];
        $new_status = $current_status ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE plugins SET is_enabled = ? WHERE id = ?");
        $stmt->execute([$new_status, $plugin_id]);

        header("Location: plugin_settings.php");
        exit;
    } elseif (isset($_POST['delete_plugin'])) {
        $plugin_id = $_POST['plugin_id'];

        $stmt = $pdo->prepare("SELECT directory FROM plugins WHERE id = ?");
        $stmt->execute([$plugin_id]);
        $plugin = $stmt->fetch();

        if ($plugin) {
            $pluginDir = $plugin['directory'];
            $pluginPath = "../plugins/$pluginDir";

            delete_plugin_tables($pdo, $pluginDir);
            delete_plugin_settings($pdo, $pluginDir);

            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pluginPath, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileinfo->getRealPath());
            }
            rmdir($pluginPath);

            $stmt = $pdo->prepare("DELETE FROM plugins WHERE id = ?");
            $stmt->execute([$plugin_id]);
        }

        header("Location: plugin_settings.php");
        exit;
    } elseif (isset($_POST['update_settings'])) {
        $pluginDir = $_POST['plugin_directory'];
        $settings = $_POST['settings'];

        update_plugin_settings($pdo, $pluginDir, $settings);

        header("Location: plugin_settings.php");
        exit;
    }
}

$plugins = $pdo->query("SELECT * FROM plugins")->fetchAll();
?>

<main class="container my-5">
    <div class="bg-dark text-light p-4 rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Plugin Manager</h2>
            <a href="admin_dashboard.php" class="btn btn-warning text-dark">Back to Admin Dashboard</a>
        </div>

        <!-- Install Plugin Form -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-light">
                <h3 class="mb-0">Install New Plugin</h3>
            </div>
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="plugin_zip" class="form-label">Upload Plugin ZIP File</label>
                        <input type="file" id="plugin_zip" name="plugin_zip" class="form-control" accept=".zip" required>
                    </div>
                    <button type="submit" name="install_plugin" class="btn btn-success">Install Plugin</button>
                </form>
            </div>
        </div>

        <!-- Plugin Settings Form -->
        <div class="row">
            <?php foreach ($plugins as $index => $plugin): ?>
                <?php
                $pluginDir = $plugin['directory'];
                $pluginJsonFile = "../plugins/$pluginDir/plugin.json";
                $metadata = file_exists($pluginJsonFile) ? json_decode(file_get_contents($pluginJsonFile), true) : [];
                $settings = get_plugin_settings($pdo, $pluginDir);
                $collapseId = "plugin-collapse-" . htmlspecialchars($plugin['id']);
                ?>
                <div class="col-12 col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-secondary text-light d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">
                                <button class="btn btn-link text-light" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapseId; ?>" aria-expanded="true" aria-controls="<?php echo $collapseId; ?>">
                                    <?php echo htmlspecialchars($metadata['name'] ?? 'Unknown Plugin'); ?>
                                </button>
                            </h3>
                            <div>
                                <form action="" method="post" class="d-inline">
                                    <input type="hidden" name="plugin_id" value="<?php echo htmlspecialchars($plugin['id']); ?>">
                                    <input type="hidden" name="current_status" value="<?php echo htmlspecialchars($plugin['is_enabled']); ?>">
                                    <button type="submit" name="toggle_status" class="btn btn-<?php echo $plugin['is_enabled'] ? 'danger' : 'success'; ?>">
                                        <?php echo $plugin['is_enabled'] ? 'Disable' : 'Enable'; ?>
                                    </button>
                                </form>
                                <form action="" method="post" class="d-inline ms-2">
                                    <input type="hidden" name="plugin_id" value="<?php echo htmlspecialchars($plugin['id']); ?>">
                                    <button type="submit" name="delete_plugin" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                        <div id="<?php echo $collapseId; ?>" class="collapse" aria-labelledby="heading<?php echo htmlspecialchars($plugin['id']); ?>" data-bs-parent="#pluginAccordion">
                            <div class="card-body">
                                <p><strong>Version:</strong> <?php echo htmlspecialchars($metadata['version'] ?? 'N/A'); ?></p>
                                <p><strong>Author:</strong> <?php echo htmlspecialchars($metadata['author'] ?? 'N/A'); ?></p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($metadata['description'] ?? 'No description available.'); ?></p>

                                <!-- Plugin Settings Form -->
                                <form action="" method="post" class="mb-3">
                                    <input type="hidden" name="plugin_directory" value="<?php echo htmlspecialchars($pluginDir); ?>">
                                    <?php foreach ($settings as $name => $value): ?>
                                        <div class="mb-3">
                                            <label class="form-label"><?php echo htmlspecialchars($name); ?></label>
                                            <input type="text" name="settings[<?php echo htmlspecialchars($name); ?>]" class="form-control" value="<?php echo htmlspecialchars($value); ?>">
                                        </div>
                                    <?php endforeach; ?>
                                    <button type="submit" name="update_settings" class="btn btn-primary">Update Settings</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>