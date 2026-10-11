<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get inputs from the form safely
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $email    = isset($_POST['email'])    ? trim($_POST['email'])    : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($email) && !empty($password)) {
        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $fullname, $email, $password);

        if ($stmt->execute()) {
            // Success - Redirect to sign in page
            header("Location: signin.php?msg=success");
            exit();
        } else {
            echo "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Please fill in all required fields.";
    }
} else {
    header("Location: signin.php");
    exit();
}
?>
