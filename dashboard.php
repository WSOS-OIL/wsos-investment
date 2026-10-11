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
            <span class="badge bg-success p-2 me-3">KYC Verified</span>
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
                        <h3 class="text-success fw-bold mb-0">$12,450.00</h3>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="section-card py-3">
                        <span class="text-muted small">Active Assets</span>
                        <h3 class="text-primary fw-bold mb-0">6 Holdings</h3>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="section-card py-3">
                        <span class="text-muted small">Total Profit / Loss</span>
                        <h3 class="text-success fw-bold mb-0">+$1,480.20 (+13.5%)</h3>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <h5 class="fw-bold text-secondary mb-3">Investment Performance Flow</h5>
                <canvas id="investmentChart" height="110"></canvas>
            </div>
        </div>

        <!-- SECTION 2: DEPOSIT & TRANSFER (Wire, Crypto, Gift Card) -->
        <div class="tab-pane fade" id="pane-deposit" role="tabpanel">
            <div class="row">
                <!-- Column A: Deposit Methods -->
                <div class="col-md-6 mb-4">
                    <div class="section-card h-100">
                        <h4 class="fw-bold text-dark mb-3">Fund Your Account</h4>
                        <label class="form-label fw-semibold">Select Deposit Method</label>
                        <select class="form-select mb-3" id="depositMethod" onchange="switchDepositMethod()">
                            <option value="wire">Bank / Wire Transfer</option>
                            <option value="crypto">Cryptocurrency Deposit</option>
                            <option value="giftcard">Gift Card Deposit</option>
                        </select>

                        <!-- Wire Transfer View -->
                        <div id="wireDetails" class="deposit-section">
                            <p class="text-muted small">Transfer funds directly through your banking institution:</p>
                            <div class="p-3 bg-light border rounded mb-3">
                                <p class="mb-1"><strong>Account Number:</strong> <code>123456789</code></p>
                                <p class="mb-0"><strong>Routing Number:</strong> <code>1234567898</code></p>
                            </div>
                            <button onclick="handlePaymentDone('wire')" class="btn btn-success w-100 fw-bold">I Have Made Payment</button>
                        </div>

                        <!-- Crypto View -->
                        <div id="cryptoDetails" class="deposit-section" style="display:none;">
                            <p class="text-muted small">Send USDT (TRC20) or BTC to the secure wallet below:</p>
                            <div class="p-3 bg-light border rounded mb-3">
                                <p class="mb-0"><strong>Wallet Address:</strong><br><code>ghghbchja16372nndnbb</code></p>
                            </div>
                            <button onclick="handlePaymentDone('crypto')" class="btn btn-success w-100 fw-bold">I Have Made Payment</button>
                        </div>

                        <!-- Gift Card View -->
                        <div id="giftcardDetails" class="deposit-section" style="display:none;">
                            <form action="process_giftcard.php" method="POST">
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold">Select Gift Card</label>
                                    <select class="form-select form-select-sm" name="card_type" required>
                                        <option value="Apple">Apple Gift Card</option>
                                        <option value="Amazon">Amazon Gift Card</option>
                                        <option value="Steam">Steam Gift Card</option>
                                        <option value="Google Play">Google Play Gift Card</option>
                                        <option value="Visa/Mastercard">Visa / Mastercard Gift Card</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold">Amount ($)</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm" name="amount" placeholder="250.00" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Gift Card Code</label>
                                    <input type="text" class="form-control form-control-sm" name="card_code" placeholder="Enter PIN / Code" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 btn-sm fw-bold">Submit Gift Card</button>
                            </form>
                        </div>

                        <div id="paymentStatusAlert" class="alert alert-info mt-3 text-center" style="display:none;"></div>
                    </div>
                </div>

                <!-- Column B: Withdrawal / Transfer -->
                <div class="col-md-6 mb-4">
                    <div class="section-card h-100">
                        <h4 class="fw-bold text-dark mb-3">Request Withdrawal / Transfer</h4>
                        <form onsubmit="alert('Withdrawal request submitted successfully! Awaiting processing.'); return false;">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Destination Bank / Wallet</label>
                                <input type="text" class="form-control" placeholder="Enter IBAN or Crypto Address" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Amount (USD)</label>
                                <input type="number" class="form-control" placeholder="500.00" required>
                            </div>
                            <button type="submit" class="btn btn-outline-primary w-100 fw-bold">Submit Transfer Request</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: ASSET PURCHASE MARKETPLACE -->
        <div class="tab-pane fade" id="pane-market" role="tabpanel">
            <h4 class="fw-bold text-dark mb-3">Asset & Stock Marketplace</h4>
            <p class="text-muted small mb-4">Select an asset below to instantly allocate and purchase units into your portfolio.</p>
            
            <div class="row">
                <!-- Stock 1 -->
                <div class="col-md-4 mb-3">
                    <div class="section-card text-center h-100">
                        <h5 class="fw-bold">Apple Inc. (AAPL)</h5>
                        <p class="text-muted small">Leading consumer electronics and software giant.</p>
                        <h4 class="text-success fw-bold mb-3">$185.50 / share</h4>
                        <button onclick="buyAsset('Apple (AAPL)', 185.50)" class="btn btn-primary btn-sm w-100">Purchase Asset</button>
                    </div>
                </div>
                <!-- Stock 2 -->
                <div class="col-md-4 mb-3">
                    <div class="section-card text-center h-100">
                        <h5 class="fw-bold">Tesla Inc. (TSLA)</h5>
                        <p class="text-muted small">Electric vehicles and clean energy infrastructure.</p>
                        <h4 class="text-success fw-bold mb-3">$240.20 / share</h4>
                        <button onclick="buyAsset('Tesla (TSLA)', 240.20)" class="btn btn-primary btn-sm w-100">Purchase Asset</button>
                    </div>
                </div>
                <!-- Stock 3 -->
                <div class="col-md-4 mb-3">
                    <div class="section-card text-center h-100">
                        <h5 class="fw-bold">S&P 500 ETF (SPY)</h5>
                        <p class="text-muted small">Diversified index tracking top 500 US companies.</p>
                        <h4 class="text-success fw-bold mb-3">$450.00 / share</h4>
                        <button onclick="buyAsset('S&P 500 ETF (SPY)', 450.00)" class="btn btn-primary btn-sm w-100">Purchase Asset</button>
                    </div>
                </div>
                <!-- Stock 4 -->
                <div class="col-md-4 mb-3">
                    <div class="section-card text-center h-100">
                        <h5 class="fw-bold">Microsoft Corp. (MSFT)</h5>
                        <p class="text-muted small">Cloud computing, enterprise software, and AI.</p>
                        <h4 class="text-success fw-bold mb-3">$415.00 / share</h4>
                        <button onclick="buyAsset('Microsoft (MSFT)', 415.00)" class="btn btn-primary btn-sm w-100">Purchase Asset</button>
                    </div>
                </div>
                <!-- Stock 5 -->
                <div class="col-md-4 mb-3">
                    <div class="section-card text-center h-100">
                        <h5 class="fw-bold">NVIDIA Corp. (NVDA)</h5>
                        <p class="text-muted small">Pioneering accelerated computing and artificial intelligence.</p>
                        <h4 class="text-success fw-bold mb-3">$880.00 / share</h4>
                        <button onclick="buyAsset('NVIDIA (NVDA)', 880.00)" class="btn btn-primary btn-sm w-100">Purchase Asset</button>
                    </div>
                </div>
                <!-- Stock 6 -->
                <div class="col-md-4 mb-3">
                    <div class="section-card text-center h-100">
                        <h5 class="fw-bold">Bitcoin Trust (BTC)</h5>
                        <p class="text-muted small">Digital asset security and cryptocurrency exposure.</p>
                        <h4 class="text-success fw-bold mb-3">$64,500.00 / coin</h4>
                        <button onclick="buyAsset('Bitcoin Trust (BTC)', 64500.00)" class="btn btn-primary btn-sm w-100">Purchase Asset</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 4: LIVE SUPPORT CHAT -->
        <div class="tab-pane fade" id="pane-chat" role="tabpanel">
            <div class="section-card">
                <h4 class="fw-bold text-dark mb-3">Live Investment Support & Advisory</h4>
                <div class="chat-box mb-3" id="chatWindow">
                    <div class="mb-2"><strong>WSOS Advisor:</strong> Hello <?php echo htmlspecialchars($_SESSION['user_name']); ?>! How can our advisory desk assist you with your investments today?</div>
                </div>
                <div class="input-group">
                    <input type="text" id="chatInput" class="form-control" placeholder="Type your message here...">
                    <button class="btn btn-primary" type="button" onclick="sendMessage()">Send Message</button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Investment Flow Chart
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
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });

    // Switch deposit method view
    function switchDepositMethod() {
        const method = document.getElementById('depositMethod').value;
        document.getElementById('wireDetails').style.display = (method === 'wire') ? 'block' : 'none';
        document.getElementById('cryptoDetails').style.display = (method === 'crypto') ? 'block' : 'none';
        document.getElementById('giftcardDetails').style.display = (method === 'giftcard') ? 'block' : 'none';
        document.getElementById('paymentStatusAlert').style.display = 'none';
    }

    // Handle "I have made payment" button click
    function handlePaymentDone(type) {
        const alertBox = document.getElementById('paymentStatusAlert');
        alertBox.style.display = 'block';
        alertBox.className = 'alert alert-success mt-3 text-center fw-bold';
        alertBox.innerHTML = "We will review your transaction.";
    }

    // Asset purchase simulation
    function buyAsset(assetName, price) {
        if (confirm(`Confirm purchase of 1 unit of ${assetName} for $${price}?`)) {
            alert(`Successfully purchased ${assetName}! Your portfolio has been updated.`);
        }
    }

    // Live chat simulator function
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
