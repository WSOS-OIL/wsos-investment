<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_input = trim($_POST['email'] ?? '');
    $password    = trim($_POST['password'] ?? '');

    if (empty($login_input) || empty($password)) {
        die("Error: Email/Username and password are required.");
    }

    // Check user by email or username
    $stmt = $conn->prepare("SELECT id, firstname, email, password FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $login_input, $login_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['firstname'];
            $_SESSION['user_email'] = $user['email'];
            
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<h3 style='color:red; font-family:sans-serif; text-align:center; margin-top:50px;'>Invalid Password. <a href='signin.php'>Try Again</a></h3>";
        }
    } else {
        echo "<h3 style='color:red; font-family:sans-serif; text-align:center; margin-top:50px;'>User not found. <a href='signup.php'>Create an Account</a></h3>";
    }
    $stmt->close();
} else {
    header("Location: signin.php");
    exit();
}
?>
