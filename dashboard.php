<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WSOS Investment Platform - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
        <a class="navbar-brand fw-bold" href="#">WSOS INVESTMENT</a>
        <div class="ms-auto">
            <span class="text-white me-3">Account No: <strong><?php echo $user['account_number']; ?></strong></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Log Out</a>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm p-3 mb-4">
                    <h5>Portfolio Balance</h5>
                    <h3 class="text-success">$<?php echo number_format($user['balance'], 2); ?></h3>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm p-3 mb-4">
                    <h5>Market Rate Live Status</h5>
                    <iframe src="https://widget.coinlib.io/widget?type=horizontal_v2&theme=light&pref_coin_id=1505&invert_price=no" width="100% "height="36px" scrolling="auto" style="border:0; margin:0; padding:0;"></iframe>
                </div>
            </div>
        </div>

        <!-- Transfer & Deposit Actions -->
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm p-4">
                    <h4>Crypto / Wire Transfer</h4>
                    <form action="process_transaction.php" method="POST">
                        <input type="hidden" name="type" value="transfer">
                        <div class="mb-3">
                            <label class="form-label">Transfer Method</label>
                            <select name="method" class="form-select" id="transferMethod" required>
                                <option value="crypto">Crypto Transfer (Bitcoin / Litecoin)</option>
                                <option value="wire">Wire / Bank Transfer</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount ($)</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                        <div id="bankDetailsBox" class="alert alert-secondary d-none">
                            <strong>Routing Number:</strong> 1234567789<br>
                            <strong>Account Number:</strong> 123456789
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit Transfer</button>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm p-4">
                    <h4>Deposit / Purchase Funds</h4>
                    <form action="process_transaction.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="type" value="deposit">
                        <div class="mb-3">
                            <label class="form-label">Deposit Option</label>
                            <select name="method" class="form-select" id="depositMethod" required>
                                <option value="giftcard">Gift Card (Apple, Amazon, Sephora, eBay, Vanilla, Visa)</option>
                                <option value="p2p">Cash App / Venmo / PayPal / Chime / Zelle</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount ($)</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                        <div id="p2pTagBox" class="alert alert-info d-none">
                            Send payment to tag/email: <strong>payment@wsosinvestment.com</strong>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload Receipt / Gift Card Image / Code Details</label>
                            <input type="text" name="details" class="form-control mb-2" placeholder="Enter Gift Card Code or Reference">
                            <input type="file" name="proof_image" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-success w-100">I Have Deposited / Transferred</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
document.getElementById('transferMethod').addEventListener('change', function() {
    let box = document.getElementById('bankDetailsBox');
    box.classList.toggle('d-none', this.value !== 'wire');
});
document.getElementById('depositMethod').addEventListener('change', function() {
    let box = document.getElementById('p2pTagBox');
    box.classList.toggle('d-none', this.value !== 'p2p');
});
</script>
</body>
</html>