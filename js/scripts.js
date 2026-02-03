// Authentication Check
async function checkAuth() {
    try {
        const response = await fetch('backend/auth/check_auth.php');
        const data = await response.json();
        if (data.is_authenticated) {
            document.getElementById('auth-link').innerText = 'Logout';
            document.getElementById('auth-link').href = 'backend/auth/logout.php';
            document.getElementById('mobile-auth-link').innerText = 'Logout';
            document.getElementById('mobile-auth-link').href = 'backend/auth/logout.php';
            if (data.is_admin) {
                document.getElementById('admin-link').classList.remove('hidden');
                document.getElementById('mobile-admin-link').classList.remove('hidden');
            }
        }
        return data.is_authenticated;
    } catch (error) {
        console.error('Auth check failed:', error);
        return false;
    }
}

// CSRF Token Fetch
async function fetchCsrfToken() {
    try {
        const response = await fetch('backend/auth/csrf.php');
        const data = await response.json();
        return data.csrf_token;
    } catch (error) {
        console.error('Failed to fetch CSRF token:', error);
        return '';
    }
}

// Notification System
function showNotification(message, type) {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.classList.remove('hidden', 'success', 'error');
    notification.classList.add(type);
    setTimeout(() => {
        notification.classList.add('hidden');
    }, 3000);
}

// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', () => {
    const hamburger = document.querySelector('.hamburger');
    const mobileMenu = document.getElementById('mobile-menu');

    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        mobileMenu.classList.toggle('active');
    });

    // Initialize auth status
    checkAuth();

    // Load notifications
    loadNotifications();
});

// Load Notifications
async function loadNotifications() {
    try {
        const response = await fetch('backend/notifications/get_notifications.php');
        const notifications = await response.json();
        notifications.forEach(notification => {
            showNotification(notification.message, notification.type);
        });
    } catch (error) {
        console.error('Failed to load notifications:', error);
    }
}