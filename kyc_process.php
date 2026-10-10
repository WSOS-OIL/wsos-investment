<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $full_name = htmlspecialchars($_POST['full_name']);
    $address = htmlspecialchars($_POST['address']);
    $id_number = htmlspecialchars($_POST['id_number']);
    $ssn = htmlspecialchars($_POST['ssn']);
    $phone = htmlspecialchars($_POST['phone']);

    $upload_dir = 'admin_reviews/uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Handle ID Image Upload
    $id_image_name = time() . '_id_' . basename($_FILES['id_image']['name']);
    $id_image_path = $upload_dir . $id_image_name;
    move_uploaded_file($_FILES['id_image']['tmp_name'], $id_image_path);

    // Handle Selfie Image Upload
    $selfie_name = time() . '_selfie_' . basename($_FILES['selfie']['name']);
    $selfie_path = $upload_dir . $selfie_name;
    move_uploaded_file($_FILES['selfie']['tmp_name'], $selfie_path);

    // Store secure identity records separately for Admin review
    $stmt = $pdo->prepare("INSERT INTO kyc_submissions (user_id, full_name, address, id_number, ssn, phone, id_image_path, selfie_image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $full_name, $address, $id_number, $ssn, $phone, $id_image_path, $selfie_path]);

    header("Location: dashboard.php?kyc=submitted");
    exit();
}
?>