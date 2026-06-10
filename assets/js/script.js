document.addEventListener('DOMContentLoaded', () => {
});

const translations = {
    en: {
        loading_weather: 'Checking the weather...',
        loading_advice: 'Wait a moment...',
        order_placed: 'We have received your order.',
        listing_added: 'Your listing has been posted.',
        registered: 'Registration successful. You can now log in.',
        login_failed: 'We could not log you in. Please check your details.',
        confirm_logout: 'Do you want to log out?',
        error: 'Something went wrong.',
        logout: 'Logout',
        naos_dashboard: 'NAOS Dashboard',
        naos_buyer_board: 'NAOS Buyer Board',
        chichewa: 'Chichewa',
        english: 'English',
        overview: 'Dashboard Overview',
        weather_advice: 'Weather & Advice',
        market_prices: 'Market Prices',
        farm_diary: 'Farm Diary',
        reports: 'Reports',
        my_produce: 'My Produce',
        incoming_orders: 'Incoming Orders',
        buyer_directory: 'Buyer Directory',
        payments: 'Payments',
        support: 'Support',
        available_produce: 'Available Produce',
        place_order: 'Place Order',
        my_orders: 'My Orders',
        market_intelligence: 'Market Intelligence',
        features: 'Features',
        about: 'About',
        login: 'Login',
        my_farms: 'My Farms',
        add_new_farm: 'Add a New Farm Location',
        farm_name: 'Farm Name',
        district: 'District',
        select_district: 'Select District',
        eco_zone: 'Ecological Zone',
        select_zone: 'Select Zone',
        soil_type: 'Predominant Soil Type',
        select_soil_type: 'Select Soil Type',
        latitude: 'Latitude',
        longitude: 'Longitude',
        use_current_location: 'Use My Current Location',
        save_farm: 'Save Farm',
        registered_farms: 'Registered Farms',
        loading_farms: 'Loading farms...',
        no_farms: 'You have not registered any farms yet.',
        delete_farm: 'Delete'
    },
    ny: {
        loading_weather: 'Tikuyang\'ana nyengo...',
        loading_advice: 'Dikirani pang\'ono...',
        order_placed: 'Mwaitanitsa bwinobwino.',
        listing_added: 'Zalembedwa.',
        registered: 'Mwalembetsa bwino. Lowani tsopano.',
        login_failed: 'Simunalowe. Onetsetsani dzina ndi mawu achinsinsi.',
        confirm_logout: 'Mukufuna kutuluka?',
        error: 'Pachitika vuto.',
        logout: 'Tulukani',
        naos_dashboard: 'NAOS Dashboard',
        naos_buyer_board: 'NAOS Buyer Board',
        chichewa: 'Chichewa',
        english: 'English',
        overview: 'Chidule cha Dashboard',
        weather_advice: 'Nyengo ndi Upangiri',
        market_prices: 'Mitengo ya Msika',
        farm_diary: 'Buku la Ulimi',
        reports: 'Malipoti',
        my_produce: 'Zokolola Zanga',
        incoming_orders: 'Maoda Ofika',
        buyer_directory: 'Mndandanda wa Ogula',
        payments: 'Malipiro',
        support: 'Thandizo',
        available_produce: 'Zokolola Zilipo',
        place_order: 'Ikani Dongosolo',
        my_orders: 'Maoda Anga',
        market_intelligence: 'Nzeru za Msika',
        features: 'Zonena',
        about: 'Zambiri',
        login: 'Lowani',
        my_farms: 'Mafamu Anga',
        add_new_farm: 'Onjezerani Farmu Yatsopano',
        farm_name: 'Dzina la Farmu',
        district: 'Boma',
        select_district: 'Sankhani Boma',
        eco_zone: 'Dera la Chilengedwe',
        select_zone: 'Sankhani Dera',
        soil_type: 'Mtundu wa Nthaka',
        select_soil_type: 'Sankhani Mtundu wa Nthaka',
        latitude: 'Latitididye',
        longitude: 'Longitididye',
        use_current_location: 'Gwiritsani Ntchito Malo Anga Panopa',
        save_farm: 'Sungani Farmu',
        registered_farms: 'Mafamu Omwe Analembetsedwa',
        loading_farms: 'Tikufufuza mafamu...',
        no_farms: 'Simunalembetsetu mafamu alionse.',
        delete_farm: 'Fufutani'
    },
    tum: {
        loading_weather: 'Kulaŵilira nyengo...',
        loading_advice: 'Lindizgani pachoko...',
        order_placed: 'Tapokera oda yinu.',
        listing_added: 'Vyakulemba vinu vyaikika.',
        registered: 'Mwalembeska bwinobwino. Sono munganjira.',
        login_failed: 'Tindatondeke kumunjizgani. Chonde woneseskani unandi winu.',
        confirm_logout: 'Kasi mukukhumba kufuma?',
        error: 'Pachitika vuto.',
        logout: 'Fumani',
        naos_dashboard: 'NAOS Dashboard',
        naos_buyer_board: 'NAOS Buyer Board',
        chichewa: 'Chichewa',
        english: 'English',
        tumbuka: 'Tumbuka',
        overview: 'Chidule cha Dashboard',
        weather_advice: 'Nyengo na Thandizo',
        market_prices: 'Mitengo ya Msika',
        farm_diary: 'Buku la Vinthu',
        reports: 'Malipoti',
        my_produce: 'Vinthu Vane',
        incoming_orders: 'Malamulo Ofika',
        buyer_directory: 'Mndandanda wa Ogula',
        payments: 'Malipiro',
        support: 'Wovwiri',
        available_produce: 'Vyakukololera vilipo',
        place_order: 'Ikani Oda',
        my_orders: 'Maoda Ghane',
        market_intelligence: 'Mahala gha Msika',
        features: 'Vapadera',
        about: 'Vyakukhwaskana na Ife',
        login: 'Njirani',
        my_farms: 'Mafamu Ghane',
        add_new_farm: 'Sanjani Malo ghaphya gha Famu',
        farm_name: 'Zina la Famu',
        district: 'Boma',
        select_district: 'Sankhani Boma',
        eco_zone: 'Dera la Chilengedwe',
        select_zone: 'Sankhani Dera',
        soil_type: 'Mtundu wa Dongo',
        select_soil_type: 'Sankhani Mtundu wa Dongo',
        latitude: 'Latitididye',
        longitude: 'Longitididye',
        use_current_location: 'Gwiliskirani nchito Malo ghane gha Sono',
        save_farm: 'Sungani Famu',
        registered_farms: 'Mafamu Agho Ghalembeskeka',
        loading_farms: 'Tikupenja mafamu...',
        no_farms: 'Mundalembeske famu yiliyose.',
        delete_farm: 'Fufutani'
    }
};

function isLandingPage() {
    return window.location.pathname.endsWith('index.php') || window.location.pathname.endsWith('/') || window.location.pathname === '/naos/';
}

let currentLang = window.NAOS_LANG || localStorage.getItem('lang') || 'en';
if (isLandingPage()) currentLang = 'en';

function t(key) {
    if (!translations[currentLang]) {
        if (currentLang === 'tum') {
             // If tum is missing but selected, we should still try to use it if we just added it
             // but if the object is missing, we fallback
             currentLang = 'en';
        } else {
             currentLang = 'en';
        }
    }
    return (translations[currentLang] && translations[currentLang][key]) ? translations[currentLang][key] : key;
}

function syncTranslations() {
    if (isLandingPage()) return;
    document.querySelectorAll('[data-t]').forEach(el => {
        const key = el.getAttribute('data-t');
        if (translations[currentLang][key]) {
            const icon = el.querySelector('i');
            if (icon) {
                el.innerHTML = '';
                el.appendChild(icon);
                el.appendChild(document.createTextNode(' ' + translations[currentLang][key]));
            } else {
                el.textContent = translations[currentLang][key];
            }
        }
    });

    const langButton = document.getElementById('langToggle');
    const langDropdownItems = document.querySelectorAll('.lang-dropdown-item');
    if (langButton) {
        const languageNames = { en: 'English', ny: 'Chichewa', tum: 'Tumbuka' };
        const currentLabel = languageNames[currentLang] || currentLang.toUpperCase();
        const labelSpan = langButton.querySelector('.lang-label');
        if (labelSpan) {
            labelSpan.textContent = currentLabel;
        }
        langDropdownItems.forEach(item => {
            item.classList.toggle('active', item.dataset.lang === currentLang);
        });
    }
}

async function setLanguage(lang) {
    if (!lang || !['en', 'ny', 'tum'].includes(lang)) return;
    currentLang = lang;
    if (!isLandingPage()) {
        localStorage.setItem('lang', currentLang);
    }
    syncTranslations();
    closeLangDropdown();

    // Trigger Google Translate Auto-Translator
    const googleSelect = document.querySelector('.goog-te-combo');
    if (googleSelect) {
        // Map 'ny' to 'ny' (Chichewa) and 'tum' to 'tum' (Tumbuka)
        // Google Translate uses these codes
        googleSelect.value = lang;
        googleSelect.dispatchEvent(new Event('change'));
    }

    try {
        await fetch('/naos/api/set_lang.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ lang: currentLang })
        });
    } catch (err) {
        console.error('Failed to sync language to session:', err);
    }
}

function toggleLangDropdown(event) {
    event.stopPropagation();
    const switcher = document.getElementById('langSwitcher');
    const button = document.getElementById('langToggle');
    if (!switcher || !button) return;
    const expanded = switcher.classList.toggle('open');
    button.setAttribute('aria-expanded', expanded.toString());
}

function closeLangDropdown() {
    const switcher = document.getElementById('langSwitcher');
    const button = document.getElementById('langToggle');
    if (switcher) switcher.classList.remove('open');
    if (button) button.setAttribute('aria-expanded', 'false');
}

document.addEventListener('DOMContentLoaded', () => {
    syncTranslations();
    const langButton = document.getElementById('langToggle');
    const langDropdownItems = document.querySelectorAll('.lang-dropdown-item');
    const langSwitcher = document.getElementById('langSwitcher');

    if (langButton) {
        langButton.addEventListener('click', toggleLangDropdown);
    }

    langDropdownItems.forEach(item => {
        item.addEventListener('click', event => {
            event.preventDefault();
            event.stopPropagation();
            const selectedLang = event.currentTarget.dataset.lang;
            setLanguage(selectedLang);
        });
    });

    document.addEventListener('click', () => {
        if (langSwitcher && langSwitcher.classList.contains('open')) {
            closeLangDropdown();
        }
    });

    // Auto-trigger Google Translate on load if not English AND NOT landing page
    if (currentLang !== 'en' && !isLandingPage()) {
        const checkGoogleLoad = setInterval(() => {
            const googleSelect = document.querySelector('.goog-te-combo');
            if (googleSelect) {
                googleSelect.value = currentLang;
                googleSelect.dispatchEvent(new Event('change'));
                clearInterval(checkGoogleLoad);
            }
        }, 500);
        // Timeout after 5 seconds to avoid infinite loop
        setTimeout(() => clearInterval(checkGoogleLoad), 5000);
    }
});

function showError(msg) {
    const errorDiv = document.getElementById('errorMsg');
    if (errorDiv) {
        errorDiv.textContent = msg;
        errorDiv.style.display = 'block';
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else {
        showToast(msg, 'error');
    }
}

function showLogin() {
    const loginSection = document.getElementById('loginSection');
    const registerSection = document.getElementById('registerSection');
    const forgotSection = document.getElementById('forgotPasswordSection');
    if (loginSection) loginSection.style.display = 'block';
    if (registerSection) registerSection.style.display = 'none';
    if (forgotSection) forgotSection.style.display = 'none';
    const err = document.getElementById('errorMsg');
    if (err) err.style.display = 'none';

    // Reset forgot password steps
    const steps = ['forgotStep1', 'forgotStep2', 'forgotStepConfirm', 'forgotStep3'];
    steps.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = id === 'forgotStep1' ? 'block' : 'none';
    });

    const container = document.querySelector('.form-container');
    if (container) container.classList.remove('scrollable');
}

function showRegister() {
    const loginSection = document.getElementById('loginSection');
    const registerSection = document.getElementById('registerSection');
    const forgotSection = document.getElementById('forgotPasswordSection');
    if (loginSection) loginSection.style.display = 'none';
    if (registerSection) registerSection.style.display = 'block';
    if (forgotSection) forgotSection.style.display = 'none';
    const err = document.getElementById('errorMsg');
    if (err) err.style.display = 'none';

    const container = document.querySelector('.form-container');
    if (container) container.classList.add('scrollable');
}

function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');
    if (!input) return;

    if (input.type === 'password') {
        input.type = 'text';
        button.classList.add('active');
        button.setAttribute('aria-label', 'Hide password');
        if (icon) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    } else {
        input.type = 'password';
        button.classList.remove('active');
        button.setAttribute('aria-label', 'Show password');
        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}

let forgotPhone = '';
let forgotCode = '';
let forgotTimerId = null;

function showForgotPassword() {
    const loginSection = document.getElementById('loginSection');
    const registerSection = document.getElementById('registerSection');
    const forgotSection = document.getElementById('forgotPasswordSection');
    if (loginSection) loginSection.style.display = 'none';
    if (registerSection) registerSection.style.display = 'none';
    if (forgotSection) forgotSection.style.display = 'block';

    const s1 = document.getElementById('forgotStep1');
    const s2 = document.getElementById('forgotStep2');
    const s3 = document.getElementById('forgotStep3');
    if (s1) s1.style.display = 'block';
    if (s2) s2.style.display = 'none';
    if (s3) s3.style.display = 'none';

    const err = document.getElementById('errorMsg');
    if (err) err.style.display = 'none';

    const container = document.querySelector('.form-container');
    if (container) container.classList.remove('scrollable');
}

function startForgotTimer() {
    const btn = document.getElementById('resendForgotBtn');
    const timerSpan = document.getElementById('forgotTimerSpan');
    const timerDisplay = document.getElementById('forgotTimer');
    if (!btn || !timerSpan || !timerDisplay) return;

    btn.style.display = 'none';
    timerSpan.style.display = 'inline';
    let timeLeft = 60;
    timerDisplay.textContent = timeLeft;

    forgotTimerId = setInterval(() => {
        timeLeft--;
        timerDisplay.textContent = timeLeft;
        if (timeLeft <= 0) {
            clearInterval(forgotTimerId);
            btn.style.display = 'inline';
            timerSpan.style.display = 'none';
        }
    }, 1000);
}

async function resendForgotCode() {
    const msgDiv = document.getElementById('resendForgotMsg');
    if (!msgDiv) return;
    msgDiv.textContent = 'Sending...';
    msgDiv.style.color = '#666';

    try {
        const response = await fetch('/naos/auth/forgot_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'send_code', phone: forgotPhone })
        });
        const data = await response.json();
        if (data.success) {
            msgDiv.textContent = 'Code resent successfully!';
            msgDiv.style.color = 'green';
            startForgotTimer();
        } else {
            msgDiv.textContent = data.error || 'Failed to resend code.';
            msgDiv.style.color = 'red';
        }
    } catch (error) {
        msgDiv.textContent = 'Network error.';
        msgDiv.style.color = 'red';
    }
}

document.getElementById('forgotPhoneForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    forgotPhone = document.getElementById('forgotPhone').value.trim();

    if (!/^0\d{9}$/.test(forgotPhone)) {
        showError('Phone number must be exactly 10 digits and start with 0');
        return;
    }

    try {
        const response = await fetch('/naos/auth/forgot_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'send_code', phone: forgotPhone })
        });
        const data = await response.json();
        if (data.success) {
            document.getElementById('forgotStep1').style.display = 'none';
            document.getElementById('forgotStep2').style.display = 'block';
            document.getElementById('forgotPhoneDisplay').textContent = forgotPhone;
            startForgotTimer();
        } else {
            showError(data.error || 'Failed to send code');
        }
    } catch (error) {
        showError('Network error');
    }
});

document.getElementById('forgotVerifyForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    forgotCode = document.getElementById('forgotCode').value.trim();
    try {
        const response = await fetch('/naos/auth/forgot_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'verify_code', phone: forgotPhone, code: forgotCode })
        });
        const data = await response.json();
        if (data.success) {
            document.getElementById('forgotStep2').style.display = 'none';
            document.getElementById('forgotStepConfirm').style.display = 'block';
            document.getElementById('forgotConfirmUsername').textContent = data.username;
            if (forgotTimerId) clearInterval(forgotTimerId);
        } else {
            showError(data.error || 'Invalid code');
        }
    } catch (error) {
        showError('Network error');
    }
});

document.getElementById('forgotResetForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const newPassword = document.getElementById('forgotNewPassword').value;
    const confirmPassword = document.getElementById('forgotConfirmPassword').value;
    if (newPassword !== confirmPassword) {
        showError('Passwords do not match');
        return;
    }
    if (newPassword.length < 6) {
        showError('Password must be at least 6 characters');
        return;
    }
    try {
        const response = await fetch('/naos/auth/forgot_password.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'reset_password',
                phone: forgotPhone,
                code: forgotCode,
                new_password: newPassword
            })
        });
        const data = await response.json();
        if (data.success) {
            showToast('Password reset successfully!', 'success');
            showLogin();
            document.getElementById('forgotPhoneForm').reset();
            document.getElementById('forgotVerifyForm').reset();
            document.getElementById('forgotResetForm').reset();
        } else {
            showError(data.error || 'Failed to reset password');
        }
    } catch (error) {
        showError('Network error');
    }
});

window.confirmForgotPasswordAccount = function () {
    document.getElementById('forgotStepConfirm').style.display = 'none';
    document.getElementById('forgotStep3').style.display = 'block';
};

document.getElementById('loginForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const username = document.getElementById('loginUsername').value.trim();
    const password = document.getElementById('loginPassword').value;
    try {
        const response = await fetch('/naos/auth/login_process.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password, lang: currentLang })
        });
        const data = await response.json();
        if (data.success) {
            if (data.redirect) {
                window.location.href = data.phone ? data.redirect + '?phone=' + encodeURIComponent(data.phone) : data.redirect;
                return;
            }
            localStorage.setItem('user_id', data.user_id);
            if (data.role === 'admin') window.location.href = '/naos/admin/dashboard.php';
            else if (data.role === 'buyer') window.location.href = '/naos/buyer/dashboard.php';
            else window.location.href = '/naos/farmer/dashboard.php';
        } else {
            showError(data.error || t('login_failed'));
        }
    } catch (error) {
        showError('Network error');
    }
});

// Toggle farmer-specific fields visibility based on role selection
function toggleFarmerFields() {
    const farmerCheckbox = document.querySelector('input[name="roles"][value="farmer"]');
    const nationalIdContainer = document.getElementById('nationalIdContainer');
    const farmerIdContainer = document.getElementById('farmerIdContainer');
    const nationalIdInput = document.getElementById('regNationalId');
    const farmerIdInput = document.getElementById('regFarmerId');
    
    if (!farmerCheckbox) return;
    
    const isFarmerSelected = farmerCheckbox.checked;
    
    // Show/hide farmer-specific containers
    if (nationalIdContainer) {
        nationalIdContainer.style.display = isFarmerSelected ? 'block' : 'none';
    }
    if (farmerIdContainer) {
        farmerIdContainer.style.display = isFarmerSelected ? 'block' : 'none';
    }
    
    // Update required attribute
    if (nationalIdInput) {
        if (isFarmerSelected) {
            nationalIdInput.setAttribute('required', 'required');
        } else {
            nationalIdInput.removeAttribute('required');
        }
    }
    if (farmerIdInput) {
        if (isFarmerSelected) {
            farmerIdInput.setAttribute('required', 'required');
        } else {
            farmerIdInput.removeAttribute('required');
        }
    }
}

// Add event listeners to role checkboxes for dynamic field toggling
document.addEventListener('DOMContentLoaded', () => {
    const roleCheckboxes = document.querySelectorAll('.role-checkbox');
    roleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', toggleFarmerFields);
    });
    // Initialize on page load
    toggleFarmerFields();
});

document.getElementById('registerForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const username = document.getElementById('regUsername').value.trim();
    const password = document.getElementById('regPassword').value;
    const confirmPassword = document.getElementById('regConfirmPassword').value;
    const gender = document.getElementById('regGender').value;
    const location = document.getElementById('regLocation').value.trim();
    const phone = document.getElementById('regPhone').value.trim();
    const roleCheckboxes = document.querySelectorAll('.role-checkbox:checked');
    const roles = Array.from(roleCheckboxes).map(cb => cb.value);
    const nationalIdInput = document.getElementById('regNationalId');
    const farmerIdInput = document.getElementById('regFarmerId');

    if (username.length < 8) {
        showError('Username must be at least 8 characters long');
        return;
    }

    if (password.length < 6) {
        showError('Password must be at least 6 characters long');
        return;
    }

    if (password !== confirmPassword) {
        showError('Passwords do not match');
        return;
    }

    if (!/^0\d{9}$/.test(phone)) {
        showError('Phone number must be exactly 10 digits and start with 0');
        return;
    }

    if (roles.length === 0) {
        showError('Please select at least one role');
        return;
    }

    // Only validate farmer-specific fields if farmer role is selected
    if (roles.includes('farmer')) {
        if (!nationalIdInput || !nationalIdInput.files[0]) {
            showError('Please upload your National ID document');
            return;
        }

        // Validate file size (max 5 MB)
        if (nationalIdInput.files[0].size > 5 * 1024 * 1024) {
            showError('National ID file must be under 5 MB');
            return;
        }

        if (!farmerIdInput || !farmerIdInput.value.trim()) {
            showError('Please provide your Farmer ID');
            return;
        }
    }

    // Use FormData so the file can be uploaded
    const formData = new FormData();
    formData.append('username', username);
    formData.append('password', password);
    formData.append('gender', gender);
    formData.append('location', location);
    formData.append('phone', phone);
    formData.append('lang', currentLang);
    
    // Only append farmer-specific fields if farmer role is selected
    if (roles.includes('farmer')) {
        if (nationalIdInput && nationalIdInput.files[0]) {
            formData.append('national_id', nationalIdInput.files[0]);
        }
        if (farmerIdInput) {
            formData.append('farmer_id', farmerIdInput.value.trim());
        }
    }
    
    roles.forEach(r => formData.append('roles[]', r));

    try {
        const response = await fetch('/naos/auth/register_process.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            if (data.redirect) {
                window.location.href = data.redirect + '?phone=' + encodeURIComponent(data.phone);
            } else {
                showToast(t('registered'), 'success');
                showLogin();
                document.getElementById('registerForm').reset();
            }
        } else {
            showError(data.error || t('error'));
        }
    } catch (error) {
        showError('Network error');
    }
});

async function getWeather() {
    const weatherEl = document.getElementById('weather-data');
    if (!weatherEl) return;
    weatherEl.innerHTML = t('loading_weather');

    const farmSelector = document.getElementById('weatherFarmSelector');
    if (farmSelector && farmSelector.value) {
        fetchWeather(null, null, farmSelector.value);
    } else if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (pos) => fetchWeather(pos.coords.latitude, pos.coords.longitude),
            () => fetchWeather()
        );
    } else {
        fetchWeather();
    }

    async function fetchWeather(lat = null, lon = null, farmId = null) {
        try {
            let url = '/naos/farmer/weather.php';
            if (farmId) {
                url += `?farm_id=${farmId}`;
            } else if (lat && lon) {
                url += `?lat=${lat}&lon=${lon}`;
            }
            const res = await fetch(url);
            if (!res.ok) { weatherEl.innerHTML = 'Weather data unavailable.'; return; }
            const data = await res.json();
            if (data.error) { weatherEl.innerHTML = data.error; return; }
            let html = `<p><strong>Current:</strong> ${data.current_weather.temperature}°C</p>`;
            html += '<table><tr><th>Day</th><th>Max</th><th>Min</th><th>Precip</th></tr>';
            data.daily.time.forEach((date, i) => {
                html += `<tr><td>${new Date(date).toDateString()}</td><td>${data.daily.temperature_2m_max[i]}°C</td><td>${data.daily.temperature_2m_min[i]}°C</td><td>${data.daily.precipitation_sum[i]}mm</td></tr>`;
            });
            html += '</table>';
            if (data.alert) html += `<p style="color:red;">${data.alert}</p>`;
            weatherEl.innerHTML = html;
        } catch (error) { weatherEl.innerHTML = 'Error: ' + error.message; }
    }
}

// Ensure function is exposed globally so the dropdown onchange can call it
window.fetchWeatherForSelectedFarm = getWeather;

async function getCropRec() {
    const recEl = document.getElementById('crop_rec');
    if (!recEl) return;
    recEl.textContent = t('loading_advice');

    const farmSelector = document.getElementById('recFarmSelector');
    if (farmSelector && farmSelector.value) {
        fetchRec(null, null, farmSelector.value);
    } else if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (pos) => fetchRec(pos.coords.latitude, pos.coords.longitude),
            () => fetchRec()
        );
    } else { 
        fetchRec(); 
    }

    async function fetchRec(lat = null, lon = null, farmId = null) {
        try {
            let url = '/naos/farmer/crop_rec.php';
            if (farmId) {
                url += `?farm_id=${farmId}`;
            } else if (lat && lon) {
                url += `?lat=${lat}&lon=${lon}`;
            }
            const res = await fetch(url);
            const data = await res.json();
            recEl.innerHTML = data.recommendation; // Using innerHTML if we return formatted HTML
        } catch (e) { recEl.textContent = 'Advice unavailable.'; }
    }
}

// Make globally available if it needs to be called from inline onclicks
window.getCropRec = getCropRec;

document.getElementById('cropPlanForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const crop = document.getElementById('plannedCrop').value;
    const qty = document.getElementById('plannedQty').value;
    const ds = document.getElementById('decisionSupport');
    if (ds) ds.innerHTML = t('loading_advice');
    try {
        const res = await fetch(`/naos/farmer/decision_support.php?crop=${crop}&qty=${qty}`);
        const data = await res.json();
        if (ds) ds.innerHTML = data.advice;
    } catch (e) { if (ds) ds.innerHTML = 'Advice unavailable.'; }
});

async function getMarket() {
    const marketEl = document.getElementById('market-data');
    if (!marketEl) return;
    try {
        const res = await fetch('/naos/api/market.php');
        const data = await res.json();
        let html = '<div class="market-grid">';
        for (let [crop, price] of Object.entries(data.prices)) {
            html += `<div class="market-card"><h3>${crop}</h3><p class="price">${price} <span class="unit">MWK/kg</span></p></div>`;
        }
        html += '</div>';
        marketEl.innerHTML = html;
    } catch (e) { marketEl.innerHTML = 'Market data unavailable.'; }
}

document.getElementById('activityType')?.addEventListener('change', function () {
    const hf = document.getElementById('harvestFields');
    const hq = document.getElementById('harvestQuantity');
    if (this.value === 'harvested') {
        if (hf) hf.style.display = 'block';
        if (hq) hq.required = true;
    } else {
        if (hf) hf.style.display = 'none';
        if (hq) hq.required = false;
        if (hq) hq.value = '';
    }
});

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('farmForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = {
            farm_id: document.getElementById('diaryFarmSelector')?.value,
            crop_name: document.getElementById('cropName').value,
            activity_type: document.getElementById('activityType').value,
            date: document.getElementById('activityDate').value,
            notes: document.getElementById('activityNotes').value
        };
        if (formData.activity_type === 'harvested') {
            formData.quantity = document.getElementById('harvestQuantity').value;
            formData.unit = document.getElementById('harvestUnit').value;
        }
        try {
            const res = await fetch('farm_entry.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
            const data = await res.json();
            if (data.success) {
                showToast('Activity recorded!', 'success');
                e.target.reset();
                const hf = document.getElementById('harvestFields');
                if (hf) hf.style.display = 'none';
                
                // Refresh data
                if (typeof loadReminders === 'function') loadReminders();
                if (typeof loadRecentActivities === 'function') loadRecentActivities();
                if (typeof closeActivityModal === 'function') closeActivityModal();
            } else {
                showToast(data.error || 'Error recording activity.', 'error');
            }
        } catch (e) {
            console.error('Submission error:', e);
            showToast('Network error.', 'error');
        }
    });
});

async function getFarmerReports() {
    const res = await fetch(`/naos/farmer/reports.php?user_id=${localStorage.getItem('user_id')}`);
    const data = await res.json();
    let table = '<tr><th>Date</th><th>Activity</th><th>Yield</th><th>Income</th></tr>';
    if (data.records) {
        data.records.forEach(r => table += `<tr><td>${r.date}</td><td>${r.activity}</td><td>${r.yield}</td><td>${r.income}</td></tr>`);
    }
    const reportsEl = document.getElementById('reports');
    if (reportsEl) reportsEl.innerHTML = table;
}

document.getElementById('listingForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const quantity = parseFloat(formData.get('quantity'));
    const price = parseFloat(formData.get('price'));

    if (isNaN(quantity) || quantity <= 0) {
        showToast('Quantity must be greater than 0', 'error');
        return;
    }

    if (isNaN(price) || price <= 0) {
        showToast('Price must be greater than 0', 'error');
        return;
    }

    formData.append('user_id', localStorage.getItem('user_id'));
    formData.append('action', 'add');
    const res = await fetch('/naos/api/listings.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
        showToast(t('listing_added'), 'success');
        e.target.reset();
        getListings();
    } else { 
        showToast(data.error || 'Error adding listing.', 'error'); 
    }
});

async function getListings() {
    const isFarmer = window.location.pathname.includes('farmer/dashboard.php');
    const formData = new FormData();
    formData.append('action', 'get');
    if (isFarmer) formData.append('user_only', '1');
    
    try {
        const res = await fetch('/naos/api/listings.php', { method: 'POST', body: formData });
        const data = await res.json();
        
        if (isFarmer) {
            const grid = document.getElementById('farmer-listings-grid');
            if (!grid) return;
            
            if (!data.listings || data.listings.length === 0) {
                grid.innerHTML = '<p style="color:#6b7280; font-size:0.9rem; grid-column:1/-1; text-align:center; padding:2rem 0;" data-t="listings_appear_here">Your active listings will appear here.</p>';
                return;
            }
            
            let html = '';
            data.listings.forEach(l => {
                const img = l.produce_image 
                    ? `<img src="/naos/${l.produce_image}" alt="${l.produce_type}" style="width:100%; height:120px; object-fit:cover; border-radius:8px 8px 0 0;">` 
                    : `<div style="width:100%; height:120px; background:linear-gradient(135deg,#1b4d3e,#2d7a5f); border-radius:8px 8px 0 0; display:flex; align-items:center; justify-content:center;"><i class="fa-solid fa-wheat-awn" style="font-size:2rem; color:rgba(255,255,255,0.4);"></i></div>`;
                
                const unit = l.unit || 'kg';
                html += `
                <div style="border:1px solid var(--border-color); border-radius:10px; overflow:hidden; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,0.05);">
                    ${img}
                    <div style="padding:1rem;">
                        <h4 style="margin:0 0 0.5rem 0; text-transform:capitalize; color:var(--text-color);">${l.produce_type}</h4>
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem; font-size:0.85rem;">
                            <span style="color:#6b7280;">Quantity:</span>
                            <span style="font-weight:600;">${l.quantity} ${unit}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:1rem; font-size:0.85rem;">
                            <span style="color:#6b7280;">Price:</span>
                            <span style="font-weight:600; color:var(--primary-color);">MWK ${Number(l.price).toLocaleString()}/${unit}</span>
                        </div>
                        <button onclick="deleteListing(${l.id})" style="width:100%; padding:0.5rem; background:#fee2e2; color:#dc2626; border:1px solid #fca5a5; border-radius:6px; font-size:0.85rem; font-weight:600; cursor:pointer; transition:0.2s;" onmouseover="this.style.background='#fca5a5'" onmouseout="this.style.background='#fee2e2'">
                            <i class="fa-solid fa-trash-can"></i> Delete
                        </button>
                    </div>
                </div>`;
            });
            grid.innerHTML = html;
        } else {
            const grid = document.getElementById('listings-grid');
            if (!grid) return;
            
            if (!data.listings || data.listings.length === 0) {
                grid.innerHTML = '<p style="grid-column:1/-1; text-align:center; padding:3rem; color:#9ca3af;"><i class="fa-solid fa-store" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>No active produce listings available at the moment.</p>';
                return;
            }
            
            let html = '';
            data.listings.forEach(l => {
                const img = l.produce_image 
                    ? `<img src="/naos/${l.produce_image}" alt="${l.produce_type}" style="width:100%; height:140px; object-fit:cover; border-radius:8px 8px 0 0;">` 
                    : `<div style="width:100%; height:140px; background:linear-gradient(135deg,#1b4d3e,#2d7a5f); border-radius:8px 8px 0 0; display:flex; align-items:center; justify-content:center;"><i class="fa-solid fa-wheat-awn" style="font-size:2rem; color:rgba(255,255,255,0.4);"></i></div>`;
                
                const unit = l.unit || 'kg';
                html += `
                <div style="border:1px solid var(--border-color); border-radius:10px; overflow:hidden; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,0.05); transition:transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
                    ${img}
                    <div style="padding:1rem;">
                        <h4 style="margin:0 0 0.5rem 0; text-transform:capitalize; color:var(--text-color);">${l.produce_type}</h4>
                        <div style="font-size:0.8rem; color:#6b7280; margin-bottom:0.75rem;">
                            <i class="fa-solid fa-user" style="width:14px;"></i> ${l.username}<br>
                            <i class="fa-solid fa-location-dot" style="width:14px;"></i> ${l.location || 'N/A'}
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:0.25rem; font-size:0.85rem;">
                            <span style="color:#6b7280;">Available:</span>
                            <span style="font-weight:600;">${l.quantity} ${unit}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:1rem; font-size:0.85rem;">
                            <span style="color:#6b7280;">Price:</span>
                            <span style="font-weight:600; color:var(--primary-color); font-size:0.9rem;">MWK ${Number(l.price).toLocaleString()}/${unit}</span>
                        </div>
                        <button class="btn-primary" onclick="Cart.add(${l.id}, '${l.produce_type.replace(/'/g,"\\'")}', ${l.price}, ${parseFloat(l.quantity)}, '${(l.username||'').replace(/'/g,"\\'")}')" style="width:100%; padding:0.6rem; border-radius:6px; font-size:0.85rem;">
                            <i class="fa-solid fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>`;
            });
            grid.innerHTML = html;
        }
    } catch (e) {
        console.error("Failed to fetch listings:", e);
    }
}

async function deleteListing(id) {
    showConfirm('Delete listing?', async () => {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        const res = await fetch('/naos/api/listings.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
            showToast('Listing deleted', 'success');
            getListings();
        } else {
            showToast('Failed to delete.', 'error');
        }
    });
}

async function updateOrderStatus(orderId, status) {
    try {
        const res = await fetch('/naos/api/orders.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'update_status', order_id: orderId, status: status })
        });
        const data = await res.json();
        if (data.success) {
            showToast('Order status updated successfully!', 'success');
            if (typeof getIncomingOrders === 'function') getIncomingOrders();
            if (typeof getOrders === 'function') getOrders();
        } else {
            showToast('Error: ' + (data.error || 'Failed to update status'), 'error');
        }
    } catch (e) {
        showToast('Network error', 'error');
    }
}

async function getOrders() {
    const res = await fetch(`/naos/api/orders.php?user_id=${localStorage.getItem('user_id')}`);
    const data = await res.json();
    let table = '<tr style="background:#f8fafc;"><th>Product / Service</th><th>Qty</th><th>Order Status</th><th>Payment</th><th>Action</th></tr>';
    if (data.orders) {
        data.orders.forEach(o => {
            let label = getStatusLabel(o.status, o.payment_status, o.escrow_status);
            let payBtn = o.payment_status === 'paid' ? '<span style="color:#10b981;font-weight:600;"><i class="fa-solid fa-circle-check"></i> Paid</span>' : '';
            if (o.payment_status === 'unpaid') {
                if (o.pending_count > 0) {
                    payBtn = `
                        <div style="text-align:center;">
                            <span style="color:#f59e0b;font-weight:600;display:block;"><i class="fa-solid fa-hourglass-half fa-spin"></i> Processing...</span>
                            <a href="javascript:void(0)" onclick="getOrders()" style="font-size:11px;color:#4f46e5;text-decoration:underline;display:inline-block;margin-top:2px;">Check Status</a>
                        </div>`;
                } else if (o.type === 'subscription' || o.status === 'confirmed') {
                    payBtn = `<button onclick="initiatePayment(${o.id})" style="background:#ff6a00;color:white;padding:6px 14px;border:none;border-radius:4px;cursor:pointer;font-weight:600;box-shadow:0 2px 4px rgba(255,106,0,0.2);">Pay Now</button>`;
                } else {
                    payBtn = '<span style="color:#94a3b8;font-size:0.8rem;">Awaiting Seller Confirmation</span>';
                }
            }

            let actionBtn = '';
            if (o.status === 'pending') {
                actionBtn = `<button onclick="updateOrderStatus(${o.id}, 'cancelled')" class="btn-secondary btn-sm" style="background:#fff1f2;color:#e11d48;padding:4px 10px;font-size:0.75rem;border:1px solid #fecdd3;border-radius:4px;cursor:pointer;">Cancel</button>`;
            } else if (o.status === 'shipped') {
                actionBtn = `<button onclick="confirmReceipt(${o.id})" class="btn-primary btn-sm" style="background:#1b4d3e;color:white;padding:6px 14px;font-size:0.75rem;border-radius:4px;font-weight:600;cursor:pointer;border:none;">Confirm Delivery</button>`;
            }

            let tradeAssurance = o.type !== 'subscription' ? '<div style="display:inline-flex;align-items:center;background:#eef2ff;color:#4f46e5;padding:2px 6px;border-radius:4px;font-size:0.65rem;font-weight:600;margin-top:4px;border:1px solid #c7d2fe;"><i class="fa-solid fa-shield-halved" style="margin-right:4px;"></i> Trade Assurance</div>' : '';
            let statusHtml = `<span class="badge status-${o.status}" style="font-weight:600;padding:4px 10px;border-radius:12px;display:inline-block;">${label}</span>`;
            if (o.courier_name) {
                statusHtml += `<br><small style="color:#1b4d3e;">${o.courier_name}: ${o.tracking_number}</small>`;
            }

            table += `<tr>
                <td style="padding:15px 10px;">
                    <div style="font-weight:600;color:#1e293b;">${o.produce_type || o.type}</div>
                    ${tradeAssurance}
                </td>
                <td style="text-align:center;">${o.quantity}</td>
                <td>${statusHtml}</td>
                <td>${payBtn}</td>
                <td>${actionBtn}</td>
            </tr>`;
        });
    }
    const ordersEl = document.getElementById('orders-table');
    if (ordersEl) ordersEl.innerHTML = table;
}

async function getIncomingOrders() {
    const res = await fetch('/naos/api/orders.php?action=incoming');
    const data = await res.json();
    let table = '<tr style="background:#f8fafc;"><th>Product Details</th><th>Qty</th><th>Buyer Info</th><th>Trade Status</th><th>Operation</th></tr>';
    if (data.orders) {
        data.orders.forEach(o => {
            let label = getStatusLabel(o.status, o.payment_status, o.escrow_status);
            let actions = `<button onclick="viewOrder(${o.id})" class="btn-primary btn-sm" style="padding:5px 12px;font-size:0.75rem;background:#1e293b;color:white;border:none;border-radius:4px;cursor:pointer;">Manage</button> `;
            
            if (o.status === 'pending') {
                actions += `<button onclick="updateOrderStatus(${o.id}, 'confirmed')" class="btn-primary btn-sm" style="background:#1b4d3e;padding:5px 12px;font-size:0.75rem;color:white;border:none;border-radius:4px;cursor:pointer;font-weight:600;">Accept</button> `;
                actions += `<button onclick="updateOrderStatus(${o.id}, 'cancelled')" class="btn-secondary btn-sm" style="background:#fff1f2;color:#e11d48;padding:4px 10px;font-size:0.75rem;border:1px solid #fecdd3;border-radius:4px;cursor:pointer;">Reject</button>`;
            } else if (o.status === 'confirmed' && o.escrow_status === 'paid') {
                actions += `<button onclick="openCourierModal(${o.id})" class="btn-primary btn-sm" style="background:#ff6a00;padding:6px 14px;font-size:0.75rem;color:white;border:none;border-radius:4px;cursor:pointer;font-weight:600;box-shadow:0 2px 4px rgba(255,106,0,0.2);"><i class="fa-solid fa-truck-ramp-box"></i> Dispatch Now</button>`;
            } else if (o.status === 'confirmed' && o.escrow_status !== 'paid') {
                actions += `<div style="color:#64748b;font-size:0.7rem;margin-top:5px;"><i class="fa-solid fa-clock"></i> Awaiting Payment</div>`;
            } else if (o.status === 'shipped') {
                actions += `<div style="color:#1b4d3e;font-size:0.75rem;font-weight:600;"><i class="fa-solid fa-truck-fast"></i> In Transit</div>`;
            }

            let escrowHtml = o.escrow_status === 'paid' ? '<div style="color:#1b4d3e;font-weight:700;font-size:0.7rem;"><i class="fa-solid fa-shield-check"></i> ESCROW PAID</div>' : (o.escrow_status === 'released' ? '<div style="color:#10b981;font-weight:700;font-size:0.7rem;"><i class="fa-solid fa-check-double"></i> FUNDS RELEASED</div>' : '<div style="color:#94a3b8;font-size:0.7rem;">UNPAID</div>');

            table += `<tr>
                <td style="padding:15px 10px;"><strong>${o.produce_type}</strong></td>
                <td style="text-align:center;">${o.quantity}</td>
                <td>
                    <div style="font-weight:600;color:#1e293b;">${o.buyer_name}</div>
                    <div style="font-size:0.75rem;color:#64748b;">${o.buyer_contact}</div>
                </td>
                <td>
                    <span class="badge status-${o.status}" style="font-weight:600;padding:4px 10px;border-radius:12px;display:inline-block;">${label}</span>
                    <div style="margin-top:5px;">${escrowHtml}</div>
                </td>
                <td>${actions}</td>
            </tr>`;
        });
    }
    const incomingOrdersEl = document.getElementById('incoming-orders-table');
    if (incomingOrdersEl) incomingOrdersEl.innerHTML = table;
}

async function viewOrder(orderId) {
    const res = await fetch('/naos/api/orders.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'view', order_id: orderId })
    });
    const data = await res.json();
    if (data.success && data.order) {
        const o = data.order;
        const modalHtml = `
            <div id="orderDetailModal" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);display:flex;justify-content:center;align-items:center;z-index:9999;padding:20px;">
                <div style="background:white;width:100%;max-width:500px;border-radius:12px;overflow:hidden;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);animation: modalFadeIn 0.3s ease-out;">
                    <div style="background:#1e293b;color:white;padding:20px;display:flex;justify-content:space-between;align-items:center;">
                        <h3 style="margin:0;font-size:1.25rem;"><i class="fa-solid fa-file-invoice" style="margin-right:10px;color:#38bdf8;"></i>Order Summary #${o.id}</h3>
                        <button onclick="document.getElementById('orderDetailModal').remove()" style="background:none;border:none;color:#94a3b8;cursor:pointer;font-size:1.5rem;transition:0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#94a3b8'">&times;</button>
                    </div>
                    <div style="padding:24px;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:20px;padding:12px;background:#f8fafc;border-radius:8px;border-left:4px solid #3b82f6;">
                            <span style="color:#64748b;font-weight:500;">Current Status</span>
                            <span style="font-weight:700;color:#1e293b;">${getStatusLabel(o.status, o.payment_status, o.escrow_status)}</span>
                        </div>
                        
                        <div style="margin-bottom:20px;">
                            <div style="color:#64748b;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;font-weight:700;">Item Details</div>
                            <div style="padding:15px;border:1px solid #e2e8f0;border-radius:10px;">
                                <div style="font-weight:700;color:#1e293b;font-size:1.1rem;margin-bottom:4px;">${o.produce_type || o.type}</div>
                                <div style="display:flex;justify-content:space-between;color:#475569;font-size:0.95rem;">
                                    <span>Quantity:</span>
                                    <span style="font-weight:600;">${o.quantity} units</span>
                                </div>
                                ${o.price_per_unit ? `
                                <div style="display:flex;justify-content:space-between;color:#475569;font-size:0.95rem;margin-top:4px;">
                                    <span>Total Value:</span>
                                    <span style="font-weight:700;color:#1b4d3e;">MWK ${parseFloat(o.quantity * o.price_per_unit).toLocaleString()}</span>
                                </div>
                                ` : ''}
                            </div>
                        </div>

                        <div style="margin-bottom:20px;">
                            <div style="color:#64748b;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;font-weight:700;">Buyer Details</div>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="width:40px;height:40px;background:#e2e8f0;border-radius:50%;display:flex;justify-content:center;align-items:center;color:#64748b;">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div>
                                    <div style="font-weight:600;color:#1e293b;">${o.buyer_name}</div>
                                    <div style="color:#64748b;font-size:0.9rem;"><i class="fa-solid fa-phone" style="margin-right:5px;font-size:0.8rem;"></i>${o.buyer_phone || o.buyer_contact}</div>
                                </div>
                            </div>
                        </div>

                        ${o.courier_name ? `
                        <div style="background:#f0f9ff;border:1px solid #bae6fd;padding:15px;border-radius:10px;">
                            <div style="color:#0369a1;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;font-weight:700;"><i class="fa-solid fa-truck-fast" style="margin-right:6px;"></i>Logistics Tracking</div>
                            <div style="font-weight:700;color:#0c4a6e;">${o.courier_name}</div>
                            <div style="font-family:monospace;font-size:1rem;color:#0284c7;background:white;padding:4px 8px;border-radius:4px;display:inline-block;margin-top:5px;border:1px solid #e0f2fe;">${o.tracking_number}</div>
                        </div>
                        ` : ''}
                    </div>
                    <div style="padding:16px 24px;background:#f8fafc;display:flex;justify-content:flex-end;gap:10px;border-top:1px solid #e2e8f0;align-items:center;">
                        ${o.status === 'pending' ? `
                            <button onclick="updateOrderStatus(${o.id}, 'confirmed'); document.getElementById('orderDetailModal').remove();" style="background:#1b4d3e;padding:8px 16px;font-size:0.9rem;color:white;border:none;border-radius:6px;cursor:pointer;font-weight:600;">Accept Order</button>
                            <button onclick="updateOrderStatus(${o.id}, 'cancelled'); document.getElementById('orderDetailModal').remove();" style="background:#fff1f2;color:#e11d48;padding:8px 16px;font-size:0.9rem;border:1px solid #fecdd3;border-radius:6px;cursor:pointer;">Reject</button>
                        ` : ''}
                        
                        ${o.status === 'confirmed' && o.escrow_status === 'paid' ? `
                            <button onclick="document.getElementById('orderDetailModal').remove(); openCourierModal(${o.id});" style="background:#ff6a00;padding:8px 16px;font-size:0.9rem;color:white;border:none;border-radius:6px;cursor:pointer;font-weight:600;box-shadow:0 2px 4px rgba(255,106,0,0.2);"><i class="fa-solid fa-truck-ramp-box" style="margin-right:6px;"></i> Dispatch Commodity</button>
                        ` : ''}
                        
                        ${o.status === 'confirmed' && o.escrow_status !== 'paid' ? `
                            <div style="color:#64748b;font-size:0.85rem;margin-right:auto;"><i class="fa-solid fa-clock" style="margin-right:4px;"></i> Awaiting Buyer Payment</div>
                        ` : ''}
                        
                        <button onclick="document.getElementById('orderDetailModal').remove()" style="padding:8px 16px;border-radius:6px;border:1px solid #cbd5e1;background:white;cursor:pointer;font-weight:600;color:#475569;transition:0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">Close</button>
                    </div>
                </div>
            </div>
            <style>
                @keyframes modalFadeIn {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            </style>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    } else {
        showToast('Error loading details.', 'error');
    }
}

function placeOrder(listingId, type, maxQty) {
    const qty = prompt(`Enter quantity for ${type} (max ${maxQty}):`);
    if (!qty || qty <= 0 || qty > maxQty) { if (qty > maxQty) alert('Exceeds stock.'); return; }
    const contact = prompt('Contact details:');
    if (!contact) { alert('Contact required.'); return; }
    fetch('/naos/api/orders.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'add', listing_id: listingId, quantity: qty, buyer_contact: contact })
    }).then(res => res.json()).then(data => {
        if (data.success) {
            showToast('Order placed!', 'success');
            getListings();
        } else {
            showToast('Error: ' + data.error, 'error');
        }
    });
}

let isProcessingPayment = false;

async function initiatePayment(orderId) {
    if (isProcessingPayment) return;
    
    const phone = prompt('Enter your Airtel Money number (e.g., 099xxxxxxx):');
    if (!phone) return;
    
    isProcessingPayment = true;
    const loader = showLoading('Initiating payment request...');
    
    try {
        const res = await fetch('/naos/api/payment_init.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: orderId, phone: phone })
        });
        const data = await res.json();
        
        if (data.status === 'success') {
            if (data.checkout_url) {
                window.location.href = data.checkout_url;
            } else {
                loader.updateText('Please enter the PIN on your phone to complete the payment...');
                
                // Set a timeout to check status after 25 seconds
                setTimeout(async () => {
                    if (data.order_reference) {
                        loader.updateText('Verification in progress. Please wait...');
                        // Redirect to callback for server-side status check
                        window.location.href = '/naos/api/payment_callback.php?ref=' + data.order_reference;
                    } else {
                        loader.hide();
                        isProcessingPayment = false;
                        showToast('Transaction initiated. Please check your dashboard for updates.', 'info');
                        if (typeof getOrders === 'function') getOrders();
                    }
                }, 25000); 
            }
        } else {
            loader.hide();
            isProcessingPayment = false;
            showToast('Payment failed: ' + (data.error || 'Unknown error'), 'error');
        }
    } catch (e) { 
        loader.hide();
        isProcessingPayment = false;
        showToast('Connection error. Please try again.', 'error'); 
    }
}

async function getBuyerAdvice() {
    const res = await fetch('/naos/buyer/advice.php');
    const data = await res.json();
    const adviceEl = document.getElementById('buyerAdvice');
    if (adviceEl) adviceEl.innerHTML = data.advice;
}

function confirmLogout() {
    showConfirm(t('confirm_logout'), () => {
        window.location.href = '/naos/auth/logout.php';
    });
}

function toggleNotifications() {
    const dropdown = document.getElementById('notifDropdown');
    if (dropdown) dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

async function getNotifications() {
    try {
        const res = await fetch('/naos/api/notifications.php');
        const data = await res.json();
        const list = document.getElementById('notifList');
        const badge = document.getElementById('notifBadge');
        if (!list || !badge) return;
        list.innerHTML = '';
        if (data.notifications && data.notifications.length > 0) {
            badge.textContent = data.notifications.filter(n => n.is_read == 0).length;
            badge.style.display = badge.textContent === '0' ? 'none' : 'block';
            data.notifications.forEach(n => {
                const li = document.createElement('li');
                li.style.padding = '0.5rem';
                li.style.borderBottom = '1px solid #eee';
                li.style.backgroundColor = n.is_read == 0 ? '#f0f9ff' : 'white';
                li.textContent = n.message;
                list.appendChild(li);
            });
        } else {
            list.innerHTML = '<li style="padding:0.5rem;color:#777;">No notifications</li>';
            badge.style.display = 'none';
        }
    } catch (e) { console.error(e); }
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.location.pathname.includes('farmer/dashboard.php')) {
        getWeather();
        getMarket();
        getCropRec();
        getNotifications();
        setInterval(getNotifications, 30000);
    } else if (window.location.pathname.includes('buyer/dashboard.php')) {
        getMarket();
        getBuyerAdvice();
        getNotifications();
        setInterval(getNotifications, 30000);
        
        // Auto-refresh orders every 10 seconds to track payment status
        getOrders();
        setInterval(getOrders, 10000);
    }
});

async function confirmReceipt(orderId) {
    showConfirm('Confirm that you have received the commodity? This will release the funds to the farmer.', async () => {
        try {
            const res = await fetch('/naos/api/orders.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'confirm_receipt', order_id: orderId })
            });
            const data = await res.json();
            if (data.success) {
                showToast('Receipt confirmed! Funds released.', 'success');
                getIncomingOrders();
            } else {
                showToast('Error: ' + data.error, 'error');
            }
        } catch (e) { showToast('Network error', 'error'); }
    });
}

async function confirmReceipt(orderId) {
    showConfirm('Confirm that you have received the commodity? This will release the funds to the farmer.', async () => {
        try {
            const res = await fetch('/naos/api/orders.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'confirm_receipt', order_id: orderId })
            });
            const data = await res.json();
            if (data.success) {
                showToast('Receipt confirmed! Funds released.', 'success');
                getOrders();
            } else {
                showToast('Error: ' + data.error, 'error');
            }
        } catch (e) { showToast('Network error', 'error'); }
    });
}

function openCourierModal(orderId) {
    // Generate a random CTS tracking number
    const trackingNo = 'CTS-' + Math.floor(100000 + Math.random() * 900000) + '-MW';
    
    // Create the modal overlay
    const modalHtml = `
        <div id="ctsCourierModal" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); display:flex; align-items:center; justify-content:center; z-index:9999;">
            <div style="background:#fff; width:450px; border-radius:12px; overflow:hidden; box-shadow:0 15px 40px rgba(0,0,0,0.2); animation:slideDown 0.3s ease;">
                <div style="background:#1e3a8a; color:#fff; padding:20px; text-align:center;">
                    <div style="font-size:40px; margin-bottom:10px;"><i class="fa-solid fa-truck-fast"></i></div>
                    <h3 style="margin:0; font-size:20px; font-weight:700;">CTS Courier Service</h3>
                    <p style="margin:5px 0 0; font-size:13px; opacity:0.8;">Secure. Reliable. Nationwide.</p>
                </div>
                
                <div style="padding:25px;">
                    <p style="margin:0 0 15px; font-size:14px; color:#475569;">
                        You are about to hand over this commodity to CTS Courier for delivery to the buyer.
                    </p>
                    
                    <div style="background:#f1f5f9; border:1px dashed #cbd5e1; border-radius:8px; padding:15px; text-align:center; margin-bottom:20px;">
                        <div style="font-size:12px; color:#64748b; font-weight:600; text-transform:uppercase;">Tracking Number Generated</div>
                        <div style="font-size:22px; font-weight:bold; color:#1e293b; margin-top:5px; letter-spacing:1px;">${trackingNo}</div>
                    </div>
                    
                    <div style="background:#ecfdf5; border-left:4px solid #10b981; padding:12px 15px; border-radius:4px; margin-bottom:25px;">
                        <div style="font-size:13px; color:#065f46;">
                            <i class="fa-solid fa-shield-halved" style="margin-right:5px;"></i>
                            <strong>Escrow Protection Active</strong><br>
                            The funds for this order are securely held in the NAOS Escrow. Your money will be automatically released to your account as soon as the delivery is confirmed!
                        </div>
                    </div>
                    
                    <div style="display:flex; justify-content:flex-end; gap:10px;">
                        <button onclick="document.getElementById('ctsCourierModal').remove()" style="padding:10px 15px; border:none; background:#e2e8f0; color:#475569; border-radius:6px; cursor:pointer; font-weight:600;">Cancel</button>
                        <button onclick="simulateAndSubmitCourier(${orderId}, 'CTS Courier', '${trackingNo}')" id="ctsConfirmBtn" style="padding:10px 20px; border:none; background:#1e3a8a; color:#fff; border-radius:6px; cursor:pointer; font-weight:600; transition:0.2s;">
                            Confirm Dispatch <i class="fa-solid fa-arrow-right" style="margin-left:5px;"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <style>
            @keyframes slideDown {
                from { transform: translateY(-20px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
        </style>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function simulateAndSubmitCourier(orderId, courier, tracking) {
    const btn = document.getElementById('ctsConfirmBtn');
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Dispatching...';
    btn.disabled = true;
    
    // Simulate API delay for courier registration
    setTimeout(() => {
        submitCourierDetails(orderId, courier, tracking);
    }, 1500);
}

async function submitCourierDetails(orderId, courier, tracking) {
    try {
        const res = await fetch('/naos/api/orders.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'update_courier', order_id: orderId, courier: courier, tracking: tracking })
        });
        const raw = await res.text();
        let data;
        try {
            data = JSON.parse(raw);
        } catch(e) {
            console.error("Raw response:", raw);
            showToast('Server returned: ' + raw.substring(0, 50), 'error');
            return;
        }
        
        const modal = document.getElementById('ctsCourierModal');
        if (modal) modal.remove();
        
        if (data.success) {
            showToast('Commodity Dispatched! Tracking: ' + tracking, 'success');
            // Optional alert for the exact details if you want it explicitly shown:
            alert(`Commodity successfully handed over to ${courier}.\nTracking No: ${tracking}\n\nYou will receive your funds once the delivery is complete.`);
            getIncomingOrders();
        } else {
            showToast('Error: ' + data.error, 'error');
        }
    } catch (e) { 
        showToast('Network error: ' + e.message, 'error');
        const modal = document.getElementById('ctsCourierModal');
        if (modal) modal.remove();
    }
}

function getStatusLabel(status, payment_status, escrow_status) {
    if (status === 'pending') return 'Awaiting Confirmation';
    if (status === 'confirmed' && payment_status === 'unpaid') return 'Awaiting Payment';
    if (status === 'confirmed' && escrow_status === 'paid') return 'Awaiting Dispatch';
    if (status === 'shipped') return 'Awaiting Receipt';
    if (status === 'delivered') return 'Completed';
    if (status === 'cancelled') return 'Cancelled';
    if (!status) return 'Unknown';
    return status.charAt(0).toUpperCase() + status.slice(1);
}


