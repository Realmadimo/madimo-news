<?php
session_start();
require '../../utils/config.php';

// Check if the user has the correct permissions
if (!isset($_SESSION['can_write']) || !$_SESSION['can_write']) {
    die("Access denied. You do not have permission to manage news.");
}

$is_edit = false;

// Fetch genres for the dropdown
$genres = $pdo->query('SELECT * FROM genre ORDER BY name ASC')->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $body = $_POST['body'];
    $genre_id = $_POST['genre_id'];
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    $user_id = $_SESSION['user_id']; // Assuming the user is logged in

    $title_pic = $_POST['existing_title_pic'] ?? '';

    // Remove the existing picture if "Remove Image" is checked
    if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
        if ($title_pic && file_exists($title_pic)) {
            unlink($title_pic);
        }
        $title_pic = '';
    }

    // Handle file upload
    if (isset($_FILES['title_pic']) && $_FILES['title_pic']['error'] == 0) {
        $target_dir = "/uploads/";
        $new_pic = $target_dir . basename($_FILES['title_pic']['name']);
        if (move_uploaded_file($_FILES['title_pic']['tmp_name'], "../../$new_pic")) {
            // Delete the previous image if it exists
            if ($title_pic && file_exists("../../$title_pic")) {
                unlink("../../$title_pic");
            }
            $title_pic = $new_pic;
        }
    }

    // Determine if it's an edit or a new submission
    if (isset($_POST['news_id'])) {
        $id = $_POST['news_id'];
        $stmt = $pdo->prepare('UPDATE news SET title = :title, body = :body, title_pic = :title_pic, genre_id = :genre_id, is_anonymous = :is_anonymous WHERE id = :id');
        $stmt->execute([
            'title' => $title,
            'body' => $body,
            'title_pic' => $title_pic,
            'genre_id' => $genre_id,
            'is_anonymous' => $is_anonymous,
            'id' => $id
        ]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO news (title, body, title_pic, genre_id, user_id, is_anonymous) VALUES (:title, :body, :title_pic, :genre_id, :user_id, :is_anonymous)');
        $stmt->execute([
            'title' => $title,
            'body' => $body,
            'title_pic' => $title_pic,
            'genre_id' => $genre_id,
            'user_id' => $user_id,
            'is_anonymous' => $is_anonymous
        ]);
    }

    // Redirect to manage_news.php after submission
    header('Location: ../manage_news');
    exit;
}

// Handle editing (fetch news details)
if (isset($_GET['id'])) {
    $is_edit = true;
    $stmt = $pdo->prepare('SELECT * FROM news WHERE id = :id');
    $stmt->execute(['id' => $_GET['id']]);
    $news = $stmt->fetch();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_edit ? 'Edit News' : 'Submit News' ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #bec8de;

        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4"><?= $is_edit ? 'Edit News Article' : 'Submit New News Article' ?></h1>

        <form method="POST" enctype="multipart/form-data">
            <?php if ($is_edit): ?>
                <input type="hidden" name="news_id" value="<?= $news['id'] ?>">
                <input type="hidden" name="existing_title_pic" value="<?= htmlspecialchars($news['title_pic']) ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label>Title:</label>
                <input type="text" name="title" class="form-control"
                    value="<?= $is_edit ? htmlspecialchars($news['title']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label>Body:</label>
                <textarea name="body" class="form-control" rows="5"
                    required><?= $is_edit ? htmlspecialchars($news['body']) : '' ?></textarea>
            </div>
            <div class="mb-3">
                <label>Genre:</label>
                <select name="genre_id" class="form-select" required>
                    <?php foreach ($genres as $genre): ?>
                        <option value="<?= $genre['id'] ?>" <?= $is_edit && $news['genre_id'] == $genre['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($genre['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Title Image:</label>
                <input type="file" name="title_pic" class="form-control">
                <?php if ($is_edit && $news['title_pic']): ?>
                    <p>Current Image: <img src="<?= htmlspecialchars($news['title_pic']) ?>" alt="Image"
                            style="width: 100px; height: auto;"></p>
                    <div class="form-check">
                        <input type="checkbox" name="remove_image" value="1" id="removeImage" class="form-check-input">
                        <label for="removeImage" class="form-check-label">Remove Current Image</label>
                    </div>
                <?php endif; ?>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="is_anonymous" id="isAnonymous" class="form-check-input" <?= $is_edit && $news['is_anonymous'] ? 'checked' : '' ?>>
                <label for="isAnonymous" class="form-check-label">Post as Anonymous</label>
            </div>
            <button type="submit" class="btn btn-primary"><?= $is_edit ? 'Update News' : 'Submit News' ?></button>
            <a href="../manage_news" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>