<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Default values
$balance = 0.00;
$profit  = 0.00;
$status  = 'Active';

// Ensure necessary database tables exist
$conn->query("CREATE TABLE IF NOT EXISTS user_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    type VARCHAR(50),
    method VARCHAR(100),
    amount DECIMAL(15,2),
    details TEXT,
    status VARCHAR(50) DEFAULT 'Pending Review',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS support_chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    sender VARCHAR(20),
    message TEXT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Fetch unread admin message count for notification badge
$unread_stmt = $conn->prepare("SELECT COUNT(*) AS unread_count FROM support_chats WHERE user_id = ? AND sender = 'admin' AND is_read = 0");
$unread_stmt->bind_param("i", $user_id);
$unread_stmt->execute();
$unread_data = $unread_stmt->get_result()->fetch_assoc();
$unread_notifications = $unread_data['unread_count'] ?? 0;
$unread_stmt->close();

// Fetch user info safely
$existing_columns = [];
$columns_query = $conn->query("SHOW COLUMNS FROM users");
if ($columns_query) {
    while ($col = $columns_query->fetch_assoc()) {
        $existing_columns[] = $col['Field'];
    }
}

$select_fields = [];
if (in_array('balance', $existing_columns)) $select_fields[] = 'balance';
if (in_array('profit', $existing_columns)) $select_fields[] = 'profit';
if (in_array('account_status', $existing_columns)) $select_fields[] = 'account_status';

if (!empty($select_fields)) {
    $field_sql = implode(', ', $select_fields);
    $stmt = $conn->prepare("SELECT $field_sql FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();
        if ($user_data) {
            if (isset($user_data['balance'])) $balance = $user_data['balance'];
            if (isset($user_data['profit'])) $profit = $user_data['profit'];
            if (isset($user_data['account_status'])) $status = $user_data['account_status'];
        }
        $stmt->close();
    }
}

// Handle form submissions for transactions (Wire, Crypto, P2P, Assets)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    $type   = $_POST['action_type']; 
    $method = $_POST['payment_method'] ?? 'Standard';
    $amount = floatval($_POST['amount'] ?? 0);
    $details = $_POST['extra_details'] ?? 'N/A';

    $ins = $conn->prepare("INSERT INTO user_transactions (user_id, type, method, amount, details, status) VALUES (?, ?, ?, ?, ?, 'Pending Review')");
    $ins->bind_param("issds", $user_id, $type, $method, $amount, $details);
    $ins->execute();
    $ins->close();
    
    $success_receipt = [
        'type' => $type,
        'method' => $method,
        'amount' => $amount,
        'details' => $details,
        'date' => date('Y-m-d H:i:s'),
        'reference' => 'WSOS-' . strtoupper(substr(md5(mt_rand()), 0, 8))
    ];
}

// Handle Receipt coming back from Gift Card submission session
if (isset($_SESSION['giftcard_receipt'])) {
    $success_receipt = $_SESSION['giftcard_receipt'];
    unset($_SESSION['giftcard_receipt']);
}

// Fetch user's transaction history
$tx_history = [];
$tx_query = $conn->prepare("SELECT * FROM user_transactions WHERE user_id = ? ORDER BY created_at DESC");
if ($tx_query) {
    $tx_query->bind_param("i", $user_id);
    $tx_query->execute();
    $res = $tx_query->get_result();
    while ($row = $res->fetch_assoc()) {
        $tx_history[] = $row;
    }
    $tx_query->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trading Dashboard - WSOS Investment</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="style.css" rel="stylesheet">
    <style>
        body {
            background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364) !important;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        .dashboard-container {
            max-width: 1250px;
            margin: 40px auto;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            padding: 40px;
        }
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #203a43, #2c5364);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(44, 83, 100, 0.3);
        }
        .nav-pills .nav-link {
            color: #2c5364;
            font-weight: 600;
            background: #eef2f5;
            margin: 0 6px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .nav-pills .nav-link:hover {
            background: #d8e2e9;
        }
        .section-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.04);
            margin-bottom: 25px;
        }
        .receipt-card {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 30px;
        }
        .chat-box {
            height: 320px;
            overflow-y: auto;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 20px;
        }
        .notification-bell {
            position: relative;
            font-size: 1.5rem;
            color: #2c5364;
            cursor: pointer;
            text-decoration: none;
            margin-right: 20px;
        }
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -10px;
            font-size: 0.75rem;
            padding: 3px 6px;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-0" style="letter-spacing: 1.5px;">WSOS INVESTMENT</h2>
            <small class="text-muted">Welcome, <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Investor'); ?></strong></small>
        </div>
        <div class="d-flex align-items-center">
            <!-- Notification Bell Logo -->
            <a href="#" class="notification-bell" data-bs-toggle="pill" data-bs-target="#pane-chat" onclick="document.getElementById('tab-chat').click();" title="Support Messages">
                <i class="bi bi-bell-fill"></i>
                <?php if ($unread_notifications > 0): ?>
                    <span class="badge bg-danger rounded-pill notification-badge"><?php echo $unread_notifications; ?></span>
                <?php endif; ?>
            </a>

            <span class="badge bg-success p-2 px-3 me-3 fs-6 shadow-sm">Account: <?php echo htmlspecialchars($status); ?></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm px-3 fw-bold">Sign Out</a>
        </div>
    </div>

    <?php if (isset($success_receipt)): ?>
        <!-- TRANSACTION SUCCESS RECEIPT SCREEN -->
        <div class="receipt-card mb-4 text-center">
            <div class="mb-3">
                <span class="badge bg-success fs-5 px-4 py-2">Transaction Order Received</span>
            </div>
            <h3 class="fw-bold text-dark mb-2">Order Reference: <?php echo $success_receipt['reference']; ?></h3>
            <p class="text-muted mb-4">Your request has been securely logged and is queued for verification.</p>
            
            <div class="row justify-content-center mb-4">
                <div class="col-md-6">
                    <ul class="list-group text-start shadow-sm">
                        <li class="list-group-item d-flex justify-content-between"><strong>Transaction Type:</strong> <span><?php echo $success_receipt['type']; ?></span></li>
                        <li class="list-group-item d-flex justify-content-between"><strong>Payment Method:</strong> <span><?php echo $success_receipt['method']; ?></span></li>
                        <li class="list-group-item d-flex justify-content-between"><strong>Amount:</strong> <span class="text-success fw-bold">$<?php echo number_format($success_receipt['amount'], 2); ?></span></li>
                        <li class="list-group-item d-flex justify-content-between"><strong>Details / Notes:</strong> <span><?php echo htmlspecialchars($success_receipt['details']); ?></span></li>
                        <li class="list-group-item d-flex justify-content-between"><strong>Status:</strong> <span class="badge bg-warning text-dark">Pending Review</span></li>
                        <li class="list-group-item d-flex justify-content-between"><strong>Timestamp:</strong> <span><?php echo $success_receipt['date']; ?></span></li>
                    </ul>
                </div>
            </div>

            <div class="alert alert-warning fw-bold py-3 mx-auto" style="max-width: 650px;">
                Your order has been received. We will get back with you. Your balance will be updated once your order has been received and approved by the admin.
            </div>

            <a href="dashboard.php" class="btn btn-primary px-5 fw-bold mt-2">Return to Dashboard</a>
        </div>
    <?php endif; ?>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4 justify-content-center" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-overview" data-bs-toggle="pill" data-bs-target="#pane-overview" type="button" role="tab">Portfolio & Chart</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-deposit" data-bs-toggle="pill" data-bs-target="#pane-deposit" type="button" role="tab">Deposit & Transfer</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-withdraw" data-bs-toggle="pill" data-bs-target="#pane-withdraw" type="button" role="tab">Withdrawal</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-market" data-bs-toggle="pill" data-bs-target="#pane-market" type="button" role="tab">Asset Marketplace</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-transactions" data-bs-toggle="pill" data-bs-target="#pane-transactions" type="button" role="tab">Transactions Log</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-chat" data-bs-toggle="pill" data-bs-target="#pane-chat" type="button" role="tab">Live Support Chat</button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="pills-tabContent">
        
        <!-- SECTION 1: OVERVIEW & CHART -->
        <div class="tab-pane fade show active" id="pane-overview" role="tabpanel">
            <div class="row text-center mb-4">
                <div class="col-md-4 mb-3">
                    <div class="section-card py-4">
                        <span class="text-muted small text-uppercase fw-semibold">Portfolio Balance</span>
                        <h2 class="text-success fw-bold mb-0 mt-1">$<?php echo number_format($balance, 2); ?></h2>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="section-card py-4">
                        <span class="text-muted small text-uppercase fw-semibold">Account Status</span>
                        <h2 class="text-primary fw-bold mb-0 mt-1"><?php echo htmlspecialchars($status); ?></h2>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="section-card py-4">
                        <span class="text-muted small text-uppercase fw-semibold">Total Profit / Loss</span>
                        <h2 class="text-success fw-bold mb-0 mt-1">+$<?php echo number_format($profit, 2); ?></h2>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <h5 class="fw-bold text-secondary mb-3">Investment Performance Flow</h5>
                <canvas id="investmentChart" height="100"></canvas>
            </div>
        </div>

        <!-- SECTION 2: DEPOSIT & FUNDING -->
        <div class="tab-pane fade" id="pane-deposit" role="tabpanel">
            <div class="section-card">
                <h4 class="fw-bold text-dark mb-3">Fund Your Account</h4>
                <label class="form-label fw-semibold">Select Deposit Method</label>
                <select class="form-select mb-3" id="depositMethod" onchange="switchDepositMethod()">
                    <option value="wire">Bank Transfer / Wire Transfer</option>
                    <option value="p2p">PayPal / Cash App / Zelle / Chime</option>
                    <option value="crypto">Cryptocurrency Deposit (Bitcoin/USDT)</option>
                    <option value="giftcard">Gift Card Deposit (Code & Image Upload)</option>
                </select>

                <!-- Bank / Wire Transfer -->
                <div id="wireDetails" class="deposit-section">
                    <p class="text-muted small">Transfer funds directly using these banking details:</p>
                    <div class="p-3 bg-light border rounded mb-3">
                        <p class="mb-1"><strong>Account Number:</strong> <code>1234567789</code></p>
                        <p class="mb-0"><strong>Routing Number:</strong> <code>1245464747</code></p>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action_type" value="Deposit">
                        <input type="hidden" name="payment_method" value="Bank Wire Transfer">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Enter Amount Deposited ($)</label>
                            <input type="number" step="0.01" class="form-control" name="amount" placeholder="enter amount" required>
                        </div>
                        <input type="hidden" name="extra_details" value="Wire Transfer to Acc: 1234567789">
                        <button type="submit" class="btn btn-success w-100 fw-bold py-2">I Have Made Payment</button>
                    </form>
                </div>

                <!-- PayPal / Cash App / Zelle / Chime -->
                <div id="p2pDetails" class="deposit-section" style="display:none;">
                    <p class="text-muted small">Send payment via PayPal, Cash App, Zelle, or Chime to:</p>
                    <div class="p-3 bg-light border rounded mb-3">
                        <p class="mb-0"><strong>Payment Email / ID:</strong> <code>payment@wsosinvestment.com</code></p>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action_type" value="Deposit">
                        <input type="hidden" name="payment_method" value="PayPal / Zelle / Cash App / Chime">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Enter Amount Sent ($)</label>
                            <input type="number" step="0.01" class="form-control" name="amount" placeholder="enter amount" required>
                        </div>
                        <input type="hidden" name="extra_details" value="Sent to payment@wsosinvestment.com">
                        <button type="submit" class="btn btn-success w-100 fw-bold py-2">I Have Made Payment</button>
                    </form>
                </div>

                <!-- Crypto View -->
                <div id="cryptoDetails" class="deposit-section" style="display:none;">
                    <p class="text-muted small">Send Bitcoin or USDT to the secure wallet below:</p>
                    <div class="p-3 bg-light border rounded mb-3">
                        <p class="mb-0"><strong>Wallet Address:</strong><br><code>ghghbchja16372nndnbb</code></p>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action_type" value="Deposit">
                        <input type="hidden" name="payment_method" value="Crypto (Bitcoin/USDT)">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Enter Amount Sent ($)</label>
                            <input type="number" step="0.01" class="form-control" name="amount" placeholder="enter amount" required>
                        </div>
                        <input type="hidden" name="extra_details" value="Crypto Wallet: ghghbchja16372nndnbb">
                        <button type="submit" class="btn btn-success w-100 fw-bold py-2">I Have Made Payment</button>
                    </form>
                </div>

                <!-- Gift Card Deposit View -->
                <div id="giftcardDetails" class="deposit-section" style="display:none;">
                    <form action="process_giftcard.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select US Gift Card Type</label>
                            <select class="form-select" name="card_type" required>
                                <option value="Amazon">Amazon Gift Card</option>
                                <option value="Apple">Apple / iTunes Gift Card</option>
                                <option value="Visa">Visa eGift Card</option>
                                <option value="Mastercard">Mastercard Gift Card</option>
                                <option value="American Express">American Express Gift Card</option>
                                <option value="Steam">Steam Wallet Card</option>
                                <option value="Google Play">Google Play Gift Card</option>
                                <option value="Walmart">Walmart Gift Card</option>
                                <option value="Target">Target Gift Card</option>
                                <option value="eBay">eBay Gift Card</option>
                                <option value="Best Buy">Best Buy Gift Card</option>
                                <option value="Starbucks">Starbucks Gift Card</option>
                                <option value="Home Depot">Home Depot Gift Card</option>
                                <option value="Sephora">Sephora Gift Card</option>
                                <option value="Nike">Nike Gift Card</option>
                                <option value="Nordstrom">Nordstrom Gift Card</option>
                                <option value="PlayStation">PlayStation Store Card</option>
                                <option value="Xbox">Xbox Gift Card</option>
                                <option value="Roblox">Roblox Gift Card</option>
                                <option value="Uber">Uber / Uber Eats Card</option>
                                <option value="DoorDash">DoorDash Gift Card</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Card Amount ($)</label>
                            <input type="number" step="0.01" class="form-control" name="amount" placeholder="enter amount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Gift Card Code / PIN</label>
                            <input type="text" class="form-control" name="card_code" placeholder="Enter card code or PIN number" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Upload Gift Card Image (Front / Back / Receipt)</label>
                            <input type="file" class="form-control" name="giftcard_image" accept="image/*" required>
                            <small class="text-muted">Upload a clear photo or screenshot of the card code and receipt.</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Submit Gift Card Deposit</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- SECTION 3: WITHDRAWAL -->
        <div class="tab-pane fade" id="pane-withdraw" role="tabpanel">
            <div class="section-card">
                <h4 class="fw-bold text-dark mb-3">Request Withdrawal</h4>
                <label class="form-label fw-semibold">Choose Withdrawal Method</label>
                <select class="form-select mb-3" id="withdrawMethod" onchange="switchWithdrawMethod()">
                    <option value="wire">Wire Transfer / Bank Transfer</option>
                    <option value="crypto">Bitcoin / Crypto Withdrawal</option>
                    <option value="paypal">PayPal</option>
                    <option value="cashapp">Cash App</option>
                    <option value="zelle">Zelle</option>
                    <option value="chime">Chime</option>
                </select>

                <form method="POST">
                    <input type="hidden" name="action_type" value="Withdrawal">
                    <input type="hidden" name="payment_method" id="hiddenWithdrawMethod" value="Wire Transfer">
                    
                    <div id="withdrawBankFields">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Account Number</label>
                            <input type="text" class="form-control" name="acc_num" placeholder="Enter your Account Number">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Routing Number</label>
                            <input type="text" class="form-control" name="routing_num" placeholder="Enter your Routing Number">
                        </div>
                    </div>

                    <div id="withdrawCryptoFields" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bitcoin / Crypto Wallet Address</label>
                            <input type="text" class="form-control" name="crypto_wallet" placeholder="Enter your BTC or USDT Wallet Address">
                        </div>
                    </div>

                    <div id="withdrawEmailFields" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" id="withdrawEmailLabel">PayPal Email Address</label>
                            <input type="text" class="form-control" name="p2p_email" placeholder="Enter Email or $Cashtag / Phone">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Withdrawal Amount ($)</label>
                        <input type="number" step="0.01" class="form-control" name="amount" placeholder="500.00" required>
                    </div>
                    
                    <input type="hidden" name="extra_details" id="withdrawDetailsText" value="Bank Wire Withdrawal">

                    <button type="submit" class="btn btn-outline-primary w-100 fw-bold py-2">Submit Withdrawal Request</button>
                </form>
            </div>
        </div>

        <!-- SECTION 4: ASSET MARKETPLACE & PURCHASE -->
        <div class="tab-pane fade" id="pane-market" role="tabpanel">
            <h4 class="fw-bold text-dark mb-3">Asset & Stock Marketplace</h4>
            <p class="text-muted small mb-4">Click "Purchase Asset" on any instrument to configure your order.</p>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="section-card text-center h-100">
                        <h5 class="fw-bold">Apple Inc. (AAPL)</h5>
                        <p class="text-muted small">Technology & Hardware Giant</p>
                        <h4 class="text-success fw-bold mb-3">$185.50 / share</h4>
                        <button onclick="openPurchaseModal('Apple Inc. (AAPL)')" class="btn btn-primary btn-sm w-100">Purchase Asset</button>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="section-card text-center h-100">
                        <h5 class="fw-bold">Tesla Inc. (TSLA)</h5>
                        <p class="text-muted small">EV & Clean Energy Infrastructure</p>
                        <h4 class="text-success fw-bold mb-3">$240.20 / share</h4>
                        <button onclick="openPurchaseModal('Tesla Inc. (TSLA)')" class="btn btn-primary btn-sm w-100">Purchase Asset</button>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="section-card text-center h-100">
                        <h5 class="fw-bold">World Strategic Oil Supply (WSOS)</h5>
                        <p class="text-muted small">Top 500 US Market Index</p>
                        <h4 class="text-success fw-bold mb-3">$0.0000026 / share</h4>
                        <button onclick="openPurchaseModal('S&P 500 ETF (SPY)')" class="btn btn-primary btn-sm w-100">Purchase Asset</button>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="section-card text-center h-100">
                        <h5 class="fw-bold">Microsoft Corp. (MSFT)</h5>
                        <p class="text-muted small">Cloud Computing & AI Innovation</p>
                        <h4 class="text-success fw-bold mb-3">$415.00 / share</h4>
                        <button onclick="openPurchaseModal('Microsoft Corp. (MSFT)')" class="btn btn-primary btn-sm w-100">Purchase Asset</button>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="section-card text-center h-100">
                        <h5 class="fw-bold">NVIDIA Corp. (NVDA)</h5>
                        <p class="text-muted small">AI Processors & GPUs</p>
                        <h4 class="text-success fw-bold mb-3">$880.00 / share</h4>
                        <button onclick="openPurchaseModal('NVIDIA Corp. (NVDA)')" class="btn btn-primary btn-sm w-100">Purchase Asset</button>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="section-card text-center h-100">
                        <h5 class="fw-bold">Bitcoin (BTC)</h5>
                        <p class="text-muted small">Premier Digital Asset</p>
                        <h4 class="text-success fw-bold mb-3">$64,500.00 / coin</h4>
                        <button onclick="openPurchaseModal('Bitcoin (BTC)')" class="btn btn-primary btn-sm w-100">Purchase Asset</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 5: TRANSACTIONS LOG -->
        <div class="tab-pane fade" id="pane-transactions" role="tabpanel">
            <div class="section-card">
                <h4 class="fw-bold text-dark mb-3">Your Transaction & Processing History</h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Method</th>
                                <th>Amount ($)</th>
                                <th>Details</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tx_history)): ?>
                                <?php foreach ($tx_history as$tx): ?>
                                    <tr>
                                        <td>#<?php echo $tx['id']; ?></td>
                                        <td><span class="badge bg-secondary"><?php echo $tx['type']; ?></span></td>
                                        <td><?php echo htmlspecialchars($tx['method']); ?></td>
                                        <td class="fw-bold text-success">$<?php echo number_format($tx['amount'], 2); ?></td>
                                        <td><small><?php echo htmlspecialchars($tx['details']); ?></small></td>
                                        <td>
                                            <?php 
                                                $status_badge = 'bg-warning text-dark';
                                                if ($tx['status'] === 'Approved' || $tx['status'] === 'Completed')$status_badge = 'bg-success';
                                                if ($tx['status'] === 'Processing')$status_badge = 'bg-info text-dark';
                                                if ($tx['status'] === 'Rejected' || $tx['status'] === 'Failed')$status_badge = 'bg-danger';
                                            ?>
                                            <span class="badge <?php echo $status_badge; ?>"><?php echo htmlspecialchars($tx['status']); ?></span>
                                        </td>
                                        <td><small><?php echo $tx['created_at']; ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center text-muted">No transactions recorded yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SECTION 6: LIVE SUPPORT CHAT -->
        <div class="tab-pane fade" id="pane-chat" role="tabpanel">
            <div class="section-card">
                <h4 class="fw-bold text-dark mb-3">Live Support & Advisory Chat</h4>
                <div class="chat-box mb-3" id="chatWindow">
                    <!-- Loaded dynamically via JS -->
                </div>
                <div class="input-group">
                    <input type="text" id="chatInput" class="form-control" placeholder="Type your message here...">
                    <button class="btn btn-primary" type="button" onclick="sendMessage()">Send Message</button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Asset Purchase Modal -->
<div class="modal fade" id="assetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="modalTitle">Purchase Asset</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST">
            <input type="hidden" name="action_type" value="Asset Purchase">
            <input type="hidden" id="modalAssetNameInput" name="extra_details" value="">

            <div class="mb-3">
                <label class="form-label fw-semibold">Method of Purchase</label>
                <select class="form-select" id="assetPayMethod" name="payment_method" onchange="switchAssetPayMethod()" required>
                    <option value="Bank Transfer">Bank Transfer / Wire Transfer</option>
                    <option value="PayPal / Zelle / Cash App / Chime">PayPal / Zelle / Cash App / Chime</option>
                    <option value="US Gift Card">US Gift Card</option>
                </select>
            </div>

            <div id="assetWireDetails" class="p-3 bg-light border rounded mb-3">
                <p class="mb-1"><strong>Account Number:</strong> <code>1234567789</code></p>
                <p class="mb-0"><strong>Routing Number:</strong> <code>1245464747</code></p>
            </div>

            <div id="assetP2PDetails" class="p-3 bg-light border rounded mb-3" style="display:none;">
                <p class="mb-0"><strong>Payment Email:</strong> <code>payment@wsosinvestment.com</code></p>
            </div>

            <div id="assetGiftcardDetails" class="mb-3" style="display:none;">
                <label class="form-label fw-semibold">Select Gift Card</label>
                <select class="form-select" name="gift_card_type">
                    <option value="Amazon">Amazon Gift Card</option><option value="Apple">Apple / iTunes Gift Card</option>
                    <option value="Visa">Visa eGift Card</option><option value="Mastercard">Mastercard Gift Card</option>
                    <option value="American Express">American Express Gift Card</option><option value="Steam">Steam Wallet Card</option>
                    <option value="Google Play">Google Play Gift Card</option><option value="Walmart">Walmart Gift Card</option>
                    <option value="Target">Target Gift Card</option><option value="eBay">eBay Gift Card</option>
                    <option value="Best Buy">Best Buy Gift Card</option><option value="Starbucks">Starbucks Gift Card</option>
                    <option value="Home Depot">Home Depot Gift Card</option><option value="Lowe's">Lowe's Gift Card</option>
                    <option value="Sephora">Sephora Gift Card</option><option value="Ulta">Ulta Beauty Gift Card</option><option value="Nike">Nike Gift Card</option>
                    <option value="Nordstrom">Nordstrom Gift Card</option><option value="Macy's">Macy's Gift Card</option>
                    <option value="Kohl's">Kohl's Gift Card</option><option value="PlayStation">PlayStation Store Card</option>
                    <option value="Xbox">Xbox Gift Card</option><option value="Nintendo">Nintendo eShop Card</option>
                    <option value="Roblox">Roblox Gift Card</option><option value="Uber">Uber / Uber Eats Card</option>
                    <option value="DoorDash">DoorDash Gift Card</option><option value="Grubhub">Grubhub Gift Card</option>
                    <option value="Airbnb">Airbnb Gift Card</option><option value="Delta">Delta Air Lines Gift Card</option>
                    <option value="Southwest">Southwest Airlines Card</option><option value="Hotels.com">Hotels.com Gift Card</option>
                    <option value="GameStop">GameStop Gift Card</option><option value="Chipotle">Chipotle Gift Card</option>
                    <option value="Subway">Subway Gift Card</option><option value="Domino's">Domino's Pizza Card</option>
                    <option value="Dunkin'">Dunkin' Gift Card</option><option value="Olive Garden">Olive Garden Card</option>
                    <option value="Texas Roadhouse">Texas Roadhouse Card</option><option value="Bed Bath & Beyond">Bed Bath & Beyond</option>
                    <option value="TJ Maxx">TJ Maxx / Marshalls Card</option><option value="Gap">Gap / Old Navy Card</option>
                    <option value="Foot Locker">Foot Locker Gift Card</option><option value="Lululemon">Lululemon Gift Card</option>
                    <option value="CVS">CVS Pharmacy Card</option><option value="Walgreens">Walgreens Gift Card</option>
                    <option value="Bass Pro Shops">Bass Pro Shops / Cabela's</option><option value="REI">REI Co-op Gift Card</option>
                    <option value="Disney">Disney Gift Card</option><option value="Cinemark">Cinemark Theatres Card</option>
                    <option value="AMC">AMC Theatres Gift Card</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Amount ($)</label>
                <input type="number" step="0.01" class="form-control" name="amount" placeholder="1000.00" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Submit Purchase Order</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Investment Chart
    const ctx = document.getElementById('investmentChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
            datasets: [{
                label: 'Portfolio Value ($)',
                data: [5000, 6300, 7200, 8500, 9200, 10400, 11100, 11600, 12000, 12450],
                borderColor: '#203a43',
                backgroundColor: 'rgba(32, 58, 67, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.35
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // Switch Deposit Method
    function switchDepositMethod() {
        const method = document.getElementById('depositMethod').value;
        document.getElementById('wireDetails').style.display = (method === 'wire') ? 'block' : 'none';
        document.getElementById('p2pDetails').style.display = (method === 'p2p') ? 'block' : 'none';
        document.getElementById('cryptoDetails').style.display = (method === 'crypto') ? 'block' : 'none';
        document.getElementById('giftcardDetails').style.display = (method === 'giftcard') ? 'block' : 'none';
    }

    // Switch Withdrawal Method
    function switchWithdrawMethod() {
        const method = document.getElementById('withdrawMethod').value;
        const bankFields = document.getElementById('withdrawBankFields');
        const cryptoFields = document.getElementById('withdrawCryptoFields');
        const emailFields = document.getElementById('withdrawEmailFields');
        const emailLabel = document.getElementById('withdrawEmailLabel');
        const hiddenMethod = document.getElementById('hiddenWithdrawMethod');
        const detailsText = document.getElementById('withdrawDetailsText');

        bankFields.style.display = 'none';
        cryptoFields.style.display = 'none';
        emailFields.style.display = 'none';

        if (method === 'wire') {
            bankFields.style.display = 'block';
            hiddenMethod.value = 'Bank Wire Transfer';
            detailsText.value = 'Wire Transfer Request';
        } else if (method === 'crypto') {
            cryptoFields.style.display = 'block';
            hiddenMethod.value = 'Crypto Withdrawal';
            detailsText.value = 'Bitcoin/USDT Withdrawal Request';
        } else {
            emailFields.style.display = 'block';
            emailLabel.innerText = method.toUpperCase() + ' Email / Username / Phone';
            hiddenMethod.value = method.toUpperCase();
            detailsText.value = method.toUpperCase() + ' Withdrawal Request';
        }
    }

    // Open Asset Modal
    function openPurchaseModal(assetName) {
        document.getElementById('modalTitle').innerText = 'Purchase ' + assetName;
        document.getElementById('modalAssetNameInput').value = 'Asset Purchase: ' + assetName;
        const modal = new bootstrap.Modal(document.getElementById('assetModal'));
        modal.show();
    }

    // Switch Asset Pay Method inside Modal
    function switchAssetPayMethod() {
        const method = document.getElementById('assetPayMethod').value;
        document.getElementById('assetWireDetails').style.display = (method === 'Bank Transfer') ? 'block' : 'none';
        document.getElementById('assetP2PDetails').style.display = (method === 'PayPal / Zelle / Cash App / Chime') ? 'block' : 'none';
        document.getElementById('assetGiftcardDetails').style.display = (method === 'US Gift Card') ? 'block' : 'none';
    }

    // Live Support Chat Fetch & Send Logic
    const currentUserId = <?php echo $user_id; ?>;

    function fetchUserMessages() {
        fetch('process_chat.php?fetch_user_id=' + currentUserId)
            .then(res => res.json())
            .then(data => {
                const win = document.getElementById('chatWindow');
                if (!win) return;
                
                let html = '';
                if (data.length === 0) {
                    html = '<div class="mb-2"><strong>WSOS Advisor:</strong> Hello <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Investor'); ?>! How can our advisory desk assist you today?</div>';
                } else {
                    data.forEach(msg => {
                        const isUser = msg.sender === 'user';
                        html += `
                            <div class="mb-3 ${isUser ? 'text-end' : 'text-start'}">
                                <span class="badge ${isUser ? 'bg-secondary' : 'bg-primary'}">${isUser ? 'You' : 'WSOS Advisor'}</span>
                                <div class="p-2 px-3 rounded mt-1 d-inline-block ${isUser ? 'bg-primary text-white' : 'bg-light border'}">
                                    ${msg.message}
                                </div>
                                <br><small class="text-muted" style="font-size: 0.75rem;">${msg.created_at}</small>
                            </div>
                        `;
                    });
                }
                win.innerHTML = html;
                win.scrollTop = win.scrollHeight;
            });
    }

    function sendMessage() {
        const input = document.getElementById('chatInput');
        const msg = input.value.trim();
        if (msg === '') return;

        const formData = new FormData();
        formData.append('message', msg);
        formData.append('sender', 'user');

        fetch('process_chat.php', { method: 'POST', body: formData })
            .then(() => {
                input.value = '';
                fetchUserMessages();
            });
    }

    // Poll chat messages and notification badges every 4 seconds
    setInterval(fetchUserMessages, 4000);
    fetchUserMessages();
</script>

</body>
</html>
