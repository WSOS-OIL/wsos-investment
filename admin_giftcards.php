<?php
session_start();
include 'db.php';

// Simple table check & creation for gift cards
$conn->query("CREATE TABLE IF NOT EXISTS giftcard_deposits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    card_type VARCHAR(100),
    amount DECIMAL(10,2),
    card_code VARCHAR(255),
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$result = $conn->query("SELECT * FROM giftcard_deposits ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gift Card Submissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
<div class="container bg-white p-4 rounded shadow-sm">
    <h3 class="mb-4 text-dark fw-bold">Admin Panel: Gift Card Deposits</h3>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Card Type</th>
                <th>Amount ($)</th>
                <th>Card Code</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['card_type']); ?></td>
                        <td>$<?php echo number_format($row['amount'], 2); ?></td>
                        <td><code><?php echo htmlspecialchars($row['card_code']); ?></code></td>
                        <td><span class="badge bg-warning text-dark"><?php echo $row['status']; ?></span></td>
                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted">No gift card submissions found yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
</body>
</html>
