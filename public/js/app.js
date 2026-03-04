/* ============================================================
   app.js – e-Rapor Sisipan
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {

    // ── Sidebar Toggle (mobile) ──────────────────────────────
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    sidebarToggle?.addEventListener('click', () => {
        sidebar?.classList.toggle('open');
    });
    // Close sidebar when clicking outside
    document.addEventListener('click', (e) => {
        if (sidebar?.classList.contains('open') &&
            !sidebar.contains(e.target) &&
            e.target !== sidebarToggle) {
            sidebar.classList.remove('open');
        }
    });

    // ── Notification Dropdown ────────────────────────────────
    const notifBtn      = document.getElementById('notifBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifWrapper  = document.getElementById('notifWrapper');

    notifBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        notifDropdown?.classList.toggle('open');
    });
    document.addEventListener('click', (e) => {
        if (!notifWrapper?.contains(e.target)) {
            notifDropdown?.classList.remove('open');
        }
    });

    // ── Flash Message Auto-close ─────────────────────────────
    document.querySelectorAll('.alert').forEach(alert => {
        // Auto dismiss after 5s
        const timer = setTimeout(() => dismissAlert(alert), 5000);
        alert.querySelector('.alert-close')?.addEventListener('click', () => {
            clearTimeout(timer);
            dismissAlert(alert);
        });
    });
    function dismissAlert(el) {
        el.style.transition = 'opacity .3s ease';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 300);
    }

    // ── Modal System ─────────────────────────────────────────
    window.openModal = function(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.add('open');
            document.body.style.overflow = 'hidden';
            // Focus first input
            setTimeout(() => {
                el.querySelector('input:not([type=hidden]):not([disabled]),' +
                                 'select:not([disabled])')?.focus();
            }, 50);
        }
    };
    window.closeModal = function(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.remove('open');
            document.body.style.overflow = '';
        }
    };
    // Close on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.remove('open');
                document.body.style.overflow = '';
            }
        });
    });
    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.open').forEach(m => {
                m.classList.remove('open');
                document.body.style.overflow = '';
            });
        }
    });

    // ── Active Nav Highlight ──────────────────────────────────
    const currentPage = new URLSearchParams(window.location.search).get('page');
    document.querySelectorAll('.nav-item').forEach(item => {
        const href     = item.getAttribute('href') || '';
        const navPage  = new URLSearchParams(href.split('?')[1] || '').get('page');
        if (navPage && currentPage && (currentPage === navPage || currentPage.startsWith(navPage))) {
            item.classList.add('active');
        }
    });

});
