<?php
session_start();

// Define Admin Passcode
$admin_passcode = "123Admin";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_pass = $_POST['admin_password'] ?? '';

    if ($entered_pass === $admin_passcode) {
        $_SESSION['is_admin'] = true;
        header("Location: admin_users.php");
        exit();
    } else {
        $error = "Invalid Admin Password! Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal Login - WSOS Investment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .admin-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            padding: 40px;
            max-width: 420px;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="admin-card text-center">
    <h3 class="fw-bold text-dark mb-1">WSOS Admin</h3>
    <p class="text-muted small mb-4">Authorized Management Portal Access</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3 text-start">
            <label class="form-label fw-semibold small">Admin Security Passcode</label>
            <input type="password" name="admin_password" class="form-control" placeholder="Enter Admin Password" required autofocus>
        </div>
        <button type="submit" class="btn btn-dark w-100 fw-bold py-2">Log In to Admin Panel</button>
    </form>
    
    <div class="mt-4 pt-3 border-top">
        <a href="dashboard.php" class="text-muted small text-decoration-none">&larr; Return to Main Platform</a>
    </div>
</div>

</body>
</html>
