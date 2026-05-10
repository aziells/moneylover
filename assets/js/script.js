// ============================================
// FUNGSI UTAMA UNTUK SEMUA HALAMAN
// ============================================

// Fungsi untuk menampilkan notifikasi
function showNotification(message, type = 'info') {
    // Hapus notifikasi sebelumnya
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    // Buat elemen notifikasi
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;

    notification.innerHTML = `
        <div>
            <strong>${type === 'success' ? 'Berhasil!' : type === 'error' ? 'Error!' : 'Info!'}</strong>
            <span>${message}</span>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">&times;</button>
    `;

    document.body.appendChild(notification);

    // Hapus otomatis setelah 5 detik
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Fungsi untuk format Rupiah
function formatRupiah(angka) {
    if (!angka) return 'Rp 0';

    const number = parseInt(angka);
    return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Fungsi untuk set menu aktif
function setActiveMenu() {
    const currentPage = window.location.pathname.split('/').pop();
    const menuItems = document.querySelectorAll('.sidebar-link');

    menuItems.forEach(item => {
        item.classList.remove('active');
        const href = item.getAttribute('href');

        if (href === currentPage) {
            item.classList.add('active');
        }
    });
}

// Fungsi untuk toggle sidebar di mobile
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('show');
    }
}

// Fungsi untuk toggle navbar menu di mobile
function toggleNavbarMenu() {
    const navbarMenu = document.querySelector('.navbar-menu');
    if (navbarMenu) {
        navbarMenu.classList.toggle('show');
    }
}

// Fungsi untuk membuka modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

// Fungsi untuk menutup modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

// Fungsi untuk logout
function logout() {
    if (confirm('Apakah Anda yakin ingin logout?')) {
        showNotification('Logout berhasil!', 'success');
        setTimeout(() => {
            window.location.href = 'index.html';
        }, 1000);
    }
}

// Event listener saat halaman dimuat
document.addEventListener('DOMContentLoaded', function () {
    // Set menu aktif
    setActiveMenu();

    // Event listener untuk logout
    const logoutButtons = document.querySelectorAll('[data-logout]');
    logoutButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            logout();
        });
    });

    // Event listener untuk navbar toggler
    const navbarToggler = document.querySelector('.navbar-toggler');
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function () {
            toggleNavbarMenu();
            toggleSidebar();
        });
    }

    // Event listener untuk menutup modal saat klik di luar
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function (e) {
            if (e.target === this) {
                this.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        });
    });

    // Event listener untuk tombol close modal
    const modalCloses = document.querySelectorAll('.modal-close, [data-dismiss="modal"]');
    modalCloses.forEach(button => {
        button.addEventListener('click', function () {
            const modal = this.closest('.modal');
            if (modal) {
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
            }
        });
    });

    // Menutup navbar menu saat klik di luar pada mobile
    document.addEventListener('click', function (e) {
        const navbarMenu = document.querySelector('.navbar-menu');
        const navbarToggler = document.querySelector('.navbar-toggler');

        if (navbarMenu && navbarToggler && window.innerWidth < 769) {
            if (!navbarMenu.contains(e.target) && !navbarToggler.contains(e.target) && navbarMenu.classList.contains('show')) {
                navbarMenu.classList.remove('show');
            }
        }

        // Menutup dropdown saat klik di luar
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(e.target)) {
                const dropdownMenu = dropdown.querySelector('.dropdown-menu');
                if (dropdownMenu) {
                    dropdownMenu.style.display = 'none';
                }
            }
        });
    });
});

// Fungsi utilitas untuk validasi form
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    const requiredInputs = form.querySelectorAll('[required]');
    let isValid = true;

    requiredInputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('error');
            isValid = false;
        } else {
            input.classList.remove('error');
        }
    });

    return isValid;
}

// CSS untuk error state
const errorStyle = document.createElement('style');
errorStyle.textContent = `
    .form-control.error,
    .form-select.error {
        border-color: var(--crimson) !important;
        box-shadow: 0 0 0 3px rgba(220, 20, 60, 0.2) !important;
    }
`;
document.head.appendChild(errorStyle);