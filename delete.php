<?php
require 'db.php';
require 'classes/Blog.php';
require_once 'helpers/flash.php';

$blog = new Blog($pdo);

// ✅ Get post ID from URL
$id = $_GET['id'] ?? null;

if ($id && is_numeric($id)) {
    $deleted = $blog->deletePost($id);

    if ($deleted) {
        set_flash("✅ Post deleted successfully!", "success");
    } else {
        set_flash("❌ Failed to delete the post.", "danger");
    }
} else {
    set_flash("❌ Invalid post ID.", "warning");
}

// 🔁 Redirect back
header("Location: index.php");
exit;
