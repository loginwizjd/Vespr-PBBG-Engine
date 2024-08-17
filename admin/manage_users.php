<?php
session_start();
include('../includes/db.php');

$page_title = "Manage Users";
include('admin_header.php');

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        header("Location: manage_users.php"); // Redirect to avoid form resubmission
        exit;
    }
}

// Fetch users
$users = $pdo->query("SELECT * FROM users")->fetchAll();
?>
<style>.user-avatar {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    margin: auto;
    object-fit: cover;
}
</style>
<main class="container my-5">
    <div class="bg-dark text-light p-4 rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Users</h2>
            <a href="admin_dashboard.php" class="btn btn-warning text-dark">Back to Admin Dashboard</a>
        </div>

        <!-- Search and Filter -->
        <div class="mb-4">
            <form class="d-flex" method="get" action="">
                <input class="form-control me-2" type="search" name="search" placeholder="Search users" aria-label="Search">
                <button class="btn btn-outline-light" type="submit">Search</button>
            </form>
        </div>

        <!-- Users List -->
        <div class="row">
            <?php foreach ($users as $user): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card bg-dark text-light"><center>
                        <img src="../uploads/avatars/<?php echo htmlspecialchars($user['avatar']) ?: 'default-avatar.png'; ?>" class="card-img-top user-avatar" alt="User Avatar">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($user['username']); ?></h5>
                            <p class="card-text"><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
                            <a href="edit_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-primary btn-sm">Edit</a>
                            <form action="" method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                            </form>
                        </div>
            </center></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
