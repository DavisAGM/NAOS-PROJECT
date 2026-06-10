<?php
require 'includes/db.php';
require 'includes/lang.php';
$system_name = getSetting($conn, 'system_name', 'NAOS');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1b4d3e">
    <meta name="description"
        content="NAOS helps farmers in Malawi with weather updates, market prices, and crop advice. Simple and easy to use.">
    <title>NAOS</title>
    <link rel="stylesheet" href="assets/vendor/css/inter.css">
    <link rel="stylesheet" href="assets/vendor/css/material-symbols.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/landing.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/toast.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/vendor/css/all.min.css">
</head>

<body class="landing-body">

    <nav class="landing-nav">
        <div class="logo-text">
            <img src="assets/images/NAOS LOGO.png" alt="NAOS Logo"
                style="height: 40px; width: 40px; border-radius: 50%;">
            <?php echo $system_name; ?>
        </div>
        <div class="nav-links">
            <a href="#features" data-t="features">Features</a>
            <!-- <a href="#help" class="btn-secondary">Help</a> -->
            <a href="#about" data-t="about">About</a>
            <a href="auth.php" class="btn-primary" style="background: #1B4332; color: white;" data-t="login">Login</a>
        </div>
    </nav>


    <section class="hero-section">
        <div class="hero-bg-container">
            <img src="assets/images/hero-bg.png" alt="Malawian farmers working in a lush green tea plantation"
                class="hero-bg-img">
            <div class="hero-overlay"></div>
        </div>

        <div class="hero-content">
            <h1 class="drop-shadow-md">
                Better Farming Starts with <br>
                <span class="text-vibrant drop-shadow-sm">Better Information</span>
            </h1>
            <p class="hero-description drop-shadow-sm">
                Get accurate weather forecasts, fair market prices, and practical advice for your crops.
                <span class="font-semibold text-white">NAOS</span> helps Malawian farmers make smarter decisions and
                grow better harvests.
            </p>
            <div class="hero-cta">
                <a href="auth.php" class="btn-get-started shadow-2xl">
                    Get Started
                    <span class="material-symbols-outlined">trending_up</span>
                </a>
                <a href="#features" class="btn-learn-more">
                    Learn More
                </a>
            </div>
        </div>

        <div class="hero-highlights">
            <div class="highlight-item">
                <span class="material-symbols-outlined text-vibrant">wb_sunny</span>
                <span>Local Weather Alerts</span>
            </div>
            <div class="highlight-item">
                <span class="material-symbols-outlined text-vibrant">edit_note</span>
                <span>Digital Farm Diary</span>
            </div>
            <div class="highlight-item">
                <span class="material-symbols-outlined text-vibrant">groups</span>
                <span>Direct Market Access</span>
            </div>
        </div>
    </section>


    <style>
        #features {
            background-image: url('assets/images/features_bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

            background: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)), url('assets/images/features_bg.png');
        }
    </style>
    <section class="features-section" id="features">
        <div class="features-container">
            <div class="section-header">
                <h2 data-t="features_title">Key Features</h2>
                <p data-t="features_subtitle">Powerful tools designed specifically for the needs of Malawian smallholder
                    farmers.</p>
                <div class="divider"></div>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <span class="material-symbols-outlined">wb_twilight</span>
                    </div>
                    <h3 data-t="f1_title">Predictive Weather</h3>
                    <p data-t="f1_desc">Stay ahead of the elements with local-weather forecasts. Receive alerts on
                        rainfall patterns and temperature shifts.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <span class="material-symbols-outlined">bar_chart</span>
                    </div>
                    <h3 data-t="f2_title">Market Insights</h3>
                    <p data-t="f2_desc">Maximize your profits by monitoring real-time commodity prices. Know exactly
                        when and where to sell.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <span class="material-symbols-outlined">lightbulb</span>
                    </div>
                    <h3 data-t="f3_title">Practical Advice</h3>
                    <p data-t="f3_desc">Access a wealth of agronomic knowledge. Get step-by-step guidance on what and
                        what to plant in your area.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <span class="material-symbols-outlined">inventory</span>
                    </div>
                    <h3 data-t="f4_title">Digital Records</h3>
                    <p data-t="f4_desc">Keep track of your harvests and expenses. Digital tools to help you manage your
                        farm's growth.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <span class="material-symbols-outlined">groups</span>
                    </div>
                    <h3 data-t="f5_title">Direct Market</h3>
                    <p data-t="f5_desc">Connect directly with buyers across the country. Cut out the middleman and grow
                        your business.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <span class="material-symbols-outlined">translate</span>
                    </div>
                    <h3 data-t="f6_title">Multi-lingual Support</h3>
                    <p data-t="f6_desc">Access a part of the system in English, Chichewa and Tumbuka. We speak your language so
                        you can focus on farming.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="features-section" id="about" style="background: white;">
        <div class="features-container">
            <div class="section-header">
                <h2 data-t="about_title">About NAOS</h2>
                <p>Nyasa Agricultural Optimization System</p>
                <div class="divider"></div>
            </div>
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <p
                    style="font-size: 1.125rem; line-height: 1.8; color: #475569; margin-bottom: 2rem; font-weight: 300;">
                    Farming is hard work. You need good information to make good choices - when to plant,
                    what prices are fair, where to find buyers. That's what NAOS does. We give you the
                    information you need, when you need it.
                </p>
                <p
                    style="font-size: 1.125rem; line-height: 1.8; color: #475569; margin-bottom: 2rem; font-weight: 300;">
                    No matter how big or small your farm is, NAOS helps you work smarter. Get weather updates,
                    track your harvest, find good prices, and connect with buyers. All this in one place.
                </p>
                <div style="margin-top: 3rem;">
                    <a href="auth.php" class="btn-get-started" style="margin: 0 auto;">Join NAOS Today</a>
                </div>
            </div>
        </div>
    </section>

    <!-- <section class="help-section" id="help">
        <div class="features-container">
            <div class="section-header">
                <h2>Help & Support</h2>
                <p>Get quick guidance for buyers and farmers, plus tips for using NAOS effectively.</p>
                <div class="divider"></div>
            </div>
            <div class="help-grid">
                <div class="help-card">
                    <h3>For Farmers</h3>
                    <ul>
                        <li>Monitor local weather forecasts and get alerts for rainfall, temperature, and wind.</li>
                        <li>Track crop performance, maintain digital farm records, and plan planting cycles.</li>
                        <li>Use market price insights to choose the best time and place to sell your produce.</li>
                        <li>Receive practical advice on crop selection, soil health, pest control, and fertilization.</li>
                        <li>Manage your farm operations efficiently from one dashboard.</li>
                    </ul>
                </div>
                <div class="help-card">
                    <h3>For Buyers</h3>
                    <ul>
                        <li>Browse available produce listings and compare fresh options from local farmers.</li>
                        <li>Place secure orders, track delivery status, and send inquiries directly to sellers.</li>
                        <li>Review price trends so you can make informed purchasing decisions.</li>
                        <li>Stay connected with notifications about new stock, market offers, and order updates.</li>
                        <li>Use buyer tools to simplify sourcing, invoicing, and communication.</li>
                    </ul>
                </div>
            </div>
            <div class="help-footer">
                <p>Need extra support? Visit the login page to access account-specific help or contact our team for fast assistance.</p>
                <a href="auth.php" class="btn-primary">Login for support</a>
            </div>
        </div>
    </section> -->


    <!-- <section class="how-it-works-section" id="how-it-works">
        <div class="features-container">
            <div class="section-header">
                <h2 data-t="how_it_works_title">How It Works</h2>
                <p data-t="how_it_works_subtitle">Three simple steps to start optimizing your farm's productivity today.</p>
                <div class="divider"></div>
            </div>

            <div class="step-grid">
                <div class="step-item">
                    <div class="step-number">1</div>
                    <h4 data-t="step1_title">Register</h4>
                    <p data-t="step1_desc">Create your profile with basic details about your farming location and goals.</p>
                </div>
                <div class="step-item">
                    <div class="step-number">2</div>
                    <h4 data-t="step2_title">Input Crop Data</h4>
                    <p data-t="step2_desc">Tell us what you're growing and your soil type for personalized recommendations.</p>
                </div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <h4 data-t="step3_title">Get Insights</h4>
                    <p data-t="step3_desc">Receive real-time updates and actionable advice directly on your dashboard.</p>
                </div>
            </div>

            <div style="margin-top: 4rem; text-align: center;">
                <button class="btn-get-started" style="margin: 0 auto; padding: 1.25rem 3rem; font-size: 1.25rem;" onclick="location.href='auth.php'">
                    Join NAOS Today
                </button>
            </div>
        </div>
    </section> -->

    <footer style="background: var(--primary-color); color: white; padding: 1rem; text-align: center;">
        <p>&copy; <?php echo date('Y'); ?> Nyasa Agricultural Optimization System.</p>
    </footer>
    <script src="assets/js/toast.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/script.js?v=<?php echo time(); ?>"></script>
</body>

</html>