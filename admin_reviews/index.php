<?php
// Simple admin session authentication (change password as needed)
session_start();
$admin_pass = "wsosadmin123"; // Change this secure password for your admin panel

if (isset($_POST['admin_login'])) {
    if ($_POST['password'] === $admin_pass) {
        $_SESSION['is_admin'] = true;
    } else {
        $error = "Incorrect Admin Password";
    }
}

if (isset($_GET['logout'])) {
    unset($_SESSION['is_admin']);
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['is_admin'])):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WSOS Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
</head>
<body class="bg-dark d-flex align-items-center py-5" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card p-4 shadow">
                    <h3 class="text-center mb-3">Admin Portal</h3>
                    <?php if (isset($error)): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Admin Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="admin_login" class="btn btn-primary w-100">Log In</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php 
exit();
endif;

// Include main database connection (adjust path since we are inside admin_reviews folder)
require '../db.php';

// Fetch all KYC submissions
$kyc_stmt = $pdo->query("SELECT k.*, u.email FROM kyc_submissions k JOIN users u ON k.user_id = u.id ORDER BY k.submitted_at DESC");
$kyc_records = $kyc_stmt->fetchAll();

// Fetch all transactions/deposits
$tx_stmt = $pdo->query("SELECT t.*, u.email, u.account_number FROM transactions t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC");
$tx_records = $tx_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WSOS Admin Review Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark px-4">
        <span class="navbar-brand fw-bold">WSOS Investment - Admin Review Panel</span>
        <a href="index.php?logout=true" class="btn btn-outline-danger btn-sm">Admin Logout</a>
    </nav>

    <div class="container my-5">
        <!-- KYC / Identity Verifications -->
        <div class="card shadow-sm p-4 mb-5">
            <h3 class="mb-3 text-primary">Pending Customer KYC & Identity Submissions</h3>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>User Email</th>
                            <th>Full Name</th>
                            <th>Address</th>
                            <th>ID Number / SSN</th>
                            <th>Phone</th>
                            <th>ID Document</th>
                            <th>Selfie</th>
                            <th>Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kyc_records)): ?>
                            <tr><td colspan="8" class="text-center">No KYC submissions found.</td></tr>
                        <?php else: foreach ($kyc_records as $kyc): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($kyc['email']); ?></td>
                                <td><?php echo htmlspecialchars($kyc['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($kyc['address']); ?></td>
                                <td>ID: <?php echo htmlspecialchars($kyc['id_number']); ?><br>SSN: <?php echo htmlspecialchars($kyc['ssn']); ?></td>
                                <td><?php echo htmlspecialchars($kyc['phone']); ?></td>
                                <td><a href="../<?php echo $kyc['id_image_path']; ?>" target="_blank" class="btn btn-sm btn-outline-info">View ID</a></td>
                                <td><a href="../<?php echo $kyc['selfie_image_path']; ?>" target="_blank" class="btn btn-sm btn-outline-info">View Selfie</a></td>
                                <td><?php echo $kyc['submitted_at']; ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transactions & Deposit Proofs -->
        <div class="card shadow-sm p-4">
            <h3 class="mb-3 text-success">Customer Transfers, Deposits & Gift Cards</h3>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>User / Account</th>
                            <th>Type</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Details / Notes</th>
                            <th>Proof / Receipt</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tx_records)): ?>
                            <tr><td colspan="8" class="text-center">No transactions recorded.</td></tr>
                        <?php else: foreach ($tx_records as $tx): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($tx['email']); ?><br><small class="text-muted">Acct: <?php echo $tx['account_number']; ?></small></td>
                                <td><span class="badge bg-secondary"><?php echo ucfirst($tx['type']); ?></span></td>
                                <td><?php echo ucfirst($tx['method']); ?></td>
                                <td class="fw-bold text-success">$<?php echo number_format($tx['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($tx['details']); ?></td>
                                <td>
                                    <?php if (!empty($tx['proof_image'])): ?>
                                        <a href="../<?php echo $tx['proof_image']; ?>" target="_blank" class="btn btn-sm btn-outline-success">View Proof</a>
                                    <?php else: ?>
                                        <span class="text-muted">None</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-warning text-dark"><?php echo $tx['status']; ?></span></td>
                                <td><?php echo $tx['created_at']; ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>