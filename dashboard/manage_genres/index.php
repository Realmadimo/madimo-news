<?php
session_start();
require '../../utils/config.php';

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    die("Access denied. Admin privileges are required.");
}

$message = '';

// Handle adding a genre
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_genre'])) {
    $name = $_POST['name'];

    try {
        $stmt = $pdo->prepare('INSERT INTO genre (name) VALUES (:name)');
        $stmt->execute(['name' => $name]);
        $message = "Genre added successfully!";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            $message = "Genre already exists!";
        } else {
            $message = "Error: " . $e->getMessage();
        }
    }
}

// Handle editing a genre
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_genre'])) {
    $id = $_POST['genre_id'];
    $name = $_POST['name'];

    try {
        $stmt = $pdo->prepare('UPDATE genre SET name = :name WHERE id = :id');
        $stmt->execute(['name' => $name, 'id' => $id]);
        $message = "Genre updated successfully!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Handle deleting a genre
if (isset($_GET['delete'])) {
    $genre_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare('DELETE FROM genre WHERE id = :id');
        $stmt->execute(['id' => $genre_id]);
        $message = "Genre deleted successfully!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch all genres
$genres = $pdo->query('SELECT * FROM genre ORDER BY name ASC')->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Genres</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Manage Genres</h1>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Add Genre Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h2 class="h5">Add Genre</h2>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Genre Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <button type="submit" name="add_genre" class="btn btn-primary">Add Genre</button>
            </form>
        </div>
    </div>

    <!-- Existing Genres Table -->
    <div class="card">
        <div class="card-header">
            <h2 class="h5">Existing Genres</h2>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($genres as $genre): ?>
                    <tr>
                        <td><?= htmlspecialchars($genre['id']) ?></td>
                        <td><?= htmlspecialchars($genre['name']) ?></td>
                        <td>
                            <!-- Edit Form -->
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $genre['id'] ?>">
                                Edit
                            </button>

                            <!-- Delete Button -->
                            <a href="?delete=<?= $genre['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this genre?')">Delete</a>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?= $genre['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $genre['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel<?= $genre['id'] ?>">Edit Genre</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="genre_id" value="<?= $genre['id'] ?>">
                                                <div class="mb-3">
                                                    <label for="genreName<?= $genre['id'] ?>" class="form-label">Genre Name</label>
                                                    <input type="text" name="name" id="genreName<?= $genre['id'] ?>" class="form-control" value="<?= htmlspecialchars($genre['name']) ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" name="edit_genre" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
