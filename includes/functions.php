<?php
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function getPageContent($slug) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT content FROM pages WHERE slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetchColumn();
}

function renderMarkdown($text) {
    include_once('Parsedown.php');
    $Parsedown = new Parsedown();
    return $Parsedown->text($text);
}


?>
