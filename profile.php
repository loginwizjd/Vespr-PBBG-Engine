<?php
include('includes/db.php');
$page_title = "My Profile";
include('includes/header.php');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Fetch user details
$username = $_SESSION['username'];
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<main class="container my-5"><center>
    <div class="bg-dark text-light p-4 rounded shadow-sm">
        <h2 class="text-center">My Profile</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="update_profile.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>">
            </div>
            <div class="mb-3">
                <label for="avatar" class="form-label">Avatar</label>
                <input type="file" id="avatar" name="avatar" class="form-control">
                <img src="uploads/avatars/<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="rounded-circle mt-2" style="width: 100px;">
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
        </center></main>

<?php include('includes/footer.php'); ?>
