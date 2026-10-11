<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - WSOS Investment</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            padding: 20px 0;
        }
        .signup-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }
        .brand-logo {
            font-size: 24px;
            font-weight: 700;
            color: #2c5364;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-align: center;
            margin-bottom: 25px;
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
        .text-center a {
            color: #2c5364;
            text-decoration: none;
            font-weight: 600;
        }
        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="signup-card">
    <div class="brand-logo">WSOS Investment</div>
    <h4 class="text-center mb-4 fw-bold">Create an Account</h4>

    <form action="save.php" method="POST">
        <div class="mb-3">
            <label for="fullname" class="form-label font-weight-bold">Full Name</label>
            <input type="text" class="form-control" id="fullname" name="fullname" placeholder="John Doe" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label font-weight-bold">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label font-weight-bold">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter a strong password" required>
        </div>

        <button type="submit" class="btn btn-primary mb-3">Create Account</button>
    </form>

    <div class="text-center mt-3">
        <p class="mb-0 text-muted">Already have an account? <a href="signin.php">Sign In</a></p>
    </div>
</div>

</body>
</html>
