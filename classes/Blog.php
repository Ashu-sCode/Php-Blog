<?php

class Blog
{
    private $pdo;

    public function __construct(pdo $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllPosts()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching posts: " . $e->getMessage());
            return [];
        }
    }

    public function getPostsPaginated($limit, $offset)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM posts ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Pagination error: " . $e->getMessage());
            return [];
        }
    }

    public function countPosts()
    {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM posts");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Count error: " . $e->getMessage());
            return 0;
        }
    }


    public function getPaginatedPosts($limit, $offset)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM posts ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalPostCount()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM posts");
        return (int) $stmt->fetchColumn();
    }



    public function getPost($id)
    {
        try {
            $stmt  = $this->pdo->prepare("SELECT * FROM posts WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching post: " . $e->getMessage());
            return null;
        }
    }


    public function addPost($title, $content, $image = null)
    {
        if (trim($title) == "" || trim($content) == "") {
            throw new Exception("All fields are required");
        }

        try {
            $sql = "INSERT INTO posts (title, content, image, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$title, $content, $image]);
            return true;
        } catch (PDOException $e) {
            error_log("Error adding post: " . $e->getMessage());
            return false;
        }
    }



    public function updatePost($id, $title, $content, $updated_at, $image = null)
    {
        if (trim($title) === '' || trim($content) === '') {
            throw new InvalidArgumentException("Title and content cannot be empty.");
        }

        try {
            if ($image !== null) {
                $stmt = $this->pdo->prepare(
                    "UPDATE posts SET title = ?, content = ?, image = ?, updated_at = ? WHERE id = ?"
                );
                $stmt->execute([$title, $content, $image, $updated_at, $id]);
            } else {
                $stmt = $this->pdo->prepare(
                    "UPDATE posts SET title = ?, content = ?, updated_at = ? WHERE id = ?"
                );
                $stmt->execute([$title, $content, $updated_at, $id]);
            }
            return true;
        } catch (PDOException $e) {
            error_log("Error updating post: " . $e->getMessage());
            return false;
        }
    }


    /*************  ✨ Windsurf Command 🌟  *************/
    /**
     * Delete a post by its ID, including its associated image file.
     *
     * @param int $id The ID of the post to delete.
     * @return bool Returns true on successful deletion, false otherwise.
     */
    public function deletePost($id)
    {
        try {
            // 🔍 Get the post to retrieve image name before deletion
            $stmt = $this->pdo->prepare("SELECT image FROM posts WHERE id = ?");
            $stmt->execute([$id]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);

            // 🗑 Delete the post from database
            $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id = ?");
            $stmt->execute([$id]);

            // ✅ Delete the image file from server
            if ($post && !empty($post['image'])) {
                $imagePath = 'uploads/' . $post['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath); // Permanently remove the file
                    unlink($imagePath); // permanently remove the file
                }
            }

            return true;
        } catch (PDOException $e) {
            // Log the error message for debugging
            error_log("Error deleting post: " . $e->getMessage());
            return false;
        }
    }
    /*******  4a8bbba7-1fad-4911-8065-76631d3fbe3c  *******/


    public static function timeAgo($datetime)
    {
        $timestamp = strtotime($datetime);

        if (!$timestamp) {
            return "invalid date";
        }

        $diff = time() - $timestamp;

        if ($diff < 0) {
            return "just now";
        }

        // Largest to smallest units
        $units = [
            31556926  => ['year', 'years'],
            2629743   => ['month', 'months'],
            604800    => ['week', 'weeks'],
            86400     => ['day', 'days'],
            3600      => ['hour', 'hours'],
            60        => ['minute', 'minutes'],
            1         => ['second', 'seconds']
        ];

        foreach ($units as $secs => $names) {
            $value = floor($diff / $secs);
            if ($value >= 1) {
                $unitName = $value === 1 ? $names[0] : $names[1];
                return "$value $unitName ago";
            }
        }

        return "just now";
    }

    // Blog.php

    public function searchAndFilterPosts($search = '', $month = '', $year = '', $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM posts WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (title LIKE :search OR content LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($month)) {
            $sql .= " AND MONTH(created_at) = :month";
            $params[':month'] = $month;
        }

        if (!empty($year)) {
            $sql .= " AND YEAR(created_at) = :year";
            $params[':year'] = $year;
        }

        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);

        // Bind all parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFilteredPostCount($search = '', $month = '', $year = '')
    {
        $sql = "SELECT COUNT(*) FROM posts WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (title LIKE :search OR content LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($month)) {
            $sql .= " AND MONTH(created_at) = :month";
            $params[':month'] = $month;
        }

        if (!empty($year)) {
            $sql .= " AND YEAR(created_at) = :year";
            $params[':year'] = $year;
        }

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getAvailableMonths()
    {
        $stmt = $this->pdo->query("SELECT DISTINCT MONTH(created_at) AS month FROM posts ORDER BY month");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getAvailableYears()
    {
        $stmt = $this->pdo->query("SELECT DISTINCT YEAR(created_at) AS year FROM posts ORDER BY year DESC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
