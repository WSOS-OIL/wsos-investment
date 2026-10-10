<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars($_POST['phone']);
    $raw_password = $_POST['password'];
    
    // Hash the password securely
    $hashed_password = password_hash($raw_password, PASSWORD_BCRYPT);
    $account_number = '123456789'; // Fixed account number requirement

    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        die("Error: Email address already registered.");
    }

    // Insert user into database
    $stmt = $pdo->prepare("INSERT INTO users (account_number, email, phone, password) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$account_number, $email, $phone, $hashed_password])) {
        $user_id = $pdo->lastInsertId();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $email;

        // Simulate confirmation code & Thank you email/SMS logic
        $confirmation_code = rand(100000, 999999);
        $_SESSION['code'] = $confirmation_code;
        
        // In production, integrate PHPMailer or Twilio SMS API here:
        // mail($email, "Welcome to WSOS Investment", "Thank you for signing up! Your confirmation code is: $confirmation_code");

        header("Location: verify_code.php?status=success");
        exit();
    } else {
        die("Registration failed. Please try again.");
    }
}
?>
