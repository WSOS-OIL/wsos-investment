<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

// Fetch live user balance, profit, and status from database
$user_id =$_SESSION['user_id'];
$stmt =$conn->prepare("SELECT balance, profit, account_status FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);$stmt->execute();
$user_data =$stmt->get_result()->fetch_assoc();

$balance =$user_data['balance'] ?? 0.00;
$profit  =$user_data['profit'] ?? 0.00;
$status  =$user_data['account_status'] ?? 'Active';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trading Dashboard - WSOS Investment</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="style.css" rel="stylesheet">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
            padding: 35px;
        }
        .nav-pills .nav-link.active {
            background-color: #2c5364;
            color: #fff;
            font-weight: 600;
        }
        .nav-pills .nav-link {
            color: #2c5364;
            font-weight: 600;
            background: #f1f3f5;
            margin: 0 5px;
            border-radius: 8px;
        }
        .section-card {
            background: #fdfdfd;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            margin-bottom: 20px;
        }
        .chat-box {
            height: 300px;
            overflow-y: auto;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
        }
    </style>
</head>
<body style="background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%) !important; min-height: 100vh;">

<div class="dashboard-container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h2 class="fw-bold text-dark mb-0" style="letter-spacing: 1px;">WSOS INVESTMENT</h2>
            <small class="text-muted">Logged in as: <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></small>
        </div>
        <div>
            <span class="badge bg-success p-2 me-3">Account: <?php echo htmlspecialchars($status); ?></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Sign Out</a>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4 justify-content-center" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-overview" data-bs-toggle="pill" data-bs-target="#pane-overview" type="button" role="tab">Portfolio & Overview</button>
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
            <button class="nav-link" id="tab-chat" data-bs-toggle="pill" data-bs-target="#pane-chat" type="button" role="tab">Live Support Chat</button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="pills-tabContent">
        
        <!-- SECTION 1: OVERVIEW & INVESTMENT FLOW CHART -->
        <div class="tab-pane fade show active" id="pane-overview" role="tabpanel">
            <div class="row text-center mb-4">
                <div class="col-md-4 mb-3">
                    <div class="section-card py-3">
                        <span class="text-muted small">Portfolio Balance</span>
                        <h3 class="text-success fw-bold mb-0">$<?php echo number_format($balance, 2); ?></h3>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="section-card py-3">
                        <span class="text-muted small">Account Status</span>
                        <h3 class="text-primary fw-bold mb-0"><?php echo htmlspecialchars($status); ?></h3>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="section-card py-3">
                        <span class="text-muted small">Total Profit / Loss</span>
                        <h3 class="text-success fw-bold mb-0">+$<?php echo number_format($profit, 2); ?></h3>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <h5 class="fw-bold text-secondary mb-3">Investment Performance Flow</h5>
                <canvas id="investmentChart" height="110"></canvas>
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
                    <option value="giftcard">Gift Card Deposit (50+ US Gift Cards)</option>
                </select>

                <!-- Bank / Wire Transfer -->
                <div id="wireDetails" class="deposit-section">
                    <p class="text-muted small">Transfer funds directly using these banking details:</p>
                    <div class="p-3 bg-light border rounded mb-3">
                        <p class="mb-1"><strong>Account Number:</strong> <code>1234567789</code></p>
                        <p class="mb-0"><strong>Routing Number:</strong> <code>1245464747</code></p>
                    </div>
                    <button onclick="handlePaymentDone('wire')" class="btn btn-success w-100 fw-bold">I Have Made Payment</button>
                </div>

                <!-- PayPal / Cash App / Zelle / Chime -->
                <div id="p2pDetails" class="deposit-section" style="display:none;">
                    <p class="text-muted small">Send payment via PayPal, Cash App, Zelle, or Chime to:</p>
                    <div class="p-3 bg-light border rounded mb-3">
                        <p class="mb-0"><strong>Payment Email / ID:</strong> <code>payment@wsosinvestment.com</code></p>
                    </div>
                    <button onclick="handlePaymentDone('p2p')" class="btn btn-success w-100 fw-bold">I Have Made Payment</button>
                </div>

                <!-- Crypto View -->
                <div id="cryptoDetails" class="deposit-section" style="display:none;">
                    <p class="text-muted small">Send Bitcoin or USDT to the secure wallet below:</p>
                    <div class="p-3 bg-light border rounded mb-3">
                        <p class="mb-0"><strong>Wallet Address:</strong><br><code>ghghbchja16372nndnbb</code></p>
                    </div>
                    <button onclick="handlePaymentDone('crypto')" class="btn btn-success w-100 fw-bold">I Have Made Payment</button>
                </div>

                <!-- Gift Card View -->
                <div id="giftcardDetails" class="deposit-section" style="display:none;">
                    <form action="process_giftcard.php" method="POST">
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Select US Gift Card (50 Options)</label>
                            <select class="form-select form-select-sm" name="card_type" required>
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
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Amount ($)</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="amount" placeholder="250.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Gift Card Code / PIN</label>
                            <input type="text" class="form-control form-control-sm" name="card_code" placeholder="Enter card code or PIN" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-sm fw-bold">Submit Gift Card</button>
                    </form>
                </div>

                <div id="paymentStatusAlert" class="alert alert-info mt-3 text-center" style="display:none;"></div>
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

                <form onsubmit="alert('Your withdrawal request has been submitted and is under review.'); return false;">
                    
                    <!-- Bank / Wire Transfer Withdrawal Inputs -->
                    <div id="withdrawBankFields">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Account Number</label>
                            <input type="text" class="form-control" placeholder="Enter your Account Number" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Routing Number</label>
                            <input type="text" class="form-control" placeholder="Enter your Routing Number" required>
                        </div>
                    </div>

                    <!-- Crypto Withdrawal Inputs -->
                    <div id="withdrawCryptoFields" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bitcoin / Crypto Wallet Address</label>
                            <input type="text" class="form-control" placeholder="Enter your BTC or USDT Wallet Address">
                        </div>
                    </div>

                    <!-- Email / P2P Withdrawal Inputs -->
                    <div id="withdrawEmailFields" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" id="withdrawEmailLabel">PayPal Email Address</label>
                            <input type="text" class="form-control" placeholder="Enter Email or $Cashtag / Phone">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Withdrawal Amount ($)</label>
                        <input type="number" step="0.01" class="form-control" placeholder="500.00" required>
                    </div>

                    <button type="submit" class="btn btn-outline-primary w-100 fw-bold">Submit Withdrawal Request</button>
                </form>
            </div>
        </div>

        <!-- SECTION 4: ASSET MARKETPLACE & PURCHASE -->
        <div class="tab-pane fade" id="pane-market" role="tabpanel">
            <h4 class="fw-bold text-dark mb-3">Asset & Stock Marketplace</h4>
            <p class="text-muted small mb-4">Click "Purchase Asset" on any instrument to configure your buy order.</p>
            
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
                        <h5 class="fw-bold">S&P 500 ETF (SPY)</h5>
                        <p class="text-muted small">Top 500 US Market Index</p>
                        <h4 class="text-success fw-bold mb-3">$450.00 / share</h4>
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

        <!-- SECTION 5: LIVE SUPPORT CHAT -->
        <div class="tab-pane fade" id="pane-chat" role="tabpanel">
            <div class="section-card">
                <h4 class="fw-bold text-dark mb-3">Live Support & Advisory Chat</h4>
                <div class="chat-box mb-3" id="chatWindow">
                    <div class="mb-2"><strong>WSOS Advisor:</strong> Hello <?php echo htmlspecialchars($_SESSION['user_name']); ?>! How can our advisory desk assist you today?</div>
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
        <h5 class="modal-title fw-bold" id="modalAssetName">Purchase Asset</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="assetPurchaseForm" onsubmit="submitAssetPurchase(event)">
            <div class="mb-3">
                <label class="form-label fw-semibold">Method of Purchase</label>
                <select class="form-select" id="assetPayMethod" onchange="switchAssetPayMethod()" required>
                    <option value="bank">Bank Transfer / Wire Transfer</option>
                    <option value="p2p">PayPal / Zelle / Cash App / Chime</option>
                    <option value="giftcard">US Gift Card</option>
                </select>
            </div>

            <!-- Asset Pay: Wire Transfer -->
            <div id="assetWireDetails" class="p-3 bg-light border rounded mb-3">
                <p class="mb-1"><strong>Account Number:</strong> <code>1234567789</code></p>
                <p class="mb-0"><strong>Routing Number:</strong> <code>1245464747</code></p>
            </div>

            <!-- Asset Pay: P2P -->
            <div id="assetP2PDetails" class="p-3 bg-light border rounded mb-3" style="display:none;">
                <p class="mb-0"><strong>Payment Email:</strong> <code>payment@wsosinvestment.com</code></p>
            </div>

            <!-- Asset Pay: Gift Cards List -->
            <div id="assetGiftcardDetails" class="mb-3" style="display:none;">
                <label class="form-label fw-semibold">Select Gift Card</label>
                <select class="form-select" id="assetGiftCardSelect">
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
                <input type="number" step="0.01" class="form-control" placeholder="1000.00" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold">Submit Purchase Order</button>
        </form>

        <div id="assetSuccessAlert" class="alert alert-success mt-3 text-center fw-bold" style="display:none;">
            Your order has been received. We will get back with you. Your balance will be updated once your order has been received.
        </div>
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
                borderColor: '#2c5364',
                backgroundColor: 'rgba(44, 83, 100, 0.12)',
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
        document.getElementById('paymentStatusAlert').style.display = 'none';
    }

    // Switch Withdrawal Method
    function switchWithdrawMethod() {
        const method = document.getElementById('withdrawMethod').value;
        const bankFields = document.getElementById('withdrawBankFields');
        const cryptoFields = document.getElementById('withdrawCryptoFields');
        const emailFields = document.getElementById('withdrawEmailFields');
        const emailLabel = document.getElementById('withdrawEmailLabel');

        bankFields.style.display = 'none';
        cryptoFields.style.display = 'none';
        emailFields.style.display = 'none';

        if (method === 'wire') {
            bankFields.style.display = 'block';
        } else if (method === 'crypto') {
            cryptoFields.style.display = 'block';
        } else {
            emailFields.style.display = 'block';
            emailLabel.innerText = method.toUpperCase() + ' Email / Username / Phone';
        }
    }

    // Handle Payment Done
    function handlePaymentDone(type) {
        const alertBox = document.getElementById('paymentStatusAlert');
        alertBox.style.display = 'block';
        alertBox.className = 'alert alert-success mt-3 text-center fw-bold';
        alertBox.innerHTML = "We will review your transaction.";
    }

    // Open Asset Modal
    let modalInstance;
    function openPurchaseModal(assetName) {
        document.getElementById('modalAssetName').innerText = 'Purchase ' + assetName;
        document.getElementById('assetSuccessAlert').style.display = 'none';
        document.getElementById('assetPurchaseForm').style.display = 'block';
        modalInstance = new bootstrap.Modal(document.getElementById('assetModal'));
        modalInstance.show();
    }

    // Switch Asset Pay Method inside Modal
    function switchAssetPayMethod() {
        const method = document.getElementById('assetPayMethod').value;
        document.getElementById('assetWireDetails').style.display = (method === 'bank') ? 'block' : 'none';
        document.getElementById('assetP2PDetails').style.display = (method === 'p2p') ? 'block' : 'none';
        document.getElementById('assetGiftcardDetails').style.display = (method === 'giftcard') ? 'block' : 'none';
    }

    // Submit Asset Purchase
    function submitAssetPurchase(e) {
        e.preventDefault();
        document.getElementById('assetPurchaseForm').style.display = 'none';
        document.getElementById('assetSuccessAlert').style.display = 'block';
    }

    // Chat function
    function sendMessage() {
        const input = document.getElementById('chatInput');
        const chatWindow = document.getElementById('chatWindow');
        if (input.value.trim() !== '') {
            chatWindow.innerHTML += `<div class="mb-2 text-end"><strong>You:</strong> ${input.value}</div>`;
            const userMsg = input.value;
            input.value = '';
            chatWindow.scrollTop = chatWindow.scrollHeight;
            
            setTimeout(() => {
                chatWindow.innerHTML += `<div class="mb-2 text-primary"><strong>WSOS Advisor:</strong> Thank you for your inquiry regarding "${userMsg}". Our compliance desk is reviewing your portfolio details.</div>`;
                chatWindow.scrollTop = chatWindow.scrollHeight;
            }, 1000);
        }
    }
</script>

</body>
</html>
