/**
 * NAOS Buyer Cart
 * Client-side cart using localStorage.
 * Each item: { listingId, name, price, maxQty, qty, farmer }
 */

const Cart = (() => {
    const KEY = 'naos_cart';

    function load() {
        try { return JSON.parse(localStorage.getItem(KEY)) || []; }
        catch { return []; }
    }

    function save(items) {
        localStorage.setItem(KEY, JSON.stringify(items));
        _updateBadge();
        _renderDrawer();
    }

    function add(listingId, name, price, maxQty, farmer) {
        const items = load();
        const existing = items.find(i => i.listingId === listingId);
        if (existing) {
            if (existing.qty < maxQty) {
                existing.qty++;
                showToast(`${name} quantity updated.`, 'info');
            } else {
                showToast(`Max available stock reached for ${name}.`, 'warning');
                return;
            }
        } else {
            items.push({ listingId, name, price: parseFloat(price), maxQty: parseInt(maxQty), qty: 1, farmer });
        }
        save(items);
        showToast(`${name} added to cart.`, 'success');
    }

    function remove(listingId) {
        save(load().filter(i => i.listingId !== listingId));
    }

    function updateQty(listingId, qty) {
        const items = load();
        const item = items.find(i => i.listingId === listingId);
        if (!item) return;
        qty = parseInt(qty);
        if (qty <= 0) { remove(listingId); return; }
        if (qty > item.maxQty) { showToast('Exceeds available stock.', 'warning'); return; }
        item.qty = qty;
        save(items);
    }

    function clear() {
        localStorage.removeItem(KEY);
        _updateBadge();
        _renderDrawer();
    }

    function total() {
        return load().reduce((sum, i) => sum + i.price * i.qty, 0);
    }

    function count() {
        return load().reduce((sum, i) => sum + i.qty, 0);
    }

    /* ---- UI ---- */

    function _updateBadge() {
        const badge = document.getElementById('cart-badge');
        if (!badge) return;
        const n = count();
        badge.textContent = n;
        badge.style.display = n > 0 ? 'flex' : 'none';
    }

    function openDrawer() {
        const drawer = document.getElementById('cart-drawer');
        const overlay = document.getElementById('cart-overlay');
        if (drawer) drawer.classList.add('open');
        if (overlay) overlay.classList.add('active');
        _renderDrawer();
    }

    function closeDrawer() {
        const drawer = document.getElementById('cart-drawer');
        const overlay = document.getElementById('cart-overlay');
        if (drawer) drawer.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
    }

    function _renderDrawer() {
        const body = document.getElementById('cart-items');
        const footerTotal = document.getElementById('cart-total');
        const checkoutBtn = document.getElementById('cart-checkout-btn');
        if (!body) return;

        const items = load();

        if (items.length === 0) {
            body.innerHTML = `
                <div style="text-align:center; padding: 3rem 1rem; color: #999;">
                    <i class="fa-solid fa-cart-shopping" style="font-size: 3rem; margin-bottom: 1rem; display:block; opacity:.3;"></i>
                    Your cart is empty.<br>
                    <small>Browse listings and click <strong>Add to Cart</strong>.</small>
                </div>`;
            if (footerTotal) footerTotal.textContent = 'MWK 0';
            if (checkoutBtn) checkoutBtn.disabled = true;
            return;
        }

        body.innerHTML = items.map(item => `
            <div class="cart-item" id="ci-${item.listingId}">
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-sub">
                        ${item.farmer ? `<span><i class="fa-solid fa-user-tie fa-xs"></i> ${item.farmer}</span>` : ''}
                        <span>MWK ${item.price.toLocaleString()}/kg</span>
                    </div>
                </div>
                <div class="cart-item-controls">
                    <button class="cart-qty-btn" onclick="Cart.updateQty(${item.listingId}, ${item.qty - 1})">
                        <i class="fa-solid fa-minus"></i>
                    </button>
                    <input type="number" class="cart-qty-input" value="${item.qty}"
                        min="1" max="${item.maxQty}"
                        onchange="Cart.updateQty(${item.listingId}, this.value)">
                    <button class="cart-qty-btn" onclick="Cart.updateQty(${item.listingId}, ${item.qty + 1})">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                    <button class="cart-remove-btn" onclick="Cart.remove(${item.listingId})" title="Remove">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
                <div class="cart-item-total">MWK ${(item.price * item.qty).toLocaleString()}</div>
            </div>
        `).join('');

        const t = total();
        if (footerTotal) footerTotal.textContent = `MWK ${t.toLocaleString()}`;
        if (checkoutBtn) checkoutBtn.disabled = false;
    }

    async function checkout() {
        const items = load();
        if (items.length === 0) return;

        const contact = prompt('Enter your contact number for the farmers to reach you:');
        if (!contact || contact.trim() === '') {
            showToast('Contact number is required to place orders.', 'warning');
            return;
        }

        const checkoutBtn = document.getElementById('cart-checkout-btn');
        if (checkoutBtn) { checkoutBtn.disabled = true; checkoutBtn.textContent = 'Placing orders...'; }

        let successCount = 0;
        let failCount = 0;

        for (const item of items) {
            try {
                const res = await fetch('/naos/api/orders.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'add',
                        listing_id: item.listingId,
                        quantity: item.qty,
                        buyer_contact: contact.trim()
                    })
                });
                const data = await res.json();
                if (data.success) {
                    successCount++;
                } else {
                    failCount++;
                    // Show specific error from server if available
                    if (data.message || data.error) {
                        showToast(`${item.name}: ${data.message || data.error}`, 'error');
                    }
                }
            } catch (err) {
                failCount++;
                console.error('Order error:', err);
            }
        }

        if (checkoutBtn) { checkoutBtn.disabled = false; checkoutBtn.textContent = 'Place Orders'; }

        if (successCount > 0) {
            showToast(`${successCount} order(s) placed successfully! Farmers will contact you at ${contact}.`, 'success');
            clear();
            closeDrawer();
            // Refresh orders list if that section is visible
            if (typeof getOrders === 'function') getOrders();
        }
        if (failCount > 0) {
            showToast(`${failCount} order(s) failed. Please try again.`, 'error');
        }
    }

    /* ---- Init ---- */
    function init() {
        _updateBadge();
        // Close drawer on overlay click
        const overlay = document.getElementById('cart-overlay');
        if (overlay) overlay.addEventListener('click', closeDrawer);
    }

    const instance = { add, remove, updateQty, clear, total, count, openDrawer, closeDrawer, checkout, init };
    window.Cart = instance;
    return instance;
})();

document.addEventListener('DOMContentLoaded', () => Cart.init());
