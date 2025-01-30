<?php
// Prevent direct access to this file
if (php_sapi_name() !== 'cli') {
    die("Access denied.");
}

// Database connection credentials
$host = 'localhost'; // Change to your host
$user = 'root'; // Change to your MySQL username
$pass = ''; // Change to your MySQL password
$db = 'news_website'; // Change to your desired database name
$charset = 'utf8mb4';

try {
    // Connect to MySQL without specifying a database
    $dsn = "mysql:host=$host;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    // Check if the database exists
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'");
    if ($stmt->rowCount() === 0) {
        // Create the database if it doesn't exist
        $pdo->exec("CREATE DATABASE $db");
        echo "Database '$db' created successfully.\n";
    }

    // Connect to the database
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    // Create users table if it doesn't exist
    $createUsersTable = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            f_name VARCHAR(50) NOT NULL,
            l_name VARCHAR(50) NOT NULL,
            is_admin TINYINT(1) DEFAULT 0,
            can_write TINYINT(1) DEFAULT 0,
            can_edit TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";
    $pdo->exec($createUsersTable);
    echo "Users table checked/created.\n";

    // Create genres table if it doesn't exist
    $createGenresTable = "
        CREATE TABLE IF NOT EXISTS genre (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) UNIQUE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";
    $pdo->exec($createGenresTable);
    echo "Genres table checked/created.\n";

    // Create news table if it doesn't exist
    $createNewsTable = "
        CREATE TABLE IF NOT EXISTS news (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            body TEXT NOT NULL,
            title_pic VARCHAR(255),
            genre_id INT NOT NULL,
            user_id INT,
            is_anonymous TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (genre_id) REFERENCES genre(id) ON DELETE SET NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        )
    ";
    $pdo->exec($createNewsTable);
    echo "News table checked/created.\n";

} catch (PDOException $e) {
    echo "Error initializing database: " . $e->getMessage() . "\n";
    exit;
}

echo "Database initialization complete.\n";
