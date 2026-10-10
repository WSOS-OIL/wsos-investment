<?php
$host = getenv('DB_HOST') ?: 'localhost';$dbname = getenv('DB_NAME') ?: 'wsos_investment';
$username = getenv('DB_USER') ?: 'root';$password = getenv('DB_PASS') ?: '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, [         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     ]);$pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `$dbname`;");

    // Auto-create necessary tables
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        account_number VARCHAR(20) DEFAULT '123456789',
        email VARCHAR(100) UNIQUE NOT NULL,
        phone VARCHAR(30) NOT NULL,
        password VARCHAR(255) NOT NULL,
        balance DECIMAL(15,2) DEFAULT 0.00,
        is_verified TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );");

    $pdo->exec("CREATE TABLE IF NOT EXISTS kyc_submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        full_name VARCHAR(150),
        address TEXT,
        id_number VARCHAR(50),
        ssn VARCHAR(50),
        phone VARCHAR(30),
        id_image_path VARCHAR(255),
        selfie_image_path VARCHAR(255),
        status VARCHAR(50) DEFAULT 'Pending Review',
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );");

    $pdo->exec("CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        type VARCHAR(50),
        method VARCHAR(50),
        amount DECIMAL(15,2),
        details TEXT,
        proof_image VARCHAR(255),
        status VARCHAR(50) DEFAULT 'Pending Review',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );");

} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}
?>