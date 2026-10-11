<?php
include 'admin_auth.php';
include 'db.php';

// Ensure table exists for gift card submissions
$conn->query("CREATE TABLE IF NOT EXISTS giftcard_deposits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    card_type VARCHAR(100),
    amount DECIMAL(10,2),
    card_code VARCHAR(255),
    status VARCHAR(50) DEFAULT 'Pending Review',
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
<body class="bg-light p-4">

<div class="container bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">Admin Panel: Gift Card Deposits</h3>
        <div>
            <a href="admin_users.php" class="btn btn-outline-primary btn-sm me-2">User Balances</a>
            <a href="admin_chat.php" class="btn btn-outline-success btn-sm me-2">Support Chat Center</a>
            <a href="dashboard.php" class="btn btn-secondary btn-sm">Back to Dashboard</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Card Type</th>
                    <th>Amount ($)</th>
                    <th>Card Code / PIN</th>
                    <th>Status</th>
                    <th>Submitted Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td>User #<?php echo $row['user_id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['card_type']); ?></strong></td>
                            <td class="text-success fw-bold">$<?php echo number_format($row['amount'], 2); ?></td>
                            <td><code><?php echo htmlspecialchars($row['card_code']); ?></code></td>
                            <td><span class="badge bg-warning text-dark"><?php echo htmlspecialchars($row['status']); ?></span></td>
                            <td><small><?php echo $row['created_at']; ?></small></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No gift card submissions found yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
