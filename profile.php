<?php
session_start();
// Optional: If user is already logged in, you can show a "Go to Dashboard" button in the navbar
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WSOS Investment - Institutional Grade Multi-Asset Trading</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364) !important;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #f8f9fa;
        }
        .navbar-custom {
            background: rgba(15, 32, 39, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .hero-section {
            padding: 100px 0 70px 0;
            text-align: center;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 18px;
            padding: 35px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.5);
        }
        .btn-gradient {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: #fff;
            border: none;
            font-weight: 700;
            padding: 12px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            background: linear-gradient(135deg, #218838, #1baa80);
            color: #fff;
            transform: scale(1.02);
        }
        .btn-outline-custom {
            border: 2px solid #ffffff;
            color: #ffffff;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .btn-outline-custom:hover {
            background: #ffffff;
            color: #0f2027;
        }
        .feature-icon {
            font-size: 2.8rem;
            color: #20c997;
            margin-bottom: 15px;
        }
        footer {
            background: rgba(10, 20, 26, 0.98);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding: 40px 0 20px 0;
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3 text-white" href="index.php" style="letter-spacing: 1.5px;">
                <i class="bi bi-graph-up-arrow text-success me-2"></i>WSOS INVESTMENT
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                        <a class="nav-link text-white fw-semibold" href="#about">About Us</a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link text-white fw-semibold" href="#location">Company Location</a>
                    </li>
                    <?php if ($is_logged_in): ?>
                        <li class="nav-item me-2">
                            <a class="btn btn-gradient btn-sm px-3 text-white" href="dashboard.php">My Dashboard</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-custom btn-sm px-3" href="signin.php">Sign In</a>
                        </li>
                        <li class="nav-item me-2">
                            <a class="btn btn-gradient btn-sm px-3 text-white" href="signup.php">Sign Up</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-warning fw-semibold small" href="forgot_password.php">Forgot Password?</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <span class="badge bg-success bg-opacity-25 text-success px-3 py-2 rounded-pill fw-semibold mb-3">
                        <i class="bi bi-shield-check me-1"></i> SECURE MULTI-ASSET PLATFORM
                    </span>
                    <h1 class="display-3 fw-bold mb-4" style="line-height: 1.2;">
                        Accelerate Your Wealth with <span class="text-success">WSOS Investment</span>
                    </h1>
                    <p class="lead text-light opacity-75 mb-5 px-lg-5">
                        Access high-yield equities, global market indices, and cryptocurrency portfolios with institutional-grade security, instant liquidity, and dedicated live advisory support.
                    </p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <?php if ($is_logged_in): ?>
                            <a href="dashboard.php" class="btn btn-gradient btn-lg">Go to Trading Dashboard</a>
                        <?php else: ?>
                            <a href="signup.php" class="btn btn-gradient btn-lg">Create Free Account</a>
                            <a href="signin.php" class="btn btn-outline-custom btn-lg">Sign In</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STATS COUNTER BAR -->
    <section class="py-4 mb-5">
        <div class="container">
            <div class="glass-card">
                <div class="row text-center">
                    <div class="col-md-3 mb-3 mb-md-0 border-end border-secondary border-opacity-25">
                        <h2 class="fw-bold text-success mb-0">$2.4B+</h2>
                        <small class="text-light opacity-75">Quarterly Volume</small>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0 border-end border-secondary border-opacity-25">
                        <h2 class="fw-bold text-success mb-0">150K+</h2>
                        <small class="text-light opacity-75">Active Investors</small>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0 border-end border-secondary border-opacity-25">
                        <h2 class="fw-bold text-success mb-0">50+</h2>
                        <small class="text-light opacity-75">Deposit & Payment Gateways</small>
                    </div>
                    <div class="col-md-3">
                        <h2 class="fw-bold text-success mb-0">99.99%</h2>
                        <small class="text-light opacity-75">Uptime & Execution Reliability</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT US SECTION -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold display-5">About WSOS Investment</h2>
                <p class="text-light opacity-75" style="max-width: 700px; margin: 0 auto;">
                    Engineered for modern financial growth, WSOS Investment bridges traditional wealth management with next-generation digital execution.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="glass-card text-center h-100">
                        <i class="bi bi-shield-lock-fill feature-icon"></i>
                        <h4 class="fw-bold mb-3">Institutional Security</h4>
                        <p class="text-light opacity-75 small mb-0">
                            Your accounts and personal data are safeguarded with multi-layer encryption, cold-wallet storage, and rigorous compliance standards.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card text-center h-100">
                        <i class="bi bi-wallet2 feature-icon"></i>
                        <h4 class="fw-bold mb-3">Flexible Funding Options</h4>
                        <p class="text-light opacity-75 small mb-0">
                            Enjoy effortless account funding and withdrawals via Wire Transfer, Crypto (BTC/USDT), PayPal, Cash App, Zelle, Chime, and 50+ US Gift Cards.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card text-center h-100">
                        <i class="bi bi-headset feature-icon"></i>
                        <h4 class="fw-bold mb-3">24/7 Live Advisory Support</h4>
                        <p class="text-light opacity-75 small mb-0">
                            Get real-time guidance from experienced wealth advisors through our persistent, in-dashboard chat support portal.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- COMPANY LOCATION SECTION -->
    <section id="location" class="py-5 mb-5">
        <div class="container">
            <div class="glass-card">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <h2 class="fw-bold display-6 mb-3">Global Headquarters & Location</h2>
                        <p class="text-light opacity-75 mb-4">
                            WSOS Investment operates out of top global financial hubs, providing secure liquidity and asset clearing to clients across North America, Europe, and Asia.
                        </p>
                        
                        <div class="d-flex align-items-start mb-3">
                            <i class="bi bi-geo-alt-fill text-success fs-4 me-3 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Corporate Address</h6>
                                <p class="text-light opacity-75 small mb-0">
                                    WSOS Investment Financial Center<br>
                                    100 Wall Street, 18th Floor<br>
                                    New York, NY 10005, United States
                                </p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start mb-3">
                            <i class="bi bi-envelope-check-fill text-success fs-4 me-3 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Official Support Desk</h6>
                                <p class="text-light opacity-75 small mb-0">
                                    payment@wsosinvestment.com
                                </p>
                            </div>
                        </div>

                        <div class="d-flex align-items-start">
                            <i class="bi bi-clock-history text-success fs-4 me-3 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Operating Hours</h6>
                                <p class="text-light opacity-75 small mb-0">
                                    Mon – Fri: 24 Hours | Sat – Sun: Advisory Operations Active
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="p-4 bg-dark bg-opacity-50 border border-secondary border-opacity-25 rounded-3 text-center">
                            <i class="bi bi-building text-success" style="font-size: 5rem;"></i>
                            <h4 class="fw-bold mt-3">WSOS Investment HQ</h4>
                            <p class="text-light opacity-75 small">Wall Street Financial District, New York, NY</p>
                            <hr class="border-secondary opacity-25">
                            <div class="d-flex justify-content-center gap-2">
                                <?php if ($is_logged_in): ?>
                                    <a href="dashboard.php" class="btn btn-sm btn-gradient">Dashboard</a>
                                <?php else: ?>
                                    <a href="signup.php" class="btn btn-sm btn-gradient">Sign Up</a>
                                    <a href="signin.php" class="btn btn-sm btn-outline-custom">Sign In</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="text-center text-md-start">
        <div class="container">
            <div class="row pb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h5 class="fw-bold text-white mb-2"><i class="bi bi-graph-up-arrow text-success me-2"></i>WSOS INVESTMENT</h5>
                    <p class="text-light opacity-50 small mb-0">
                        © <?php echo date('Y'); ?> WSOS Investment Inc. All rights reserved. Professional wealth management and trading infrastructure.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <?php if (!$is_logged_in): ?>
                        <a href="signup.php" class="text-light opacity-75 me-3 text-decoration-none small">Sign Up</a>
                        <a href="signin.php" class="text-light opacity-75 me-3 text-decoration-none small">Sign In</a>
                        <a href="forgot_password.php" class="text-warning text-decoration-none small">Forgot Password</a>
                    <?php else: ?>
                        <a href="dashboard.php" class="text-success fw-bold text-decoration-none small">Go to Dashboard</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
