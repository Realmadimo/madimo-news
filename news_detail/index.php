<?php
require '../utils/config.php';

// Check if an article ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("News article not found.");
}

// Fetch the article details along with the author's name
$stmt = $pdo->prepare(
    'SELECT news.*, 
            genre.name AS genre_name, 
            users.f_name, 
            users.l_name 
     FROM news 
     JOIN genre ON news.genre_id = genre.id 
     LEFT JOIN users ON news.user_id = users.id 
     WHERE news.id = :id'
);
$stmt->execute(['id' => $_GET['id']]);
$news = $stmt->fetch();

if (!$news) {
    die("News article not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($news['title']) ?> - News Portal</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #525e79;

        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">News Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- News Detail Section -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <div class="card" style="background-color: #cfefe1">
                    <?php if ($news['title_pic']): ?>
                        <img src="<?= htmlspecialchars($news['title_pic']) ?>" class="card-img-top"
                            alt="<?= htmlspecialchars($news['title']) ?>">
                    <?php endif; ?>
                    <div class="card-body">
                        <h1 class="card-title"><?= htmlspecialchars($news['title']) ?></h1>
                        <p class="text-muted">
                            Category: <?= htmlspecialchars($news['genre_name']) ?>
                        </p>
                        <p class="text-muted">
                            Author:
                            <?= $news['is_anonymous'] ? 'Anonymous' : htmlspecialchars($news['f_name'] . ' ' . $news['l_name']) ?>
                        </p>
                        <p class="card-text"><?= nl2br(htmlspecialchars($news['body'])) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- Sidebar -->
                <div class="card mb-4" style="background-color: #a1ddc2">
                    <div class="card-header">
                        <h5>More News</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php
                        // Fetch some additional news articles for the sidebar
                        $stmt = $pdo->query('SELECT id, title FROM news ORDER BY created_at DESC LIMIT 5');
                        $more_news = $stmt->fetchAll();
                        foreach ($more_news as $more_article):
                            ?>
                            <li class="list-group-item" style="background-color: #cfefe1">
                                <a
                                    href="news_detail.php?id=<?= $more_article['id'] ?>"><?= htmlspecialchars($more_article['title']) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">© <?= date('Y') ?> News Portal. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>