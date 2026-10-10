<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $type = $_POST['type']; // 'transfer' or 'deposit'
    $method = $_POST['method']; // 'crypto', 'wire', 'giftcard', 'cashapp', etc.
    $amount = floatval($_POST['amount']);
    $details = isset($_POST['details']) ? htmlspecialchars($_POST['details']) : '';
    
    $proof_path = '';
    if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'admin_reviews/receipts/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $proof_path = $upload_dir . time() . '_' . basename($_FILES['proof_image']['name']);
        move_uploaded_file($_FILES['proof_image']['tmp_name'], $proof_path);
    }

    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, method, amount, details, proof_image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $type, $method, $amount, $details, $proof_path]);

    if ($type === 'transfer') {
        echo json_encode(["status" => "success", "message" => "Our team will verify your transfer. It should be available within 15 minutes."]);
    } else {
        echo json_encode(["status" => "success", "message" => "Our team will get back with you shortly after verifying your payment."]);
    }
}
?>