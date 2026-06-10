<?php
require '../includes/db.php';
require '../includes/auth.php';
require '../includes/lang.php';

if (!isLoggedIn()) {
    header('Location: ../auth.php');
    exit();
}

// Role guard — redirect farmers/admins to their own dashboard
$_activeRole = getActiveRole();
if ($_activeRole !== 'buyer' && $_activeRole !== 'admin') {
    $roleMap = ['farmer' => '../farmer/dashboard.php', 'admin' => '../admin/dashboard.php'];
    header('Location: ' . ($roleMap[$_activeRole] ?? '../auth.php'));
    exit();
}

// Security: Check if user is still active in DB
if (!isUserActive($conn, $_SESSION['user_id'])) {
    session_unset();
    session_destroy();
    header('Location: ../auth.php?error=deactivated');
    exit();
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1b4d3e">
    <script>window.NAOS_LANG = '<?php echo htmlspecialchars($lang, ENT_QUOTES); ?>';</script>
    <title>NAOS Buyer Board</title>
    <link rel="stylesheet" href="../assets/vendor/css/inter.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/toast.css">
    <link rel="stylesheet" href="../assets/vendor/css/all.min.css">
    <script src="../assets/vendor/js/jspdf.umd.min.js"></script>
    <script src="../assets/vendor/js/jspdf.plugin.autotable.min.js"></script>
</head>

<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar Navigation -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="../assets/images/NAOS LOGO.png" alt="NAOS Logo" class="sidebar-logo">
                <div class="sidebar-title">NAOS</div>
            </div>

            <div class="sidebar-menu">
                <a href="#" class="sidebar-menu-item active" data-section="overview">
                    <span class="sidebar-menu-item-icon"><i class="fa-solid fa-house"></i></span>
                    <span data-t="overview">Overview</span>
                </a>
                <a href="#" class="sidebar-menu-item" data-section="market">
                    <span class="sidebar-menu-item-icon"><i class="fa-solid fa-chart-line"></i></span>
                    <span data-t="market_intelligence">Market Intelligence</span>
                </a>
                <a href="#" class="sidebar-menu-item" data-section="listings">
                    <span class="sidebar-menu-item-icon"><i class="fa-solid fa-wheat-awn"></i></span>
                    <span data-t="available_produce">Available Produce</span>
                </a>
                <a href="#" class="sidebar-menu-item" data-section="orders">
                    <span class="sidebar-menu-item-icon"><i class="fa-solid fa-box"></i></span>
                    <span data-t="my_orders">My Orders</span>
                </a>
                <a href="#" class="sidebar-menu-item" data-section="reports">
                    <span class="sidebar-menu-item-icon"><i class="fa-solid fa-file-lines"></i></span>
                    <span data-t="reports">Reports</span>
                </a>
                <a href="#" class="sidebar-menu-item" data-section="support">
                    <span class="sidebar-menu-item-icon"><i class="fa-solid fa-headset"></i></span>
                    <span data-t="support">Support</span>
                </a>
                <a href="#" class="sidebar-menu-item" data-section="payments">
                    <span class="sidebar-menu-item-icon"><i class="fa-solid fa-credit-card"></i></span>
                    <span data-t="payments">Payments</span>
                </a>
            </div>

            <div class="sidebar-footer">
                <button onclick="confirmLogout()" class="btn-primary" style="width: 100%;"
                    data-t="logout">Logout</button>
            </div>
        </nav>

        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-bar">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="hamburger" id="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <h1 class="top-bar-title" data-t="overview"><?php echo t('overview'); ?></h1>
                </div>
                <div class="top-bar-actions" style="display: flex; align-items: center; gap: 10px;">
                    <!-- Cart Icon -->
                    <button class="cart-icon-btn" onclick="Cart.openDrawer()" title="My Cart"
                        style="border: 1px solid var(--border-color); padding: 0.5rem 0.75rem; border-radius: 6px; display: flex; align-items: center; justify-content: center; height: 100%;">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span id="cart-badge">0</span>
                    </button>
                    <div id="google_translate_element" style="display: none;"></div>
                    <div class="lang-switcher" id="langSwitcher">
                        <button type="button" class="lang-switcher-btn" id="langToggle" aria-haspopup="listbox" aria-expanded="false">
                            <span class="lang-flag"><i class="fa-solid fa-language"></i></span>
                            <span class="lang-label">English</span>
                            <i class="fa-solid fa-chevron-down lang-chevron"></i>
                        </button>
                        <div class="lang-dropdown-panel" id="langDropdownPanel" role="listbox" aria-label="Language selection">
                            <button type="button" class="lang-dropdown-item" data-lang="en">English</button>
                            <button type="button" class="lang-dropdown-item" data-lang="ny">Chichewa</button>
                            <button type="button" class="lang-dropdown-item" data-lang="tum">Tumbuka</button>
                        </div>
                    </div>
                    <script type="text/javascript">
                        function googleTranslateElementInit() {
                            new google.translate.TranslateElement({
                                pageLanguage: 'en',
                                includedLanguages: 'en,ny,tum',
                                layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                                autoDisplay: false
                            }, 'google_translate_element');
                        }
                    </script>
                    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
                    <?php
                    // Check if user has multiple roles
                    $userRoles = getUserRoles($conn, $_SESSION['user_id']);
                    if (count($userRoles) > 1):
                        ?>
                        <div style="position: relative;">
                            <select id="roleSwitcher" onchange="switchRole(this.value)"
                                style="padding: 0.5rem 2rem 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 6px; background: white; cursor: pointer; font-size: 0.875rem; width: auto; min-width: max-content;">
                                <option value="farmer" <?php echo (getActiveRole() === 'farmer') ? 'selected' : ''; ?>>
                                    Farmer View</option>
                                <option value="buyer" <?php echo (getActiveRole() === 'buyer') ? 'selected' : ''; ?>> Buyer
                                    View</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="user-profile-trigger" onclick="openProfileModal()" title="Edit Profile"
                        style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <div style="display: flex; flex-direction: column; align-items: flex-end; line-height: 1.2;">
                            <span class="text-sm" style="color: var(--text-secondary);">Welcome,</span>
                            <strong style="white-space: nowrap; font-size: 0.95rem; color: var(--primary-color);"><?php echo htmlspecialchars(getUsername($conn, $_SESSION['user_id']) ?? 'Buyer'); ?></strong>
                        </div>
                        <?php echo renderAvatar($conn, $_SESSION['user_id'], '../assets/images/profiles/', 'headerProfilePic', '40px'); ?>
                    </div>
                </div>
            </div>

            <div class="content-wrapper">

                <!-- Overview Section -->
                <div class="content-section active" id="overview">
                    <!-- <h2 class="section-title">Buyer Dashboard Overview</h2> -->
                    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">

                        <div class="panel overview-panel" data-section="market">
                            <div class="panel-header" data-t="market_intelligence"><i class="fa-solid fa-chart-line"></i> Market Intelligence</div>
                            <p class="text-sm" data-t="market_insights">View market trends and price analytics.</p>
                        </div>
                        <div class="panel overview-panel" data-section="listings">
                            <div class="panel-header" data-t="available_produce"><i class="fa-solid fa-wheat-awn"></i> Available Produce</div>
                            <p class="text-sm" data-t="browse_produce">Browse produce listings from farmers.</p>
                        </div>
                        <div class="panel overview-panel" data-section="orders">
                            <div class="panel-header" data-t="my_orders"><i class="fa-solid fa-box"></i> My Orders</div>
                            <p class="text-sm" data-t="no_order_history">Track your order history and status.</p>
                        </div>
                    </div>

                    <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                        <!-- Recent Listings -->
                        <div class="panel">
                            <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
                                <span><i class="fa-solid fa-clock"></i> <span data-t="recent_listings">Recent Produce Listings</span></span>
                                <button class="btn-secondary btn-sm" onclick="document.querySelector('[data-section=\'listings\']').click()" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">View All</button>
                            </div>
                            <p class="text-sm" style="margin-bottom: 0.75rem; color: #666;" data-t="browse_produce">Freshly added crops available for purchase.</p>
                            <div id="overview-recent-listings" style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                                <p style="color:#9ca3af; font-size:0.85rem; width:100%; text-align:center; padding:1rem 0;" data-t="loading">Loading recent listings...</p>
                            </div>
                        </div>

                        <!-- Market Snapshot -->
                        <div class="panel">
                            <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
                                <span><i class="fa-solid fa-money-bill-trend-up"></i> <span data-t="market_insights">Market Snapshot</span></span>
                                <button class="btn-secondary btn-sm" onclick="document.querySelector('[data-section=\'market\']').click()" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">Details</button>
                            </div>
                            <p class="text-sm" style="margin-bottom: 1rem; color: #666;">Current prices for major crops.</p>
                            <div id="overview-market-data">
                                <p style="text-align: center; color: #666; padding: 1rem;">Loading market data...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Market Intelligence Section -->
                <div class="content-section" id="market">
                    <!-- <h2 class="section-title">Market Intelligence</h2> -->
                    <div class="panel">
                        <div class="panel-header" data-t="current_market_prices">Current Market Trends</div>
                        <div id="market-data"></div>
                    </div>
                    <div class="panel">
                        <div class="panel-header" data-t="buying_recommendations">Buying Recommendations</div>
                        <div id="buyerAdvice"></div>
                        <button onclick="getBuyerAdvice()" class="btn-primary" style="margin-top: 1rem;" data-t="get_recommendations">Get
                            Recommendations</button>
                    </div>
                </div>

                <!-- Available Produce Section -->
                <div class="content-section" id="listings">
                    <div class="panel">
                        <!-- Search & Filter bar -->
                        <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-bottom:1.25rem; align-items:center;">
                            <div style="position:relative; flex:1; min-width:180px;">
                                <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:0.75rem; top:50%; transform:translateY(-50%); color:#9ca3af;"></i>
                                <input type="text" id="listingSearch" placeholder="Search produce..."
                                    oninput="filterListings()" data-t-placeholder="search_placeholder"
                                    style="width:100%; padding:0.65rem 0.75rem 0.65rem 2.25rem; border:1px solid var(--border-color); border-radius:8px; font-size:0.9rem; background:#f8fafc;">
                            </div>
                            <div style="position:relative; flex:1; min-width:160px;">
                                <i class="fa-solid fa-location-dot" style="position:absolute; left:0.75rem; top:50%; transform:translateY(-50%); color:#9ca3af;"></i>
                                <input type="text" id="listingLocation" placeholder="Filter by location..."
                                    oninput="filterListings()" data-t-placeholder="filter_location"
                                    style="width:100%; padding:0.65rem 0.75rem 0.65rem 2.25rem; border:1px solid var(--border-color); border-radius:8px; font-size:0.9rem; background:#f8fafc;">
                            </div>
                            <button onclick="getListings()" class="btn-primary" style="padding:0.65rem 1.1rem; border-radius:8px; white-space:nowrap;">
                                <i class="fa-solid fa-arrows-rotate"></i> <span data-t="refresh">Refresh</span>
                            </button>
                        </div>

                        <!-- Results count -->
                        <div id="listingCount" style="font-size:0.85rem; color:#6b7280; margin-bottom:1rem;"></div>

                        <!-- Product card grid -->
                        <div id="listings-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:1.25rem;">
                            <p style="grid-column:1/-1; text-align:center; padding:3rem; color:#9ca3af;">
                                <i class="fa-solid fa-store" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                                Click Refresh to load available produce.
                            </p>
                        </div>
                    </div>
                </div>



                <!-- My Orders Section -->
                <div class="content-section" id="orders">
                    <!-- <h2 class="section-title">My Orders</h2> -->
                    <div class="panel">
                        <div class="panel-header" data-t="my_orders_hdr">Order History</div>
                        <button onclick="getOrders()" class="btn-primary" data-t="my_orders">View Orders</button>
                        <div class="table-container" style="margin-top: 1rem;">
                            <table id="orders-table"></table>
                        </div>
                    </div>
                </div>

                <!-- Buyer Reports Section -->
                <div class="content-section" id="reports">
                    <!-- <h2 class="section-title">My Reports</h2> -->
                    <div class="panel">
                        <div class="panel-header" style="flex-direction: column; align-items: flex-start; gap: 1rem;">
                            <div
                                style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                                <span data-t="get_reports">Get your reports here</span>
                                <div class="panel-actions" style="display: flex; gap: 1rem;">
                                    <button onclick="exportUserReport('excel')" class="btn-primary"><i
                                            class="fa-solid fa-file-excel"></i> <span data-t="export_excel">Export Excel</span></button>
                                    <button onclick="exportUserReport('pdf')" class="btn-primary"
                                        style="background-color: #000000; color: white;"><i
                                            class="fa-solid fa-file-pdf"></i> <span data-t="export_pdf">Export PDF</span></button>
                                </div>
                            </div>

                            <div class="reports-filters"
                                style="width: 100%; display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1rem;">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label data-t="report_type">Report Type</label>
                                    <select id="userReportType" onchange="loadUserReports()"
                                        style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                                        <option value="orders" data-t="my_orders_hdr">Order History</option>
                                        <option value="market" data-t="market_insights">Market Trends</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label data-t="time_period">Time Period</label>
                                    <select id="userReportPeriod" onchange="loadUserReports()"
                                        style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                                        <option value="monthly" data-t="this_month">This Month</option>
                                        <option value="weekly" data-t="this_week">This Week</option>
                                        <option value="custom" data-t="custom_range">Custom Range</option>
                                    </select>
                                </div>
                                <div id="customDateContainer" class="form-group" style="margin-bottom: 0; display: none; gap: 0.5rem; align-items: center;">
                                    <input type="date" id="reportStartDate" onchange="loadUserReports()" max="<?php echo date('Y-m-d'); ?>" style="padding: 0.4rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem;">
                                    <span data-t="date_to">to</span>
                                    <input type="date" id="reportEndDate" onchange="loadUserReports()" max="<?php echo date('Y-m-d'); ?>" style="padding: 0.4rem; border: 1px solid #ccc; border-radius: 4px; font-size: 0.85rem;">
                                </div>

                            </div>
                        </div>

                        <div class="table-container" style="margin-top: 1rem;">
                            <table id="userReportsTable" style="width: 100%; border-collapse: collapse;">
                                <thead id="userReportsHead"></thead>
                                <tbody id="userReportsBody">
                                    <tr>
                                        <td colspan="5" style="text-align: center;">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Support Section -->
                <div class="content-section" id="support">
                    <!-- <h2 class="section-title">Help & Support</h2> -->
                    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                        <div class="panel">
                            <div class="panel-header"><i class="fa-solid fa-circle-question"></i> Frequently Asked
                                Questions</div>
                            <div style="margin-top: 1rem;">
                                <details style="margin-bottom: 1rem; cursor: pointer;">
                                    <summary style="font-weight: bold; color: #1b4d3e;">How do I place an order?
                                    </summary>
                                    <p class="text-sm" style="margin-top: 0.5rem;">Find a listing you like in "Available
                                        Produce", click "Order", and provide your contact details. The farmer will be
                                        notified and will call you directly to arrange the transaction and delivery.</p>
                                </details>
                                <details style="margin-bottom: 1rem; cursor: pointer;">
                                    <summary style="font-weight: bold; color: #1b4d3e;">What are the payment options?
                                    </summary>
                                    <p class="text-sm" style="margin-top: 0.5rem;">For <strong>produce</strong>, you pay
                                        the farmer directly upon delivery or as agreed during your call. Detailed
                                        payment history for orders processed through the platform can be viewed in the
                                        Payments section.
                                    </p>
                                </details>
                                <details style="margin-bottom: 1rem; cursor: pointer;">
                                    <summary style="font-weight: bold; color: #1b4d3e;">Is my contact info safe?
                                    </summary>
                                    <p class="text-sm" style="margin-top: 0.5rem;">Yes. Your contact information is only
                                        shared with the specific farmer from whom you have requested produce, to
                                        facilitate the sale.</p>
                                </details>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="panel-header"><i class="fa-solid fa-envelope"></i> Contact Us</div>
                            <p class="text-sm" style="margin-top: 1rem;">Need more help? Reach out to our team:</p>
                            <ul style="list-style: none; padding: 0; margin-top: 1rem;">
                                <li style="margin-bottom: 0.5rem;"><i class="fa-solid fa-phone"
                                        style="color: #1b4d3e; margin-right: 10px;"></i> 0997079797</li>
                                <li style="margin-bottom: 0.5rem;"><i class="fa-solid fa-envelope"
                                        style="color: #1b4d3e; margin-right: 10px;"></i> support@naos.mw</li>
                                <li><i class="fa-solid fa-location-dot" style="color: #1b4d3e; margin-right: 10px;"></i>
                                    Lilongwe, Malawi</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Payments Section -->
                <div class="content-section" id="payments">
                    <!-- <h2 class="section-title">Payments</h2> -->

                    <div class="panel">
                        <div class="panel-header">Payment History</div>
                        <div class="table-container" style="margin-top: 1rem;">
                            <table id="paymentsTable" style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentsBody">
                                    <tr>
                                        <td colspan="5" style="text-align: center;">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/toast.js?v=<?php echo time(); ?>"></script>
    <script src="../assets/js/cart.js?v=<?php echo time(); ?>"></script>
    <script src="../assets/js/script.js?v=<?php echo time(); ?>"></script>
    <script src="../assets/js/dashboard.js?v=<?php echo time(); ?>"></script>

    <!-- Cart Overlay -->
    <div id="cart-overlay"></div>

    <!-- Cart Drawer -->
    <div id="cart-drawer">
        <div class="cart-drawer-header">
            <div class="cart-drawer-title">
                <i class="fa-solid fa-cart-shopping"></i>
                My Cart
            </div>
            <button class="cart-close-btn" onclick="Cart.closeDrawer()">
                <i class="fa-solid fa-xmark"></i> Close
            </button>
        </div>

        <div id="cart-items"></div>

        <div class="cart-drawer-footer">
            <div class="cart-footer-row">
                <span style="font-weight:600;">Order Total</span>
                <span class="cart-footer-total" id="cart-total">MWK 0</span>
            </div>
            <p class="cart-footer-note">
                <i class="fa-solid fa-circle-info fa-xs"></i>
                Payment is arranged directly with each farmer after they contact you.
            </p>
            <div class="cart-footer-row" style="margin-bottom:0.5rem;">
                <button class="cart-clear-btn" onclick="Cart.clear()">
                    <i class="fa-solid fa-trash fa-xs"></i> Clear Cart
                </button>
            </div>
            <button class="cart-checkout-btn" id="cart-checkout-btn" onclick="Cart.checkout()" disabled>
                <i class="fa-solid fa-check"></i> Place Orders
            </button>
        </div>
    </div>

    <!-- Profile Edit Modal -->
    <div id="profile-modal" class="profile-modal-overlay">
        <div class="modal-content profile-modal">
            <div class="modal-header">
                <h3>Edit Profile</h3>
                <button class="close-modal" onclick="closeProfileModal()">&times;</button>
            </div>
            <form id="profileForm" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_profile">

                <div class="profile-pic-edit">
                    <?php echo renderAvatar($conn, $_SESSION['user_id'], '../assets/images/profiles/', 'modalProfilePreview', '100px'); ?>
                    <label for="profile_picture_input" class="btn-secondary btn-sm">Change Photo</label>
                    <input type="file" id="profile_picture_input" name="profile_picture" accept="image/*"
                        style="display: none;" onchange="previewProfilePic(this)">
                </div>

                <div class="form-group">
                    <label for="profileUsername">Username</label>
                    <input type="text" id="profileUsername" name="username" required>
                </div>

                <div class="form-group">
                    <label for="profileLocation">Location</label>
                    <input type="text" id="profileLocation" name="location">
                </div>

                <div class="form-group">
                    <label for="profilePhone">Phone Number</label>
                    <input type="text" id="profilePhone" name="phone_number" readonly
                        style="background: #f1f5f9; cursor: not-allowed;" title="Phone number cannot be changed here.">
                </div>

                <div class="form-group">
                    <label>Gender</label>
                    <div style="display: flex; gap: 20px; align-items: center; margin-top: 5px; cursor: not-allowed;" title="Gender cannot be changed here.">
                        <div style="pointer-events: none; display: flex; gap: 20px;">
                            <label style="display: flex; align-items: center; gap: 5px; font-weight: normal; color: #666;">
                                <input type="radio" name="gender" value="male" id="genderMale" tabindex="-1"> Male
                            </label>
                            <label style="display: flex; align-items: center; gap: 5px; font-weight: normal; color: #666;">
                                <input type="radio" name="gender" value="female" id="genderFemale" tabindex="-1"> Female
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeProfileModal()">Maybe Later</button>
                    <button type="submit" class="btn-primary">Save Profile</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', async () => {
        // Fetch recent listings — rendered as mini cards
        try {
            const res  = await fetch('/naos/api/listings.php?action=get');
            const data = await res.json();
            const container = document.getElementById('overview-recent-listings');
            if (data.listings && data.listings.length > 0) {
                const recent = data.listings.slice(0, 5);
                container.innerHTML = recent.map(l => {
                    const img = l.produce_image
                        ? `<img src="/naos/${l.produce_image}" alt="${l.produce_type}" style="width:100%; height:90px; object-fit:cover; border-radius:8px 8px 0 0;">`
                        : `<div style="width:100%; height:90px; background:linear-gradient(135deg,#1b4d3e,#2d7a5f); border-radius:8px 8px 0 0; display:flex; align-items:center; justify-content:center;"><i class="fa-solid fa-wheat-awn" style="font-size:1.8rem; color:rgba(255,255,255,0.4);"></i></div>`;
                    const unit = l.unit || 'kg';
                    return `
                    <div style="width:150px; flex-shrink:0; border:1px solid var(--border-color); border-radius:10px; overflow:hidden; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,0.06); transition:transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
                        ${img}
                        <div style="padding:0.5rem;">
                            <div style="font-weight:700; font-size:0.8rem; text-transform:capitalize; color:var(--text-color); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${l.produce_type}</div>
                            <div style="font-size:0.72rem; color:var(--primary-color); font-weight:600; margin-top:0.1rem;">MWK ${Number(l.price).toLocaleString()}/${unit}</div>
                            <button onclick="Cart.add(${l.id}, '${l.produce_type.replace(/'/g,"\\'")}', ${l.price}, ${parseFloat(l.quantity)}, '${(l.username||'').replace(/'/g,"\\'")}');this.textContent='✓ Added!';setTimeout(()=>this.textContent='Add to Cart',1500)"
                                style="margin-top:0.4rem; width:100%; padding:0.3rem; background:var(--primary-color); color:#fff; border:none; border-radius:5px; font-size:0.7rem; font-weight:600; cursor:pointer;">
                                Add to Cart
                            </button>
                        </div>
                    </div>`;
                }).join('');
            } else {
                container.innerHTML = '<p style="color:#9ca3af; font-size:0.85rem; width:100%; text-align:center; padding:1rem 0;">No recent listings found.</p>';
            }
        } catch (e) {
            document.getElementById('overview-recent-listings').innerHTML = '<p style="color:red; font-size:0.85rem; width:100%; text-align:center;">Error loading listings.</p>';
        }

        // Fetch market snapshot
        try {
            const res = await fetch('/naos/api/market.php');
            const data = await res.json();
            const marketEl = document.getElementById('overview-market-data');
            if (data.prices) {
                const crops = Object.keys(data.prices).slice(0, 4);
                let html = '<div style="display: flex; flex-direction: column; gap: 0.5rem;">';
                crops.forEach(crop => {
                    html += `<div style="display: flex; justify-content: space-between; padding: 0.75rem; background: #f8f9fa; border-radius: 6px; border: 1px solid var(--border-color);">
                        <strong style="color: var(--text-color);">${crop}</strong>
                        <span style="color: var(--primary-color); font-weight: 600;">MWK ${data.prices[crop]}/kg</span>
                    </div>`;
                });
                html += '</div>';
                marketEl.innerHTML = html;
            }
        } catch (e) {
            document.getElementById('overview-market-data').innerHTML = '<p style="text-align: center; color: red;">Error loading market data.</p>';
        }
    });
    </script>
</body>

</html>