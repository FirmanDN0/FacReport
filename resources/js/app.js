import './bootstrap';
import './echo';

// Real-time Notification Listener
if (window.Echo) {
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    
    if (userId) {
        window.Echo.private(`App.Models.User.${userId}`)
            .notification((notification) => {
                console.log('New notification:', notification);
                
                // Show a toast
                showToast(notification.message, notification.url);
                
                // Update unread dot if it exists
                const unreadDot = document.querySelector('.unread-dot');
                if (!unreadDot) {
                    const bell = document.querySelector('.nav-notification');
                    if (bell) {
                        const dot = document.createElement('span');
                        dot.className = 'unread-dot';
                        bell.appendChild(dot);
                    }
                }
            });
    }
}

function showToast(message, url) {
    const toast = document.createElement('div');
    toast.className = 'realtime-toast';
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-bell"></i>
            <div class="toast-text">
                <p>${message}</p>
                <a href="${url}">Lihat Detail</a>
            </div>
            <button onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    `;
    
    // Add toast to body or container
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    container.appendChild(toast);
    
    // Auto remove after 10 seconds
    setTimeout(() => {
        if (toast.parentElement) toast.remove();
    }, 10000);
}
