<?php
session_start();
require '../../utils/config.php';

// die($_SERVER['REQUEST_METHOD']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user details from the database
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        $_SESSION['can_write'] = $user['can_write'];
        $_SESSION['can_edit'] = $user['can_edit'];

        // Redirect to dashboard
        header('Location: /');
        exit;
    } else {
        // Invalid login
        $error_message = "Invalid username or password.";
        header("Location: ../?error=" . urlencode($error_message));
        exit;
    }
} else {
    // Redirect to login page if accessed directly
    header('Location: /login');
    exit;
}
