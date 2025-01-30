<?php
require '../../utils/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $f_name = trim($_POST['f_name']);
    $l_name = trim($_POST['l_name']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate required fields
    if (empty($f_name) || empty($l_name) || empty($username) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
        header("Location: ../?error=" . urlencode($error_message));
        exit;
    }

    // Validate password confirmation
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
        header("Location: ../?error=" . urlencode($error_message));
        exit;
    }

    // Check if the username already exists
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    if ($stmt->fetchColumn() > 0) {
        $error_message = "Username already exists.";
        header("Location: ../?error=" . urlencode($error_message));
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the user into the database
    $stmt = $pdo->prepare('INSERT INTO users (f_name, l_name, username, password) VALUES (:f_name, :l_name, :username, :password)');
    $stmt->execute([
        'f_name' => $f_name,
        'l_name' => $l_name,
        'username' => $username,
        'password' => $hashed_password,
    ]);

    // Redirect to login page with success message
    $success_message = "Account created successfully! Please log in.";
    header("Location: ../?success=" . urlencode($success_message));
    exit;
} else {
    // Redirect to login page if accessed directly
    header('Location: /login');
    exit;
}
