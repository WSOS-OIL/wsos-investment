<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account & KYC - WSOS Investment</title>
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
            padding: 40px 15px;
        }
        .signup-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 750px;
        }
        .brand-logo {
            font-size: 26px;
            font-weight: 700;
            color: #2c5364;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-align: center;
            margin-bottom: 10px;
        }
        .section-header {
            color: #2c5364;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 8px;
            margin-top: 25px;
            margin-bottom: 20px;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 14px;
            border: 1px solid #ced4da;
        }
        .form-control:focus, .form-select:focus {
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
    <h4 class="text-center mb-4 fw-bold">Full Account Registration</h4>

    <!-- Note enctype="multipart/form-data" for file uploads -->
    <form action="save.php" method="POST" enctype="multipart/form-data">
        
        <!-- SECTION 1: Personal Information -->
        <div class="section-header">1. Personal Details</div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="fullname" class="form-label fw-semibold">Full Name</label>
                <input type="text" class="form-control" id="fullname" name="fullname" placeholder="John Doe" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="username" class="form-label fw-semibold">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="johndoe123" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label fw-semibold">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label fw-semibold">Phone Number</label>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="+1234567890" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="dob" class="form-label fw-semibold">Date of Birth</label>
                <input type="date" class="form-control" id="dob" name="dob" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="ssn" class="form-label fw-semibold">SSN / Tax Identification Number</label>
                <input type="password" class="form-control" id="ssn" name="ssn" placeholder="XXX-XX-XXXX" required>
            </div>
        </div>

        <!-- SECTION 2: Identity Verification (KYC) -->
        <div class="section-header">2. Identity Verification (KYC)</div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="id_number" class="form-label fw-semibold">Government ID / Passport Number</label>
                <input type="text" class="form-control" id="id_number" name="id_number" placeholder="A12345678" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="id_front" class="form-label fw-semibold">ID Card (Front Photo)</label>
                <input type="file" class="form-control" id="id_front" name="id_front" accept="image/*,.pdf" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="id_back" class="form-label fw-semibold">ID Card (Back Photo)</label>
                <input type="file" class="form-control" id="id_back" name="id_back" accept="image/*,.pdf" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="id_selfie" class="form-label fw-semibold">Selfie Holding ID Card</label>
                <input type="file" class="form-control" id="id_selfie" name="id_selfie" accept="image/*" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="address_doc" class="form-label fw-semibold">Proof of Address (Utility Bill / Bank Statement)</label>
                <input type="file" class="form-control" id="address_doc" name="address_doc" accept="image/*,.pdf" required>
            </div>
        </div>

        <!-- SECTION 3: Security & Preferences -->
        <div class="section-header">3. Security & Currency</div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="currency" class="form-label fw-semibold">Preferred Currency</label>
                <select class="form-select" id="currency" name="currency" required>
                    <option value="USD" selected>USD ($)</option>
                    <option value="EUR">EUR (€)</option>
                    <option value="GBP">GBP (£)</option>
                    <option value="BTC">Bitcoin (BTC)</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="country" class="form-label fw-semibold">Country of Residence</label>
                <input type="text" class="form-control" id="country" name="country" placeholder="United States" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <label for="password" class="form-label fw-semibold">Account Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
            </div>
            <div class="col-md-6 mb-4">
                <label for="confirm_password" class="form-label fw-semibold">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Repeat password" required>
            </div>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="terms" required>
            <label class="form-check-label text-muted" for="terms">
                I confirm that all provided KYC identity documents are accurate and I agree to the <a href="#">Terms & Conditions</a>.
            </label>
        </div>

        <button type="submit" class="btn btn-primary mb-3">Submit Registration & KYC</button>
    </form>

    <div class="text-center mt-3">
        <p class="mb-0 text-muted">Already have an account? <a href="signin.php">Sign In</a></p>
    </div>
</div>

</body>
</html>
