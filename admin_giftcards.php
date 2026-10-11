<?php
include 'admin_auth.php';
include 'db.php';

// Handle Gift Card Status Updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_gc_status'])) {
    $gc_id = intval($_POST['gc_id']);
    $new_status = $_POST['status'];

    $update = $conn->prepare("UPDATE giftcard_deposits SET status = ? WHERE id = ?");
    $update->bind_param("si", $new_status, $gc_id);
    $update->execute();
    $update->close();
    
    $msg = "Gift Card submission #$gc_id status updated to '$new_status'.";
}

$result = $conn->query("
    SELECT g.*, u.firstname, u.lastname, u.email 
    FROM giftcard_deposits g 
    LEFT JOIN users u ON g.user_id = u.id 
    ORDER BY g.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gift Card Submissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">

<div class="container-fluid bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">Admin Panel: Gift Card Deposits & Verification</h3>
        <div>
            <a href="admin_users.php" class="btn btn-outline-primary btn-sm me-2">User Balances</a>
            <a href="admin_transactions.php" class="btn btn-outline-info btn-sm me-2">Transactions</a>
            <a href="admin_chat.php" class="btn btn-outline-success btn-sm me-2">Support Chat</a>
            <a href="dashboard.php" class="btn btn-secondary btn-sm">Dashboard</a>
        </div>
    </div>

    <?php if (isset($msg)): ?>
        <div class="alert alert-success"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>User Details</th>
                    <th>Card Type</th>
                    <th>Amount ($)</th>
                    <th>Card Code / PIN</th>
                    <th>Uploaded Card Image</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th>Submitted Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars(($row['firstname'] ?? 'User') . ' ' . ($row['lastname'] ?? '')); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($row['email'] ?? 'ID: #'.$row['user_id']); ?></small>
                            </td>
                            <td><strong><?php echo htmlspecialchars($row['card_type']); ?></strong></td>
                            <td class="text-success fw-bold">$<?php echo number_format($row['amount'], 2); ?></td>
                            <td><code><?php echo htmlspecialchars($row['card_code']); ?></code></td>
                            <td>
                                <?php if (!empty($row['card_image'])): ?>
                                    <a href="<?php echo htmlspecialchars($row['card_image']); ?>" target="_blank" class="btn btn-sm btn-info text-dark fw-bold">View Image</a>
                                <?php else: ?>
                                    <span class="text-muted small">No Image Attached</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $status_badge = 'bg-warning text-dark';
                                    if ($row['status'] === 'Approved') $status_badge = 'bg-success';
                                    if ($row['status'] === 'Rejected') $status_badge = 'bg-danger';
                                ?>
                                <span class="badge <?php echo $status_badge; ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                            </td>
                            <td>
                                <form method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="gc_id" value="<?php echo $row['id']; ?>">
                                    <select name="status" class="form-select form-select-sm" style="min-width: 120px;">
                                        <option value="Pending Review" <?php echo ($row['status'] === 'Pending Review') ? 'selected' : ''; ?>>Pending Review</option>
                                        <option value="Approved" <?php echo ($row['status'] === 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                        <option value="Rejected" <?php echo ($row['status'] === 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                    <button type="submit" name="update_gc_status" class="btn btn-sm btn-success fw-bold">Save</button>
                                </form>
                            </td>
                            <td><small><?php echo $row['created_at']; ?></small></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">No gift card submissions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
