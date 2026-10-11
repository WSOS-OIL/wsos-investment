<?php
session_start();
include 'db.php';

// Ensure balance & profit columns exist in database
$conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS balance DECIMAL(15,2) DEFAULT 0.00");
$conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS profit DECIMAL(15,2) DEFAULT 0.00");
$conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS account_status VARCHAR(50) DEFAULT 'Active'");

// Handle balance update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $target_id = $_POST['user_id'];
    $new_balance = $_POST['balance'];
    $new_profit  = $_POST['profit'];
    $new_status  = $_POST['account_status'];

    $update_stmt = $conn->prepare("UPDATE users SET balance = ?, profit = ?, account_status = ? WHERE id = ?");
    $update_stmt->bind_param("ddsi", $new_balance, $new_profit, $new_status, $target_id);
    
    if ($update_stmt->execute()) {
        $msg = "User #$target_id updated successfully!";
    } else {
        $error = "Error updating user: " . $conn->error;
    }
}

// Fetch all registered users
$users_result = $conn->query("SELECT id, firstname, lastname, email, username, balance, profit, account_status FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage User Balances</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">

<div class="container bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">Admin Panel: User Accounts & Balances</h3>
        <a href="admin_giftcards.php" class="btn btn-outline-primary btn-sm">View Gift Cards</a>
    </div>

    <?php if (isset($msg)): ?>
        <div class="alert alert-success"><?php echo $msg; ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>User Details</th>
                    <th>Email / Username</th>
                    <th>Current Balance ($)</th>
                    <th>Current Profit ($)</th>
                    <th>Status</th>
                    <th>Update Account</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users_result && $users_result->num_rows > 0): ?>
                    <?php while($user = $users_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '')); ?></strong></td>
                            <td>
                                <?php echo htmlspecialchars($user['email']); ?><br>
                                <small class="text-muted">@<?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></small>
                            </td>
                            <td class="text-success fw-bold">$<?php echo number_format($user['balance'] ?? 0, 2); ?></td>
                            <td class="text-success fw-bold">$<?php echo number_format($user['profit'] ?? 0, 2); ?></td>
                            <td><span class="badge bg-primary"><?php echo htmlspecialchars($user['account_status'] ?? 'Active'); ?></span></td>
                            <td>
                                <form method="POST" class="row g-2">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <div class="col-4">
                                        <input type="number" step="0.01" class="form-control form-control-sm" name="balance" value="<?php echo $user['balance'] ?? 0.00; ?>" placeholder="Balance">
                                    </div>
                                    <div class="col-4">
                                        <input type="number" step="0.01" class="form-control form-control-sm" name="profit" value="<?php echo $user['profit'] ?? 0.00; ?>" placeholder="Profit">
                                    </div>
                                    <div class="col-4">
                                        <select name="account_status" class="form-select form-select-sm">
                                            <option value="Active" <?php echo ($user['account_status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                            <option value="Verified" <?php echo ($user['account_status'] == 'Verified') ? 'selected' : ''; ?>>Verified</option>
                                            <option value="Pending" <?php echo ($user['account_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Suspended" <?php echo ($user['account_status'] == 'Suspended') ? 'selected' : ''; ?>>Suspended</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" name="update_user" class="btn btn-sm btn-success w-100 mt-1">Save Changes</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center text-muted">No registered users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
