<?php
session_start();
include('../includes/db.php');

$page_title = "Admin Dashboard";
include('admin_header.php');

// Fetch site metrics
try {
    // Total Users
    $stmt = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
    $totalUsers = $stmt->fetchColumn();

    // Total Pages
    $stmt = $pdo->query("SELECT COUNT(*) AS total_pages FROM pages");
    $totalPages = $stmt->fetchColumn();

    // Total Plugins
    $stmt = $pdo->query("SELECT COUNT(*) AS total_plugins FROM plugins");
    $totalPlugins = $stmt->fetchColumn();

    // Total User Activity
    $stmt = $pdo->query("SELECT COUNT(*) AS total_activity FROM user_activity");
    $totalActivity = $stmt->fetchColumn();

    // Recent User Activities
    $stmt = $pdo->query("SELECT user_id, action, timestamp FROM user_activity ORDER BY timestamp DESC LIMIT 5");
    $recentActivities = $stmt->fetchAll();

    // User Activity Data for Graph
    $stmt = $pdo->query("SELECT DATE(timestamp) AS date, COUNT(*) AS activity_count FROM user_activity GROUP BY DATE(timestamp) ORDER BY DATE(timestamp) DESC LIMIT 30");
    $activityData = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<main class="container my-5">
    <div class="bg-dark text-light p-4 rounded shadow-sm">
        <h2 class="text-center">Admin Dashboard</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
<!-- Admin Actions -->
<div class="text-center mt-4">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <a class="btn btn-outline-light w-100 p-3" href="manage_users.php">
                        <i class="bi bi-person-lines-fill"></i> Manage Users
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <a class="btn btn-outline-light w-100 p-3" href="settings.php">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <a class="btn btn-outline-light w-100 p-3" href="edit_page.php">
                        <i class="bi bi-file-earmark-text"></i> Edit Pages
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-3 mb-3">
                    <a class="btn btn-outline-light w-100 p-3" href="plugin_settings.php">
                        <i class="bi bi-plug"></i> Plugin Settings
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Total Users -->
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card bg-dark text-light shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text display-4"><?php echo htmlspecialchars($totalUsers); ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Pages -->
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card bg-primary text-light shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Pages</h5>
                        <p class="card-text display-4"><?php echo htmlspecialchars($totalPages); ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Plugins -->
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card bg-success text-light shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Plugins</h5>
                        <p class="card-text display-4"><?php echo htmlspecialchars($totalPlugins); ?></p>
                    </div>
                </div>
            </div>

            <!-- Total User Activity -->
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card bg-info text-light shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Activity</h5>
                        <p class="card-text display-4"><?php echo htmlspecialchars($totalActivity); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent User Activities -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-light">
                <h5 class="mb-0">Recent User Activities</h5>
            </div>
            <ul class="list-group list-group-flush">
                <?php if ($recentActivities): ?>
                    <?php foreach ($recentActivities as $activity): ?>
                        <li class="list-group-item">
                            User ID: <?php echo htmlspecialchars($activity['user_id']); ?> |
                            Action: <?php echo htmlspecialchars($activity['action']); ?> |
                            Date: <?php echo htmlspecialchars($activity['timestamp']); ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item">No recent activities.</li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Activity Graph -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-light">
                <h5 class="mb-0">User Activity Over Time</h5>
            </div>
            <div class="card-body">
                <canvas id="activityGraph" height="200"></canvas>
            </div>
        </div>

        
    </div>
</main>

<!-- Include Bootstrap JavaScript and its dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js library for the graph -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('activityGraph').getContext('2d');
        var activityData = <?php echo json_encode($activityData); ?>;
        var labels = activityData.map(data => data.date);
        var data = activityData.map(data => data.activity_count);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'User Activity',
                    data: data,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                    },
                    y: {
                        beginAtZero: true,
                    }
                }
            }
        });
    });
</script>

<?php include('../includes/footer.php'); ?>
