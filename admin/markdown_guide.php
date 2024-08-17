<?php
session_start();
include('../includes/db.php');

$page_title = "Markdown Guide";
include('admin_header.php');
?>

<main class="container my-5">
    <div class="bg-dark text-light p-4 rounded shadow-sm">
        <h2 class="text-center">Markdown Guide</h2>

        <div class="mt-4">
            <h3>What is Markdown?</h3>
            <p>Markdown is a lightweight markup language with plain-text formatting syntax. Its design allows it to be converted to many formats, but the original tool by the same name only converts to HTML.</p>

            <h3>Basic Syntax</h3>
            <ul>
                <li><strong>Headers:</strong> Use '#' symbols followed by a space. Example: <code># Header 1</code></li>
                <li><strong>Bold Text:</strong> Use <code>**</code> or <code>__</code>. Example: <code>**bold**</code></li>
                <li><strong>Italic Text:</strong> Use <code>*</code> or <code>_</code>. Example: <code>*italic*</code></li>
                <li><strong>Lists:</strong> Use dashes or asterisks for unordered lists, numbers for ordered lists.</li>
                <li><strong>Links:</strong> Use <code>[Link Text](URL)</code></li>
                <li><strong>Images:</strong> Use <code>![Alt Text](URL)</code></li>
                <li><strong>Code:</strong> Use <code>``</code> for inline code and triple backticks for blocks of code.</li>
            </ul>

<a href="admin_dashboard.php" class="btn btn-warning text-dark">Back to Admin Dashboard</a>
</div>
</div>
</main>
<?php include('../includes/footer.php'); ?>