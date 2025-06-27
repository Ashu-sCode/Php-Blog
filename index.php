<?php
session_start();
require 'db.php';
require 'classes/Blog.php';

$blog = new Blog($pdo);

// 🔢 Pagination setup
$limit = 6;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$totalPosts = $blog->countPosts();
$totalPages = ceil($totalPosts / $limit);
$posts = $blog->getPostsPaginated($limit, $offset);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My PHP Blog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4">
        <h1 class="display-5 text-center mb-4">📖 My PHP Blog</h1>

        <!-- 🔐 Admin Login Button -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
                <a href="admin/login.php" class="btn btn-outline-primary">🔐 Admin Login</a>
            <?php endif; ?>
        </div>

        <!-- 📝 Blog Posts -->
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
                                    </small>
                                    <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary mt-2">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- 🔹 No Posts -->
        <?php if (empty($posts)): ?>
            <div class="alert alert-info text-center mt-4">
                No posts yet. Check back soon!
            </div>
        <?php endif; ?>

        <!-- 🔢 Pagination Links -->
        <?php if ($totalPages > 1): ?>
            <nav class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">&laquo; Prev</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
