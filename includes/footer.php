<?php
// Fetch footer content from the database
$stmt = $pdo->query("SELECT footer FROM settings WHERE id = 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
$footer = $settings['footer'] ?? '&copy; 2024 Vespr.VIP. All rights reserved.';
?>
<footer class="bg-secondary text-light text-center py-3 mt-auto">
    <div class="container">
        <?php echo $footer; ?>
    </div>
</footer>
</body>
</html>
