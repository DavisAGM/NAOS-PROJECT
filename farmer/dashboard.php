<?php
require '../includes/db.php';
require '../includes/auth.php';
require '../includes/lang.php';

if (!isLoggedIn()) {
    header('Location: ../auth.php');
    exit();
}

// Role guard — redirect buyers/admins to their own dashboard
$_activeRole = getActiveRole();
if ($_activeRole !== 'farmer' && $_activeRole !== 'admin') {
    $roleMap = ['buyer' => '../buyer/dashboard.php', 'admin' => '../admin/dashboard.php'];
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

// Fetch crops for the dropdown
$crops_query = "SELECT crop_name FROM crop_config ORDER BY crop_name ASC";
$crops_result = $conn->query($crops_query);
$crops = [];
if ($crops_result && $crops_result->num_rows > 0) {
    while ($row = $crops_result->fetch_assoc()) {
        $crops[] = $row['crop_name'];
    }
}

?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1b4d3e">
    <script>window.NAOS_LANG = '<?php echo htmlspecialchars($lang, ENT_QUOTES); ?>';</script>
    <title>NAOS Dashboard</title>
    <link rel="stylesheet" href="../assets/vendor/css/inter.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/toast.css">
    <link rel="stylesheet" href="../assets/vendor/css/all.min.css">
    <script src="../assets/vendor/js/jspdf.umd.min.js"></script>
    <script src="../assets/vendor/js/jspdf.plugin.autotable.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- <style>
        body {
            background-image: url('../assets/images/dashboard-bg.jpg');
            background-size: cover;
        }
    </style> -->
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

                <a href="#" class="sidebar-menu-item" data-section="weather">
                    <span class="sidebar-menu-item-icon"><i class="fa-solid fa-cloud-sun"></i></span>
                    <span data-t="weather_advice">Weather & Advice</span>
                </a>
                <a href="#" class="sidebar-menu-item" data-section="market">
                    <span class="sidebar-menu-item-icon"><i class="fa-solid fa-chart-line"></i></span>
                    <span data-t="market_prices">Market Prices</span>
                </a>
                <a href="#" class="sidebar-menu-item" data-section="diary">
                    <span class="sidebar-menu-item-icon"><i class="fa-solid fa-book"></i></span>
                    <span data-t="farm_diary">Farm Diary</span>
                </a>
                <a href="#" class="sidebar-menu-item" data-section="reports">
                    <span class="sidebar-menu-item-icon"><i class="fa-solid fa-file-lines"></i></span>
                    <span data-t="reports">Reports</span>
                </a>
                <a href="#" class="sidebar-menu-item" data-section="produce">
                    <span class="sidebar-menu-item-icon"><i class="fa-solid fa-wheat-awn"></i></span>
                    <span data-t="my_produce">My Produce</span>
                </a>
                <a href="#" class="sidebar-menu-item" data-section="incoming-orders">
                    <span class="sidebar-menu-item-icon"><i class="fa-solid fa-cart-arrow-down"></i></span>
                    <span data-t="incoming_orders">Incoming Orders</span>
                </a>
                <a href="#" class="sidebar-menu-item" data-section="buyers">
                    <span class="sidebar-menu-item-icon"><i class="fa-solid fa-users"></i></span>
                    <span data-t="buyer_directory">Buyer Directory</span>
                </a>
                <a href="#" class="sidebar-menu-item" data-section="payments">
                    <span class="sidebar-menu-item-icon"><i class="fa-solid fa-credit-card"></i></span>
                    <span data-t="payments">Payments</span>
                </a>
            </div>

            <div class="sidebar-footer">
                <button onclick="confirmLogout()" class="btn-primary" style="width: 100%;"
                    data-t="logout"><?php echo t('logout'); ?></button>
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
                    <div class="notifications" style="position: relative; cursor: pointer;"
                        onclick="toggleNotifications()">
                        <span style="font-size: 1.5rem;"><i class="fa-solid fa-bell"></i></span>
                        <span id="notifBadge"
                            style="position: absolute; top: 0; right: 0; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; display: none;">0</span>
                        <div id="notifDropdown"
                            style="display: none; position: absolute; right: 0; top: 30px; background: white; border: 1px solid #ccc; width: 300px; max-height: 400px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 0.5rem;">
                            <h4 style="margin: 0 0 0.5rem 0; border-bottom: 1px solid #eee; padding-bottom: 0.5rem;" data-t="notifications">
                                Notifications</h4>
                            <ul id="notifList" style="list-style: none; padding: 0; margin: 0;"></ul>
                        </div>
                    </div>
                </div>
                <div class="top-bar-actions" style="display: flex; align-items: center; gap: 10px;">
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
                            <strong style="white-space: nowrap; font-size: 0.95rem; color: var(--primary-color);"><?php echo htmlspecialchars(getUsername($conn, $_SESSION['user_id']) ?? 'Farmer'); ?></strong>
                        </div>
                        <?php echo renderAvatar($conn, $_SESSION['user_id'], '../assets/images/profiles/', 'headerProfilePic', '40px'); ?>
                    </div>
                </div>
            </div>

            <div class="content-wrapper">

                <?php if (getUserRole() === 'farmer' && !checkSubscription($conn, $_SESSION['user_id'] ?? 0)): ?>
                    <div class="alert alert-warning"
                        style="background-color: #fff3cd; color: #856404; padding: 1rem; margin-bottom: 1rem; border-radius: 0.5rem; border: 1px solid #ffeeba;">
                        <strong data-t="notice">Notice:</strong> <span data-t="sub_ended">You have no Active subscription.</span>
                        <a href="javascript:void(0)" onclick="renewSubscription()"
                            style="color: #856404; text-decoration: underline;" data-t="subscribe_now">Subscribe now</a> <span data-t="subscribe_now_desc">to Use all features.</span>
                    </div>
                <?php endif; ?>

                <!-- Overview Section -->
                <div class="content-section active" id="overview">
                    <!-- <h2 class="section-title">Overview</h2> -->
                    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                        <div class="panel">
                            <div class="panel-header" data-t="sub_status"><i class="fa-solid fa-credit-card"></i> Subscription Status</div>
                            <div style="text-align: center; padding: 1rem;">
                                <div id="subBadgeOverview"
                                    style="font-size: 1.2rem; font-weight: bold; margin-bottom: 0.5rem; color: #856404;">
                                    <?php
                                    $sub = getSubscriptionDetails($conn, $_SESSION['user_id']);
                                    if ($sub) {
                                        echo '<span class="text-success" data-t="active">ACTIVE</span>';
                                        echo '<br><span style="font-size: 0.8rem; font-weight: normal; color: #666;">(<span data-t="expires">Expires</span>: ' . date('Y-m-d', strtotime($sub['expiry_date'])) . ')</span>';
                                    } else {
                                        echo '<span class="text-danger" data-t="inactive">INACTIVE</span>';
                                    }
                                    ?>
                                </div>
                                <?php if (!checkSubscription($conn, $_SESSION['user_id'])): ?>
                                    <button onclick="renewSubscription()" class="btn-primary btn-sm"><i class="fa-solid fa-credit-card"></i> <span data-t="subscribe">Subscribe</span></button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="panel-header" data-t="weather"><i class="fa-solid fa-cloud-sun"></i> Weather</div>
                            <p class="text-sm" data-t="weather_desc">Check today's weather and get crop recommendations.</p>
                        </div>
                        <div class="panel">
                            <div class="panel-header" data-t="market"><i class="fa-solid fa-chart-line"></i> Market</div>
                            <p class="text-sm" data-t="market_desc">View current market prices for your crops.</p>
                        </div>
                        <div class="panel">
                            <div class="panel-header" data-t="produce"><i class="fa-solid fa-wheat-awn"></i> Produce</div>
                            <p class="text-sm" data-t="produce_desc">List your produce for buyers to see.</p>
                        </div>
                    </div>

                    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
                        <!-- Recent Farm Activities -->
                        <div class="panel">
                            <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
                                <span><i class="fa-solid fa-clock-rotate-left"></i> <span data-t="recent_activities">Recent Activities</span></span>
                                <button class="btn-secondary btn-sm" onclick="document.querySelector('[data-section=\'diary\']').click()" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;" data-t="go_to_diary">Go to Diary</button>
                            </div>
                            <div class="recent-activities-list">
                                <p class="text-sm text-center" style="color: #666; margin-top: 1rem;" data-t="loading">Loading...</p>
                            </div>
                        </div>

                        <!-- Reminders & Alerts -->
                        <div class="panel">
                            <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
                                <span><i class="fa-solid fa-bell"></i> <span data-t="upcoming_reminders">Upcoming Reminders</span></span>
                            </div>
                            <div class="reminders-list">
                                <p class="text-sm text-center" style="color: #666; margin-top: 1rem;">Loading reminders...</p>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Weather & Crop Advice Section -->
                <div class="content-section" id="weather">
                    <!-- <h2 class="section-title">Weather & Advice</h2> -->
                    <div class="panel">
                        <div class="panel-header" data-t="todays_weather">Today's Weather</div>
                        <!-- Provide a farm selector for weather viewing -->
                        <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <label data-t="viewing_weather_for">Viewing Weather For:</label>
                                <select id="weatherFarmSelector" onchange="fetchWeatherForSelectedFarm()"
                                    style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; margin-left:10px;">
                                    <option value="">Home Location</option>
                                    <!-- options populated by JS -->
                                </select>
                            </div>

                        </div>
                        <div id="weather-data"></div>
                    </div>
                    <div class="panel">
                        <div class="panel-header" data-t="what_plant">What Should I Plant?</div>
                        <div style="margin-bottom:1rem;">
                            <label data-t="get_rec_for">Get Recommendations For:</label>
                            <select id="recFarmSelector"
                                style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; margin-left:10px;">
                                <option value="">Home Location (General)</option>
                                <!-- options populated by JS -->
                            </select>
                        </div>
                        <button onclick="getCropRec()" class="btn-primary" data-t="get_recommendation">Get Recommendation</button>
                        <div id="crop_rec" style="margin-top: 1rem;"></div>
                    </div>
                    <div class="panel">
                        <div class="panel-header" data-t="crop_planning">Crop Planning</div>
                        <p data-t="crop_planning_desc">Tell us what you want to plant, and we'll tell you if it's a good idea or not.</p>
                        <form id="cropPlanForm" style="margin-top: 1rem;">
                            <input list="cropList" id="plannedCrop" class="form-control"
                                placeholder="Select or type a crop..." required>
                            <datalist id="cropList">
                                <?php foreach ($crops as $crop): ?>
                                    <option value="<?php echo ucfirst(htmlspecialchars($crop)); ?>">
                                    <?php endforeach; ?>
                            </datalist>
                            <label for="plannedQty" class="visually-hidden"><?php echo t('planned_qty'); ?></label>
                            <input type="number" placeholder="<?php echo t('planned_qty'); ?>" id="plannedQty">
                            <button type="submit"><?php echo t('get_decision'); ?></button>
                        </form>
                        <div id="decisionSupport" style="margin-top: 1rem;"></div>
                    </div>
                </div>

                <!-- Market Intelligence Section -->
                <div class="content-section" id="market">
                    <!-- <h2 class="section-title">Market Intelligence</h2> -->
                    <div class="panel">
                        <div class="panel-header" data-t="current_market_prices">Current Market Prices</div>
                        <div id="market-data"></div>
                    </div>
                </div>

                <!-- Farm Diary Section -->
                <div class="content-section" id="diary">
                    <!-- <h2 class="section-title">Digital Farm Diary</h2> -->

                    <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <!-- Left Card: Add Activity Trigger -->
                        <button type="button" onclick="openActivityModal()" class="panel"
                            style="margin: 0; text-align: left; cursor: pointer; border: none; background: white; width: 100%; transition: transform 0.2s;"
                            onmouseover="this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.transform='translateY(0)'">
                            <div class="panel-header" style="pointer-events: none;" data-t="record_activity"><i class="fa-solid fa-plus"></i>
                                Record Activity</div>
                            <p class="text-sm" style="pointer-events: none;" data-t="record_activity_desc">Record a new farm activity like planting or
                                harvesting.</p>
                        </button>

                        <!-- Right Card: View Activities Trigger -->
                        <button type="button" onclick="openHistoryModal()" class="panel"
                            style="margin: 0; text-align: left; cursor: pointer; border: none; background: white; width: 100%; transition: transform 0.2s;"
                            onmouseover="this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.transform='translateY(0)'">
                            <div class="panel-header" style="pointer-events: none;" data-t="history"><i
                                    class="fa-solid fa-clock-rotate-left"></i> History</div>
                            <p class="text-sm" style="pointer-events: none;" data-t="history_desc">View your previously recorded farm
                                activities.</p>
                        </button>
                    </div>

                    <div class="panel" style="margin-top: 1rem;">
                        <div class="panel-header" data-t="upcoming_activities"><i class="fa-solid fa-list-check"></i> Upcoming Activities</div>
                        <p class="text-sm" style="margin-bottom: 1rem;" data-t="upcoming_activities_desc">Based on your planting records, here's what you
                            should be doing next.</p>
                        <div class="reminders-list">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                    <div class="panel" style="margin-top: 1rem;">
                        <div class="panel-header" data-t="registered_farms">Registered Farms</div>
                        <div class="grid" id="farmsList"
                            style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-top:1rem;">
                            <!-- Farm cards will be loaded here via JS -->
                        </div>
                    </div>

                    <div class="panel" style="margin-top: 1.5rem;">
                        <div class="panel-header" data-t="add_new_farm">Add a New Farm Location</div>
                        <form id="addFarmForm" onsubmit="handleAddFarm(event)">
                            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <label data-t="farm_name">Farm Name</label>
                                    <input type="text" id="farm_name" required
                                        placeholder="e.g., Home Garden, Kasungu Plot"
                                        style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                                </div>
                                <div>
                                    <label data-t="district">District</label>
                                    <select id="farm_district" required
                                        style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;"
                                        onchange="(function(sel){ var d = sel.value; if(d){ document.getElementById('farm_name').value = d + ' Farm'; } })(this)">
                                        <option value="" data-t="select_district">Select District</option>
                                        <option value="Lilongwe">Lilongwe</option>
                                        <option value="Blantyre">Blantyre</option>
                                        <option value="Mzuzu">Mzuzu</option>
                                        <option value="Zomba">Zomba</option>
                                        <option value="Kasungu">Kasungu</option>
                                        <option value="Rumphi">Rumphi</option>
                                        <option value="Mzimba">Mzimba</option>
                                        <option value="Salima">Salima</option>
                                        <option value="Thyolo">Thyolo</option>
                                        <option value="Mulanje">Mulanje</option>
                                        <!-- Add more as needed -->
                                    </select>
                                </div>
                                <div>
                                    <label data-t="eco_zone">Ecological Zone</label>
                                    <select id="farm_eco_zone" required
                                        style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                                        <option value="" data-t="select_zone">Select Zone</option>
                                        <option value="Shire Valley">Shire Valley</option>
                                        <option value="Lakeshore">Lakeshore</option>
                                        <option value="Medium Altitude">Medium Altitude</option>
                                        <option value="High Altitude">High Altitude</option>
                                    </select>
                                </div>
                                <div>
                                    <label data-t="soil_type">Predominant Soil Type</label>
                                    <select id="farm_soil_type" required
                                        style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                                        <option value="" data-t="select_soil_type">Select Soil Type</option>
                                        <option value="Ferruginous">Ferruginous (Red soils)</option>
                                        <option value="Alluvial">Alluvial (River/Lake deposits)</option>
                                        <option value="Lithosols">Lithosols (Shallow/Stony)</option>
                                        <option value="Vertisols">Vertisols (Black cotton soils)</option>
                                        <option value="Sandy">Sandy/Loamy Sand</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Map for location selection -->
                            <div id="farmMap" style="height: 300px; width: 100%; border-radius: 8px; margin-bottom: 1rem; border: 1px solid #ccc; z-index: 1;"></div>

                            <!-- Hidden coordinates populated by map/geolocation -->
                            <input type="hidden" id="farm_lat" required>
                            <input type="hidden" id="farm_lon" required>
                            
                            <div style="margin-bottom: 1rem; text-align: right;">
                                <small style="display:block; color:#666;">
                                    <a href="javascript:void(0)" onclick="getLocationForFarm()" data-t="use_current_location">
                                        <i class="fa-solid fa-location-crosshairs"></i> Use My Current Location
                                    </a>
                                    | <i class="fa-solid fa-map-pin"></i> Click map to pin location
                                </small>
                            </div>
                            <button type="submit" class="btn-primary" data-t="save_farm">Save Farm</button>
                        </form>
                    </div>
                </div>

                <!-- Farm Reports Section -->
                <div class="content-section" id="reports">
                    <!-- <h2 class="section-title">Farm Reports</h2> -->
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
                                        <option value="sales" data-t="sales_history">Sales History</option>
                                        <option value="production" data-t="production_diary">Production (Diary)</option>
                                        <option value="weather" data-t="weather_history">Weather History</option>
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

                <!-- Produce Listings Section -->
                <div class="content-section" id="produce">
                    <div class="panel">
                        <div class="panel-header" style="display:flex; justify-content:space-between; align-items:center;">
                            <span><i class="fa-solid fa-store"></i> <span data-t="add_new_listing">Add New Listing</span></span>
                        </div>

                        <form id="listingForm" enctype="multipart/form-data" style="margin-top:1rem;">
                            <!-- Image upload -->
                            <div style="margin-bottom:1.25rem;">
                                <label style="display:block; font-weight:600; margin-bottom:0.5rem; color:var(--text-color);">
                                    <i class="fa-solid fa-camera" style="color:var(--primary-color);"></i> <span data-t="produce_photo">Produce Photo</span> <span style="color:#ef4444;">*</span>
                                </label>
                                <div id="imageUploadArea" onclick="document.getElementById('produceImage').click()"
                                    style="border:2px dashed var(--primary-color); border-radius:10px; padding:1.5rem; text-align:center; cursor:pointer; background:#f0fdf4; transition:all 0.2s; position:relative; min-height:150px; display:flex; align-items:center; justify-content:center;">
                                    <div id="uploadPlaceholder">
                                        <i class="fa-solid fa-cloud-arrow-up" style="font-size:2rem; color:var(--primary-color); margin-bottom:0.5rem; display:block;"></i>
                                        <div style="font-weight:600; color:var(--primary-color);" data-t="click_upload_photo">Click to upload photo</div>
                                        <div style="font-size:0.8rem; color:#6b7280; margin-top:0.25rem;" data-t="upload_hint">JPG, PNG or WebP · Max 5 MB</div>
                                    </div>
                                    <img id="imagePreview" src="" alt="Preview" style="display:none; max-height:200px; max-width:100%; border-radius:8px; object-fit:cover;">
                                </div>
                                <input type="file" id="produceImage" name="produce_image" accept="image/*" style="display:none;" onchange="previewProduceImage(this)" required>
                            </div>

                            <!-- Crop type -->
                            <div style="margin-bottom:1rem;">
                                <label style="display:block; font-weight:600; margin-bottom:0.4rem; color:var(--text-color);" data-t="produce_type">Produce Type <span style="color:#ef4444;">*</span></label>
                                <select id="type" name="type" required
                                    style="width:100%; padding:0.75rem; border:1px solid var(--border-color); border-radius:8px; background:#f8fafc; font-size:0.95rem;">
                                    <option value="">-- Select Crop --</option>
                                    <?php foreach ($crops as $crop): ?>
                                        <option value="<?php echo htmlspecialchars($crop); ?>"><?php echo ucfirst(htmlspecialchars($crop)); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Quantity + unit -->
                            <div style="display:grid; grid-template-columns:2fr 1fr; gap:0.75rem; margin-bottom:1rem;">
                                <div>
                                    <label style="display:block; font-weight:600; margin-bottom:0.4rem; color:var(--text-color);" data-t="quantity">Quantity <span style="color:#ef4444;">*</span></label>
                                    <input type="number" id="quantity" name="quantity" min="0.1" step="0.1" required placeholder="e.g. 50"
                                        style="width:100%; padding:0.75rem; border:1px solid var(--border-color); border-radius:8px; background:#f8fafc;">
                                </div>
                                <div>
                                    <label style="display:block; font-weight:600; margin-bottom:0.4rem; color:var(--text-color);" data-t="unit">Unit</label>
                                    <select name="unit" id="listingUnit"
                                        style="width:100%; padding:0.75rem; border:1px solid var(--border-color); border-radius:8px; background:#f8fafc;">
                                        <option value="kg">kg</option>
                                        <option value="bags">bags</option>
                                        <option value="crates">crates</option>
                                        <option value="tonnes">tonnes</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Price -->
                            <div style="margin-bottom:1rem;">
                                <label style="display:block; font-weight:600; margin-bottom:0.4rem; color:var(--text-color);" data-t="price_mwk">Price (MWK per unit) <span style="color:#ef4444;">*</span></label>
                                <input type="number" id="price" name="price" min="1" step="1" required placeholder="e.g. 500"
                                    style="width:100%; padding:0.75rem; border:1px solid var(--border-color); border-radius:8px; background:#f8fafc;">
                            </div>

                            <!-- Description -->
                            <div style="margin-bottom:1.25rem;">
                                <label style="display:block; font-weight:600; margin-bottom:0.4rem; color:var(--text-color);" data-t="description">Description <span style="font-weight:400; color:#6b7280;" data-t="description_opt">(optional)</span></label>
                                <textarea name="description" id="listingDescription" rows="3" placeholder="e.g. Freshly harvested, Grade A, available for immediate pickup..."
                                    style="width:100%; padding:0.75rem; border:1px solid var(--border-color); border-radius:8px; background:#f8fafc; resize:vertical; font-family:inherit; font-size:0.95rem;"></textarea>
                            </div>

                            <button type="submit" class="btn-primary" style="width:100%; padding:0.85rem; font-size:1rem; border-radius:8px;" data-t="publish_listing">
                                <i class="fa-solid fa-store"></i> Publish Listing
                            </button>
                        </form>
                    </div>

                    <!-- Farmer's own listings -->
                    <div class="panel" style="margin-top:1.5rem;">
                        <div class="panel-header" style="display:flex; justify-content:space-between; align-items:center;">
                            <span><i class="fa-solid fa-list"></i> <span data-t="your_active_listings">Your Active Listings</span></span>
                            <button onclick="getListings()" class="btn-secondary" style="padding:0.4rem 0.9rem; font-size:0.85rem; border-radius:6px;">
                                <i class="fa-solid fa-arrows-rotate"></i> <span data-t="refresh">Refresh</span>
                            </button>
                        </div>
                        <div id="farmer-listings-grid" style="margin-top:1rem; display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:1rem;">
                            <p style="color:#6b7280; font-size:0.9rem; grid-column:1/-1; text-align:center; padding:2rem 0;" data-t="listings_appear_here">Your listings will appear here.</p>
                        </div>
                    </div>
                </div>


                <!-- Incoming Orders Section -->
                <div class="content-section" id="incoming-orders">
                    <!-- <h2 class="section-title">Incoming Orders</h2> -->
                    <div class="panel">
                        <div class="panel-header">Orders from Buyers</div>
                        <p class="text-sm" style="margin-bottom: 1rem;">View orders for your produce. Please call the
                            buyers directly to arrange delivery and payment.</p>
                        <button onclick="getIncomingOrders()" class="btn-primary">Refresh Orders</button>
                        <div class="table-container" style="margin-top: 1rem;">
                            <table id="incoming-orders-table"></table>
                        </div>
                    </div>
                </div>

                <!-- Buyer Directory Section -->
                <div class="content-section" id="buyers">
                    <!-- <h2 class="section-title">Buyer Directory</h2> -->
                    <div class="panel">
                        <div class="panel-header">Registered Buyers</div>
                        <div style="margin-bottom: 1rem;">
                            <input type="text" id="buyerSearchInput" placeholder="Search buyers..."
                                onkeyup="filterBuyers()"
                                style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; width: 100%; max-width: 300px;">
                        </div>
                        <div class="table-container">
                            <table id="buyerDirectoryTable" style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="buyerDirectoryBody">
                                    <tr>
                                        <td colspan="3" style="text-align: center;">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payments Section -->
                <div class="content-section" id="payments">
                    <!-- <h2 class="section-title">Payments & Subscriptions</h2> -->

                    <div class="grid"
                        style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div class="panel">
                            <div class="panel-header">Subscription Status</div>
                            <div id="subStatusCard" style="padding: 1rem; text-align: center;">
                                <div id="subBadge" style="font-size: 1.25rem; font-weight: bold; margin-bottom: 1rem;">
                                    Checking status...</div>
                                <button onclick="renewSubscription()" class="btn-primary" id="renewBtn"
                                    style="display: none;">Subscribe</button>
                            </div>
                        </div>
                    </div>

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

    <!-- Activity Record Modal -->
    <div id="activity-modal" class="profile-modal-overlay" style="display: none;">
        <div class="modal-content profile-modal">
            <div class="modal-header">
                <h3><i class="fa-solid fa-plus"></i> Record Activity</h3>
                <button class="close-modal" onclick="closeActivityModal()">&times;</button>
            </div>
            <div style="padding: 1rem;">
                <form id="farmForm">
                    <label for="diaryFarmSelector"
                        style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Which farm?</label>
                    <select id="diaryFarmSelector" name="farm_id" required
                        style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; width: 100%; margin-bottom: 1rem;">
                        <option value="">Select a farm...</option>
                        <!-- Populated by JS -->
                    </select>

                    <label for="activityType" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">What did
                        you do?</label>
                    <select id="activityType" name="activity_type" required
                        style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; width: 100%; margin-bottom: 1rem;">
                        <option value="">Select activity...</option>
                        <option value="planted">Planted</option>
                        <option value="weeded">Weeded</option>
                        <option value="fertilized">Applied Fertilizer</option>
                        <option value="sprayed">Sprayed Pesticide</option>
                        <option value="harvested">Harvested</option>
                    </select>

                    <label for="cropName" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Crop:</label>
                    <select id="cropName" name="crop_name" required
                        style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; width: 100%; margin-bottom: 1rem;">
                        <option value="">Select crop...</option>
                        <?php foreach ($crops as $crop): ?>
                            <option value="<?php echo htmlspecialchars($crop); ?>">
                                <?php echo ucfirst(htmlspecialchars($crop)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="activityDate"
                        style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Date:</label>
                    <input type="date" id="activityDate" name="date" required max="<?php echo date('Y-m-d'); ?>"
                        style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; width: 100%; margin-bottom: 1rem;">

                    <!-- Conditional harvest fields -->
                    <div id="harvestFields" style="display: none;">
                        <label for="harvestQuantity"
                            style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Amount harvested:</label>
                        <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                            <input type="number" id="harvestQuantity" name="quantity" placeholder="e.g., 50" step="0.01"
                                style="flex: 2; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                            <select id="harvestUnit" name="unit"
                                style="flex: 1; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
                                <option value="kg">kg</option>
                                <option value="bags">bags</option>
                                <option value="tons">tons</option>
                            </select>
                        </div>
                    </div>

                    <label for="activityNotes" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Notes
                        (optional):</label>
                    <textarea id="activityNotes" name="notes" rows="2" placeholder="Any additional details..."
                        style="padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; width: 100%; margin-bottom: 1rem;"></textarea>

                    <div class="modal-footer" style="padding: 1rem 0 0 0; margin-top: 1rem;">
                        <button type="submit" class="btn-primary">Record Activity</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- History Modal -->
    <div id="history-modal" class="profile-modal-overlay" style="display: none;">
        <div class="modal-content profile-modal">
            <div class="modal-header">
                <h3><i class="fa-solid fa-clock-rotate-left"></i> Farm History</h3>
                <button class="close-modal" onclick="closeHistoryModal()">&times;</button>
            </div>
            <div class="recent-activities-list" style="padding: 1rem; max-height: 480px; overflow-y: auto;">
                <p class="text-sm text-center" style="color: #666; margin-top: 2rem;">Loading...</p>
            </div>
        </div>
    </div>

    <script src="../assets/js/toast.js?v=<?php echo time(); ?>"></script>
    <script src="../assets/js/script.js?v=<?php echo time(); ?>"></script>
    <script src="../assets/js/dashboard.js?v=<?php echo time(); ?>"></script>
</body>

</html>