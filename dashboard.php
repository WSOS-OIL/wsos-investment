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
    <!-- Chart.js for Investment Flow Graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="style.css" rel="stylesheet">
    <style>
        .dashboard-container {
            max-width: 1100px;
            margin: auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
            padding: 30px;
        }
        .nav-pills .nav-link.active {
            background-color: #2c5364;
        }
        .nav-pills .nav-link {
            color: #2c5364;
            font-weight: 600;
        }
        .chat-box {
            height: 250px;
            overflow-y: auto;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <div class="brand-logo mb-0 text-start">WSOS Investment</div>
            <small class="text-muted">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></small>
        </div>
        <div>
            <span class="badge bg-success p-2 me-3">KYC Verified</span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Sign Out</a>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4 justify-content-center" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-overview-tab" data-bs-toggle="pill" data-bs-target="#pills-overview" type="button" role="tab">Overview & Chart</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-transfer-tab" data-bs-toggle="pill" data-bs-target="#pills-transfer" type="button" role="tab">Transfer & Deposit</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-market-tab" data-bs-toggle="pill" data-bs-target="#pills-market" type="button" role="tab">Asset Purchase</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-chat-tab" data-bs-toggle="pill" data-bs-target="#pills-chat" type="button" role="tab">Support Chat</button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="pills-tabContent">
        
        <!-- TAB 1: OVERVIEW & INVESTMENT FLOW CHART -->
        <div class="tab-pane fade show active" id="pills-overview" role="tabpanel">
            <div class="row text-center mb-4">
                <div class="col-md-4 mb-3">
                    <div class="p-3 border rounded bg-light shadow-sm">
                        <span class="text-muted small">Portfolio Balance</span>
                        <h3 class="text-success fw-bold mb-0">$12,450.00</h3>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-3 border rounded bg-light shadow-sm">
                        <span class="text-muted small">Active Investments</span>
                        <h3 class="text-primary fw-bold mb-0">4 Assets</h3>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-3 border rounded bg-light shadow-sm">
                        <span class="text-muted small">Total Profit / Loss</span>
                        <h3 class="text-success fw-bold mb-0">+$1,280.40 (11.4%)</h3>
                    </div>
                </div>
            </div>

            <div class="card p-3 border-0 shadow-sm">
                <h5 class="fw-bold text-secondary mb-3">Investment Performance Flow</h5>
                <canvas id="investmentChart" height="100"></canvas>
            </div>
        </div>

        <!-- TAB 2: TRANSFER & DEPOSIT -->
        <div class="tab-pane fade" id="pills-transfer" role="tabpanel">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card p-4 border-0 shadow-sm h-100">
                        <h5 class="fw-bold mb-3 text-secondary">Deposit Funds</h5>
                        <form onsubmit="alert('Deposit request submitted successfully! Awaiting wire/crypto confirmation.'); return false;">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Select Method</label>
                                <select class="form-select" required>
                                    <option value="bank">Bank Wire Transfer</option>
                                    <option value="crypto">Cryptocurrency (USDT / BTC)</option>
                                    <option value="card">Credit / Debit Card</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Amount (USD)</label>
                                <input type="number" class="form-control" placeholder="1000.00" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Proceed to Deposit</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card p-4 border-0 shadow-sm h-100">
                        <h5 class="fw-bold mb-3 text-secondary">Transfer / Withdraw</h5>
                        <form onsubmit="alert('Withdrawal request submitted for review.'); return false;">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Destination Account / Wallet</label>
                                <input type="text" class="form-control" placeholder="IBAN or Crypto Address" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Withdrawal Amount (USD)</label>
                                <input type="number" class="form-control" placeholder="500.00" required>
                            </div>
                            <button type="submit" class="btn btn-outline-primary w-100">Request Withdrawal</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: ASSET PURCHASE MARKETPLACE -->
        <div class="tab-pane fade" id="pills-market" role="tabpanel">
            <h5 class="fw-bold mb-3 text-secondary">Investment Marketplace</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card p-3 shadow-sm border text-center">
                        <h5 class="fw-bold">S&P 500 Index Fund</h5>
                        <p class="text-muted small">Stable growth, diversified equity portfolio.</p>
                        <h4 class="text-success fw-bold mb-3">$450.00 / share</h4>
                        <button onclick="alert('Successfully purchased 1 share of S&P 500!');" class="btn btn-primary btn-sm">Buy Asset</button>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card p-3 shadow-sm border text-center">
                        <h5 class="fw-bold">Tech Growth Portfolio</h5>
                        <p class="text-muted small">High-yield allocation in AI and Cloud computing.</p>
                        <h4 class="text-success fw-bold mb-3">$1,200.00 / unit</h4>
                        <button onclick="alert('Successfully purchased Tech Growth Portfolio!');" class="btn btn-primary btn-sm">Buy Asset</button>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card p-3 shadow-sm border text-center">
                        <h5 class="fw-bold">Gold & Commodities</h5>
                        <p class="text-muted small">Hedge against inflation with physical bullion backing.</p>
                        <h4 class="text-success fw-bold mb-3">$1,950.00 / oz</h4>
                        <button onclick="alert('Successfully purchased Gold commodity unit!');" class="btn btn-primary btn-sm">Buy Asset</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 4: SUPPORT LIVE CHAT -->
        <div class="tab-pane fade" id="pills-chat" role="tabpanel">
            <h5 class="fw-bold mb-3 text-secondary">Live Investment Support & Advisory</h5>
            <div class="chat-box mb-3" id="chatWindow">
                <div class="mb-2"><strong>WSOS Advisor:</strong> Hello <?php echo htmlspecialchars($_SESSION['user_name']); ?>! How can we assist with your portfolio today?</div>
            </div>
            <div class="input-group">
                <input type="text" id="chatInput" class="form-control" placeholder="Type your message to your account manager...">
                <button class="btn btn-primary" type="button" onclick="sendMessage()">Send</button>
            </div>
        </div>

    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Investment Flow Performance Chart
    const ctx = document.getElementById('investmentChart').getContext('2d');
    const investmentChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
            datasets: [{
                label: 'Portfolio Value ($)',
                data: [5000, 6200, 7100, 8400, 9100, 10200, 10900, 11500, 11900, 12450],
                borderColor: '#2c5364',
                backgroundColor: 'rgba(44, 83, 100, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Chat Simulation Function
    function sendMessage() {
        const input = document.getElementById('chatInput');
        const chatWindow = document.getElementById('chatWindow');
        if (input.value.trim() !== '') {
            chatWindow.innerHTML += `<div class="mb-2 text-end"><strong>You:</strong> ${input.value}</div>`;
            const userMsg = input.value;
            input.value = '';
            chatWindow.scrollTop = chatWindow.scrollHeight;
            
            setTimeout(() => {
                chatWindow.innerHTML += `<div class="mb-2 text-primary"><strong>WSOS Advisor:</strong> Thank you for your message regarding "${userMsg}". Our compliance and trading desk will review your request promptly.</div>`;
                chatWindow.scrollTop = chatWindow.scrollHeight;
            }, 1000);
        }
    }
</script>

</body>
</html>
