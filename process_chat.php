<?php
session_start();
include 'db.php';

// Auto-create chat table if missing
$conn->query("CREATE TABLE IF NOT EXISTS support_chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    sender VARCHAR(20), -- 'user' or 'admin'
    message TEXT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Store incoming chat message
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $user_id = $_SESSION['user_id'] ?? 0;
    $sender  = $_POST['sender'] ?? 'user';
    $message = trim($_POST['message']);

    if (!empty($message) && $user_id > 0) {
        $stmt = $conn->prepare("INSERT INTO support_chats (user_id, sender, message, is_read) VALUES (?, ?, ?, 0)");
        $stmt->bind_param("iss", $user_id, $sender, $message);
        $stmt->execute();
        $stmt->close();

        // Optional: Trigger admin email alert for new user message
        if ($sender === 'user') {
            @mail("admin@wsosinvestment.com", "New Live Support Message from User #$user_id", $message);
        }

        echo json_encode(["status" => "success"]);
        exit();
    }
}

// Fetch chat history for user or admin
if (isset($_GET['fetch_user_id'])) {
    $target_user_id = intval($_GET['fetch_user_id']);
    
    // Mark admin messages as read if user is fetching
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $target_user_id) {
        $conn->query("UPDATE support_chats SET is_read = 1 WHERE user_id = $target_user_id AND sender = 'admin'");
    }

    $res = $conn->query("SELECT * FROM support_chats WHERE user_id = $target_user_id ORDER BY created_at ASC");
    $messages = [];
    while ($row = $res->fetch_assoc()) {
        $messages[] = $row;
    }
    echo json_encode($messages);
    exit();
}
?>
