<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - WSOS Investment</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>

<div class="dashboard-card" style="max-width: 800px; margin: auto;">
    <div class="brand-logo">WSOS Investment</div>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h4>
        <span class="badge bg-success p-2">KYC Verified</span>
    </div>

    <p class="text-muted">Your investment account is fully active and ready.</p>
    <hr class="my-4">

    <div class="row text-center mb-4">
        <div class="col-md-6 mb-3">
            <div class="p-4 border rounded bg-light shadow-sm">
                <h6 class="text-muted">Portfolio Balance</h6>
                <h3 class="text-success fw-bold">$0.00</h3>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="p-4 border rounded bg-light shadow-sm">
                <h6 class="text-muted">Account Status</h6>
                <h3 class="text-primary fw-bold">Active</h3>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <span class="text-muted small">Logged in as: <strong><?php echo htmlspecialchars($_SESSION['user_email']); ?></strong></span>
        <a href="logout.php" class="btn btn-outline-danger px-4">Sign Out</a>
    </div>
</div>

</body>
</html>
