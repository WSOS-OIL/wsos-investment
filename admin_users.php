<?php
include 'admin_auth.php';
include 'db.php';

// Safely check and add required columns to users table
$required_user_columns = [
    "balance"        => "DECIMAL(15,2) DEFAULT 0.00",
    "profit"         => "DECIMAL(15,2) DEFAULT 0.00",
    "account_status" => "VARCHAR(50) DEFAULT 'Active'"
];

foreach ($required_user_columns as $col => $definition) {
    $check_col = $conn->query("SHOW COLUMNS FROM users LIKE '$col'");
    if ($check_col && $check_col->num_rows == 0) {
        $conn->query("ALTER TABLE users ADD COLUMN $col $definition");
    }
}

// Handle balance & status updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $target_id   = $_POST['user_id'];
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
    $update_stmt->close();
}

// Fetch full user records
$users_result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - User Accounts & Registrations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">

<div class="container-fluid bg-white p-4 rounded shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">Admin Panel: Registered Users & KYC Entries</h3>
        <div>
            <a href="admin_giftcards.php" class="btn btn-outline-primary btn-sm me-2">View Gift Cards</a>
            <a href="admin_chat.php" class="btn btn-outline-success btn-sm me-2">Support Chat Center</a>
            <a href="dashboard.php" class="btn btn-secondary btn-sm">Back to Dashboard</a>
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
            <thead class="table-dark text-nowrap">
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Contact Info</th>
                    <th>Address / Country</th>
                    <th>KYC Documents</th>
                    <th>Balance ($)</th>
                    <th>Profit ($)</th>
                    <th>Status</th>
                    <th>Update Account</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users_result && $users_result->num_rows > 0): ?>
                    <?php while($user = $users_result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $user['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? '')); ?></strong><br>
                                <small class="text-muted">Username: @<?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></small>
                            </td>
                            <td>
                                <strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?><br>
                                <strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?><br>
                                <small><strong>DOB:</strong> <?php echo htmlspecialchars($user['dob'] ?? 'N/A'); ?></small>
                            </td>
                            <td>
                                <small>
                                    <?php echo htmlspecialchars($user['street_address'] ?? ''); ?><br>
                                    <?php echo htmlspecialchars(($user['city'] ?? '') . ', ' . ($user['state'] ?? '') . ' ' . ($user['zip_code'] ?? '')); ?><br>
                                    <strong><?php echo htmlspecialchars($user['country'] ?? ''); ?></strong>
                                </small>
                            </td>
                            <td>
                                <small>
                                    <?php if (!empty($user['id_front'])): ?>
                                        <a href="<?php echo htmlspecialchars($user['id_front']); ?>" target="_blank" class="badge bg-info text-dark">ID Front</a>
                                    <?php endif; ?>
                                    <?php if (!empty($user['id_back'])): ?>
                                        <a href="<?php echo htmlspecialchars($user['id_back']); ?>" target="_blank" class="badge bg-info text-dark">ID Back</a>
                                    <?php endif; ?>
                                    <?php if (!empty($user['id_selfie'])): ?>
                                        <a href="<?php echo htmlspecialchars($user['id_selfie']); ?>" target="_blank" class="badge bg-info text-dark">Selfie</a>
                                    <?php endif; ?>
                                    <?php if (!empty($user['address_doc'])): ?>
                                        <a href="<?php echo htmlspecialchars($user['address_doc']); ?>" target="_blank" class="badge bg-info text-dark">Address Doc</a>
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td class="text-success fw-bold">$<?php echo number_format($user['balance'] ?? 0, 2); ?></td>
                            <td class="text-success fw-bold">$<?php echo number_format($user['profit'] ?? 0, 2); ?></td>
                            <td><span class="badge bg-primary"><?php echo htmlspecialchars($user['account_status'] ?? 'Active'); ?></span></td>
                            <td>
                                <form method="POST" class="row g-1" style="min-width: 200px;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <div class="col-6">
                                        <input type="number" step="0.01" class="form-control form-control-sm" name="balance" value="<?php echo $user['balance'] ?? 0.00; ?>" placeholder="Balance">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" step="0.01" class="form-control form-control-sm" name="profit" value="<?php echo $user['profit'] ?? 0.00; ?>" placeholder="Profit">
                                    </div>
                                    <div class="col-12 mt-1">
                                        <select name="account_status" class="form-select form-select-sm">
                                            <option value="Active" <?php echo (($user['account_status'] ?? '') == 'Active') ? 'selected' : ''; ?>>Active</option>
                                            <option value="Verified" <?php echo (($user['account_status'] ?? '') == 'Verified') ? 'selected' : ''; ?>>Verified</option>
                                            <option value="Pending" <?php echo (($user['account_status'] ?? '') == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Suspended" <?php echo (($user['account_status'] ?? '') == 'Suspended') ? 'selected' : ''; ?>>Suspended</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" name="update_user" class="btn btn-sm btn-success w-100 mt-1 fw-bold">Save Changes</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">No registered users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
