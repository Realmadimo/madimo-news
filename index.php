<?php
session_start();
require 'utils/config.php';

// Fetch genres for the dropdown menu
$genres = $pdo->query('SELECT * FROM genre ORDER BY name ASC')->fetchAll();

// Fetch the latest news or news by genre
if (isset($_GET['genre_id']) && !empty($_GET['genre_id'])) {
    $stmt = $pdo->prepare('SELECT news.*, genre.name AS genre_name FROM news JOIN genre ON news.genre_id = genre.id WHERE news.genre_id = :genre_id ORDER BY news.created_at DESC');
    $stmt->execute(['genre_id' => $_GET['genre_id']]);
    $news_list = $stmt->fetchAll();
} else {
    $stmt = $pdo->query('SELECT news.*, genre.name AS genre_name FROM news JOIN genre ON news.genre_id = genre.id ORDER BY news.created_at DESC LIMIT 5');
    $news_list = $stmt->fetchAll();
}

// Check user permissions
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
$can_write = isset($_SESSION['can_write']) && $_SESSION['can_write'];
$can_edit = isset($_SESSION['can_edit']) && $_SESSION['can_edit'];
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Portal</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">News Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    <!-- News by Genre Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="genreDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            News by Genre
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="genreDropdown">
                            <!-- All Genres Option -->
                            <li>
                                <a class="dropdown-item" href="/">همه</a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <!-- Dynamic Genre List -->
                            <?php foreach ($genres as $genre): ?>
                                <li>
                                    <a class="dropdown-item" href="?genre_id=<?= $genre['id'] ?>"><?= htmlspecialchars($genre['name']) ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
                <!-- Conditional Buttons -->
                <div class="d-flex ms-3">
                    <?php if ($is_admin): ?>
                        <a href="/dashboard" class="btn btn-warning me-2">Dashboard</a>
                    <?php elseif ($can_write || $can_edit): ?>
                        <a href="/dashboard/manage_news" class="btn btn-info me-2">Manage Articles</a>
                    <?php endif; ?>
                    <?php if ($is_logged_in): ?>
                        <a href="/logout" class="btn btn-danger">Logout</a>
                    <?php else: ?>
                        <a href="/login" class="btn btn-light">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="bg-light py-5">
        <div class="container text-center">
            <h1 class="display-4">Welcome to the News Portal</h1>
            <p class="lead">Stay updated with the latest news from around the world.</p>
        </div>
    </header>

    <!-- News Section -->
    <section id="news" class="py-5">
        <div class="container">
            <h2 class="mb-4"><?= isset($_GET['genre_id']) ? 'News by Genre' : 'Latest News' ?></h2>
            <div class="row">
                <?php if (!empty($news_list)): ?>
                    <?php foreach ($news_list as $news): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <?php if ($news['title_pic']): ?>
                                    <img src="<?= htmlspecialchars($news['title_pic']) ?>" class="card-img-top" alt="<?= htmlspecialchars($news['title']) ?>" style="height: 200px; object-fit: cover;">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($news['title']) ?></h5>
                                    <p class="text-muted"><?= htmlspecialchars($news['genre_name']) ?></p>
                                    <p class="card-text"><?= htmlspecialchars(substr($news['body'], 0, 100)) ?>...</p>
                                    <a href="/news_detail?id=<?= $news['id'] ?>" class="btn btn-primary btn-sm">Read More</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No news articles available for this genre.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">© <?= date('Y') ?> News Portal. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
