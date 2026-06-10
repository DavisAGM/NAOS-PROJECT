
// ─── Global fetch interceptor: handle session expiry ─────────────────────────
(function () {
    const _origFetch = window.fetch;
    window.fetch = async function (...args) {
        const response = await _origFetch.apply(this, args);
        const clone = response.clone();
        try {
            const ct = response.headers.get('Content-Type') || '';
            if (ct.includes('application/json')) {
                const json = await clone.json();
                if (json && json.error === 'session_expired') {
                    if (typeof showToast === 'function') {
                        showToast('Your session has expired. Please log in again.', 'warning');
                    }
                    setTimeout(() => {
                        window.location.href = '/naos/auth.php?error=session_expired';
                    }, 1800);
                }
            }
        } catch (_) { /* non-JSON — ignore */ }
        return response;
    };
})();

/**
 * Updates an avatar element (div or img) with a photo URL.
 * Works with the renderAvatar() div-based approach.
 */
function setAvatarPhoto(el, url) {
    if (!el) return;
    if (el.tagName === 'IMG') {
        el.src = url;
    } else {
        el.style.backgroundImage = 'url(' + url + ')';
        el.style.backgroundSize = 'cover';
        el.style.backgroundPosition = 'center';
        el.style.color = 'transparent';
        el.style.background = 'url(' + url + ') center/cover no-repeat';
    }
}


document.addEventListener('DOMContentLoaded', function () {

    // ── Payment redirect feedback ─────────────────────────────────────────────
    (function checkPaymentStatus() {
        const params = new URLSearchParams(window.location.search);
        const payStatus = params.get('payment');
        if (payStatus === 'success') {
            showToast('✅ Payment successful! Your account has been updated.', 'success');
        } else if (payStatus === 'failed') {
            let reason = params.get('reason') || 'Payment was not completed.';
            // Trim to first sentence to avoid showing the full Airtel PIN-reset instruction
            const firstSentence = reason.split(/[.!?]/)[0].trim();
            showToast('❌ Payment failed: ' + (firstSentence || reason), 'error');
        } else if (payStatus === 'pending') {
            showToast('⏳ Payment is still being processed. Please check back in a few minutes.', 'warning');
        }
        // Clean the URL so the toast doesn't reappear on refresh
        if (payStatus) {
            const cleanUrl = window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
        }
    })();
    // ─────────────────────────────────────────────────────────────────────────

    const menuItems = document.querySelectorAll('.sidebar-menu-item, .overview-panel');
    const contentSections = document.querySelectorAll('.content-section');
    const sidebar = document.getElementById('sidebar');
    const hamburger = document.getElementById('hamburger');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    let currentUserReportData = [];

    async function loadUserReports() {
        const tableBody = document.getElementById('userReportsBody');
        const tableHead = document.getElementById('userReportsHead');
        const type = document.getElementById('userReportType').value;
        const period = document.getElementById('userReportPeriod').value;
        const start = document.getElementById('reportStartDate')?.value || '';
        const end = document.getElementById('reportEndDate')?.value || '';

        // Show/hide custom date inputs
        const customContainer = document.getElementById('customDateContainer');
        if (customContainer) {
            customContainer.style.display = period === 'custom' ? 'flex' : 'none';
        }

        tableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 1rem;">Loading report data...</td></tr>';

        let headers = '';
        if (type === 'orders') {
            headers = '<tr><th>Order ID</th><th>Item</th><th>Qty</th><th>Total</th><th>Status</th><th>Date</th></tr>';
        } else if (type === 'market') {
            headers = '<tr><th>Crop Name</th><th>Current Price (MWK)</th><th>Last Updated</th></tr>';
        } else if (type === 'sales') {
            headers = '<tr><th>Order ID</th><th>Item</th><th>Qty</th><th>Revenue</th><th>Date</th></tr>';
        } else if (type === 'production') {
            headers = '<tr><th>Date</th><th>Activity</th><th>Input</th><th>Yield</th><th>Income</th><th>Expense</th></tr>';
        } else if (type === 'weather') {
            headers = '<tr><th>Date</th><th>Max Temp (°C)</th><th>Min Temp (°C)</th><th>Rainfall (mm)</th></tr>';
        }
        tableHead.innerHTML = headers;

        try {
            let url = `../api/reports.php?type=${type}&period=${period}`;
            if (period === 'custom' && start && end) {
                url += `&start_date=${start}&end_date=${end}`;
            }
            const res = await fetch(url);
            const data = await res.json();

            if (data.error) {
                tableBody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: red;">Error: ${data.error}</td></tr>`;
                return;
            }

            currentUserReportData = data.reports || [];

            if (currentUserReportData.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 1rem;">No records found.</td></tr>';
                return;
            }

            let html = '';
            currentUserReportData.forEach(row => {
                if (type === 'orders') {
                    html += `<tr><td>#${row.order_id}</td><td>${row.item}</td><td>${row.quantity}</td><td>${row.total}</td><td>${row.status}</td><td>${row.date}</td></tr>`;
                } else if (type === 'market') {
                    html += `<tr><td>${row.crop_name}</td><td>${row.current_price}</td><td>${row.last_updated}</td></tr>`;
                } else if (type === 'sales') {
                    html += `<tr><td>#${row.order_id}</td><td>${row.item}</td><td>${row.quantity}</td><td>${row.revenue}</td><td>${row.date}</td></tr>`;
                } else if (type === 'production') {
                    html += `<tr><td>${row.date}</td><td>${row.activity}</td><td>${row.input_usage || '-'}</td><td>${row.yield || '-'}</td><td>${row.income || '-'}</td><td>${row.expense || '-'}</td></tr>`;
                } else if (type === 'weather') {
                    html += `<tr><td>${row.date}</td><td>${row.max_temp}°C</td><td>${row.min_temp}°C</td><td>${row.rainfall}mm</td></tr>`;
                }
            });
            tableBody.innerHTML = html;

        } catch (error) {
            console.error(error);
            tableBody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: red;">Failed to load data.</td></tr>`;
        }
    }

    function getPeriodLabel(period, start, end) {
        if (period === 'weekly') {
            return 'This Week';
        }
        if (period === 'monthly') {
            return 'This Month';
        }
        if (period === 'custom' && start && end) {
            return `${start} to ${end}`;
        }
        return 'All Time';
    }

    async function exportUserReport(format) {
        if (!currentUserReportData || currentUserReportData.length === 0) {
            showToast('No data to export.', 'warning');
            return;
        }

        const type = document.getElementById('userReportType').value;
        const period = document.getElementById('userReportPeriod').value;
        const start = document.getElementById('reportStartDate')?.value || '';
        const end = document.getElementById('reportEndDate')?.value || '';
        const periodLabel = getPeriodLabel(period, start, end).replace(/\s+/g, '_').replace(/:/g, '-');
        const filename = `NAOS_Report_${type}_${periodLabel}_${new Date().toISOString().slice(0, 10)}`;

        if (format === 'excel') {
            exportToCSV(currentUserReportData, filename);
        } else if (format === 'pdf') {
            exportToPDF(currentUserReportData, type, filename, periodLabel);
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

    function exportToPDF(data, type, filename, periodLabel) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        doc.setFontSize(18);
        doc.text(`NAOS Report: ${type.toUpperCase()}`, 14, 22);
        doc.setFontSize(11);
        doc.text(`Period: ${periodLabel.replace(/_/g, ' ')}`, 14, 30);
        doc.text(`Generated on: ${new Date().toLocaleString()}`, 14, 36);

        const headers = Object.keys(data[0]).map(h => h.toUpperCase());
        const body = data.map(row => Object.values(row));

        doc.autoTable({
            head: [headers],
            body: body,
            startY: 44,
            theme: 'grid',
            styles: { fontSize: 8 },
            headStyles: { fillColor: [40, 167, 69] }
        });

        doc.save(`${filename}.pdf`);
    }

    async function loadBuyers() {
        const body = document.getElementById('buyerDirectoryBody');
        if (!body) return;

        body.innerHTML = '<tr><td colspan="3" style="text-align: center;">Loading buyers...</td></tr>';

        try {
            const res = await fetch('../api/users.php?role=buyer');
            const data = await res.json();

            if (data.users && data.users.length > 0) {
                let html = '';
                data.users.forEach(u => {
                    html += `<tr><td>${u.username}</td><td>${u.location || 'N/A'}</td><td><span class="status-badge" style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:10px;font-size:12px;">Active</span></td></tr>`;
                });
                body.innerHTML = html;
            } else {
                body.innerHTML = '<tr><td colspan="3" style="text-align: center;">No buyers found.</td></tr>';
            }
        } catch (e) {
            body.innerHTML = '<tr><td colspan="3" style="text-align: center; color: red;">Error loading buyers.</td></tr>';
        }
    }

    function filterBuyers() {
        const input = document.getElementById('buyerSearchInput');
        const filter = input.value.toLowerCase();
        const rows = document.getElementById('buyerDirectoryBody').getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const name = rows[i].getElementsByTagName('td')[0];
            const location = rows[i].getElementsByTagName('td')[1];
            if (name || location) {
                const text = (name.textContent || name.innerText) + (location.textContent || location.innerText);
                rows[i].style.display = text.toLowerCase().includes(filter) ? '' : 'none';
            }
        }
    }

    window.filterBuyers = filterBuyers;

    window.loadUserReports = loadUserReports;
    window.exportUserReport = exportUserReport;
    window.loadBuyers = loadBuyers;

    menuItems.forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();

            menuItems.forEach(i => i.classList.remove('active'));

            this.classList.add('active');

            contentSections.forEach(section => section.classList.remove('active'));

            const sectionId = this.getAttribute('data-section');
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.classList.add('active');
            }

            // Update top bar title
            const titleEl = document.querySelector('.top-bar-title');
            const menuTextSpan = this.querySelector('[data-t]');
            if (titleEl && menuTextSpan) {
                const translationKey = menuTextSpan.getAttribute('data-t');
                titleEl.setAttribute('data-t', translationKey);
                // We use the text of the span but skip the icon if it exists via InnerText
                titleEl.textContent = menuTextSpan.textContent;
            }

            if (sectionId === 'reports' || (sectionId === 'diary' && document.getElementById('userReportType'))) {
                loadUserReports();
            }

            if (sectionId === 'buyers') {
                loadBuyers();
            }

            if (sectionId === 'produce' || sectionId === 'listings') {
                if (typeof getListings === 'function') {
                    getListings();
                }
            }

            if (sectionId === 'incoming-orders') {
                if (typeof getIncomingOrders === 'function') {
                    getIncomingOrders();
                }
            }

            if (sectionId === 'payments') {
                loadPayments();
                checkSubscriptionStatus();
            }

            if (sectionId === 'diary') {
                loadFarms();
                loadRecentActivities();
                loadReminders();
            }

            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            }
        });
    });

    // --- Farms Logic ---
    let userFarms = [];

    async function loadFarms() {
        const container = document.getElementById('farmsList');
        if (!container) return;

        container.innerHTML = `<p>${t('loading_farms')}</p>`;

        try {
            const res = await fetch('../api/farms.php?action=get_farms');
            const data = await res.json();
            
            if (data.success) {
                userFarms = data.farms;
                renderFarmsList();
                updateFarmSelectors();
            } else {
                container.innerHTML = `<p style="color:red;">Error: ${data.error}</p>`;
            }
        } catch (e) {
            container.innerHTML = `<p style="color:red;">${t('error')}</p>`;
        }
    }

    function renderFarmsList() {
        const container = document.getElementById('farmsList');
        if (!userFarms || userFarms.length === 0) {
            container.innerHTML = `<p>${t('no_farms')}</p>`;
            return;
        }

        let html = '';
        userFarms.forEach(farm => {
            html += `
                <div style="border:1px solid #ddd; padding:1rem; border-radius:8px; background:#f9f9f9;">
                    <h3 style="margin-top:0;">${farm.farm_name}</h3>
                    <p style="margin:5px 0; font-size:14px;"><strong>${t('district')}:</strong> ${farm.district}</p>
                    <p style="margin:5px 0; font-size:14px;"><strong>${t('eco_zone')}:</strong> ${farm.ecological_zone}</p>
                    <p style="margin:5px 0; font-size:14px;"><strong>${t('soil_type')}:</strong> ${farm.soil_type}</p>
                    <p style="margin:5px 0; font-size:12px; color:#666;">Lat: ${farm.latitude}, Lon: ${farm.longitude}</p>
                    <button onclick="deleteFarm(${farm.id})" class="btn-secondary btn-sm" style="margin-top:10px; background:#fee2e2; color:#b91c1c; border-color:#fca5a5;">${t('delete_farm')}</button>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function updateFarmSelectors() {
        const wSelector = document.getElementById('weatherFarmSelector');
        const rSelector = document.getElementById('recFarmSelector');
        const dSelector = document.getElementById('diaryFarmSelector');
        
        const optionsHtml = '<option value="">Home Location</option>' + 
            userFarms.map(f => `<option value="${f.id}">${f.farm_name}</option>`).join('');

        if (wSelector) wSelector.innerHTML = optionsHtml;
        if (rSelector) rSelector.innerHTML = optionsHtml;
        
        const diaryOptionsHtml = '<option value="">Select a farm...</option>' + 
            userFarms.map(f => `<option value="${f.id}">${f.farm_name}</option>`).join('');
            
        if (dSelector) dSelector.innerHTML = diaryOptionsHtml;
    }

    // Auto-fill farm details based on Malawian districts
    const districtSelect = document.getElementById('farm_district');
    if (districtSelect) {
        districtSelect.addEventListener('change', function() {
            const district = this.value;
            const ecoZoneInput = document.getElementById('farm_eco_zone');
            const soilTypeInput = document.getElementById('farm_soil_type');
            const latInput = document.getElementById('farm_lat');
            const lonInput = document.getElementById('farm_lon');

            const districtData = {
                'Lilongwe': { zone: 'Medium Altitude', soil: 'Ferruginous', lat: -13.9626, lon: 33.7741 },
                'Blantyre': { zone: 'Medium Altitude', soil: 'Ferruginous', lat: -15.7861, lon: 35.0058 },
                'Mzuzu': { zone: 'High Altitude', soil: 'Lithosols', lat: -11.4656, lon: 34.0207 },
                'Zomba': { zone: 'Medium Altitude', soil: 'Ferruginous', lat: -15.3817, lon: 35.3188 },
                'Kasungu': { zone: 'Medium Altitude', soil: 'Ferruginous', lat: -13.0333, lon: 33.4833 },
                'Rumphi': { zone: 'High Altitude', soil: 'Lithosols', lat: -11.0167, lon: 33.8500 },
                'Mzimba': { zone: 'Medium Altitude', soil: 'Ferruginous', lat: -11.9000, lon: 33.6000 },
                'Salima': { zone: 'Lakeshore', soil: 'Alluvial', lat: -13.7804, lon: 34.4587 },
                'Thyolo': { zone: 'High Altitude', soil: 'Ferruginous', lat: -16.0667, lon: 35.1333 },
                'Mulanje': { zone: 'High Altitude', soil: 'Ferruginous', lat: -16.0316, lon: 35.5000 },
                'Chikwawa': { zone: 'Shire Valley', soil: 'Vertisols', lat: -16.0333, lon: 34.8000 },
                'Nsanje': { zone: 'Shire Valley', soil: 'Vertisols', lat: -16.9167, lon: 35.2667 },
                'Mangochi': { zone: 'Lakeshore', soil: 'Alluvial', lat: -14.4782, lon: 35.2645 },
                'Nkhata Bay': { zone: 'Lakeshore', soil: 'Alluvial', lat: -11.6066, lon: 34.2907 },
                'Karonga': { zone: 'Lakeshore', soil: 'Alluvial', lat: -9.9333, lon: 33.9333 }
            };

            if (district && districtData[district]) {
                const data = districtData[district];
                if (ecoZoneInput) ecoZoneInput.value = data.zone;
                if (soilTypeInput) soilTypeInput.value = data.soil;
                
                // Update map preview for the selected district
                if (window.updateMarker) {
                    window.updateMarker(data.lat, data.lon);
                    if (farmMap) farmMap.setView([data.lat, data.lon], 11); // Focus on district
                }
            } else {
                if (ecoZoneInput) ecoZoneInput.value = '';
                if (soilTypeInput) soilTypeInput.value = '';
            }
        });
    }

    window.handleAddFarm = async function(e) {
        e.preventDefault();
        const form = e.target;
        const farmName = document.getElementById('farm_name').value.trim();
        const lat = document.getElementById('farm_lat').value;
        const lon = document.getElementById('farm_lon').value;

        if (!farmName) {
            showToast('Farm name is required', 'error');
            return;
        }

        if (!lat || !lon) {
            showToast('Please click on the map to pin your farm location', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'add_farm');
        formData.append('farm_name', farmName);
        formData.append('district', document.getElementById('farm_district').value);
        formData.append('ecological_zone', document.getElementById('farm_eco_zone').value);
        formData.append('soil_type', document.getElementById('farm_soil_type').value);
        formData.append('latitude', lat);
        formData.append('longitude', lon);

        const btn = form.querySelector('button');
        btn.disabled = true;
        btn.innerText = 'Saving...';

        try {
            const res = await fetch('../api/farms.php', { method: 'POST', body: formData });
            const data = await res.json();
            
            if (data.success) {
                showToast(data.message, 'success');
                form.reset();
                if (farmMarker) {
                    farmMap.removeLayer(farmMarker);
                    farmMarker = null;
                }
                loadFarms(); // reload list
            } else {
                showToast(data.error || 'Failed to add farm', 'error');
            }
        } catch (error) {
            showToast('Network error', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Save Farm';
        }
    };

    window.deleteFarm = async function(farmId) {
        showConfirm('Are you sure you want to delete this farm?', async () => {
            const formData = new FormData();
            formData.append('action', 'delete_farm');
            formData.append('farm_id', farmId);

            try {
                const res = await fetch('../api/farms.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    loadFarms();
                } else {
                    showToast(data.error || 'Failed to delete farm', 'error');
                }
            } catch (error) {
                showToast('Network error', 'error');
            }
        });
    };

    window.getLocationForFarm = function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    if (window.updateMarker) window.updateMarker(lat, lon);
                    if (typeof farmMap !== 'undefined' && farmMap) farmMap.setView([lat, lon], 14);
                },
                (error) => {
                    showToast('Could not get location. Please allow location access.', 'error');
                }
            );
        } else {
            showToast('Geolocation is not supported by this browser.', 'error');
        }
    };

    // Load farms initially behind the scenes so dropdowns are ready
    loadFarms();

    async function loadPayments() {
        const body = document.getElementById('paymentsBody');
        if (!body) return;

        body.innerHTML = '<tr><td colspan="5" style="text-align: center;">Loading payments...</td></tr>';

        try {
            const res = await fetch(`../api/reports.php?type=payments`);
            const data = await res.json();

            if (data.reports && data.reports.length > 0) {
                let html = '';
                data.reports.forEach(p => {
                    const statusClass = p.status === 'completed' ? 'status-success' : (p.status === 'failed' ? 'status-failed' : 'status-pending');
                    html += `<tr>
                        <td>${p.transaction_id}</td>
                        <td>${rowDate(p.date)}</td>
                        <td>MWK ${parseFloat(p.amount).toLocaleString()}</td>
                        <td>${p.order_type || 'Order'}</td>
                        <td><span class="badge ${statusClass}">${p.status}</span></td>
                    </tr>`;
                });
                body.innerHTML = html;
            } else {
                body.innerHTML = '<tr><td colspan="5" style="text-align: center;">No payments found.</td></tr>';
            }
        } catch (e) {
            body.innerHTML = '<tr><td colspan="5" style="text-align: center; color: red;">Error loading payments.</td></tr>';
        }
    }

    function rowDate(dateStr) {
        if (!dateStr) return 'N/A';
        const d = new Date(dateStr);
        return d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    async function checkSubscriptionStatus() {
        const badge = document.getElementById('subBadge');
        const renewBtn = document.getElementById('renewBtn');
        if (!badge) return;

        try {
            const res = await fetch('../api/users.php?action=profile');
            const data = await res.json();

            if (data.user) {
                const status = data.user.subscription_status || 'inactive';
                badge.innerText = status.toUpperCase();
                badge.className = status === 'active' ? 'text-success' : 'text-danger';

                if (status !== 'active') {
                    // Fetch dynamic amount for the button
                    try {
                        const sRes = await fetch('../api/settings.php?keys=subscription_amount');
                        const sData = await sRes.json();
                        const amount = (sData.success && sData.settings.subscription_amount) ? sData.settings.subscription_amount : '5,000';
                        renewBtn.innerText = `Subscribe (${parseFloat(amount).toLocaleString()} MWK)`;
                    } catch (e) {
                        console.error('Error fetching subscription amount:', e);
                    }
                    renewBtn.style.display = 'inline-block';
                } else {
                    renewBtn.style.display = 'none';
                    badge.innerHTML += ` <span style="font-size: 0.8rem; font-weight: normal; color: #666;">(Expires: ${data.user.subscription_expiry || 'N/A'})</span>`;
                }
            }
        } catch (e) {
            badge.innerText = 'Error checking status';
        }
    }

    async function renewSubscription() {
        let amountStr = '5,000';
        try {
            const sRes = await fetch('../api/settings.php?keys=subscription_amount');
            const sData = await sRes.json();
            if (sData.success && sData.settings.subscription_amount) {
                amountStr = parseFloat(sData.settings.subscription_amount).toLocaleString();
            }
        } catch (e) {
            console.error('Error fetching amount for confirm:', e);
        }

        showConfirm(`You are about to pay your NAOS subscription fee of ${amountStr} MWK for the full utilization of Our Services. Proceed?`, async () => {
            try {
                const res = await fetch('../api/payment_init.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ type: 'subscription' })
                });
                const data = await res.json();

                if (data.status === 'success') {
                    if (data.checkout_url) {
                        window.location.href = data.checkout_url;
                    } else {
                        const loader = showLoading('Please enter the PIN on your phone to complete the payment...');
                        setTimeout(() => {
                            if (data.order_reference) {
                                loader.updateText('Verification in progress. Please wait...');
                                window.location.href = '../api/payment_callback.php?ref=' + data.order_reference;
                            } else {
                                loader.hide();
                                showToast('Subscription request sent. Please check your phone.', 'info');
                            }
                        }, 25000); 
                    }
                } else {
                    showToast('Error: ' + (data.error || 'Could not initiate payment'), 'error');
                }
            } catch (e) {
                showToast('Network error: ' + e.message, 'error');
            }
        });
    }

    window.renewSubscription = renewSubscription;
    window.loadPayments = loadPayments;

    if (hamburger) {
        hamburger.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });
    }

    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        }
    });

    // --- Profile Modal Logic ---
    window.openProfileModal = async function () {
        console.log('Attempting to open profile modal...');
        try {
            const res = await fetch('../api/users.php?action=profile');
            console.log('Profile fetch response received:', res.status);
            const data = await res.json();

            if (data.success) {
                const user = data.user;
                console.log('Profile data loaded for user:', user.username);

                const modal = document.getElementById('profile-modal');
                const usernameInput = document.getElementById('profileUsername');
                const locationInput = document.getElementById('profileLocation');
                const phoneInput = document.getElementById('profilePhone');
                const maleRadio = document.getElementById('genderMale');
                const femaleRadio = document.getElementById('genderFemale');
                const previewImg = document.getElementById('modalProfilePreview');

                console.log('Checking modal elements:', { modal: !!modal, username: !!usernameInput, location: !!locationInput, phone: !!phoneInput, male: !!maleRadio, female: !!femaleRadio, preview: !!previewImg });

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
                    console.log('Profile modal display set to flex');
                } else {
                    console.error('ERROR: #profile-modal not found in DOM');
                    showToast('Error: Profile modal element missing', 'error');
                }
            } else {
                showToast('Error loading profile: ' + (data.error || 'Unknown error'), 'error');
            }
        } catch (e) {
            console.error('Exception in openProfileModal:', e);
            showToast('Network error: ' + e.message, 'error');
        }
    };

    window.closeProfileModal = function () {
        document.getElementById('profile-modal').style.display = 'none';
        // Reset preview if needed 
    };

    // --- Farm Diary Modal Logic ---
    window.openActivityModal = function() {
        const modal = document.getElementById('activity-modal');
        if (modal) modal.style.display = 'flex';
    };

    window.closeActivityModal = function() {
        const modal = document.getElementById('activity-modal');
        if (modal) modal.style.display = 'none';
        document.getElementById('farmForm')?.reset();
        const hf = document.getElementById('harvestFields');
        if (hf) hf.style.display = 'none';
    };

    window.openHistoryModal = function() {
        const modal = document.getElementById('history-modal');
        if (modal) modal.style.display = 'flex';
    };

    window.closeHistoryModal = function() {
        const modal = document.getElementById('history-modal');
        if (modal) modal.style.display = 'none';
    };

    window.previewProfilePic = function (input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const el = document.getElementById('modalProfilePreview');
                if (el) setAvatarPhoto(el, e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const username = formData.get('username')?.trim();

            if (username && username.length < 8) {
                showToast('Username must be at least 8 characters long', 'error');
                return;
            }

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
                    const nameEls = document.querySelectorAll('.user-profile-trigger strong');
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
});

async function loadReminders() {
    const listEls = document.querySelectorAll('.reminders-list');
    if (listEls.length === 0) return;
    listEls.forEach(el => el.innerHTML = '<p class="text-sm">Loading reminders...</p>');

    try {
        const res = await fetch('/naos/api/reminders.php');
        const data = await res.json();
        
        if (data.success) {
            if (data.reminders.length === 0) {
                listEls.forEach(el => el.innerHTML = '<p class="text-sm" style="color:#666;">No upcoming scheduled activities based on your recorded planting dates.</p>');
                return;
            }

            let html = '<ul style="list-style: none; padding: 0; margin: 0;">';
            data.reminders.forEach(r => {
                const color = r.is_overdue ? '#dc3545' : (r.days_remaining <= 3 ? '#ffc107' : '#28a745');
                const timeText = r.is_overdue ? `Overdue by ${Math.abs(r.days_remaining)} days` : `In ${r.days_remaining} days`;
                
                html += `
                    <li style="padding: 0.75rem; border-bottom: 1px solid #eee; display: flex; flex-direction: column;">
                        <strong style="color: ${color};"><i class="fa-solid fa-leaf"></i> ${r.crop_name} - ${r.description}</strong>
                        <span style="font-size: 0.85rem; color: #666; margin-top: 0.25rem;">
                            <i class="fa-solid fa-clock"></i> ${timeText} (Due: ${r.target_date}) 
                            &bull; <i class="fa-solid fa-location-dot"></i> Farm: ${r.farm_name}
                        </span>
                    </li>
                `;
            });
            html += '</ul>';
            listEls.forEach(el => el.innerHTML = html);
        } else {
            listEls.forEach(el => el.innerHTML = '<p class="text-sm" style="color:red;">Failed to load reminders.</p>');
        }
    } catch (e) {
        listEls.forEach(el => el.innerHTML = '<p class="text-sm" style="color:red;">Error loading reminders.</p>');
    }
}

// Attach to DOMContentLoaded to run on startup
document.addEventListener('DOMContentLoaded', () => {
    loadReminders();
    loadRecentActivities();
});

async function loadRecentActivities() {
    const listEls = document.querySelectorAll('.recent-activities-list');
    if (listEls.length === 0) return;
    listEls.forEach(el => el.innerHTML = '<p class="text-sm text-center" style="color: #666; margin-top: 2rem;">Loading...</p>');

    try {
        const res = await fetch('/naos/api/activities.php');
        const data = await res.json();
        
        if (data.success) {
            if (data.activities.length === 0) {
                listEls.forEach(el => el.innerHTML = '<p class="text-sm text-center" style="color:#666; margin-top: 2rem;">No activities logged yet.</p>');
                return;
            }

            let html = '<ul style="list-style: none; padding: 0; margin: 0;">';
            data.activities.forEach(a => {
                let badgeColor = '#6c757d'; // default gray
                if (a.activity_type.toLowerCase() === 'planted') badgeColor = '#28a745'; // green
                else if (a.activity_type.toLowerCase() === 'harvested') badgeColor = '#ffc107'; // yellow
                else if (a.activity_type.toLowerCase() === 'fertilized') badgeColor = '#17a2b8'; // info
                else if (a.activity_type.toLowerCase() === 'sprayed') badgeColor = '#dc3545'; // danger
                else if (a.activity_type.toLowerCase() === 'weeded') badgeColor = '#20c997'; // teal

                let details = `<span class="badge" style="background-color:${badgeColor}; color:white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem;">${a.activity_type}</span> ${a.crop_name} on ${a.date}`;
                
                let extra = '';
                if (a.activity_type.toLowerCase() === 'harvested' && a.quantity) {
                    extra += ` &bull; <strong>Yield:</strong> ${a.quantity} ${a.unit || ''}`;
                }
                if (a.notes) {
                    extra += ` &bull; <em>${a.notes}</em>`;
                }

                html += `
                    <li style="padding: 0.75rem; border-bottom: 1px solid #eee;">
                        <div style="font-weight: 500;">${details}</div>
                        <div style="font-size: 0.85rem; color: #666; margin-top: 0.25rem;">
                            <i class="fa-solid fa-location-dot"></i> Farm: ${a.farm_name}${extra}
                        </div>
                    </li>
                `;
            });
            html += '</ul>';
            listEls.forEach(el => el.innerHTML = html);
        } else {
            listEls.forEach(el => el.innerHTML = '<p class="text-sm text-center" style="color:red; margin-top: 2rem;">Failed to load activities.</p>');
        }
    } catch (e) {
        listEls.forEach(el => el.innerHTML = '<p class="text-sm text-center" style="color:red; margin-top: 2rem;">Error loading activities.</p>');
    }
}

async function switchRole(newRole) {
    if (!newRole || !['farmer', 'buyer'].includes(newRole)) {
        showToast('Invalid role', 'error');
        return;
    }

    // Immediate UI feedback
    const switcher = document.getElementById('roleSwitcher');
    if (switcher) switcher.disabled = true;
    showToast('Switching to ' + newRole + ' view...', 'info');

    try {
        const response = await fetch('/naos/api/switch_role.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ role: newRole })
        });

        const data = await response.json();

        if (data.success) {
            window.location.href = data.redirect;
        } else {
            showToast('Error: ' + (data.error || 'Could not switch role'), 'error');
            if (switcher) switcher.disabled = false;
        }
    } catch (error) {
        showToast('Network error: ' + error.message, 'error');
        if (switcher) switcher.disabled = false;
    }
}

// --- Map Selection Logic ---
let farmMap;
let farmMarker;

function initFarmMap() {
    const mapContainer = document.getElementById('farmMap');
    if (!mapContainer || farmMap) return;

    const malawiCenter = [-13.2543, 34.3015];
    try {
        farmMap = L.map('farmMap').setView(malawiCenter, 6);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(farmMap);

        farmMap.on('click', function (e) {
            updateMarker(e.latlng.lat, e.latlng.lng);
        });

        // Initialize from inputs if they have data
        const lat = document.getElementById('farm_lat').value;
        const lon = document.getElementById('farm_lon').value;
        if (lat && lon && lat !== "" && lon !== "") {
            updateMarker(lat, lon);
            farmMap.setView([lat, lon], 12);
        }
    } catch (err) {
        console.error("Map init error:", err);
    }
}

window.updateMarker = function (lat, lon) {
    if (!farmMap) return;
    document.getElementById('farm_lat').value = parseFloat(lat).toFixed(6);
    document.getElementById('farm_lon').value = parseFloat(lon).toFixed(6);

    if (farmMarker) {
        farmMarker.setLatLng([lat, lon]);
    } else {
        farmMarker = L.marker([lat, lon], { draggable: true }).addTo(farmMap);
        farmMarker.on('dragend', function (e) {
            updateMarker(e.target.getLatLng().lat, e.target.getLatLng().lng);
        });
    }
}

// Trigger map load when sidebar section changes
document.addEventListener('DOMContentLoaded', () => {
    const items = document.querySelectorAll('.sidebar-menu-item');
    items.forEach(item => {
        item.addEventListener('click', () => {
            if (item.getAttribute('data-section') === 'diary') {
                setTimeout(initFarmMap, 300);
            }
        });
    });
});


