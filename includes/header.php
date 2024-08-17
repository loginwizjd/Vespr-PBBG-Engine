<?php
session_start();
include('db.php');
include('functions.php');

// Fetch settings from the database
$stmt = $pdo->query("SELECT nav_bar, header FROM settings WHERE id = 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

$nav_bar = $settings['nav_bar'] ?? '';
$header = $settings['header'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    // Set default page title if not defined
    $default_title = "Vespr.VIP";
    $page_title = isset($page_title) ? htmlspecialchars($page_title) : $default_title;
    ?>
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/custom.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>
</head>
<body class="bg-dark text-light">
    <header class="bg-dark text-light py-3 rounded-bottom">
        <div class="container">
            <?php echo $header; ?>
            <nav class="navbar navbar-expand-lg navbar-dark bg-secondary rounded shadow-sm">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">Vespr.VIP</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                        <ul class="navbar-nav">
                            <?php if (isset($_SESSION['username'])): ?>
                                <!-- For logged-in users -->
                                <?php echo $nav_bar; ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <img src="../uploads/avatars/<?php echo htmlspecialchars($_SESSION['avatar']); ?>" alt="Avatar" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                                        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                        <li><a class="dropdown-item" href="../profile.php">My Profile</a></li>
                                        <?php if ($_SESSION['role'] === 'admin'): ?>
                                            <li><a class="dropdown-item" href="../admin/admin_dashboard.php">Admin Panel</a></li>
                                        <?php endif; ?>
                                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                                    </ul>
                                </li>
                            <?php else: ?>
                                <!-- For guests -->
                                <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                                <li class="nav-item"><a class="nav-link" href="../login.php">Login</a></li>
                                <li class="nav-item"><a class="nav-link" href="../register.php">Register</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <!-- Page content goes here -->
</body>
</html>
