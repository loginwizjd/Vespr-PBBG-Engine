<?php
session_start();
include('../includes/db.php');

$page_title = "Site Settings";
include('admin_header.php');

// Handle settings form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nav_bar = $_POST['nav_bar'];
    $header = $_POST['header'];
    $footer = $_POST['footer'];
    
    // Update site settings
    $stmt = $pdo->prepare("UPDATE settings SET nav_bar = ?, header = ?, footer = ? WHERE id = 1");
    $stmt->execute([$nav_bar, $header, $footer]);
}

// Fetch current settings
$settings = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch();
?>

<main class="container my-5">
    <div class="bg-dark text-light p-4 rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Site Settings</h2>
            <a href="admin_dashboard.php" class="btn btn-warning text-dark">Back to Admin Dashboard</a>
        </div>

        <!-- Site settings form -->
        <form action="" method="post">
            <div class="card mb-4 bg-secondary border-0">
                <div class="card-header bg-dark text-light">
                    <h3 class="mb-0">Navigation Bar</h3>
                </div>
                <div class="card-body bg-dark text-light">
                    <div class="mb-3">
                        <label for="nav_bar" class="form-label">Navigation Bar HTML</label>
                        <textarea id="nav_bar" name="nav_bar" class="form-control" rows="5" required><?php echo htmlspecialchars($settings['nav_bar']); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-4 bg-secondary border-0">
                <div class="card-header bg-dark text-light">
                    <h3 class="mb-0">Header</h3>
                </div>
                <div class="card-body bg-dark text-light">
                    <div class="mb-3">
                        <label for="header" class="form-label">Header HTML</label>
                        <textarea id="header" name="header" class="form-control" rows="5" required><?php echo htmlspecialchars($settings['header']); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-4 bg-secondary border-0">
                <div class="card-header bg-dark text-light">
                    <h3 class="mb-0">Footer</h3>
                </div>
                <div class="card-body bg-dark text-light">
                    <div class="mb-3">
                        <label for="footer" class="form-label">Footer HTML</label>
                        <textarea id="footer" name="footer" class="form-control" rows="5" required><?php echo htmlspecialchars($settings['footer']); ?></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
