const Toast = {
    init() {
        if (!document.getElementById('toast-container')) {
            const container = document.createElement('div');
            container.id = 'toast-container';
            document.body.appendChild(container);
        }
        if (!document.getElementById('modal-container')) {
            const modalContainer = document.createElement('div');
            modalContainer.id = 'modal-container';
            document.body.appendChild(modalContainer);
        }
    },

    show(message, type = 'info', duration = 3000) {
        this.init();
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        const icons = {
            success: '<i class="fa-solid fa-circle-check"></i>',
            error: '<i class="fa-solid fa-circle-exclamation"></i>',
            warning: '<i class="fa-solid fa-triangle-exclamation"></i>',
            info: '<i class="fa-solid fa-circle-info"></i>'
        };

        const iconHtml = icons[type] || icons.info;
        toast.innerHTML = `
            <div class="toast-icon">${iconHtml}</div>
            <div class="toast-message">${message}</div>
            <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
        `;

        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('show'));

        if (duration > 0) {
            setTimeout(() => {
                toast.classList.remove('show');
                toast.classList.add('hide');
                toast.addEventListener('transitionend', () => toast.remove());
            }, duration);
        }
    },

    confirm(message, onConfirm, onCancel) {
        this.init();
        const container = document.getElementById('modal-container');
        container.innerHTML = '';
        container.className = 'modal-overlay show';

        const modal = document.createElement('div');
        modal.className = 'confirm-modal';
        modal.innerHTML = `
            <div class="modal-body">
                <p>${message}</p>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel">Cancel</button>
                <button class="btn-confirm">Confirm</button>
            </div>
        `;

        container.appendChild(modal);

        modal.querySelector('.btn-confirm').onclick = () => {
            container.classList.remove('show');
            if (onConfirm) onConfirm();
        };

        modal.querySelector('.btn-cancel').onclick = () => {
            container.classList.remove('show');
            if (onCancel) onCancel();
        };

        container.onclick = (e) => {
            if (e.target === container) {
                container.classList.remove('show');
                if (onCancel) onCancel();
            }
        };
    },
    
    showLoading(message) {
        this.init();
        const container = document.getElementById('modal-container');
        container.innerHTML = '';
        container.className = 'modal-overlay show loading';
        
        const modal = document.createElement('div');
        modal.className = 'confirm-modal loading-modal';
        modal.innerHTML = `
            <div class="modal-body">
                <div class="loader-spinner"></div>
                <p id="loading-text">${message}</p>
            </div>
        `;
        container.appendChild(modal);
        
        return {
            updateText: (newText) => {
                const p = document.getElementById('loading-text');
                if (p) p.textContent = newText;
            },
            close: () => {
                container.classList.remove('show');
            }
        };
    }
};

window.showLoading = (message) => {
    return Toast.showLoading(message);
};

window.showToast = (message, type = 'info') => {
    if (type === 'info' && (message.toLowerCase().includes('error') || message.toLowerCase().includes('failed'))) type = 'error';
    if (type === 'info' && (message.toLowerCase().includes('success') || message.toLowerCase().includes('completed'))) type = 'success';
    Toast.show(message, type);
};

window.showConfirm = (message, onConfirm, onCancel) => {
    Toast.confirm(message, onConfirm, onCancel);
};
