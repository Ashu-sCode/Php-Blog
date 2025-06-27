<?php
require 'auth.php';
require '../db.php';
require '../classes/Blog.php';
require_once '../helpers/flash.php';

$blog = new Blog($pdo);

// ✅ Handle search and filter inputs
$search = $_GET['search'] ?? '';
$month  = $_GET['month'] ?? '';
$year   = $_GET['year'] ?? '';
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit  = 5;
$offset = ($page - 1) * $limit;

// ✅ Get filtered posts & total count
$posts = $blog->searchAndFilterPosts($search, $month, $year, $limit, $offset);
$totalFilteredPosts = $blog->getFilteredPostCount($search, $month, $year);
$totalPages = ceil($totalFilteredPosts / $limit);

// 🗓️ Month and Year dropdown options
$months = $blog->getAvailableMonths();
$years = $blog->getAvailableYears();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php show_flash_toast(); ?>

<div class="container py-5">
    <h1 class="mb-4">📋 Admin Dashboard</h1>

    <!-- 🔍 Filters -->
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by title..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-3">
            <select name="month" class="form-select">
                <option value="">📅 All Months</option>
                <?php foreach ($months as $m): ?>
                    <option value="<?php echo $m; ?>" <?php if ($month === $m) echo 'selected'; ?>>
                        <?php echo date("F", mktime(0, 0, 0, $m, 1)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="year" class="form-select">
                <option value="">📆 All Years</option>
                <?php foreach ($years as $y): ?>
                    <option value="<?php echo $y; ?>" <?php if ($year === $y) echo 'selected'; ?>>
                        <?php echo $y; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">🔍 Filter</button>
        </div>
    </form>

    <!-- 📋 Post List -->
    <?php if (!empty($posts)): ?>
        <ul class="list-group mb-4">
            <?php foreach ($posts as $post): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?php echo htmlspecialchars($post['title']); ?></strong><br>
                        <small><?php echo date('d M Y, H:i', strtotime($post['created_at'])); ?></small>
                    </div>
                    <div class="d-flex gap-1">
                        <a href="../edit.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $post['id']; ?>">Delete</button>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal<?php echo $post['id']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirm Deletion</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete "<strong><?php echo htmlspecialchars($post['title']); ?></strong>"?
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <a href="../delete.php?id=<?php echo $post['id']; ?>" class="btn btn-danger">Yes, Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- ⏪ Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php if ($i === $page) echo 'active'; ?>">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-info">No posts found matching your filter/search.</div>
    <?php endif; ?>

    <!-- 🧭 Actions -->
    <div class="mt-4 d-flex gap-2">
        <a href="../create.php" class="btn btn-success">+ Create New Post</a>
        <a href="../index.php" class="btn btn-outline-primary">🔍 View Public Blog</a>
        <a href="logout.php" class="btn btn-outline-danger">🚪 Logout</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
