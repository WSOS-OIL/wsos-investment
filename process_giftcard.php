<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Self-healing check for giftcard_deposits table
$conn->query("CREATE TABLE IF NOT EXISTS giftcard_deposits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    card_type VARCHAR(100),
    amount DECIMAL(15,2),
    card_code VARCHAR(255),
    card_image VARCHAR(255) DEFAULT NULL,
    status VARCHAR(50) DEFAULT 'Pending Review',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Self-healing check to ensure card_image column exists
$check_img = $conn->query("SHOW COLUMNS FROM giftcard_deposits LIKE 'card_image'");
if ($check_img && $check_img->num_rows == 0) {
    $conn->query("ALTER TABLE giftcard_deposits ADD COLUMN card_image VARCHAR(255) DEFAULT NULL");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $card_type = $_POST['card_type'] ?? 'US Gift Card';
    $amount    = floatval($_POST['amount'] ?? 0);
    $card_code = $_POST['card_code'] ?? '';
    $card_image_path = null;

    // Handle File Upload for Gift Card Image
    if (isset($_FILES['giftcard_image']) && $_FILES['giftcard_image']['error'] == 0) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_ext  = pathinfo($_FILES['giftcard_image']['name'], PATHINFO_EXTENSION);
        $file_name = 'gc_' . $user_id . '_' . time() . '.' . $file_ext;
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['giftcard_image']['tmp_name'], $target_path)) {
            $card_image_path = $target_path;
        }
    }

    // 1. Insert into giftcard_deposits table for dedicated giftcard audit
    $gc_stmt = $conn->prepare("INSERT INTO giftcard_deposits (user_id, card_type, amount, card_code, card_image, status) VALUES (?, ?, ?, ?, ?, 'Pending Review')");
    $gc_stmt->bind_param("isdss", $user_id, $card_type, $amount, $card_code, $card_image_path);
    $gc_stmt->execute();
    $gc_stmt->close();

    // 2. Insert into user_transactions table so it displays in user's transaction log as Pending Review
    $details = "Gift Card: " . $card_type . " | Code: " . $card_code;
    if ($card_image_path) {
        $details .= " | Image Attached";
    }
    
    $tx_stmt = $conn->prepare("INSERT INTO user_transactions (user_id, type, method, amount, details, status) VALUES (?, 'Deposit', ?, ?, ?, 'Pending Review')");
    $tx_stmt->bind_param("isds", $user_id, $card_type, $amount, $details);
    $tx_stmt->execute();
    $tx_stmt->close();

    // 3. Set Receipt session variable for dashboard screen display
    $_SESSION['giftcard_receipt'] = [
        'type'      => 'Gift Card Deposit',
        'method'    => $card_type,
        'amount'    => $amount,
        'details'   => $details,
        'date'      => date('Y-m-d H:i:s'),
        'reference' => 'WSOS-GC-' . strtoupper(substr(md5(mt_rand()), 0, 8))
    ];

    header("Location: dashboard.php");
    exit();
}
?>
