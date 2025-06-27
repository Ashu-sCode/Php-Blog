<?php
session_start();

require 'db.php';
require 'classes/Blog.php';

$blog = new Blog($pdo);
$post = null;

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    try {
        $post = $blog->getPost($id);
        if (!$post) {
            header('Location: index.php');
            exit;
        }
    } catch (Exception $e) {
        error_log("Error fetching post: " . $e->getMessage());
        header('Location: index.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($post['title']); ?> - My Blog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .post-image {
            max-height: 400px;
            object-fit: cover;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .card-title {
            font-size: 2rem;
            font-weight: bold;
        }

        .card-body p {
            font-size: 1.1rem;
            line-height: 1.6;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container py-5">

        <!-- 🔙 Navigation Buttons -->
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <a href="index.php" class="btn btn-outline-secondary">&larr; Back to Home</a>

            <!-- 🔐 Show Login if not logged in -->
            <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
                <a href="admin/login.php" class="btn btn-outline-primary">🔐 Admin Login</a>
            <?php endif; ?>
        </div>

        <!-- 📝 Post Card -->
        <div class="card shadow-sm border-0">
            <?php if (!empty($post['image']) && file_exists('uploads/' . $post['image'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>"
                     alt="Post Image" class="post-image mb-3">
            <?php endif; ?>

            <div class="card-body">
                <h1 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h1>

                <p class="text-muted mb-3">
                    <small>
                        Posted: <?php echo Blog::timeAgo($post['created_at']); ?>
                        <?php if (!empty($post['updated_at']) && $post['updated_at'] !== $post['created_at']): ?>
                            | Edited: <?php echo Blog::timeAgo($post['updated_at']); ?>
                        <?php endif; ?>
                    </small>
                </p>

                <!-- ✅ TinyMCE HTML Content Output -->
                <div class="mb-4">
                    <?php echo $post['content']; ?>
                </div>

                <!-- 👨‍💻 Admin Actions -->
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <div class="d-flex gap-2">
                        <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn btn-outline-primary">✏️ Edit</a>
                        <a href="delete.php?id=<?php echo $post['id']; ?>"
                           onclick="return confirm('Are you sure you want to delete this post?');"
                           class="btn btn-outline-danger">🗑️ Delete</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ✅ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
