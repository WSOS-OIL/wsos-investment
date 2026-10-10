<?php
session_start();
require 'db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact = htmlspecialchars($_POST['contact']); // Email or Phone

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
    $stmt->execute([$contact, $contact]);
    $user = $stmt->fetch();

    if ($user) {
        $reset_code = rand(100000, 999999);
        $_SESSION['reset_code'] = $reset_code;
        $_SESSION['reset_user'] = $contact;
        
        // In production, integrate email or SMS gateway here:
        // mail($contact, "Password Reset Code", "Your confirmation code is: $reset_code");
        
        $message = "A confirmation code has been sent to your email/phone. Code (Simulated): <strong>$reset_code</strong>";
    } else {
        $error = "No account found matching that email or phone number.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WSOS Investment - Forgot Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
</head>
<body class="bg-light d-flex align-items-center py-5" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm p-4">
                    <h3 class="text-center mb-3">Password Recovery</h3>
                    <p class="text-muted text-center mb-4">Enter your registered email or phone to receive a reset code.</p>
                    
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="forgot_password.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email or Phone Number</label>
                            <input type="text" name="contact" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 mb-3">Send Confirmation Code</button>
                        <div class="text-center">
                            <a href="signin.php" class="text-decoration-none">Back to Sign In</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>