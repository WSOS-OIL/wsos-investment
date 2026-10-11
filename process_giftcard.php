<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id   = $_SESSION['user_id'];
    $card_type = trim($_POST['card_type']);
    $amount    = trim($_POST['amount']);
    $card_code = trim($_POST['card_code']);

    // Ensure table exists
    $conn->query("CREATE TABLE IF NOT EXISTS giftcard_deposits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        card_type VARCHAR(100),
        amount DECIMAL(10,2),
        card_code VARCHAR(255),
        status VARCHAR(50) DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $stmt = $conn->prepare("INSERT INTO giftcard_deposits (user_id, card_type, amount, card_code) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $user_id, $card_type, $amount, $card_code);
    
    if ($stmt->execute()) {
        echo "<script>alert('Gift card submitted successfully! Our representative will get back with you shortly.'); window.location.href='dashboard.php';</script>";
    } else {
        echo "Error saving gift card: " . $stmt->error;
    }
    $stmt->close();
}
?>
