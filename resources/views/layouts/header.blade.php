<!--  Header Start -->
<header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
                <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link nav-icon-hover position-relative" href="#" 
                   id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-bell-ringing"></i>
                    <span class="notification-badge bg-primary rounded-circle position-absolute start-100 translate-middle badge badge-sm" 
                          id="notificationCount" style="display: none;">
                        0
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown" 
                     aria-labelledby="notificationDropdown">
                    <div class="notification-header p-3 border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="mb-0">Notifikasi</h6>
                            <button class="btn btn-sm btn-link text-decoration-none p-0" id="markAllReadBtn" type="button">
                                Tandai semua dibaca
                            </button>
                        </div>
                    </div>
                    <div id="notificationList" class="notification-list-container">
                        <!-- Notifikasi akan dimuat di sini -->
                        <div class="text-center p-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                <li class="nav-item">
                    <p class="text-muted mb-0 me-3">{{ Auth::user()->nama }}</p>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                       aria-expanded="false">
                        <img src="{{ asset('assets/images/profile/user-1.jpg') }}" alt="" width="35" height="35" class="rounded-circle">
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>
<!--  Header End -->

<style>
/* Force header and notification to be on top */
.app-header {
    position: sticky;
    top: 0;
    z-index: 1050 !important;
    background: #fff;
}

/* Sidebar harus lebih rendah */
.left-sidebar {
    z-index: 1040 !important;
}

/* Notification Dropdown */
.notification-dropdown {
    min-width: 380px;
    max-width: 400px;
    border: none;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
    border-radius: 8px;
    padding: 0;
    z-index: 1060 !important;
    margin-top: 8px !important;
}

/* Notification Badge */
.notification-badge {
    font-size: 0.65rem;
    padding: 0.25em 0.5em;
    min-width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* List Container */
.notification-list-container {
    max-height: 400px;
    width: 300px;
    overflow-y: auto;
    overflow-x: hidden;
}

/* Scrollbar */
.notification-list-container::-webkit-scrollbar {
    width: 6px;
}

.notification-list-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.notification-list-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

/* Notification Item */
.notification-item {
    padding: 14px 16px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background-color 0.2s;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #e7f3ff;
}

.notification-item.unread:hover {
    background-color: #d0e7ff;
}

/* Icons */
.notification-icon {
    width: 40px;
    height: 40px;
    min-width: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.notification-icon.info { background-color: #e3f2fd; color: #1976d2; }
.notification-icon.success { background-color: #e8f5e9; color: #388e3c; }
.notification-icon.warning { background-color: #fff3e0; color: #f57c00; }
.notification-icon.danger { background-color: #ffebee; color: #d32f2f; }

/* Text */
.notification-text {
    flex: 1;
    min-width: 0;
}

.notification-text p {
    margin-bottom: 4px;
    line-height: 1.4;
    font-size: 0.9rem;
}

.notification-time {
    font-size: 0.75rem;
    color: #999;
}

/* Empty State */
.notification-empty {
    padding: 3rem 2rem;
    text-align: center;
    color: #999;
}

.notification-empty i {
    font-size: 3rem;
    opacity: 0.3;
}

/* Responsive */
@media (max-width: 576px) {
    .notification-dropdown {
        min-width: 320px;
        max-width: calc(100vw - 40px);
    }
    
    .notification-list-container {
        max-height: 300px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Notification script loaded');
    
    // Load notifikasi
    loadNotifications();
    
    // Auto refresh setiap 30 detik
    setInterval(loadNotifications, 30000);
    
    // Mark all as read button
    const markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            markAllAsRead();
        });
    }
});

function loadNotifications() {
    console.log('Loading notifications...');
    
    fetch('{{ route("notifikasi.index") }}')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Notifications loaded:', data);
            updateNotificationBadge(data.unread_count);
            displayNotifications(data.notifikasi);
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            document.getElementById('notificationList').innerHTML = `
                <div class="text-center p-4 text-danger">
                    <p>Gagal memuat notifikasi</p>
                </div>
            `;
        });
}

function updateNotificationBadge(count) {
    const badge = document.getElementById('notificationCount');
    if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.style.display = 'flex';
    } else {
        badge.style.display = 'none';
    }
}

function displayNotifications(notifikasi) {
    const listContainer = document.getElementById('notificationList');
    
    if (!notifikasi || notifikasi.length === 0) {
        listContainer.innerHTML = `
            <div class="notification-empty">
                <i class="ti ti-bell-off d-block mb-2"></i>
                <p class="mb-0">Tidak ada notifikasi</p>
            </div>
        `;
        return;
    }

    let html = '';
    notifikasi.forEach(notif => {
        const iconType = getIconType(notif.tipe);
        const isUnread = notif.status_baca == 0 ? 'unread' : '';
        const time = formatTime(notif.created_at);
        
        html += `
            <div class="notification-item ${isUnread}" data-id="${notif.id_notifikasi}">
                <div class="d-flex align-items-start">
                    <div class="notification-icon ${iconType} me-3">
                        <i class="ti ${getIcon(notif.tipe)}"></i>
                    </div>
                    <div class="notification-text">
                        <p class="mb-1 ${isUnread ? 'fw-bold' : ''}">${escapeHtml(notif.pesan)}</p>
                        <span class="notification-time">${time}</span>
                    </div>
                    ${isUnread ? '<div class="ms-2"><span class="badge bg-primary" style="font-size: 0.7rem;">Baru</span></div>' : ''}
                </div>
            </div>
        `;
    });

    listContainer.innerHTML = html;
    
    // Add click handlers
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            markAsRead(id);
        });
    });
}

function getIconType(tipe) {
    const types = {
        'info': 'info',
        'success': 'success',
        'warning': 'warning',
        'danger': 'danger',
        'tugas': 'info',
        'jadwal': 'warning',
        'approval': 'success'
    };
    return types[tipe] || 'info';
}

function getIcon(tipe) {
    const icons = {
        'info': 'ti-info-circle',
        'success': 'ti-check',
        'warning': 'ti-alert-triangle',
        'danger': 'ti-alert-circle',
        'tugas': 'ti-clipboard',
        'jadwal': 'ti-calendar',
        'approval': 'ti-circle-check'
    };
    return icons[tipe] || 'ti-bell';
}

function formatTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);

    if (diff < 60) return 'Baru saja';
    if (diff < 3600) return Math.floor(diff / 60) + ' menit yang lalu';
    if (diff < 86400) return Math.floor(diff / 3600) + ' jam yang lalu';
    if (diff < 604800) return Math.floor(diff / 86400) + ' hari yang lalu';
    
    return date.toLocaleDateString('id-ID', { 
        day: 'numeric', 
        month: 'short', 
        year: 'numeric' 
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function markAsRead(id) {
    console.log('Marking as read:', id);
    
    fetch(`/notifikasi/${id}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Marked as read successfully');
            loadNotifications();
        }
    })
    .catch(error => console.error('Error marking as read:', error));
}

function markAllAsRead() {
    console.log('Marking all as read');
    
    fetch('{{ route("notifikasi.readAll") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('All marked as read successfully');
            loadNotifications();
        }
    })
    .catch(error => console.error('Error marking all as read:', error));
}
</script>