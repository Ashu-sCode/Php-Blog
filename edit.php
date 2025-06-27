<?php
require 'admin/auth.php';      // ✅ Protect route
require 'db.php';              // ✅ DB connection
require 'classes/Blog.php';    // ✅ Blog class
require_once 'helpers/flash.php'; // ✅ Flash helper

$blog = new Blog($pdo);        // ✅ Use PDO-enabled Blog
$error = "";
$post = null;

// 🔎 Load post for editing
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        $post = $blog->getPost($id);
        if (!$post) {
            header('Location: index.php');
            exit;
        }
    } catch (Exception $e) {
        error_log("Error loading post: " . $e->getMessage());
        header('Location: index.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}

// 💾 Update logic
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $updated_at = date('Y-m-d H:i:s');

    if ($title === "" || $content === "") {
        $error = "All fields are required";
    } else {
        try {
            $blog->updatePost($id, $title, $content, $updated_at);
            set_flash("✅ Post updated successfully!", "success"); // ✅ Set toast message
            header("Location: index.php");
            exit;
        } catch (InvalidArgumentException $e) {
            $error = $e->getMessage();
        } catch (Exception $e) {
            error_log("Update error: " . $e->getMessage());
            $error = "Something went wrong while updating the post.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>

    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ TinyMCE (with your API key) -->
    <script src="https://cdn.tiny.cloud/1/cvr46qmwz5cy2qfcc4aperhchy4s5j8jy303d0xipdociwlw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content',
            plugins: 'lists link image code preview fullscreen',
            toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image | preview code fullscreen',
            menubar: false,
            height: 300,
            branding: false,
            promotion: false,
            content_style: "body { font-family:Helvetica,Arial,sans-serif; font-size:14px }"
        });
    </script>
</head>

<body class="bg-light">
    <?php show_flash_toast(); ?>

    <div class="container py-5">

        <!-- Back to Home -->
        <div class="mb-4">
            <a href="index.php" class="btn btn-outline-secondary">&larr; Back to Home</a>
        </div>

        <!-- Form Card -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title mb-4">Edit Post</h2>

                <!-- Error Message -->
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <!-- Form -->
                <form method="POST">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            class="form-control"
                            value="<?php echo htmlspecialchars($post['title']); ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea
                            name="content"
                            id="content"
                            rows="6"
                            class="form-control"
                            required><?php echo htmlspecialchars($post['content']); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Update Post</button>
                </form>
            </div>
        </div>

    </div>

    <!-- ✅ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
