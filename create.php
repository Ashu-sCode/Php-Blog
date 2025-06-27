<?php
require 'admin/auth.php';
require 'db.php';
require 'classes/Blog.php';
require_once 'helpers/flash.php';

$blog = new Blog($pdo);
$error = "";

// ✅ Form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $content = $_POST['content']; // ❗ DON'T trim — preserves HTML spacing
    $imagePath = null;

    try {
        if ($title === "" || $content === "") {
            $error = "All fields are required";
        } else {
            // ✅ Handle image upload
            if (!empty($_FILES['image']['name'])) {
                $targetDir = "uploads/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
                $targetFile = $targetDir . $imageName;
                $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (!in_array($fileType, $allowedTypes)) {
                    throw new Exception("Only image files are allowed.");
                }

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    throw new Exception("Image upload failed.");
                }

                $imagePath = $imageName;
            }

            // ✅ Save to DB
            $blog->addPost($title, $content, $imagePath);
            set_flash("✅ Post created successfully!", "success");
            header("Location: admin.php");
            exit;
        }
    } catch (InvalidArgumentException $e) {
        $error = $e->getMessage();
    } catch (Exception $e) {
        error_log("Create Post Error: " . $e->getMessage());
        $error = "Something went wrong: " . $e->getMessage();
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>

    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- TinyMCE Editor CDN -->
    <script src="https://cdn.tiny.cloud/1/cvr46qmwz5cy2qfcc4aperhchy4s5j8jy303d0xipdociwlw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>


    <script>
        tinymce.init({
            selector: '#content',
            height: 400,
            menubar: 'file edit view insert format tools table help',
            plugins: [
                'advlist autolink lists link image charmap preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table code help wordcount'
            ],
            toolbar: `undo redo | blocks | 
                  bold italic underline strikethrough | 
                  alignleft aligncenter alignright alignjustify |
                  bullist numlist outdent indent | 
                  removeformat | code preview fullscreen`,
            toolbar_sticky: true,
            branding: false,
            content_style: "body { font-family:Helvetica,Arial,sans-serif; font-size:14px }"
        });
    </script>


</head>

<body class="bg-light">

    <div class="container py-5">

        <!-- Back to Home -->
        <div class="mb-4">
            <a href="index.php" class="btn btn-outline-secondary">&larr; Back to Home</a>
        </div>

        <!-- Form Card -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title mb-4">Create a New Post</h2>

                <!-- Error Message -->
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <!-- Form -->
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea name="content" id="content" rows="10" class="form-control"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Post</button>
                </form>
            </div>
        </div>

    </div>

    <!-- ✅ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>