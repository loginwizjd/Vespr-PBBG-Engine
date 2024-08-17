<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$email = $_POST['email'];
$avatar = $_FILES['avatar']['name'] ? $_FILES['avatar']['name'] : $_SESSION['avatar'];

// Handle avatar upload
if ($_FILES['avatar']['name']) {
    $targetDir = 'uploads/avatars/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $targetFile = $targetDir . basename($_FILES['avatar']['name']);
    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
        // Success
        header('Location: profile.php');
    } else {
        $error = "Failed to upload avatar.";
    }
}

try {
    $stmt = $pdo->prepare("UPDATE users SET email = ?, avatar = ? WHERE username = ?");
    if ($stmt->execute([$email, $avatar, $username])) {
        $_SESSION['avatar'] = $avatar;
        header('Location: profile.php');
        exit();
    } else {
        $error = "Profile update failed.";
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
