<?php
session_start();
require '../utils/config.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    die("Access denied. Admin privileges are required.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        <h1 class="mb-4">Admin Dashboard</h1>

        <div class="row">
            <!-- Manage Users -->
            <div class="col-md-4">
                <div class="card text-bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Manage Users</h5>
                        <p class="card-text">Create, edit, or delete user accounts and assign roles and permissions.</p>
                        <a href="./manage_users" class="btn btn-light">Go to Manage Users</a>
                    </div>
                </div>
            </div>

            <!-- Manage News -->
            <div class="col-md-4">
                <div class="card text-bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Manage News</h5>
                        <p class="card-text">Add, edit, or delete news articles for your website.</p>
                        <a href="./manage_news" class="btn btn-light">Go to Manage News</a>
                    </div>
                </div>
            </div>

            <!-- Manage Genres -->
            <div class="col-md-4">
                <div class="card text-bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Manage Genres</h5>
                        <p class="card-text">Create, edit, or delete genres for categorizing news articles.</p>
                        <a href="./manage_genres" class="btn btn-light">Go to Manage Genres</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="/logout" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>