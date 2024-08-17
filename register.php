<?php
include('includes/db.php');
$page_title = "Register";
include('includes/header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $avatar = isset($_FILES['avatar']['name']) ? $_FILES['avatar']['name'] : 'default_avatar.png'; // Default avatar

    // Check if the directory exists
    $targetDir = 'uploads/avatars/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Move uploaded avatar to the directory
    if ($_FILES['avatar']['name']) {
        $targetFile = $targetDir . basename($_FILES['avatar']['name']);
        if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
            $error = "Failed to upload avatar. Please try again.";
        }
    }

    $termsAccepted = isset($_POST['terms']) ? 1 : 0;

    if ($termsAccepted) {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, avatar, terms_accepted) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $password, $avatar, $termsAccepted])) {
                header('Location: login.php');
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        $error = "You must accept the Terms of Service and Privacy Policy.";
    }
}
?>

<main class="container my-5">
    <div class="bg-dark text-light p-4 rounded shadow-sm mx-auto" style="max-width: 600px;">
        <form action="" method="post" enctype="multipart/form-data">
            <h2 class="text-center">Register</h2>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="mb-3">
                <label for="avatar" class="form-label">Avatar (optional)</label>
                <input type="file" id="avatar" name="avatar" class="form-control">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" id="terms" name="terms" class="form-check-input" required>
                <label for="terms" class="form-check-label">
                    I accept the <a href="terms.php" class="text-decoration-none">Terms of Service</a> and <a href="privacy.php" class="text-decoration-none">Privacy Policy</a>
                </label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
        </form>
    </div>
</main>

<?php include('includes/footer.php'); ?>
