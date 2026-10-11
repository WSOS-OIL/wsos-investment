<?php
include 'admin_auth.php';
include 'db.php';

// Handle status updates
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_tx_status'])) {
    $tx_id      = intval($_POST['tx_id']);
    $new_status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE user_transactions SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $tx_id);
    if ($stmt->execute()) {
        $msg = "Transaction #$tx_id status updated to '$new_status' successfully!";
    } else {
        $error = "Failed to update transaction: " . $conn->error;
    }
    $stmt->close();
}

// Fetch all transactions with user information
$tx_query = "
    SELECT t.*, u.firstname, u.lastname, u.email 
    FROM user_transactions t
    LEFT JOIN users u ON t.user_id = u.id 
    ORDER BY t.created_at DESC
";
$result = $conn->query($tx_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Transaction Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">

<div class="container-fluid bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">Admin Panel: Transaction Management</h3>
        <div>
            <a href="admin_users.php" class="btn btn-outline-primary btn-sm me-2">User Accounts</a>
            <a href="admin_giftcards.php" class="btn btn-outline-warning btn-sm me-2">Gift Cards</a>
            <a href="admin_chat.php" class="btn btn-outline-success btn-sm me-2">Support Chat</a>
            <a href="dashboard.php" class="btn btn-secondary btn-sm">Dashboard</a>
        </div>
    </div>

    <?php if (isset($msg)): ?>
        <div class="alert alert-success"><?php echo $msg; ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle" style="font-size: 0.9rem;">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>User Details</th>
                    <th>Type</th>
                    <th>Method</th>
                    <th>Amount ($)</th>
                    <th>Details</th>
                    <th>Current Status</th>
                    <th>Update Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($tx = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $tx['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars(($tx['firstname'] ?? 'User') . ' ' . ($tx['lastname'] ?? '')); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($tx['email'] ?? 'ID: #'.$tx['user_id']); ?></small>
                            </td>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($tx['type']); ?></span></td>
                            <td><?php echo htmlspecialchars($tx['method']); ?></td>
                            <td class="fw-bold text-success">$<?php echo number_format($tx['amount'], 2); ?></td>
                            <td><small><?php echo htmlspecialchars($tx['details']); ?></small></td>
                            <td>
                                <?php 
                                    $badge_class = 'bg-warning text-dark';
                                    if ($tx['status'] === 'Approved' || $tx['status'] === 'Completed') $badge_class = 'bg-success';
                                    if ($tx['status'] === 'Processing') $badge_class = 'bg-info text-dark';
                                    if ($tx['status'] === 'Rejected' || $tx['status'] === 'Failed') $badge_class = 'bg-danger';
                                ?>
                                <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($tx['status']); ?></span>
                            </td>
                            <td>
                                <form method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="tx_id" value="<?php echo $tx['id']; ?>">
                                    <select name="status" class="form-select form-select-sm" style="min-width: 130px;">
                                        <option value="Pending Review" <?php echo ($tx['status'] === 'Pending Review') ? 'selected' : ''; ?>>Pending Review</option>
                                        <option value="Processing" <?php echo ($tx['status'] === 'Processing') ? 'selected' : ''; ?>>Processing</option>
                                        <option value="Approved" <?php echo ($tx['status'] === 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                        <option value="Rejected" <?php echo ($tx['status'] === 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                    <button type="submit" name="update_tx_status" class="btn btn-sm btn-success fw-bold">Save</button>
                                </form>
                            </td>
                            <td><small><?php echo $tx['created_at']; ?></small></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">No transaction entries found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
