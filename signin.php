<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - WSOS Investment</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Force dark gradient background across html and body */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%) !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333333;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        .auth-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }

        .brand-logo {
            font-size: 26px;
            font-weight: 700;
            color: #2c5364;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #ced4da;
        }

        .form-control:focus {
            border-color: #2c5364;
            box-shadow: 0 0 0 0.25rem rgba(44, 83, 100, 0.25);
        }

        .btn-primary {
            background-color: #2c5364;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #203a43;
        }

        a {
            color: #2c5364;
            text-decoration: none;
            font-weight: 600;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="brand-logo">WSOS Investment</div>
    <h4 class="text-center mb-4 fw-bold">Sign In to Your Account</h4>

    <?php if (isset($_GET['registration']) && $_GET['registration'] === 'success'): ?>
        <div class="alert alert-success text-center mb-4">
            Registration successful! Please sign in below.
        </div>
    <?php endif; ?>

    <form action="signin_process.php" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email Address or Username</label>
            <input type="text" class="form-control" id="email" name="email" placeholder="name@example.com" required>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label fw-semibold">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
        </div>

        <button type="submit" class="btn btn-primary mb-3">Sign In</button>
    </form>

    <div class="text-center mt-3">
        <p class="mb-0 text-muted">Don't have an account? <a href="signup.php">Create an Account</a></p>
    </div>
</div>

</body>
</html>
