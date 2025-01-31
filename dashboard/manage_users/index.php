<?php
session_start();
require '../../utils/config.php';

// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    die("Access denied. Admin privileges are required.");
}

// Handle user creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_user'])) {
    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    $can_write = isset($_POST['can_write']) ? 1 : 0;
    $can_edit = isset($_POST['can_edit']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare('INSERT INTO users (f_name, l_name, username, password, is_admin, can_write, can_edit) VALUES (:f_name, :l_name, :username, :password, :is_admin, :can_write, :can_edit)');
        $stmt->execute([
            'f_name' => $f_name,
            'l_name' => $l_name,
            'username' => $username,
            'password' => $password,
            'is_admin' => $is_admin,
            'can_write' => $can_write,
            'can_edit' => $can_edit
        ]);
        $message = "User created successfully!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $id = $_POST['user_id'];
    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];
    $username = $_POST['username'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    $can_write = isset($_POST['can_write']) ? 1 : 0;
    $can_edit = isset($_POST['can_edit']) ? 1 : 0;

    // Update password if provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE users SET f_name = :f_name, l_name = :l_name, username = :username, password = :password, is_admin = :is_admin, can_write = :can_write, can_edit = :can_edit WHERE id = :id');
        $stmt->execute([
            'f_name' => $f_name,
            'l_name' => $l_name,
            'username' => $username,
            'password' => $password,
            'is_admin' => $is_admin,
            'can_write' => $can_write,
            'can_edit' => $can_edit,
            'id' => $id
        ]);
    } else {
        $stmt = $pdo->prepare('UPDATE users SET f_name = :f_name, l_name = :l_name, username = :username, is_admin = :is_admin, can_write = :can_write, can_edit = :can_edit WHERE id = :id');
        $stmt->execute([
            'f_name' => $f_name,
            'l_name' => $l_name,
            'username' => $username,
            'is_admin' => $is_admin,
            'can_write' => $can_write,
            'can_edit' => $can_edit,
            'id' => $id
        ]);
    }

    $message = "User updated successfully!";
}

// Handle user deletion
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
    $stmt->execute(['id' => $user_id]);
    $message = "User deleted successfully!";
}

// Fetch all users
$users = $pdo->query('SELECT * FROM users ORDER BY id ASC')->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #bec8de;

        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <!-- Back Button -->
        <a href="../index.php" class="btn btn-secondary mb-3">← Back</a>

        <h1 class="mb-4">Manage Users</h1>

        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <h2>Add New User</h2>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label>First Name:</label>
                <input type="text" name="f_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Last Name:</label>
                <input type="text" name="l_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-check">
                <input type="checkbox" name="is_admin" class="form-check-input">
                <label class="form-check-label">Admin</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="can_write" class="form-check-input">
                <label class="form-check-label">Can Write</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="can_edit" class="form-check-input">
                <label class="form-check-label">Can Edit</label>
            </div>
            <button type="submit" name="create_user" class="btn btn-primary mt-3">Create User</button>
        </form>

        <h2>Existing Users</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Username</th>
                    <th>Admin</th>
                    <th>Can Write</th>
                    <th>Can Edit</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['f_name']) ?></td>
                        <td><?= htmlspecialchars($user['l_name']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= $user['is_admin'] ? 'Yes' : 'No' ?></td>
                        <td><?= $user['can_write'] ? 'Yes' : 'No' ?></td>
                        <td><?= $user['can_edit'] ? 'Yes' : 'No' ?></td>
                        <td>
                            <a href="?delete=<?= $user['id'] ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>