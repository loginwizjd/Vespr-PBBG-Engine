<?php
session_start();
include('../includes/db.php');

$page_title = "Edit User";
include('admin_header.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo '<div class="alert alert-danger">Invalid user ID.</div>';
    exit;
}

$id = $_GET['id'];

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo '<div class="alert alert-danger">User not found.</div>';
    exit;
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $avatar = $_FILES['avatar']['name'] ? 'avatars/' . basename($_FILES['avatar']['name']) : $user['avatar'];

    if ($avatar !== $user['avatar'] && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        move_uploaded_file($_FILES['avatar']['tmp_name'], '../avatars/' . basename($_FILES['avatar']['name']));
    }

    $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ?, avatar = ? WHERE id = ?");
    $stmt->execute([$username, $role, $avatar, $id]);

    header("Location: manage_users.php");
    exit;
}
?>

<main class="container my-5">
    <div class="bg-dark text-light p-4 rounded shadow-sm">
        <h2>Edit User</h2>

        <!-- User Edit Form -->
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <input type="text" id="role" name="role" class="form-control" value="<?php echo htmlspecialchars($user['role']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="avatar" class="form-label">Avatar</label>
                <input type="file" id="avatar" name="avatar" class="form-control">
                <?php if ($user['avatar']): ?>
                    <img src="../uploads/avatars/<?php echo htmlspecialchars($user['avatar']); ?>" alt="User Avatar" class="mt-2" style="max-width: 150px;">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
