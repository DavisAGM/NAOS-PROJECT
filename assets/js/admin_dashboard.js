// Admin Dashboard JavaScript

/**
 * Updates an avatar element (div or img) with a photo URL.
 */
function setAvatarPhoto(el, url) {
    if (!el) return;
    if (el.tagName === 'IMG') {
        el.src = url;
    } else {
        el.style.color = 'transparent';
        el.style.background = 'url(' + url + ') center/cover no-repeat';
    }
}

// Mobile Menu Toggle
const adminHamburger = document.getElementById('adminHamburger');
const adminSidebar = document.getElementById('adminSidebar');
const adminSidebarOverlay = document.getElementById('adminSidebarOverlay');

if (adminHamburger) {
    adminHamburger.addEventListener('click', () => {
        adminSidebar.classList.toggle('active');
        adminSidebarOverlay.classList.toggle('active');
    });
}

if (adminSidebarOverlay) {
    adminSidebarOverlay.addEventListener('click', () => {
        adminSidebar.classList.remove('active');
        adminSidebarOverlay.classList.remove('active');
    });
}

// Section Switching
const adminMenuItems = document.querySelectorAll('.admin-sidebar-menu-item');
const adminSections = document.querySelectorAll('.admin-content-section');

adminMenuItems.forEach(item => {
    item.addEventListener('click', (e) => {
        e.preventDefault();
        const targetSection = item.getAttribute('data-section');

        // Update active menu item
        adminMenuItems.forEach(mi => mi.classList.remove('active'));
        item.classList.add('active');

        // Update active section
        adminSections.forEach(section => section.classList.remove('active'));
        document.getElementById(targetSection)?.classList.add('active');

        // Close mobile menu
        adminSidebar.classList.remove('active');
        adminSidebarOverlay.classList.remove('active');

        // Update top bar title
        const adminTitleEl = document.querySelector('.admin-top-bar-title');
        const menuTextSpan = item.querySelector('span:not(.admin-sidebar-menu-item-icon)');
        if (adminTitleEl && menuTextSpan) {
            adminTitleEl.textContent = menuTextSpan.textContent;
        }

        // Load section data
        loadSectionData(targetSection);
    });
});

// Load section data when switched
function loadSectionData(section) {
    switch (section) {
        case 'overview':
            loadOverview();
            break;
        case 'users':
            refreshUsers();
            break;
        case 'analytics':
            loadAnalytics();
            break;
        case 'market-prices':
            refreshMarketPrices();
            break;
        case 'user-verification':
            loadVerifications();
            break;
        case 'payments':
            refreshPayments();
            break;
        case 'reports':
            refreshReports();
            break;
        case 'crop-configs':
            if (typeof loadCropConfigs === 'function') loadCropConfigs();
            break;
        case 'settings':
            loadSettings();
            break;
    }
}

// Load Overview Statistics
async function loadOverview() {
    try {
        const res = await fetch('/naos/admin/api.php?action=stats');
        const data = await res.json();

        if (data.error) {
            console.error('Error loading stats:', data.error);
            return;
        }

        // Update stat cards
        document.getElementById('totalUsers').textContent = data.total_users || '0';
        document.getElementById('totalFarmers').textContent = data.total_farmers || '0';
        document.getElementById('totalBuyers').textContent = data.total_buyers || '0';
        document.getElementById('totalPayments').textContent = data.total_payments || '0';

        // Recent activity (placeholder)
        const activityDiv = document.getElementById('recentActivity');
        if (data.recent_activity && data.recent_activity.length > 0) {
            let html = '<ul style="list-style: none; padding: 0;">';
            data.recent_activity.forEach(activity => {
                html += `<li style="padding: 0.5rem 0; border-bottom: 1px solid #e2e8f0;">
                    ${activity.message} - <span style="color: #64748b; font-size: 0.875rem;">${activity.date}</span>
                </li>`;
            });
            html += '</ul>';
            activityDiv.innerHTML = html;
        } else {
            activityDiv.innerHTML = '<p style="color: #64748b;">No recent activity</p>';
        }
    } catch (error) {
        console.error('Error loading overview:', error);
    }
}

// Refresh Users Table
let currentUserPage = 1;
const usersPerPage = 8;
let userSearchQuery = '';

async function refreshUsers() {
    const tbody = document.getElementById('usersTableBody');
    const countSpan = document.getElementById('totalUsersCount');
    tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; color: #64748b;">Loading users...</td></tr>';

    try {
        const res = await fetch(`/naos/admin/users.php?action=list&page=${currentUserPage}&limit=${usersPerPage}&search=${encodeURIComponent(userSearchQuery)}`);
        const data = await res.json();

        if (data.error) {
            tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; color: #ef4444;">Error: ${data.error}</td></tr>`;
            return;
        }

        const totalCount = data.total_count || 0;
        if (countSpan) countSpan.textContent = totalCount;

        if (!data.users || data.users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; color: #64748b;">No users found</td></tr>';
            renderUsersPagination(0);
            return;
        }

        let html = '';
        data.users.forEach(user => {
            const roleClass = getRoleBadgeClass(user.role);
            const statusClass = user.is_active ? 'admin-badge-active' : 'admin-badge-inactive';
            const statusLabel = user.is_active ? 'Active' : 'Inactive';
            const rowClass = user.is_active ? '' : 'user-row-inactive';

            html += `
                <tr class="${rowClass}">
                    <td>${user.id}</td>
                    <td><strong>${user.username}</strong></td>
                    <td><span class="admin-badge ${roleClass}">${user.role}</span></td>
                    <td>${user.location || '-'}</td>
                    <td>${user.lang || 'en'}</td>
                    <td><span class="admin-badge ${statusClass}">${statusLabel}</span></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <button onclick="openEditUserModal(${user.id})" class="admin-btn admin-btn-warning admin-btn-sm" title="Edit User"><i class="fa-solid fa-pen-to-square"></i></button>
                            ${user.is_active
                    ? `<button onclick="toggleUserStatus(${user.id}, 0)" class="admin-btn admin-btn-danger admin-btn-sm" title="Deactivate User"><i class="fa-solid fa-ban"></i></button>`
                    : `<button onclick="toggleUserStatus(${user.id}, 1)" class="admin-btn admin-btn-primary admin-btn-sm" title="Activate User"><i class="fa-solid fa-check"></i></button>`
                }
                        </div>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
        renderUsersPagination(totalCount);
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; color: #ef4444;">Error: ${error.message}</td></tr>`;
    }
}

function renderUsersPagination(totalCount) {
    const container = document.getElementById('usersPagination');
    if (!container) return;

    const totalPages = Math.ceil(totalCount / usersPerPage);
    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    container.innerHTML = `
        <button onclick="changeUsersPage(${currentUserPage - 1})" ${currentUserPage === 1 ? 'disabled' : ''} class="admin-btn admin-btn-sm" style="padding: 0.25rem 0.75rem;">
            <i class="fa-solid fa-chevron-left"></i> Previous
        </button>
        <span style="font-size: 0.875rem; color: #64748b;">Page ${currentUserPage} of ${totalPages}</span>
        <button onclick="changeUsersPage(${currentUserPage + 1})" ${currentUserPage === totalPages ? 'disabled' : ''} class="admin-btn admin-btn-sm" style="padding: 0.25rem 0.75rem;">
            Next <i class="fa-solid fa-chevron-right"></i>
        </button>
    `;
}

function changeUsersPage(page) {
    currentUserPage = page;
    refreshUsers();
}

window.changeUsersPage = changeUsersPage;

// Search listener
let userSearchTimeout;
document.getElementById('userSearch')?.addEventListener('input', (e) => {
    clearTimeout(userSearchTimeout);
    userSearchTimeout = setTimeout(() => {
        userSearchQuery = e.target.value;
        currentUserPage = 1; // Reset to first page on search
        refreshUsers();
    }, 500);
});

// Get role badge class
function getRoleBadgeClass(role) {
    switch (role?.toLowerCase()) {
        case 'admin': return 'admin-badge-admin';
        case 'farmer': return 'admin-badge-farmer';
        case 'buyer': return 'admin-badge-buyer';
        default: return 'admin-badge-farmer';
    }
}

// User Modal Logic
const userModal = document.getElementById('userModal');
const userForm = document.getElementById('userForm');

function openAddUserModal() {
    document.getElementById('modalTitle').textContent = 'Add New User';
    document.getElementById('formAction').value = 'add';
    document.getElementById('userId').value = '';
    document.getElementById('passwordHint').style.display = 'none';
    document.getElementById('password').required = true;
    
    const roleOpt = document.querySelector('#role option[value="admin"]');
    if (roleOpt) {
        roleOpt.remove();
    }
    userForm.reset();
    userModal.style.display = 'block';
}

function openEditUserModal(userId) {
    document.getElementById('modalTitle').textContent = 'Edit User Details';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('userId').value = userId;
    document.getElementById('passwordHint').style.display = 'inline';
    document.getElementById('password').required = false;

    // Fetch user details
    fetch(`/naos/admin/users.php?action=get&user_id=${userId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const user = data.user;
                
                const roleSelect = document.getElementById('role');
                const adminOpt = document.querySelector('#role option[value="admin"]');
                if (user.role === 'admin' && !adminOpt) {
                    const option = document.createElement('option');
                    option.value = 'admin';
                    option.textContent = 'Administrator';
                    roleSelect.appendChild(option);
                } else if (user.role !== 'admin' && adminOpt) {
                    adminOpt.remove();
                }

                document.getElementById('username').value = user.username;
                if (document.getElementById('phone_number')) document.getElementById('phone_number').value = user.phone_number || '';
                document.getElementById('role').value = user.role;
                document.getElementById('location').value = user.location || '';
                document.getElementById('lang').value = user.lang || 'en';
                userModal.style.display = 'block';
            } else {
                showToast('Error loading user details: ' + data.error, 'error');
            }
        })
        .catch(error => showToast('Error: ' + error.message, 'error'));
}

function closeUserModal() {
    userModal.style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function (event) {
    if (event.target == userModal) {
        closeUserModal();
    }
}

async function handleUserSubmit(event) {
    event.preventDefault();
    const formData = new FormData(userForm);
    const saveButton = document.getElementById('saveButton');
    const originalText = saveButton.textContent;

    saveButton.disabled = true;
    saveButton.textContent = 'Saving...';

    try {
        const res = await fetch('/naos/admin/users.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            showToast(data.message || 'Operation successful', 'success');
            closeUserModal();
            refreshUsers();
        } else {
            showToast('Error: ' + data.error, 'error');
        }
    } catch (error) {
        showToast('Error: ' + error.message, 'error');
    } finally {
        saveButton.disabled = false;
        saveButton.textContent = originalText;
    }
}

async function toggleUserStatus(userId, activate) {
    const action = activate ? 'activate' : 'deactivate';
    const confirmMsg = activate
        ? 'Are you sure you want to activate this user?'
        : 'Are you sure you want to deactivate this user? They will no longer be able to log in.';

    showConfirm(confirmMsg, async () => {
        try {
            const formData = new FormData();
            formData.append('action', action);
            formData.append('user_id', userId);

            const res = await fetch('/naos/admin/users.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                showToast(`User ${activate ? 'activated' : 'deactivated'}`, 'success');
                refreshUsers();
            } else {
                showToast('Error: ' + data.error, 'error');
            }
        } catch (error) {
            showToast('Error: ' + error.message, 'error');
        }
    });
}

// Refresh Payments Table
async function refreshPayments() {
    const tbody = document.getElementById('paymentsTableBody');
    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #64748b;">Loading payments...</td></tr>';

    try {
        const res = await fetch('/naos/admin/api.php?action=payments');
        const data = await res.json();

        if (data.error) {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; color: #ef4444;">Error: ${data.error}</td></tr>`;
            return;
        }

        if (!data.payments || data.payments.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #64748b;">No payments found</td></tr>';
            return;
        }

        let html = '';
        data.payments.forEach(payment => {
            const statusClass = payment.status === 'paid' ? 'admin-badge-paid' :
                payment.status === 'completed' ? 'admin-badge-paid' :
                    payment.status === 'pending' ? 'admin-badge-pending' : 'admin-badge-failed';
            html += `
                <tr>
                    <td><code>${payment.transaction_id || '-'}</code></td>
                    <td>${payment.user_id || '-'}</td>
                    <td>${payment.amount || '0.00'} ${payment.currency || 'MWK'}</td>
                    <td><span class="admin-badge ${statusClass}">${payment.status}</span></td>
                    <td>${payment.date || '-'}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; color: #ef4444;">Error: ${error.message}</td></tr>`;
    }
}

// Reports Logic (Enhanced with Filtering, Export & Pagination)
let currentReportData = [];
let currentReportsPage = 1;
const reportsPerPage = 10;

function toggleCustomDate() {
    const period = document.getElementById('reportPeriod').value;
    const customRange = document.getElementById('customDateRange');
    customRange.style.display = period === 'custom' ? 'flex' : 'none';
    currentReportsPage = 1; // Reset to first page
    if (period !== 'custom') loadReports();
}

async function loadReports() {
    const tbody = document.getElementById('reportsTableBody');
    const thead = document.getElementById('reportsTableHead');
    const type = document.getElementById('reportType').value;
    const period = document.getElementById('reportPeriod').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const countSpan = document.getElementById('totalRecordsCount');

    // Update Headers based on Type
    let headers = '';
    if (type === 'users') {
        headers = '<tr><th>ID</th><th>Username</th><th>Role</th><th>Location</th><th>Joined Date</th></tr>';
    } else if (type === 'payments') {
        headers = '<tr><th>Transaction ID</th><th>User ID</th><th>Amount</th><th>Status</th><th>Date</th></tr>';
    } else {
        headers = '<tr><th>Log ID</th><th>Activity Message</th><th>Timestamp</th></tr>';
    }
    thead.innerHTML = headers;
    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #64748b;">Loading reports...</td></tr>';
    countSpan.textContent = '0';

    try {
        let url = `/naos/admin/api.php?action=reports&type=${type}&period=${period}&page=${currentReportsPage}&limit=${reportsPerPage}`;
        if (period === 'custom') {
            if (!startDate || !endDate) return; // Don't load if dates missing
            url += `&start_date=${startDate}&end_date=${endDate}`;
        }

        const res = await fetch(url);
        const data = await res.json();

        if (data.error) {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; color: #ef4444;">Error: ${data.error}</td></tr>`;
            return;
        }

        currentReportData = data.reports || [];
        const totalCount = data.total_count || 0;
        countSpan.textContent = totalCount;

        if (currentReportData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #64748b;">No records found for this period</td></tr>';
            renderReportsPagination(0);
            return;
        }

        let html = '';
        currentReportData.forEach(row => {
            if (type === 'users') {
                html += `<tr><td>${row.id}</td><td>${row.username}</td><td>${row.role}</td><td>${row.location || '-'}</td><td>${row.date}</td></tr>`;
            } else if (type === 'payments') {
                const statusClass = row.status === 'paid' || row.status === 'completed' ? 'admin-badge-paid' :
                    row.status === 'pending' ? 'admin-badge-pending' : 'admin-badge-failed';
                html += `<tr>
                    <td><code>${row.transaction_id || '-'}</code></td>
                    <td>${row.user_id || '-'}</td>
                    <td>${row.amount} ${row.currency || 'MWK'}</td>
                    <td><span class="admin-badge ${statusClass}">${row.status}</span></td>
                    <td>${row.date}</td></tr>`;
            } else {
                html += `<tr><td>${row.id}</td><td>${row.message}</td><td>${row.date}</td></tr>`;
            }
        });
        tbody.innerHTML = html;

        renderReportsPagination(totalCount);

    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; color: #ef4444;">Error: ${error.message}</td></tr>`;
    }
}

function renderReportsPagination(totalCount) {
    const container = document.getElementById('reportsPagination');
    if (!container) return;

    const totalPages = Math.ceil(totalCount / reportsPerPage);
    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    container.innerHTML = `
        <button onclick="changeReportsPage(${currentReportsPage - 1})" ${currentReportsPage === 1 ? 'disabled' : ''} class="admin-btn admin-btn-sm" style="padding: 0.25rem 0.75rem;">
            <i class="fa-solid fa-chevron-left"></i> Previous
        </button>
        <span style="font-size: 0.875rem; color: #64748b;">Page ${currentReportsPage} of ${totalPages}</span>
        <button onclick="changeReportsPage(${currentReportsPage + 1})" ${currentReportsPage === totalPages ? 'disabled' : ''} class="admin-btn admin-btn-sm" style="padding: 0.25rem 0.75rem;">
            Next <i class="fa-solid fa-chevron-right"></i>
        </button>
    `;
}

function changeReportsPage(page) {
    currentReportsPage = page;
    loadReports();
}

window.changeReportsPage = changeReportsPage;

// Reset page on filter changes
document.getElementById('reportType')?.addEventListener('change', () => {
    currentReportsPage = 1;
    loadReports();
});

// Export Functionality
async function exportReport(format) {
    showToast('Preparing export data...', 'info');
    const type = document.getElementById('reportType').value;
    const period = document.getElementById('reportPeriod').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    let url = `/naos/admin/api.php?action=reports&type=${type}&period=${period}&page=1&limit=999999`;
    if (period === 'custom') {
        if (!startDate || !endDate) {
            showToast('Please select start and end dates', 'warning');
            return;
        }
        url += `&start_date=${startDate}&end_date=${endDate}`;
    }

    try {
        const res = await fetch(url);
        const data = await res.json();

        if (data.error || !data.reports || data.reports.length === 0) {
            showToast('No data to export.', 'warning');
            return;
        }

        const filename = `NAOS_${type}_report_${period}_${new Date().toISOString().slice(0, 10)}`;

        if (format === 'excel') {
            exportToCSV(data.reports, filename);
        } else if (format === 'pdf') {
            exportToPDF(data.reports, type, filename);
        }
    } catch (error) {
        showToast('Export failed: ' + error.message, 'error');
    }
}

function exportToCSV(data, filename) {
    if (!data.length) return;
    const headers = Object.keys(data[0]);
    const csvRows = [];
    csvRows.push(headers.join(','));

    for (const row of data) {
        const values = headers.map(header => {
            const escaped = ('' + row[header]).replace(/"/g, '\\"');
            return `"${escaped}"`;
        });
        csvRows.push(values.join(','));
    }

    const blob = new Blob([csvRows.join('\n')], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('hidden', '');
    a.setAttribute('href', url);
    a.setAttribute('download', `${filename}.csv`);
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function exportToPDF(data, type, filename) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.setFontSize(18);
    doc.text(`NAOS System Report: ${type.toUpperCase()}`, 14, 22);
    doc.setFontSize(11);
    doc.text(`Generated on: ${new Date().toLocaleString()}`, 14, 30);

    const headers = Object.keys(data[0]).map(h => h.toUpperCase());
    const body = data.map(row => Object.values(row));

    doc.autoTable({
        head: [headers],
        body: body,
        startY: 40,
        theme: 'grid',
        styles: { fontSize: 8 },
        headStyles: { fillColor: [30, 41, 59] } // Dark blue header
    });

    doc.save(`${filename}.pdf`);
}

// Helper to refresh acts as load initial
function refreshReports() {
    loadReports();
}

// Analytics Logic
async function loadAnalytics() {
    const container = document.getElementById('analyticsContent');
    container.innerHTML = '<p style="color: #64748b; text-align: center; padding: 2rem;">Loading analytics...</p>';

    try {
        const res = await fetch('/naos/admin/api.php?action=analytics');
        const data = await res.json();

        if (data.error) throw new Error(data.error);

        // Create layout
        container.innerHTML = `
            <div class="admin-analytics-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
                <div class="admin-panel" style="grid-column: 1 / -1;">
                    <div class="admin-panel-header"><i class="fa-solid fa-chart-line"></i> Revenue Overview (Last 30 Days)</div>
                    <div style="height: 300px; padding: 1rem;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
                
                <div class="admin-panel">
                    <div class="admin-panel-header"><i class="fa-solid fa-seedling"></i> Most Listed Crops</div>
                    <div style="height: 300px; padding: 1rem;">
                        <canvas id="cropChart"></canvas>
                    </div>
                </div>

                <div class="admin-panel">
                    <div class="admin-panel-header"><i class="fa-solid fa-map-location-dot"></i> Regional User Distribution</div>
                    <div style="height: 300px; padding: 1rem;">
                        <canvas id="regionalChart"></canvas>
                    </div>
                </div>

                <div class="admin-panel">
                    <div class="admin-panel-header"><i class="fa-solid fa-users"></i> User Roles</div>
                    <div style="height: 300px; padding: 1rem;">
                        <canvas id="roleChart"></canvas>
                    </div>
                </div>

                <div class="admin-panel">
                    <div class="admin-panel-header"><i class="fa-solid fa-wallet"></i> Payment Status</div>
                    <div style="height: 300px; padding: 1rem;">
                        <canvas id="paymentChart"></canvas>
                    </div>
                </div>
            </div>
        `;

        // Render Charts
        renderRevenueChart(data.revenue_trends);
        renderCropChart(data.top_crops);
        renderRegionalChart(data.regional_data);
        renderRoleChart(data.role_distribution);
        renderPaymentChart(data.payment_stats);

    } catch (error) {
        container.innerHTML = `<div class="error-state">${error.message}</div>`;
    }
}

function renderRevenueChart(data) {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;
    
    const canvasCtx = ctx.getContext('2d');
    if (!data || data.length === 0) {
        canvasCtx.font = '14px Inter';
        canvasCtx.fillStyle = '#64748b';
        canvasCtx.textAlign = 'center';
        canvasCtx.fillText('No revenue data available', ctx.width / 2, ctx.height / 2);
        return;
    }

    const labels = data.map(d => {
        try {
            return new Date(d.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        } catch (e) {
            return d.date;
        }
    });
    
    // Ensure totals are numeric values
    const totals = data.map(d => {
        const value = parseFloat(d.total);
        return isNaN(value) ? 0 : value;
    });

    new Chart(canvasCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue (MWK)',
                data: totals,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#10b981'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => {
                            if (value >= 1000000) {
                                return (value / 1000000).toFixed(1) + 'M MWK';
                            } else if (value >= 1000) {
                                return (value / 1000).toFixed(1) + 'K MWK';
                            }
                            return value.toLocaleString() + ' MWK';
                        }
                    }
                }
            }
        }
    });
}

function renderCropChart(data) {
    const ctx = document.getElementById('cropChart').getContext('2d');
    if (!data || data.length === 0) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.name),
            datasets: [{
                label: 'Listings',
                data: data.map(d => d.count),
                backgroundColor: '#3b82f6',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false }
            }
        }
    });
}

function renderRegionalChart(data) {
    const ctx = document.getElementById('regionalChart').getContext('2d');
    if (!data || data.length === 0) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(d => d.district),
            datasets: [{
                data: data.map(d => d.count),
                backgroundColor: ['#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f97316', '#f59e0b', '#eab308', '#84cc16', '#22c55e', '#10b981'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } }
            }
        }
    });
}

function renderRoleChart(data) {
    const ctx = document.getElementById('roleChart').getContext('2d');

    // Process data (handle empty data safely)
    if (!data || data.length === 0) return;

    const labels = data.map(d => d.role.charAt(0).toUpperCase() + d.role.slice(1));
    const counts = data.map(d => d.count);
    const colors = ['#10b981', '#f59e0b', '#6366f1']; // Green, Amber, Indigo

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: counts,
                backgroundColor: colors,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

function renderPaymentChart(data) {
    const ctx = document.getElementById('paymentChart').getContext('2d');

    if (!data || data.length === 0) return;

    const labels = data.map(d => d.payment_status.charAt(0).toUpperCase() + d.payment_status.slice(1));
    const counts = data.map(d => d.count);
    const colors = {
        'paid': '#22c55e',
        'pending': '#f97316',
        'failed': '#ef4444'
    };

    const bgColors = data.map(d => colors[d.payment_status] || '#cbd5e1');

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: counts,
                backgroundColor: bgColors,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}


// Settings Logic
async function loadSettings() {
    const container = document.getElementById('settingsContent');
    container.innerHTML = '<p style="color: #64748b; text-align: center; padding: 2rem;">Loading configuration...</p>';

    try {
        const res = await fetch('/naos/admin/api.php?action=get_settings');
        const data = await res.json();
        if (!data.success) throw new Error(data.error);

        const s = data.settings;
        container.innerHTML = `
            <form id="settingsForm" onsubmit="handleSettingsSubmit(event)" class="admin-settings-form">
                <div class="admin-form-group">
                    <label>System Name <span style="font-size: 0.8rem; font-weight: normal; color: #64748b;">(Read-only)</span></label>
                    <input type="text" name="system_name" value="${s.system_name || ''}" readonly style="background: #f1f5f9; cursor: not-allowed;">
                </div>
                
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label>Maintenance Mode</label>
                        <div class="admin-toggle-wrapper">
                            <input type="checkbox" id="maintenance_mode" name="maintenance_mode" ${s.maintenance_mode == '1' ? 'checked' : ''} value="1">
                            <label for="maintenance_mode" class="admin-toggle-label">
                                <span class="toggle-text">Blocked all non-admin access</span>
                            </label>
                        </div>
                    </div>

                    <div class="admin-form-group">
                        <label>Allow Registration</label>
                        <div class="admin-toggle-wrapper">
                            <input type="checkbox" id="allow_registration" name="allow_registration" ${s.allow_registration == '1' ? 'checked' : ''} value="1">
                            <label for="allow_registration" class="admin-toggle-label">
                                <span class="toggle-text">Allow new users to sign up</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="admin-form-group">
                    <label>Default Language</label>
                    <select name="default_lang">
                        <option value="en" ${s.default_lang == 'en' ? 'selected' : ''}>English</option>
                        <option value="ny" ${s.default_lang == 'ny' ? 'selected' : ''}>Chichewa</option>
                    </select>
                </div>

                <div style="margin-top: 1.5rem; text-align: right;">
                    <button type="submit" class="admin-btn admin-btn-primary" id="saveSettingsBtn">Save Configuration</button>
                </div>
            </form>
        `;
    } catch (error) {
        container.innerHTML = `<div class="error-state">${error.message}</div>`;
    }
}

async function handleSettingsSubmit(event) {
    event.preventDefault();
    const btn = document.getElementById('saveSettingsBtn');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Saving...';

    const formData = new FormData(event.target);
    const settings = {};

    // Handle checkboxes correctly (default to 0 if not checked)
    settings['maintenance_mode'] = '0';
    settings['allow_registration'] = '0';

    formData.forEach((value, key) => {
        settings[key] = value;
    });

    try {
        const bodyArr = [];
        for (const [key, value] of Object.entries(settings)) {
            bodyArr.push(`settings[${key}]=${encodeURIComponent(value)}`);
        }
        bodyArr.push('action=update_settings');

        const res = await fetch('/naos/admin/api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: bodyArr.join('&')
        });
        const data = await res.json();

        if (data.success) {
            showToast('Settings updated successfully!', 'success');
            loadSettings();
        } else {
            showToast('Error: ' + data.error, 'error');
        }
    } catch (error) {
        showToast('Error saving settings: ' + error.message, 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = originalText;
    }
}

// Market Prices Management
let priceChanges = {}; // Track changes for bulk update
let allPrices = []; // Store all prices for filtering/pagination
let filteredPrices = []; // Store filtered results
let currentPage = 1;
const itemsPerPage = 7;

async function refreshMarketPrices() {
    const tbody = document.getElementById('marketPricesTableBody');
    tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #64748b;">Loading market prices...</td></tr>';
    priceChanges = {}; // Reset changes

    try {
        // Fetch prices AND sync metadata
        const res = await fetch('/naos/api/market.php');
        const data = await res.json();

        // Update Sync Status Badge if exists
        const syncBadge = document.getElementById('wfpSyncStatus');
        if (syncBadge && data.sync_meta) {
            const time = data.sync_meta.synced_at ? new Date(data.sync_meta.synced_at).toLocaleString() : 'Never';
            syncBadge.innerHTML = `<i class="fa-solid fa-clock-rotate-left"></i> Last WFP Sync: <strong>${time}</strong> (Source: ${data.sync_meta.source})`;
            syncBadge.style.display = 'block';
        }

        // Map data.prices (which is an object) to the expected array format for the table
        const priceList = [];
        for (const [name, price] of Object.entries(data.prices)) {
            priceList.push({
                crop_name: name,
                price_per_kg: price,
                last_updated: data.last_updated[name] || new Date().toISOString(),
                id: data.ids[name] || name // Use actual ID from DB
            });
        }

        if (priceList.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #64748b;">No market prices found</td></tr>';
            return;
        }

        allPrices = priceList;
        filteredPrices = allPrices;
        currentPage = 1;

        renderPricesTable();
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="4" style="text-align: center; color: #ef4444;">Error: ${error.message}</td></tr>`;
    }
}

async function syncMarketPrices() {
    const btn = document.getElementById('syncWfpBtn');
    const originalContent = btn.innerHTML;
    
    showConfirm('Sync prices with World Food Programme (WFP) data? This will update all matching crops to the latest regional averages.', async () => {
        try {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Syncing...';
            
            const res = await fetch('/naos/api/sync_market_prices.php');
            const data = await res.json();
            
            if (data.success) {
                showToast(data.message, 'success');
                refreshMarketPrices();
            } else {
                showToast('Sync failed: ' + data.message, 'error');
            }
        } catch (error) {
            showToast('Sync error: ' + error.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalContent;
        }
    });
}

function renderPricesTable() {
    const tbody = document.getElementById('marketPricesTableBody');

    if (filteredPrices.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #64748b;">No crops found matching your search</td></tr>';
        updatePaginationControls();
        return;
    }

    // Calculate pagination
    const totalPages = Math.ceil(filteredPrices.length / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, filteredPrices.length);
    const paginatedPrices = filteredPrices.slice(startIndex, endIndex);

    let html = '';
    paginatedPrices.forEach(price => {
        const lastUpdated = new Date(price.last_updated).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        html += `
            <tr id="price-row-${price.id}">
                <td><strong>${price.crop_name.charAt(0).toUpperCase() + price.crop_name.slice(1)}</strong></td>
                <td>
                    <input 
                        type="number" 
                        id="price-input-${price.id}" 
                        value="${price.price_per_kg}" 
                        min="0" 
                        step="0.01"
                        onchange="markPriceChanged(${price.id})"
                        style="width: 120px; padding: 0.25rem 0.5rem; border: 1px solid #cbd5e1; border-radius: 0.375rem;"
                    >
                    <span style="margin-left: 0.5rem; color: #64748b;">MWK</span>
                </td>
                <td><span style="color: #64748b; font-size: 0.875rem;">${lastUpdated}</span></td>
                <td>
                    <button onclick="updateSinglePrice(${price.id})" class="admin-btn admin-btn-primary admin-btn-sm">Update</button>
                </td>
            </tr>
        `;
    });
    tbody.innerHTML = html;

    updatePaginationControls();
}

function updatePaginationControls() {
    const totalPages = Math.ceil(filteredPrices.length / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, filteredPrices.length);

    // Update page info
    document.getElementById('pricePageInfo').textContent =
        `Showing ${filteredPrices.length > 0 ? startIndex + 1 : 0}-${endIndex} of ${filteredPrices.length}`;

    // Update buttons
    document.getElementById('prevPageBtn').disabled = currentPage === 1;
    document.getElementById('nextPageBtn').disabled = currentPage === totalPages || totalPages === 0;

    // Update page numbers
    const pageNumbers = document.getElementById('pageNumbers');
    let pageHtml = '';

    for (let i = 1; i <= totalPages; i++) {
        if (i === currentPage) {
            pageHtml += `<span style="padding: 0.25rem 0.5rem; background: #1e293b; color: white; border-radius: 0.25rem; font-size: 0.875rem;">${i}</span>`;
        } else if (
            i === 1 ||
            i === totalPages ||
            (i >= currentPage - 1 && i <= currentPage + 1)
        ) {
            pageHtml += `<button onclick="goToPage(${i})" style="padding: 0.25rem 0.5rem; background: transparent; border: 1px solid #cbd5e1; cursor: pointer; border-radius: 0.25rem; font-size: 0.875rem;">${i}</button>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            pageHtml += `<span style="padding: 0.25rem;">...</span>`;
        }
    }

    pageNumbers.innerHTML = pageHtml;
}

function changePricePage(direction) {
    const totalPages = Math.ceil(filteredPrices.length / itemsPerPage);
    currentPage = Math.max(1, Math.min(currentPage + direction, totalPages));
    renderPricesTable();
}

function goToPage(page) {
    currentPage = page;
    renderPricesTable();
}

function filterMarketPrices() {
    const searchInput = document.getElementById('priceSearchInput');
    const searchTerm = searchInput.value.toLowerCase().trim();

    if (searchTerm === '') {
        filteredPrices = allPrices;
    } else {
        filteredPrices = allPrices.filter(price =>
            price.crop_name.toLowerCase().includes(searchTerm)
        );
    }

    currentPage = 1; // Reset to first page when filtering
    renderPricesTable();
}

function markPriceChanged(cropId) {
    const input = document.getElementById(`price-input-${cropId}`);
    priceChanges[cropId] = parseFloat(input.value);
    // Visual feedback
    input.style.borderColor = '#f59e0b';
    input.style.backgroundColor = '#fffbeb';
}

async function updateSinglePrice(cropId) {
    const input = document.getElementById(`price-input-${cropId}`);
    const newPrice = parseFloat(input.value);

    if (newPrice < 0) {
        showToast('Price cannot be negative', 'error');
        return;
    }

    const confirmMsg = `Update this price to ${newPrice} MWK/kg?`;
    showConfirm(confirmMsg, async () => {
        try {
            const formData = new FormData();
            formData.append('action', 'update');
            formData.append('crop_id', cropId);
            formData.append('price', newPrice);

            const res = await fetch('/naos/admin/market_prices.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                input.style.borderColor = '#cbd5e1';
                input.style.backgroundColor = 'white';
                delete priceChanges[cropId];

                const row = document.getElementById(`price-row-${cropId}`);
                row.style.backgroundColor = '#d1fae5';
                setTimeout(() => {
                    row.style.backgroundColor = 'transparent';
                }, 1500);

                showToast('Price updated', 'success');
                refreshMarketPrices();
            } else {
                showToast('Error: ' + data.error, 'error');
            }
        } catch (error) {
            showToast('Error: ' + error.message, 'error');
        }
    });
}

async function saveAllPrices() {
    const changedPrices = [];

    // Collect all current values from ALL prices (not just visible ones)
    allPrices.forEach(price => {
        const input = document.getElementById(`price-input-${price.id}`);
        if (input) {
            const newPrice = parseFloat(input.value);
            if (newPrice >= 0) {
                changedPrices.push({ id: price.id, price: newPrice });
            }
        }
    });

    if (changedPrices.length === 0) {
        showToast('No prices to update', 'warning');
        return;
    }

    showConfirm(`Update all ${changedPrices.length} prices?`, async () => {
        try {
            const res = await fetch('/naos/admin/market_prices.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'bulk_update', prices: changedPrices })
            });
            const data = await res.json();

            if (data.success) {
                showToast(data.message, 'success');
                refreshMarketPrices();
            } else {
                showToast('Error: ' + data.error, 'error');
            }
        } catch (error) {
            showToast('Error: ' + error.message, 'error');
        }
    });
}

// Auto-load overview on page load
if (window.location.pathname.includes('admin/dashboard.php')) {
    loadOverview();
}
// --- Profile Modal Logic ---
window.openProfileModal = async function () {
    console.log('Admin: Attempting to open profile modal...');
    try {
        const res = await fetch('../api/users.php?action=profile');
        console.log('Admin: Profile fetch response received:', res.status);
        const data = await res.json();

        if (data.success) {
            const user = data.user;
            console.log('Admin: Profile data loaded for user:', user.username);

            const modal = document.getElementById('profile-modal');
            const usernameInput = document.getElementById('profileUsername');
            const locationInput = document.getElementById('profileLocation');
            const phoneInput = document.getElementById('profilePhone');
            const maleRadio = document.getElementById('genderMale');
            const femaleRadio = document.getElementById('genderFemale');
            const previewImg = document.getElementById('modalProfilePreview');

            console.log('Admin: Checking modal elements:', { modal: !!modal, username: !!usernameInput, location: !!locationInput, phone: !!phoneInput, male: !!maleRadio, female: !!femaleRadio, preview: !!previewImg });

            if (usernameInput) usernameInput.value = user.username || '';
            if (locationInput) locationInput.value = user.location || '';
            if (phoneInput) phoneInput.value = user.phone_number || '';

            if (femaleRadio && maleRadio) {
                if (user.gender === 'female') {
                    femaleRadio.checked = true;
                } else {
                    maleRadio.checked = true;
                }
            }

            if (previewImg && user.profile_picture) {
                setAvatarPhoto(previewImg, '../assets/images/profiles/' + user.profile_picture);
            }

            if (modal) {
                modal.style.display = 'flex';
                console.log('Admin: Profile modal display set to flex');
            } else {
                console.error('Admin ERROR: #profile-modal not found in DOM');
                showToast('Error: Profile modal element missing', 'error');
            }
        } else {
            showToast('Error loading profile: ' + (data.error || 'Unknown error'), 'error');
        }
    } catch (e) {
        console.error('Admin: Exception in openProfileModal:', e);
        showToast('Network error: ' + e.message, 'error');
    }
};

window.closeProfileModal = function () {
    document.getElementById('profile-modal').style.display = 'none';
};

window.previewProfilePic = function (input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const el = document.getElementById('modalProfilePreview'); if (el) setAvatarPhoto(el, e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerText;
            submitBtn.innerText = 'Saving...';
            submitBtn.disabled = true;

            try {
                const res = await fetch('../api/users.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    showToast(data.message || 'Profile updated!', 'success');

                    // Update header UI immediately
                    const newUsername = document.getElementById('profileUsername').value;
                    const nameEls = document.querySelectorAll('.admin-profile-trigger strong');
                    nameEls.forEach(el => el.innerText = newUsername);

                    if (data.profile_picture) {
                        const newPicUrl = '../assets/images/profiles/' + data.profile_picture;
                        const headerPic = document.getElementById('headerProfilePic');
                        if (headerPic) setAvatarPhoto(headerPic, newPicUrl);
                    }

                    closeProfileModal();
                } else {
                    showToast('Update failed: ' + (data.error || 'Unknown error'), 'error');
                }
            } catch (err) {
                showToast('Network error: ' + err.message, 'error');
            } finally {
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    // Load settings if on the settings page
    if (document.getElementById('settings')?.classList.contains('active')) {
        loadSettings();
    }
});

async function loadSettings() {
    console.log('Admin: Loading settings...');
    try {
        const keys = ['subscription_amount', 'system_name', 'allow_registration', 'default_lang', 'maintenance_mode'];
        const res = await fetch(`../api/settings.php?keys=${keys.join(',')}`);
        const data = await res.json();

        if (data.success && data.settings) {
            keys.forEach(key => {
                const el = document.getElementById(key);
                if (el && data.settings[key] !== undefined) {
                    el.value = data.settings[key];
                }
            });
        }
    } catch (e) {
        console.error('Admin: Error loading settings:', e);
    }
}

async function handleSettingsSubmit(e) {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
    btn.disabled = true;

    const keys = ['subscription_amount', 'system_name', 'allow_registration', 'default_lang', 'maintenance_mode'];
    const settings = {};
    keys.forEach(key => {
        settings[key] = document.getElementById(key).value;
    });

    try {
        const res = await fetch('../api/settings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(settings)
        });
        const data = await res.json();

        if (data.success) {
            showToast('Settings updated successfully!', 'success');
        } else {
            showToast('Error: ' + (data.error || 'Failed to update settings'), 'error');
        }
    } catch (e) {
        showToast('Network error: ' + e.message, 'error');
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

window.loadSettings = loadSettings;
window.handleSettingsSubmit = handleSettingsSubmit;

// Ã¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢Â
// User Verification Logic
// Ã¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢Â
let rejectingUserId = null;

async function loadVerifications() {
    const tbody = document.getElementById('verificationsTableBody');
    const filter = document.getElementById('verificationFilter')?.value || 'pending';
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; color: #64748b;">Loading...</td></tr>';

    // Update panel title based on filter
    const panelTitle = document.querySelector('#user-verification .admin-panel-header span');
    if (panelTitle) {
        panelTitle.textContent = filter.charAt(0).toUpperCase() + filter.slice(1) + ' Verifications';
    }

    try {
        const res = await fetch(`/naos/api/verify_user.php?action=list&user_id=0&status=${filter}`);
        const data = await res.json();

        if (!data.success) {
            tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;color:#ef4444;">Error: ${data.error}</td></tr>`;
            return;
        }

        // Update badge
        updateVerificationBadge(data.pending_count || 0);

        if (!data.users || data.users.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;color:#64748b;">No ${filter} verifications found</td></tr>`;
            return;
        }

        let html = '';
        data.users.forEach(u => {
            const roleClass = getRoleBadgeClass(u.role);
            const regDate = new Date(u.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

            // ID thumbnail
            const idThumb = u.national_id_path
                ? `<img src="/naos/api/verify_user.php?action=view_id&user_id=${u.id}"
                        style="width:50px;height:35px;object-fit:cover;border-radius:4px;cursor:pointer;border:1px solid #cbd5e1;"
                        onclick="previewId(${u.id})" title="Click to preview">`
                : '<span style="color:#94a3b8;font-size:0.8rem;">No file</span>';

            // Actions
            let actions = `<button onclick='previewUserDetails(${JSON.stringify(u).replace(/'/g, "&apos;")})' class="admin-btn admin-btn-secondary admin-btn-sm" title="Preview Details"><i class="fa-solid fa-eye"></i> Details</button>`;
            
            if (filter === 'pending') {
                actions += `
                    <button onclick="approveUser(${u.id})" class="admin-btn admin-btn-primary admin-btn-sm" title="Approve"><i class="fa-solid fa-check"></i> Approve</button>
                    <button onclick="openRejectModal(${u.id}, '${u.username.replace(/'/g, "\\'")}')"
                        class="admin-btn admin-btn-danger admin-btn-sm" title="Reject"><i class="fa-solid fa-xmark"></i> Reject</button>`;
            }

            html += `
                <tr>
                    <td>${u.id}</td>
                    <td><strong>${u.username}</strong></td>
                    <td>${u.phone_number || '-'}</td>
                    <td><span class="admin-badge ${roleClass}">${u.role}</span></td>
                    <td>${u.farmer_id || '-'}</td>
                    <td>${idThumb}</td>
                    <td>${regDate}</td>
                    <td><div style="display:flex;gap:6px;align-items:center;">${actions}</div></td>
                </tr>`;
        });
        tbody.innerHTML = html;

    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;color:#ef4444;">Error: ${err.message}</td></tr>`;
    }
}

function updateVerificationBadge(count) {
    const badge = document.getElementById('verificationBadge');
    if (!badge) return;
    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'inline-block';
    } else {
        badge.style.display = 'none';
    }
}

async function approveUser(userId) {
    showConfirm('Approve this user? An SMS will be sent to notify them.', async () => {
        try {
            const formData = new FormData();
            formData.append('action', 'approve');
            formData.append('user_id', userId);

            const res = await fetch('/naos/api/verify_user.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.success) {
                showToast(data.message || 'User approved!', 'success');
                loadVerifications();
            } else {
                showToast('Error: ' + data.error, 'error');
            }
        } catch (err) {
            showToast('Network error: ' + err.message, 'error');
        }
    });
}

function openRejectModal(userId, username) {
    rejectingUserId = userId;
    document.getElementById('rejectUserName').textContent = username;
    document.getElementById('rejectionNotes').value = '';
    document.getElementById('rejectModal').style.display = 'block';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
    rejectingUserId = null;
}

async function confirmReject() {
    if (!rejectingUserId) return;
    const notes = document.getElementById('rejectionNotes').value.trim();
    const btn = document.getElementById('confirmRejectBtn');
    btn.disabled = true;
    btn.textContent = 'Rejecting...';

    try {
        const formData = new FormData();
        formData.append('action', 'reject');
        formData.append('user_id', rejectingUserId);
        formData.append('notes', notes);

        const res = await fetch('/naos/api/verify_user.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.success) {
            showToast(data.message || 'User rejected.', 'success');
            closeRejectModal();
            loadVerifications();
        } else {
            showToast('Error: ' + data.error, 'error');
        }
    } catch (err) {
        showToast('Network error: ' + err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Reject User';
    }
}

function previewId(userId) {
    const img = document.getElementById('idPreviewImage');
    img.src = `/naos/api/verify_user.php?action=view_id&user_id=${userId}`;
    document.getElementById('idPreviewModal').style.display = 'block';
}

function previewUserDetails(user) {
    const html = `
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; text-align: left; font-size: 0.95rem;">
            <strong style="color: #475569;">Name:</strong> <span>${user.username}</span>
            <strong style="color: #475569;">Role:</strong> <span style="text-transform: capitalize;">${user.role}</span>
            <strong style="color: #475569;">Phone:</strong> <span>${user.phone_number || '-'}</span>
            <strong style="color: #475569;">Gender:</strong> <span style="text-transform: capitalize;">${user.gender || '-'}</span>
            <strong style="color: #475569;">Location:</strong> <span>${user.location || '-'}</span>
            <strong style="color: #475569;">Farmer ID:</strong> <span style="font-family: monospace; font-weight: bold; color: #1b4d3e;">${user.farmer_id || '-'}</span>
            <strong style="color: #475569;">Status:</strong> <span class="admin-badge ${user.id_verification_status === 'approved' ? 'admin-badge-active' : 'admin-badge-pending'}">${user.id_verification_status}</span>
            <strong style="color: #475569;">System Notes:</strong> <span>${user.id_verification_notes || '-'}</span>
        </div>
    `;
    document.getElementById('userDetailsContent').innerHTML = html;
    document.getElementById('userDetailsModal').style.display = 'block';
}

function closeUserDetailsModal() {
    document.getElementById('userDetailsModal').style.display = 'none';
}

function closeIdPreview() {
    document.getElementById('idPreviewModal').style.display = 'none';
}

// Close modals on outside click
window.addEventListener('click', (e) => {
    if (e.target === document.getElementById('idPreviewModal')) closeIdPreview();
    if (e.target === document.getElementById('rejectModal')) closeRejectModal();
    if (e.target === document.getElementById('userDetailsModal')) closeUserDetailsModal();
});

// Expose functions globally
window.loadVerifications = loadVerifications;
window.approveUser = approveUser;
window.openRejectModal = openRejectModal;
window.closeRejectModal = closeRejectModal;
window.confirmReject = confirmReject;
window.previewId = previewId;
window.closeIdPreview = closeIdPreview;
window.previewUserDetails = previewUserDetails;
window.closeUserDetailsModal = closeUserDetailsModal;

// Load pending count on page load for the badge
(async function loadPendingCount() {
    try {
        const res = await fetch('/naos/api/verify_user.php?action=list&user_id=0&status=pending');
        const data = await res.json();
        if (data.success) updateVerificationBadge(data.pending_count || 0);
    } catch (e) { /* silent */ }
})();

// Ã¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢Â
// Crop Configurations Logic
// Ã¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢ÂÃ¢â€¢Â

async function loadCropConfigs() {
    const tbody = document.getElementById('cropConfigTableBody');
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: #64748b;">Loading crops...</td></tr>';

    try {
        const res = await fetch('/naos/api/crop_config.php?action=list');
        const data = await res.json();

        if (!data.success) {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:#ef4444;">Error: ${data.error}</td></tr>`;
            return;
        }

        if (!data.crops || data.crops.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#64748b;">No crop configurations found</td></tr>';
            return;
        }

        let html = '';
        data.crops.forEach(c => {
            html += `
                <tr>
                    <td><strong>${c.crop_name.charAt(0).toUpperCase() + c.crop_name.slice(1)}</strong></td>
                    <td>${c.min_rainfall} - ${c.max_rainfall}</td>
                    <td>${c.min_temp} - ${c.max_temp}</td>
                    <td><span class="admin-badge ${c.drought_resistant == 1 ? 'admin-badge-active' : 'admin-badge-inactive'}">${c.drought_resistant == 1 ? 'Yes' : 'No'}</span></td>
                    <td>${c.current_price}</td>
                    <td>
                        <button onclick='openCropConfigModal(${JSON.stringify(c).replace(/'/g, "&apos;")})' class="admin-btn admin-btn-primary admin-btn-sm" title="Edit"><i class="fa-solid fa-pen"></i></button>
                        <button onclick="deleteCropConfig(${c.id})" class="admin-btn admin-btn-danger admin-btn-sm" title="Delete"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>`;
        });
        tbody.innerHTML = html;
    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:#ef4444;">Error: ${err.message}</td></tr>`;
    }
}

function openCropConfigModal(cropData = null) {
    const modal = document.getElementById('cropConfigModal');
    const form = document.getElementById('cropConfigForm');
    
    if (cropData && cropData.id) {
        document.getElementById('cropModalTitle').textContent = 'Edit Crop Configuration';
        document.getElementById('cropConfigId').value = cropData.id;
        document.getElementById('cropAction').value = 'update';
        document.getElementById('crop_name').value = cropData.crop_name;
        document.getElementById('min_rainfall').value = cropData.min_rainfall;
        document.getElementById('max_rainfall').value = cropData.max_rainfall;
        document.getElementById('min_temp').value = cropData.min_temp;
        document.getElementById('max_temp').value = cropData.max_temp;
        document.getElementById('current_price').value = cropData.current_price;
        document.getElementById('drought_resistant').value = cropData.drought_resistant;
    } else {
        document.getElementById('cropModalTitle').textContent = 'Add Crop Configuration';
        form.reset();
        document.getElementById('cropConfigId').value = '';
        document.getElementById('cropAction').value = 'add';
    }
    
    modal.style.display = 'block';
}

function closeCropConfigModal() {
    document.getElementById('cropConfigModal').style.display = 'none';
}

async function handleCropConfigSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Saving...';

    try {
        const res = await fetch('/naos/api/crop_config.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            showToast(data.message || 'Saved successfully', 'success');
            closeCropConfigModal();
            loadCropConfigs();
        } else {
            showToast('Error: ' + data.error, 'error');
        }
    } catch (err) {
        showToast('Network error: ' + err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = originalText;
    }
}

async function deleteCropConfig(id) {
    showConfirm('Are you sure you want to delete this crop configuration? This action cannot be undone.', async () => {
        try {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);

            const res = await fetch('/naos/api/crop_config.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                showToast(data.message || 'Crop deleted', 'success');
                loadCropConfigs();
            } else {
                showToast('Error: ' + data.error, 'error');
            }
        } catch (err) {
            showToast('Network error: ' + err.message, 'error');
        }
    });
}

window.openCropConfigModal = openCropConfigModal;
window.closeCropConfigModal = closeCropConfigModal;
window.handleCropConfigSubmit = handleCropConfigSubmit;
window.deleteCropConfig = deleteCropConfig;

