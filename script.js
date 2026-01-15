// script.js - API helper functions
async function apiFetch(path, method = 'POST', data = null) {
    try {
        const options = {
            method: method,
            headers: {}
        };

        if (method === 'POST' && data) {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(data);
        } else if (method === 'GET' && data) {
            const params = new URLSearchParams(data);
            path += '?' + params.toString();
        }

        const response = await fetch(path, options);
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        return { ok: false, message: 'Network error. Please check your connection.' };
    }
}

// Utility functions
function formatCurrency(amount) {
    return '₹' + parseFloat(amount).toFixed(2);
}

function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('en-IN', options);
}

function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-message">${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;

    // Add styles if not already added
    if (!document.querySelector('#notification-styles')) {
        const styles = document.createElement('style');
        styles.id = 'notification-styles';
        styles.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                animation: slideInRight 0.3s ease;
            }
            .notification-content {
                background: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                display: flex;
                align-items: center;
                gap: 15px;
                min-width: 300px;
                border-left: 4px solid #667eea;
            }
            .notification-success .notification-content { border-left-color: #10b981; }
            .notification-error .notification-content { border-left-color: #ef4444; }
            .notification-warning .notification-content { border-left-color: #f59e0b; }
            .notification-close {
                background: none;
                border: none;
                font-size: 18px;
                cursor: pointer;
                color: #666;
            }
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(styles);
    }

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Form validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[0-9]{10}$/;
    return re.test(phone);
}

// Local storage helpers
function getCurrentUser() {
    const user = localStorage.getItem('dbg_user');
    return user ? JSON.parse(user) : null;
}

function isAuthenticated() {
    return localStorage.getItem('dbg_auth') === 'true';
}

function logout() {
    localStorage.removeItem('dbg_auth');
    localStorage.removeItem('dbg_user');
    window.location.href = 'index.html';
}