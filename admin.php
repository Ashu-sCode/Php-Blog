<?php
session_start();

// 🔐 Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: admin/login.php');
    exit;
}

require_once 'helpers/flash.php';
require 'db.php';
require 'classes/Blog.php';

$blog = new Blog($pdo);

// 🔢 Pagination settings
$limit = 6;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$totalPosts = $blog->getTotalPostCount();
$totalPages = ceil($totalPosts / $limit);

$posts = $blog->getPaginatedPosts($limit, $offset);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My PHP Blog (Admin)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-4">

        <!-- ✅ Flash Toast -->
        <?php show_flash_toast(); ?>

        <!-- ✅ Header Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="display-5">Admin Panel - PHP Blog</h1>
            <div class="d-flex gap-2">
                <a href="create.php" class="btn btn-primary">+ New Post</a>
                <a href="admin/dashboard.php" class="btn btn-outline-danger">Dashboard</a>
                <a href="admin/logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>

        <!-- ✅ Posts List -->
        <div class="row g-4">
            <?php foreach ($posts as $post): ?>
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="row g-0">
                            <?php if (!empty($post['image']) && file_exists('uploads/' . $post['image'])): ?>
                                <div class="col-4">
                                    <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>"
                                         class="img-fluid rounded-start"
                                         alt="Post Image"
                                         loading="lazy"
                                         style="object-fit: cover; height: 100%; width: 100%;">
                                </div>
                            <?php endif; ?>

                            <div class="col-8">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                                    <p class="card-text text-muted">
                                        <?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 100))) . '...'; ?>
                                    </p>
                                    <small class="text-muted mt-auto">
                                        Posted: <?php echo Blog::timeAgo($post['created_at']); ?>
                                        <?php if (!empty($post['updated_at']) && $post['updated_at'] !== $post['created_at']): ?>
                                            | Edited: <?php echo Blog::timeAgo($post['updated_at']); ?>
                                        <?php endif; ?>
                                    </small>

                                    <div class="mt-2 d-flex gap-2">
                                        <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-primary">Read More</a>
                                        <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal<?php echo $post['id']; ?>">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 🗑️ Delete Confirmation Modal -->
                    <div class="modal fade" id="deleteModal<?php echo $post['id']; ?>" tabindex="-1"
                         aria-labelledby="deleteModalLabel<?php echo $post['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirm Deletion</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete "<strong><?php echo htmlspecialchars($post['title']); ?></strong>"?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <a href="delete.php?id=<?php echo $post['id']; ?>" class="btn btn-danger">Yes, Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- 🧭 Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($i === $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>

        <!-- 📝 No Posts -->
        <?php if (empty($posts)): ?>
            <div class="alert alert-info text-center mt-4">
                No posts found. <a href="create.php">Create your first post</a>!
            </div>
        <?php endif; ?>
    </div>

    <!-- ✅ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
