<?php
session_start();
include('../includes/db.php');

// Include Parsedown library
require_once '../assets/parsedown/Parsedown.php';

$page_title = "Edit Pages";
include('admin_header.php');

$parsedown = new Parsedown();

// Handle form submission for creating/updating pages
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $slug = $_POST['slug'];
    $content = $_POST['content'];

    // Insert or update the page in the database
    $stmt = $pdo->prepare("INSERT INTO pages (title, content, slug) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content)");
    $stmt->execute([$title, $content, $slug]);

    // Create or update the page file in /pages/
    $filePath = "../pages/" . htmlspecialchars($slug) . ".php";
    $fileContent = "<?php\n";
    $fileContent .= "require_once '../assets/parsedown/Parsedown.php';\n";
    $fileContent .= "\$parsedown = new Parsedown();\n";
    $fileContent .= "include('../includes/header.php');\n";
    $fileContent .= "?>\n";
    $fileContent .= "<main class=\"container my-5\">\n";
    $fileContent .= "    <h1>" . htmlspecialchars($title) . "</h1>\n";
    $fileContent .= "    <div><?php echo \$parsedown->text('" . addslashes($content) . "'); ?></div>\n";
    $fileContent .= "</main>\n";
    $fileContent .= "<?php include('../includes/footer.php'); ?>\n";

    // Write the content to the file
    file_put_contents($filePath, $fileContent);
}

// Handle page deletion
if (isset($_GET['delete'])) {
    $slugToDelete = $_GET['delete'];

    // Delete the page record from the database
    $stmt = $pdo->prepare("DELETE FROM pages WHERE slug = ?");
    $stmt->execute([$slugToDelete]);

    // Delete the page file
    $filePath = "../pages/" . htmlspecialchars($slugToDelete) . ".php";
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Redirect to avoid re-triggering the deletion on refresh
    header("Location: edit_pages.php");
    exit;
}

// Handle page editing
$pageToEdit = null;
if (isset($_GET['edit'])) {
    $slugToEdit = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ?");
    $stmt->execute([$slugToEdit]);
    $pageToEdit = $stmt->fetch();
}

// Fetch existing pages
$pages = $pdo->query("SELECT * FROM pages")->fetchAll();
?>


<main class="container my-5">
    <div class="bg-dark text-light p-4 rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Pages</h2>
            <a href="admin_dashboard.php" class="btn btn-warning text-dark">Back to Admin Dashboard</a>
        </div>

        <!-- Form to add or edit pages -->
        <form action="" method="post" class="mb-4">
            <div class="mb-3">
                <label for="title" class="form-label">Page Title</label>
                <input type="text" id="title" name="title" class="form-control" placeholder="Page Title" value="<?php echo htmlspecialchars($pageToEdit['title'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="slug" class="form-label">Page Slug</label>
                <input type="text" id="slug" name="slug" class="form-control" placeholder="Page Slug" value="<?php echo htmlspecialchars($pageToEdit['slug'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Page Content (<a href="markdown_guide.php">Markdown Guide</a>)</label>
                <textarea id="content" name="content" class="form-control" placeholder="Page Content" rows="5" required><?php echo htmlspecialchars($pageToEdit['content'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Page</button>
        </form>

        <!-- List of existing pages with edit and delete options -->
        <h3 class="mb-3">Existing Pages</h3>
        <ul class="list-group">
            <?php foreach ($pages as $page): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="../view_page.php?id=<?php echo htmlspecialchars($page['id']); ?>" class="text-light">
                        <?php echo htmlspecialchars($page['title']); ?>(<?php echo htmlspecialchars($page['id']); ?>)
                    </a>
                    <div>
                        <a href="?edit=<?php echo htmlspecialchars($page['slug']); ?>" class="btn btn-warning btn-sm ms-2">Edit</a>
                        <a href="?delete=<?php echo htmlspecialchars($page['slug']); ?>" class="btn btn-danger btn-sm ms-2" onclick="return confirm('Are you sure you want to delete this page?');">Delete</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</main>


<?php include('../includes/footer.php'); ?>
