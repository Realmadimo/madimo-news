<?php
session_start();
require '../../utils/config.php';

// Check if the user has the correct permissions
if (!isset($_SESSION['can_write']) && !isset($_SESSION['can_edit'])) {
    die("Access denied. You do not have permission to manage news.");
}

$user_id = $_SESSION['user_id']; // Assuming logged-in user ID is stored in the session
$can_write = isset($_SESSION['can_write']) && $_SESSION['can_write'];
$can_edit = isset($_SESSION['can_edit']) && $_SESSION['can_edit'];

// Handle deleting a news article
if (isset($_GET['delete'])) {
    $news_id = $_GET['delete'];

    // Fetch the article's author
    $stmt = $pdo->prepare('SELECT user_id FROM news WHERE id = :id');
    $stmt->execute(['id' => $news_id]);
    $article = $stmt->fetch();

    // Check if the user is authorized to delete
    if ($can_edit || ($can_write && $article && $article['user_id'] == $user_id)) {
        $stmt = $pdo->prepare('DELETE FROM news WHERE id = :id');
        $stmt->execute(['id' => $news_id]);
        $message = "News article deleted successfully!";
    } else {
        $message = "Access denied. You cannot delete this article.";
    }
}

// Fetch news articles
if ($can_edit) {
    // If the user can edit, show all articles
    $stmt = $pdo->query('SELECT news.*, genre.name AS genre_name FROM news JOIN genre ON news.genre_id = genre.id ORDER BY news.created_at DESC');
    $news = $stmt->fetchAll();
} elseif ($can_write) {
    // If the user can only write, show their own articles
    $stmt = $pdo->prepare('SELECT news.*, genre.name AS genre_name FROM news JOIN genre ON news.genre_id = genre.id WHERE news.user_id = :user_id ORDER BY news.created_at DESC');
    $stmt->execute(['user_id' => $user_id]);
    $news = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage News</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Manage News</h1>

    <?php if (isset($message)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Add News Button (only for can_write users) -->
    <?php if ($can_write): ?>
        <div class="mb-3">
            <a href="../news_form" class="btn btn-primary">Submit New News</a>
        </div>
    <?php endif; ?>

    <!-- News Articles Table -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Genre</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($news as $article): ?>
            <tr>
                <td><?= htmlspecialchars($article['id']) ?></td>
                <td><?= htmlspecialchars($article['title']) ?></td>
                <td><?= htmlspecialchars($article['genre_name']) ?></td>
                <td>
                    <?php if ($article['title_pic']): ?>
                        <img src="<?= htmlspecialchars($article['title_pic']) ?>" alt="Image" style="width: 100px; height: auto;">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($can_edit || ($can_write && $article['user_id'] == $user_id)): ?>
                        <a href="../news_form?id=<?= $article['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?delete=<?= $article['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this news article?')">Delete</a>
                    <?php else: ?>
                        <span class="text-muted">No Actions</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
