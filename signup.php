<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account & KYC - WSOS Investment</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <style>
        .signup-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
            padding: 40px;
            width: 100%;
            max-width: 800px;
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
    </style>
</head>
<body>

<div class="signup-card">
    <div class="brand-logo">WSOS Investment</div>
    <h4 class="text-center mb-4 fw-bold">Full Account Registration & KYC</h4>

    <form action="save.php" method="POST" enctype="multipart/form-data">
        
        <!-- SECTION 1: Personal Details -->
        <div class="section-header">1. Personal Information</div>
        
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="firstname" class="form-label fw-semibold">First Name</label>
                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="John" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="middlename" class="form-label fw-semibold">Middle Name (Optional)</label>
                <input type="text" class="form-control" id="middlename" name="middlename" placeholder="Michael">
            </div>
            <div class="col-md-4 mb-3">
                <label for="lastname" class="form-label fw-semibold">Last Name</label>
                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Doe" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="username" class="form-label fw-semibold">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="johndoe123" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="email" class="form-label fw-semibold">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="phone" class="form-label fw-semibold">Phone Number</label>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="+1 (555) 000-0000" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="dob" class="form-label fw-semibold">Date of Birth</label>
                <input type="date" class="form-control" id="dob" name="dob" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="ssn" class="form-label fw-semibold">SSN / Tax ID</label>
                <input type="password" class="form-control" id="ssn" name="ssn" placeholder="XXX-XX-XXXX" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="currency" class="form-label fw-semibold">Preferred Currency</label>
                <select class="form-select" id="currency" name="currency" required>
                    <option value="USD" selected>USD ($)</option>
                    <option value="EUR">EUR (€)</option>
                    <option value="GBP">GBP (£)</option>
                    <option value="BTC">Bitcoin (BTC)</option>
                </select>
            </div>
        </div>

        <!-- SECTION 2: Address Details -->
        <div class="section-header">2. Residential Address</div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="street_address" class="form-label fw-semibold">Street Address</label>
                <input type="text" class="form-control" id="street_address" name="street_address" placeholder="123 Financial Way, Suite 400" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="city" class="form-label fw-semibold">City</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="New York" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="state" class="form-label fw-semibold">State / Province</label>
                <select class="form-select" id="state" name="state" required>
                    <option value="" disabled selected>Select State</option>
                    <option value="AL">Alabama</option>
                    <option value="CA">California</option>
                    <option value="FL">Florida</option>
                    <option value="NY">New York</option>
                    <option value="TX">Texas</option>
                    <option value="Other">Other / International</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="zip_code" class="form-label fw-semibold">Zip / Postal Code</label>
                <input type="text" class="form-control" id="zip_code" name="zip_code" placeholder="10001" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="country" class="form-label fw-semibold">Country</label>
                <select class="form-select" id="country" name="country" required>
                    <option value="" disabled selected>Select Country</option>
                    <option value="United States">United States</option>
                    <option value="United Kingdom">United Kingdom</option>
                    <option value="Canada">Canada</option>
                    <option value="Australia">Australia</option>
                    <option value="Germany">Germany</option>
                    <option value="France">France</option>
                    <option value="Other">Other</option>
                </select>
            </div>
        </div>

        <!-- SECTION 3: Identity Verification (KYC) & Files -->
        <div class="section-header">3. Identity Verification (KYC Documents)</div>

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

        <!-- SECTION 4: Password Security -->
        <div class="section-header">4. Account Security</div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <label for="password" class="form-label fw-semibold">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Create a secure password" required>
            </div>
            <div class="col-md-6 mb-4">
                <label for="confirm_password" class="form-label fw-semibold">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Repeat password" required>
            </div>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="terms" required>
            <label class="form-check-label text-muted" for="terms">
                I certify that all details and KYC documents provided are accurate and belong to me.
            </label>
        </div>

        <button type="submit" class="btn btn-primary mb-3">Complete Registration & Submit KYC</button>
    </form>

    <div class="text-center mt-3">
        <p class="mb-0 text-muted">Already have an account? <a href="signin.php">Sign In</a></p>
    </div>
</div>

</body>
</html>
