<?php
include('includes/db.php');
$page_title = "Login";
include('includes/header.php');

// Function to log or update user activity
function logUserActivity($userId, $action) {
    global $pdo;
    
    // Check if an activity record already exists for this user
    $stmt = $pdo->prepare("SELECT id FROM user_activity WHERE user_id = ?");
    $stmt->execute([$userId]);
    $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingRecord) {
        // Update the existing record
        $stmt = $pdo->prepare("UPDATE user_activity SET action = ?, timestamp = NOW() WHERE user_id = ?");
        $stmt->execute([$action, $userId]);
    } else {
        // Insert a new record
        $stmt = $pdo->prepare("INSERT INTO user_activity (user_id, action, timestamp) VALUES (?, ?, NOW())");
        $stmt->execute([$userId, $action]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username;
        $_SESSION['id'] = $user['id'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role']; // Store role in session
        
        // Log or update user activity
        logUserActivity($_SESSION['id'], 'User logged in');
        
        header('Location: index.php');
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<main class="container my-5">
    <div class="bg-dark text-light p-4 rounded shadow-sm mx-auto" style="max-width: 500px;">
        <form action="" method="post">
            <h2 class="text-center">Login</h2>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
        </form>
    </div>
</main>

<?php include('includes/footer.php'); ?>
