// Global variables
let barangData = [];
let currentPage = 1;
let itemsPerPage = 25;
const apiUrl = 'api_barang.php';

// Format Rupiah
function formatRupiah(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

// Show notification
function showNotification(message, type = 'info') {
    // Check if toast container exists, if not create it
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container';
        toastContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
        document.body.appendChild(toastContainer);
    }

    const toast = document.createElement('div');
    toast.className = `toast show align-items-center text-white bg-${type === 'error' ? 'danger' : (type === 'success' ? 'success' : 'primary')} border-0`;
    toast.role = 'alert';
    toast.ariaLive = 'assertive';
    toast.ariaAtomic = 'true';
    toast.style.cssText = 'min-width: 200px; padding: 15px; margin-bottom: 10px; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: space-between;';

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;

    toastContainer.appendChild(toast);

    // Remove after 3 seconds
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Fetch items from API
async function fetchItems() {
    try {
        const response = await fetch(apiUrl);
        const result = await response.json();

        if (result.status === 'success') {
            barangData = result.data;
            renderBarangTable();
        } else {
            showNotification('Gagal mengambil data baran: ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Error fetching items:', error);
        showNotification('Terjadi kesalahan saat mengambil data', 'error');
    }
}

// Render table
function renderBarangTable() {
    const tbody = document.getElementById('barangTableBody');
    if (!tbody) return;

    tbody.innerHTML = '';

    // Pagination logic
    const totalItems = barangData.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    // Adjust current page if out of bounds
    if (currentPage > totalPages && totalPages > 0) {
        currentPage = totalPages;
    }
    if (currentPage < 1) currentPage = 1;

    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedData = barangData.slice(startIndex, endIndex);

    if (paginatedData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">Tidak ada data barang</td></tr>';
        document.getElementById('tableInfo').textContent = 'Showing 0 to 0 of 0 entries';
        updatePagination(0);
        return;
    }

    paginatedData.forEach((barang, index) => {
        let statusBadge = '';
        if (barang.status === 'tersedia') {
            statusBadge = '<span class="badge badge-success" style="background-color: #28a745; color: white; padding: 5px 10px; border-radius: 20px;">Tersedia</span>';
        } else {
            statusBadge = '<span class="badge badge-danger" style="background-color: #dc3545; color: white; padding: 5px 10px; border-radius: 20px;">Habis</span>';
        }

        const imgPath = barang.gambar ? `../assets/images/items/${barang.gambar}` : '../assets/images/no-image.png';

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${startIndex + index + 1}</td>
            <td>
                <img src="${imgPath}" alt="${barang.nama_menu}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;" onerror="this.src='https://placehold.co/50x50?text=No+Image'">
            </td>
            <td>${barang.id_menu}</td>
            <td>${barang.nama_menu}</td>
            <td class="fw-bold text-success">${formatRupiah(barang.harga_jual)}</td>
            <td>${barang.stok || 0}</td>
            <td>${statusBadge}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="openEditModal(${barang.id_menu})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger ml-1" onclick="deleteBarang(${barang.id_menu})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });

    const tableInfo = document.getElementById('tableInfo');
    if (tableInfo) {
        tableInfo.textContent = `Showing ${startIndex + 1} to ${Math.min(endIndex, totalItems)} of ${totalItems} entries`;
    }

    updatePagination(totalItems);
}

function sortBarangByStok(order) {
    const direction = order === 'desc' ? -1 : 1;

    barangData.sort((a, b) => {
        const stokA = parseInt(a.stok ?? 0, 10) || 0;
        const stokB = parseInt(b.stok ?? 0, 10) || 0;
        return (stokA - stokB) * direction;
    });

    currentPage = 1;
    renderBarangTable();
}

function setStokSortOrder(order) {
    sortBarangByStok(order);
}

function sortStokPrompt() {
    const isDesc = confirm(
        'Urutkan stok dari terbesar ke terkecil?\n\nOK = Terbesar → Terkecil\nCancel = Terkecil → Terbesar'
    );
    sortBarangByStok(isDesc ? 'desc' : 'asc');
    showNotification(
        isDesc ? 'Stok diurutkan: terbesar ke terkecil' : 'Stok diurutkan: terkecil ke terbesar',
        'success'
    );
}

// Open Edit Modal
function openEditModal(id) {
    const barang = barangData.find(b => b.id_menu == id);
    if (!barang) return;

    const modalTitle = document.querySelector('#tambahBarangModal .modal-title');
    if (modalTitle) {
        modalTitle.innerHTML = '<i class="fas fa-edit"></i> <span>Edit Barang</span>';
    }

    document.getElementById('barangId').value = barang.id_menu;
    document.getElementById('namaBarang').value = barang.nama_menu;
    document.getElementById('hargaBarang').value = barang.harga_jual;
    document.getElementById('stokBarang').value = barang.stok || 0;
    document.getElementById('statusBarang').value = barang.status;

    // Reset file input
    document.getElementById('gambarBarang').value = '';

    // Call global openModal from script.js
    openModal('tambahBarangModal');
}

// Reset form when opening for Add
function openAddModal() {
    document.getElementById('formTambahBarang').reset();
    document.getElementById('barangId').value = ''; // clear ID for new adds

    const modalTitle = document.querySelector('#tambahBarangModal .modal-title');
    if (modalTitle) {
        modalTitle.innerHTML = '<i class="fas fa-plus-circle"></i> <span>Tambah Barang Baru</span>';
    }

    document.getElementById('statusBarang').value = 'tersedia';
    // Call global openModal from script.js
    openModal('tambahBarangModal');
}

// Function to handle form submit (Add/Edit)
async function saveBarang(event) {
    event.preventDefault();

    const form = document.getElementById('formTambahBarang');
    const formData = new FormData(form);

    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === 'success') {
            showNotification(result.message, 'success');
            closeModal('tambahBarangModal');
            fetchItems(); // Refresh data
        } else {
            showNotification(result.message, 'error');
        }
    } catch (error) {
        console.error('Error saving item:', error);
        showNotification('Terjadi kesalahan saat menyimpan data', 'error');
    }
}

// Function to delete barang
async function deleteBarang(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus barang ini?')) return;

    try {
        const response = await fetch(apiUrl, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id_menu: id })
        });

        const result = await response.json();

        if (result.status === 'success') {
            showNotification(result.message, 'success');
            // If deleting last item on page, go back one page
            if (barangData.length % itemsPerPage === 1 && currentPage > 1) {
                currentPage--;
            }
            fetchItems();
        } else {
            showNotification(result.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting item:', error);
        showNotification('Terjadi kesalahan saat menghapus data', 'error');
    }
}

// Update Pagination UI
function updatePagination(totalItems) {
    const pagination = document.getElementById('pagination');
    if (!pagination) return;

    const totalPages = Math.ceil(totalItems / itemsPerPage);
    let paginationHTML = '';

    // Previous button
    paginationHTML += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a href="#" class="page-link" onclick="changePage(${currentPage - 1}); return false;">&laquo;</a>
        </li>
    `;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        paginationHTML += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a href="#" class="page-link" onclick="changePage(${i}); return false;">${i}</a>
            </li>
        `;
    }

    // Next button
    paginationHTML += `
        <li class="page-item ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}">
            <a href="#" class="page-link" onclick="changePage(${currentPage + 1}); return false;">&raquo;</a>
        </li>
    `;

    pagination.innerHTML = paginationHTML;
}

function changePage(page) {
    const totalItems = barangData.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    if (page < 1 || page > totalPages) return;

    currentPage = page;
    renderBarangTable();
}

// Initialization
document.addEventListener('DOMContentLoaded', function () {
    // Only run on pages that have the table
    if (document.getElementById('barangTable')) {
        const showEntries = document.getElementById('showEntries');
        if (showEntries) {
            const initial = parseInt(showEntries.value, 10);
            if (!Number.isNaN(initial) && initial > 0) {
                itemsPerPage = initial;
            }

            showEntries.addEventListener('change', () => {
                const next = parseInt(showEntries.value, 10);
                if (!Number.isNaN(next) && next > 0) {
                    itemsPerPage = next;
                    currentPage = 1;
                    renderBarangTable();
                }
            });
        }

        fetchItems();

        const formTambah = document.getElementById('formTambahBarang');
        if (formTambah) {
            formTambah.addEventListener('submit', saveBarang);
        }

        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                fetchItems();
                showNotification('Data direfresh', 'info');
            });
        }

        const refreshDataBtn = document.getElementById('refreshData');
        if (refreshDataBtn) {
            refreshDataBtn.addEventListener('click', () => {
                fetchItems();
                showNotification('Data direfresh', 'info');
            });
        }

        const stokSortOrder = document.getElementById('stokSortOrder');
        if (stokSortOrder) {
            setStokSortOrder(stokSortOrder.value);
            stokSortOrder.addEventListener('change', () => {
                setStokSortOrder(stokSortOrder.value);
            });
        }
    }
});

// Expose functions to global scope for HTML onclick attributes
window.deleteBarang = deleteBarang;
window.changePage = changePage;
window.openEditModal = openEditModal;
window.openAddModal = openAddModal;
window.setStokSortOrder = setStokSortOrder;