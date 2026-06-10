<?php
require '../includes/db.php';
require '../includes/auth.php';
require '../includes/lang.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    header('Location: ../auth.php');
    exit();
}

// Security: Check if user is still active
if (!isUserActive($conn, $_SESSION['user_id'])) {
    session_destroy();
    header('Location: ../auth.php?error=deactivated');
    exit();
}

// Get admin username
$username = getUsername($conn, $_SESSION['user_id']) ?? 'Admin';
$system_name = getSetting($conn, 'system_name', 'NAOS');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1e293b">
    <script>window.NAOS_LANG = '<?php echo htmlspecialchars($lang ?? "en", ENT_QUOTES); ?>';</script>
    <title><?php echo $system_name; ?> Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/vendor/css/inter.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../assets/css/toast.css">
    <link rel="stylesheet" href="../assets/vendor/css/all.min.css">
    <script src="../assets/vendor/js/chart.min.js"></script>
    <script src="../assets/vendor/js/jspdf.umd.min.js"></script>
    <script src="../assets/vendor/js/jspdf.plugin.autotable.min.js"></script>
</head>

<body>
    <div class="admin-dashboard-wrapper">
        <!-- Admin Sidebar Navigation -->
        <nav class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-header">
                <img src="../assets/images/NAOS LOGO.png" alt="NAOS Logo" class=" admin-sidebar-logo">
                <div>
                    <div class="admin-sidebar-title">
                        <?php echo ('NAOS'); ?>
                    </div>
                    <div class="admin-sidebar-subtitle">Admin Panel</div>
                </div>
            </div>

            <div class="admin-sidebar-menu">
                <a href="#" class="admin-sidebar-menu-item active" data-section="overview">
                    <span class="admin-sidebar-menu-item-icon"><i class="fa-solid fa-chart-pie"></i></span>
                    <span>Overview</span>
                </a>
                <a href="#" class="admin-sidebar-menu-item" data-section="users">
                    <span class="admin-sidebar-menu-item-icon"><i class="fa-solid fa-users"></i></span>
                    <span>User Management</span>
                </a>
                <a href="#" class="admin-sidebar-menu-item" data-section="user-verification">
                    <span class="admin-sidebar-menu-item-icon"><i class="fa-solid fa-user-check"></i></span>
                    <span>User Verification</span>
                    <span id="verificationBadge" class="admin-badge" style="background: #ef4444; color: white; font-size: 0.7rem; padding: 2px 8px; border-radius: 999px; margin-left: auto; display: none;">0</span>
                </a>
                <a href="#" class="admin-sidebar-menu-item" data-section="analytics">
                    <span class="admin-sidebar-menu-item-icon"><i class="fa-solid fa-arrow-trend-up"></i></span>
                    <span>System Analytics</span>
                </a>
                <a href="#" class="admin-sidebar-menu-item" data-section="market-prices">
                    <span class="admin-sidebar-menu-item-icon"><i class="fa-solid fa-money-bill-trend-up"></i></span>
                    <span>Market Prices</span>
                </a>
                <a href="#" class="admin-sidebar-menu-item" data-section="payments">
                    <span class="admin-sidebar-menu-item-icon"><i class="fa-solid fa-credit-card"></i></span>
                    <span>Payments</span>
                </a>
                <a href="#" class="admin-sidebar-menu-item" data-section="reports">
                    <span class="admin-sidebar-menu-item-icon"><i class="fa-solid fa-clipboard-list"></i></span>
                    <span>Reports & Logs</span>
                </a>
                <a href="#" class="admin-sidebar-menu-item" data-section="crop-configs">
                    <span class="admin-sidebar-menu-item-icon"><i class="fa-solid fa-seedling"></i></span>
                    <span>Crop Details</span>
                </a>
                <a href="#" class="admin-sidebar-menu-item" data-section="settings">
                    <span class="admin-sidebar-menu-item-icon"><i class="fa-solid fa-gear"></i></span>
                    <span>Settings</span>
                </a>
            </div>

            <div class="admin-sidebar-footer">
                <button onclick=" confirmLogout()" class="admin-btn admin-btn-primary" style="width: 100%;">
                    <?php echo t('logout'); ?></button>
            </div>
        </nav>

        <!-- Sidebar Overlay for Mobile -->
        <div class="admin-sidebar-overlay" id="adminSidebarOverlay"></div>

        <!-- Main Content -->
        <div class="admin-main-content">
            <div class="admin-top-bar">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="admin-hamburger" id="adminHamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <h1 class="admin-top-bar-title">Overview</h1>
                </div>
                <div class="admin-top-bar-actions" style="display: flex; align-items: center; gap: 10px;">
                    <div class="admin-profile-trigger" onclick="openProfileModal()" title="Edit Profile"
                        style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <div style="display: flex; flex-direction: column; align-items: flex-end; line-height: 1.2;">
                            <span class="admin-welcome" style="color: #64748b; font-size: 0.875rem;">Welcome,</span>
                            <strong style="white-space: nowrap; font-size: 0.95rem; color: #1e293b;"><?php echo htmlspecialchars($username); ?></strong>
                        </div>
                        <?php echo renderAvatar($conn, $_SESSION['user_id'], '../assets/images/profiles/', 'headerProfilePic', '40px'); ?>
                    </div>
                </div>
            </div>

            <div class="admin-content-wrapper">
                <!-- Overview Section -->
                <div class="admin-content-section active" id="overview">
                    <!-- <h2 class="admin-section-title">System Overview</h2> -->

                    <div class="admin-stats-grid">
                        <div class="admin-stat-card">
                            <div class="admin-stat-card-header">
                                <span class="admin-stat-card-icon"><i class="fa-solid fa-users"></i></span>
                            </div>
                            <div class="admin-stat-card-value" id="totalUsers">-</div>
                            <div class="admin-stat-card-label">Total Users</div>
                        </div>

                        <div class="admin-stat-card">
                            <div class="admin-stat-card-header">
                                <span class="admin-stat-card-icon"><i class="fa-solid fa-wheat-awn"></i></span>
                            </div>
                            <div class="admin-stat-card-value" id="totalFarmers">-</div>
                            <div class="admin-stat-card-label">Farmers</div>
                        </div>

                        <div class="admin-stat-card">
                            <div class="admin-stat-card-header">
                                <span class="admin-stat-card-icon"><i class="fa-solid fa-cart-shopping"></i></span>
                            </div>
                            <div class="admin-stat-card-value" id="totalBuyers">-</div>
                            <div class="admin-stat-card-label">Buyers</div>
                        </div>

                        <div class="admin-stat-card">
                            <div class="admin-stat-card-header">
                                <span class="admin-stat-card-icon"><i class="fa-solid fa-wallet"></i></span>
                            </div>
                            <div class="admin-stat-card-value" id="totalPayments">-</div>
                            <div class="admin-stat-card-label">Total Payments</div>
                        </div>
                    </div>

                    <div class="admin-dashboard-grid">
                        <div class="admin-panel">
                            <div class="admin-panel-header">Recent Activity</div>
                            <div id="recentActivity">
                                <p style="color: #64748b;">Loading recent activity...</p>
                            </div>
                        </div>

                        <div class="admin-panel">
                            <div class="admin-panel-header">Quick Actions</div>
                            <div class="quick-links-grid">
                                <div class="quick-link-card" onclick="openAddUserModal()">
                                    <i class="fa-solid fa-user-plus"></i>
                                    <div class="quick-link-text">Add User</div>
                                </div>
                                <div class="quick-link-card"
                                    onclick="document.querySelector('[data-section=\'analytics\']').click()">
                                    <i class="fa-solid fa-chart-line"></i>
                                    <div class="quick-link-text">Analytics</div>
                                </div>
                                <div class="quick-link-card"
                                    onclick="document.querySelector('[data-section=\'market-prices\']').click()">
                                    <i class="fa-solid fa-tag"></i>
                                    <div class="quick-link-text">Prices</div>
                                </div>
                                <div class="quick-link-card"
                                    onclick="document.querySelector('[data-section=\'reports\']').click()">
                                    <i class="fa-solid fa-file-invoice"></i>
                                    <div class="quick-link-text">Reports</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Management Section -->
                <div class="admin-content-section" id="users">
                    <!-- <h2 class="admin-section-title">User Management</h2> -->

                    <div class="admin-panel">
                        <div class="admin-panel-header">
                            <span>All Users</span>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div class="admin-search-box" style="position: relative;">
                                    <i class="fa-solid fa-search"
                                        style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 0.875rem;"></i>
                                    <input type="text" id="userSearch" placeholder="Search users or location..."
                                        style="padding: 0.4rem 0.5rem 0.4rem 2rem; border: 1px solid #cbd5e1; border-radius: 0.375rem; font-size: 0.875rem; width: 220px;">
                                </div>
                                <div class="admin-panel-actions">
                                    <button onclick="openAddUserModal()"
                                        class="admin-btn admin-btn-primary admin-btn-sm" style="margin-right: 10px;"
                                        title="Add New User"><i class="fa-solid fa-user-plus"></i></button>
                                    <button onclick="refreshUsers()" class="admin-btn admin-btn-secondary admin-btn-sm"
                                        title="Refresh Users"><i class="fa-solid fa-rotate-right"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="admin-table-container">
                            <table class="admin-table" id="usersTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Location</th>
                                        <th>Language</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <tr>
                                        <td colspan="7" style="text-align: center; color: #64748b;">Loading users...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="usersSummary"
                            style="padding: 1rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
                            <div id="usersPagination" style="display: flex; gap: 0.5rem; align-items: center;">
                                <!-- Pagination buttons populated by JS -->
                            </div>
                            <div style="font-weight: bold; color: #334155;">
                                Total Users: <span id="totalUsersCount">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Verification Section -->
                <div class="admin-content-section" id="user-verification">
                    <!-- <h2 class="admin-section-title">User Verification</h2> -->

                    <div class="admin-panel">
                        <div class="admin-panel-header">
                            <span>Approved Verifications</span>
                            <div class="admin-panel-actions" style="display: flex; align-items: center; gap: 0.75rem;">
                                <select id="verificationFilter" onchange="loadVerifications()" style="padding: 0.4rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.375rem; font-size: 0.875rem;">
                                    <option value="approved" selected>Approved</option>
                                    <option value="pending">Pending</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                                <button onclick="loadVerifications()" class="admin-btn admin-btn-secondary admin-btn-sm" title="Refresh">
                                    <i class="fa-solid fa-rotate-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="admin-table-container">
                            <table class="admin-table" id="verificationsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Phone</th>
                                        <th>Role</th>
                                        <th>Farmer ID</th>
                                        <th>National ID</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="verificationsTableBody">
                                    <tr>
                                        <td colspan="7" style="text-align: center; color: #64748b;">Select a filter and load data</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ID Preview Modal -->
                <div id="idPreviewModal" class="admin-modal" style="display:none;">
                    <div class="admin-modal-content" style="max-width: 600px;">
                        <div class="admin-modal-header">
                            <h3>National ID Preview</h3>
                            <span class="admin-modal-close" onclick="closeIdPreview()">&times;</span>
                        </div>
                        <div class="admin-modal-body" style="text-align: center; padding: 1rem;">
                            <img id="idPreviewImage" src="" alt="National ID" style="max-width: 100%; max-height: 70vh; border-radius: 8px; border: 1px solid #e2e8f0;">
                        </div>
                    </div>
                </div>

                <!-- User Details Preview Modal -->
                <div id="userDetailsModal" class="admin-modal" style="display:none;">
                    <div class="admin-modal-content" style="max-width: 500px;">
                        <div class="admin-modal-header">
                            <h3>User Details Preview</h3>
                            <span class="admin-modal-close" onclick="closeUserDetailsModal()">&times;</span>
                        </div>
                        <div class="admin-modal-body" style="padding: 1.5rem;" id="userDetailsContent">
                            <p>Loading...</p>
                        </div>
                        <div class="admin-modal-footer">
                            <button class="admin-btn admin-btn-secondary" onclick="closeUserDetailsModal()">Close</button>
                        </div>
                    </div>
                </div>

                <!-- Rejection Notes Modal -->
                <div id="rejectModal" class="admin-modal" style="display:none;">
                    <div class="admin-modal-content" style="max-width: 450px;">
                        <div class="admin-modal-header">
                            <h3>Reject User</h3>
                            <span class="admin-modal-close" onclick="closeRejectModal()">&times;</span>
                        </div>
                        <div class="admin-modal-body" style="padding: 1.5rem;">
                            <p style="margin-bottom: 1rem; color: #475569;">User: <strong id="rejectUserName"></strong></p>
                            <div class="admin-form-group">
                                <label for="rejectionNotes" style="font-weight: 600; margin-bottom: 0.5rem; display: block;">Reason for Rejection (optional)</label>
                                <textarea id="rejectionNotes" rows="3" placeholder="e.g. ID image was unclear, please re-upload a clearer photo..."
                                    style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; resize: vertical;"></textarea>
                            </div>
                            <div class="admin-modal-footer" style="margin-top: 1rem;">
                                <button class="admin-btn admin-btn-secondary" onclick="closeRejectModal()">Cancel</button>
                                <button class="admin-btn admin-btn-danger" id="confirmRejectBtn" onclick="confirmReject()">Reject User</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Analytics Section -->
                <div class="admin-content-section" id="analytics">
                    <!-- <h2 class="admin-section-title">System Analytics</h2> -->
                    <div id="analyticsContent">
                        <!-- Content will be loaded dynamically via JS -->
                        <p style="color: #64748b;">Loading analytics dashboard...</p>
                    </div>
                </div>

                <!-- Market Prices Section -->
                <div class="admin-content-section" id="market-prices">
                    <h2 class="admin-section-title">Market Price Management</h2>

                    <div class="admin-panel">
                        <div class="admin-panel-header">
                            <span>Current Crop Prices</span>
                            <div class="admin-panel-actions" style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                                <button id="syncWfpBtn" onclick="syncMarketPrices()" class="admin-btn admin-btn-success admin-btn-sm" title="Sync with World Food Programme (Malawi)">
                                    <i class="fa-solid fa-cloud-arrow-down"></i> Sync from WFP
                                </button>
                                <button onclick="document.getElementById('priceImportInput').click()" class="admin-btn admin-btn-secondary admin-btn-sm" title="Import from CSV (Crop, Price)">
                                    <i class="fa-solid fa-file-import"></i> Bulk Import
                                </button>
                                <input type="file" id="priceImportInput" style="display:none" accept=".csv" onchange="handlePriceImport(event)">
                                <button onclick="refreshMarketPrices()" class="admin-btn admin-btn-secondary admin-btn-sm">Refresh</button>
                                <button onclick="saveAllPrices()" class="admin-btn admin-btn-primary admin-btn-sm">Save All Changes</button>
                            </div>
                        </div>
                        <div class="admin-panel-body">
                            <p style="color: #64748b; margin-bottom: 0.5rem;">Update market prices based on current market
                                surveys. Changes will be reflected immediately across the platform.</p>
                            
                            <div id="wfpSyncStatus" style="display: none; font-size: 0.8rem; color: #10b981; margin-bottom: 1rem; padding: 0.5rem 0.75rem; background: #f0fdf4; border-radius: 6px; border: 1px solid #dcfce7; width: fit-content;">
                                <!-- Populated by JS -->
                            </div>

                            <!-- Search Bar -->
                            <div style="margin-bottom: 1rem;">
                                <input type="text" id="priceSearchInput" placeholder="Search crop name..."
                                    onkeyup="filterMarketPrices()"
                                    style="width: 100%; max-width: 300px; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 0.375rem;">
                            </div>

                            <div class="admin-table-container">
                                <table class="admin-table" id="marketPricesTable">
                                    <thead>
                                        <tr>
                                            <th>Crop Name</th>
                                            <th>Current Price (MWK/kg)</th>
                                            <th>Last Updated</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="marketPricesTableBody">
                                        <tr>
                                            <td colspan="4" style="text-align: center; color: #64748b;">Loading market
                                                prices...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination Controls -->
                            <div id="pricePaginationControls"
                                style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                                <div style="color: #64748b; font-size: 0.875rem;">
                                    <span id="pricePageInfo">Showing 0 of 0</span>
                                </div>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button onclick="changePricePage(-1)" id="prevPageBtn"
                                        class="admin-btn admin-btn-secondary admin-btn-sm" disabled>Previous</button>
                                    <span id="pageNumbers"
                                        style="display: flex; gap: 0.25rem; align-items: center;"></span>
                                    <button onclick="changePricePage(1)" id="nextPageBtn"
                                        class="admin-btn admin-btn-secondary admin-btn-sm" disabled>Next</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payments Section -->
                <div class="admin-content-section" id="payments">
                    <!-- <h2 class="admin-section-title">Payment Management</h2> -->

                    <div class="admin-panel">
                        <div class="admin-panel-header">
                            <span>All Payments</span>
                            <div class="admin-panel-actions">
                                <button onclick="refreshPayments()"
                                    class="admin-btn admin-btn-primary admin-btn-sm">Refresh</button>
                            </div>
                        </div>
                        <div class="admin-table-container">
                            <table class="admin-table" id="paymentsTable">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>User</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentsTableBody">
                                    <tr>
                                        <td colspan="5" style="text-align: center; color: #64748b;">Loading payments...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Reports & Logs Section -->
                <div class="admin-content-section" id="reports">
                    <!-- <h2 class="admin-section-title">Reports & Logs</h2> -->

                    <div class="admin-panel">
                        <div class="admin-panel-header"
                            style="flex-direction: column; align-items: flex-start; gap: 1rem;">
                            <div
                                style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                                <span>System Reports</span>
                                <div class="admin-panel-actions">
                                    <button onclick="exportReport('excel')"
                                        class="admin-btn admin-btn-success admin-btn-sm"><i
                                            class="fa-solid fa-file-excel"></i> Export Excel</button>
                                    <button onclick="exportReport('pdf')"
                                        class="admin-btn admin-btn-danger admin-btn-sm"><i
                                            class="fa-solid fa-file-pdf"></i> Export PDF</button>
                                </div>
                            </div>

                            <!-- Filter Bar -->
                            <div class="admin-reports-filters"
                                style="width: 100%; display: flex; gap: 1rem; flex-wrap: wrap; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                                <div class="admin-form-group" style="margin-bottom: 0; min-width: 200px;">
                                    <label for="reportType" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Report
                                        Type</label>
                                    <select id="reportType" onchange="loadReports()"
                                        style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 0.375rem;">
                                        <option value="logs">System Logs</option>
                                        <option value="users">User Registrations</option>
                                        <option value="payments">Payments</option>
                                    </select>
                                </div>

                                <div class="admin-form-group" style="margin-bottom: 0; min-width: 200px;">
                                    <label for="reportPeriod" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Time
                                        Period</label>
                                    <select id="reportPeriod" onchange="toggleCustomDate()"
                                        style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 0.375rem;">
                                        <option value="all">All Time</option>
                                        <option value="daily">Today (Daily)</option>
                                        <option value="weekly">This Week</option>
                                        <option value="monthly">This Month</option>
                                        <option value="custom">Custom Range</option>
                                    </select>
                                </div>

                                <div id="customDateRange" style="display: none; gap: 0.5rem; align-items: flex-end;">
                                    <div class="admin-form-group" style="margin-bottom: 0;">
                                        <label for="startDate" style="font-size: 0.8rem; margin-bottom: 0.25rem;">Start
                                            Date</label>
                                        <input type="date" id="startDate" class="admin-input-sm" max="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <div class="admin-form-group" style="margin-bottom: 0;">
                                        <label for="endDate" style="font-size: 0.8rem; margin-bottom: 0.25rem;">End
                                            Date</label>
                                        <input type="date" id="endDate" class="admin-input-sm" max="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <button onclick="loadReports()" class="admin-btn admin-btn-primary admin-btn-sm"
                                        style="height: 38px;">Apply</button>
                                </div>
                            </div>
                        </div>

                        <div class="admin-table-container">
                            <table class="admin-table" id="reportsTable">
                                <thead id="reportsTableHead">
                                    <!-- Dynamic Headers -->
                                </thead>
                                <tbody id="reportsTableBody">
                                    <tr>
                                        <td colspan="5" style="text-align: center; color: #64748b;">Loading reports...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="reportSummary"
                            style="padding: 1rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
                            <div id="reportsPagination" style="display: flex; gap: 0.5rem; align-items: center;">
                                <!-- Pagination buttons populated by JS -->
                            </div>
                            <div style="font-weight: bold; color: #334155;">
                                Total Records: <span id="totalRecordsCount">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Crop Configurations Section -->
                <div class="admin-content-section" id="crop-configs">
                    <div class="admin-panel">
                        <div class="admin-panel-header">
                            <!-- <span>Crop Configurations</span> -->
                            <button onclick="openCropConfigModal()" class="admin-btn admin-btn-primary admin-btn-sm"><i class="fa-solid fa-plus" style="margin-right: 5px;"></i> Add Crop</button>
                        </div>
                        <div class="admin-table-container">
                            <table class="admin-table" id="cropConfigTable">
                                <thead>
                                    <tr>
                                        <th>Crop Name</th>
                                        <th>Rainfall (mm)</th>
                                        <th>Temp (°C)</th>
                                        <th>Drought Resistant</th>
                                        <th>Current Price (MWK/kg)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="cropConfigTableBody">
                                    <tr><td colspan="6" style="text-align: center;">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Settings Section -->
                <div class="admin-content-section" id="settings">
                    <!-- <h2 class="admin-section-title">System Settings</h2> -->
                    <div class="admin-panel">
                        <div class="admin-panel-header">System Configuration</div>
                        <div id="settingsContent" class="admin-panel-body" style="padding: 1.5rem;">
                            <form id="systemSettingsForm" onsubmit="handleSettingsSubmit(event)">
                                <div
                                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">

                                    <div class="admin-form-group">
                                        <label for="system_name"
                                            style="font-weight: 600; display: block; margin-bottom: 0.5rem;">System
                                            Name</label>
                                        <input type="text" id="system_name" name="system_name" required
                                            style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 1rem;"
                                            readonly>
                                    </div>

                                    <div class="admin-form-group">
                                        <label for="subscription_amount"
                                            style="font-weight: 600; display: block; margin-bottom: 0.5rem;">Monthly
                                            Subscription Amount (MWK)</label>
                                        <input type="number" id="subscription_amount" name="subscription_amount"
                                            required min="50" step="100"
                                            style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 1rem;">
                                    </div>

                                    <div class="admin-form-group">
                                        <label for="default_lang"
                                            style="font-weight: 600; display: block; margin-bottom: 0.5rem;">Default
                                            System Language</label>
                                        <select id="default_lang" name="default_lang"
                                            style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 1rem;">
                                            <option value="en">English</option>
                                            <option value="ny">Chichewa</option>
                                        </select>
                                    </div>

                                    <div class="admin-form-group">
                                        <label for="allow_registration"
                                            style="font-weight: 600; display: block; margin-bottom: 0.5rem;">Allow New
                                            Registrations</label>
                                        <select id="allow_registration" name="allow_registration"
                                            style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 1rem;">
                                            <option value="1">Enabled</option>
                                            <option value="0">Disabled</option>
                                        </select>
                                    </div>

                                    <div class="admin-form-group">
                                        <label for="maintenance_mode"
                                            style="font-weight: 600; display: block; margin-bottom: 0.5rem;">Maintenance
                                            Mode</label>
                                        <select id="maintenance_mode" name="maintenance_mode"
                                            style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-size: 1rem;">
                                            <option value="0">Off (Live)</option>
                                            <option value="1">On (Maintenance)</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="admin-btn admin-btn-primary">
                                    <i class="fa-solid fa-save" style="margin-right: 8px;"></i> Save All Settings
                                </button>
                            </form>
                        </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Crop Config Modal -->
    <div id="cropConfigModal" class="admin-modal" style="display:none;">
        <div class="admin-modal-content" style="max-width: 500px;">
            <div class="admin-modal-header">
                <h3 id="cropModalTitle">Add Crop Configuration</h3>
                <span class="admin-modal-close" onclick="closeCropConfigModal()">&times;</span>
            </div>
            <div class="admin-modal-body" style="padding: 1.5rem;">
                <form id="cropConfigForm" onsubmit="handleCropConfigSubmit(event)">
                    <input type="hidden" id="cropConfigId" name="id">
                    <input type="hidden" id="cropAction" name="action" value="add">

                    <div class="admin-form-group">
                        <label for="crop_name" style="font-weight: 600;">Crop Name</label>
                        <input type="text" id="crop_name" name="crop_name" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="admin-form-group">
                            <label for="min_rainfall" style="font-weight: 600;">Min Rainfall (mm)</label>
                            <input type="number" id="min_rainfall" name="min_rainfall" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem;" value="0">
                        </div>
                        <div class="admin-form-group">
                            <label for="max_rainfall" style="font-weight: 600;">Max Rainfall (mm)</label>
                            <input type="number" id="max_rainfall" name="max_rainfall" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem;" value="9999">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="admin-form-group">
                            <label for="min_temp" style="font-weight: 600;">Min Temp (°C)</label>
                            <input type="number" step="0.1" id="min_temp" name="min_temp" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem;" value="0">
                        </div>
                        <div class="admin-form-group">
                            <label for="max_temp" style="font-weight: 600;">Max Temp (°C)</label>
                            <input type="number" step="0.1" id="max_temp" name="max_temp" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem;" value="99">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="admin-form-group">
                            <label for="current_price" style="font-weight: 600;">Initial Price (MWK/kg)</label>
                            <input type="number" step="0.01" id="current_price" name="current_price" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem;">
                        </div>
                        <div class="admin-form-group">
                            <label for="drought_resistant" style="font-weight: 600;">Drought Resistant?</label>
                            <select id="drought_resistant" name="drought_resistant" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem;">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>

                    <div class="admin-modal-footer" style="margin-top: 1rem; display: flex; justify-content: flex-end; gap: 1rem;">
                        <button type="button" class="admin-btn admin-btn-secondary" onclick="closeCropConfigModal()">Cancel</button>
                        <button type="submit" class="admin-btn admin-btn-primary">Save Config</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User Modal (Add/Edit) -->
    <div id="userModal" class="admin-modal">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3 id="modalTitle">Add New User</h3>
                <span class="admin-modal-close" onclick="closeUserModal()">&times;</span>
            </div>
            <div class="admin-modal-body" style="max-height: 65vh; overflow-y: auto;">
                <form id="userForm" onsubmit="handleUserSubmit(event)">
                    <input type="hidden" id="userId" name="user_id">
                    <input type="hidden" id="formAction" name="action" value="add">

                    <div class="admin-form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required placeholder="Enter username">
                    </div>

                    <div class="admin-form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="tel" id="phone_number" name="phone_number" required placeholder="Enter 10-digit phone number (e.g., 09...)">
                    </div>

                    <div class="admin-form-group">
                        <label for="password">Password <span id="passwordHint"
                                style="font-size: 0.8rem; font-weight: normal; color: #64748b;">(Leave blank to keep
                                current)</span></label>
                        <input type="password" id="password" name="password" placeholder="Enter password">
                    </div>

                    <div class="admin-form-group">
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            <option value="farmer">Farmer</option>
                            <option value="buyer">Buyer</option>
                        </select>
                    </div>

                    <div class="admin-form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" placeholder="Enter location (optional)">
                    </div>

                    <div class="admin-form-group">
                        <label for="lang">Language</label>
                        <select id="lang" name="lang" required>
                            <option value="en" selected>English</option>
                            <option value="ny">Chichewa</option>
                        </select>
                    </div>

                    <div class="admin-modal-footer">
                        <button type="button" class="admin-btn admin-btn-secondary"
                            onclick="closeUserModal()">Cancel</button>
                        <button type="submit" class="admin-btn admin-btn-primary" id="saveButton">Save User</button>
                    </div>
                </form>
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
                    <button type="button" class="admin-btn admin-btn-secondary"
                        onclick="closeProfileModal()">Maybe Later</button>
                    <button type="submit" class="admin-btn admin-btn-primary">Save Profile</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/toast.js?v=<?php echo time(); ?>"></script>
    <script src="../assets/js/script.js?v=<?php echo time(); ?>"></script>
    <script src="../assets/js/admin_dashboard.js?v=<?php echo time(); ?>"></script>
</body>

</html>