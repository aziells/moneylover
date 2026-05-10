// Variabel pagination
let currentPageLaporan = 1;
const itemsPerPageLaporan = 5;
let transaksiData = []; // Global variable untuk data transaksi
let laporanViewData = []; // Dataset yang sedang ditampilkan (full / filtered)

// Fungsi untuk render tabel laporan
function renderLaporanTable(data) {
    const tbody = document.getElementById('laporanTableBody');
    if (!tbody) return;

    tbody.innerHTML = '';

    data.forEach(row => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${row.formatted_date}</td>
            <td>${row.jumlah_transaksi} Transaksi</td>
            <td>${row.kasir}</td>
            <td class="fw-bold">Rp ${parseInt(row.total_pendapatan).toLocaleString('id-ID')}</td>
            <td>
                <a class="btn btn-sm btn-outline-primary" href="detail_laporan.php?tanggal=${encodeURIComponent(row.tanggal)}">
                    <i class="fas fa-eye"></i> Detail
                </a>
            </td>
        `;
        tbody.appendChild(tr);
    });

    const tableInfo = document.getElementById('tableInfo');
    if (tableInfo) {
        tableInfo.textContent = `Menampilkan ${data.length} dari ${laporanViewData.length} transaksi`;
    }

    const infoTransaksiTop = document.getElementById('infoTransaksiTop');
    if (infoTransaksiTop) {
        infoTransaksiTop.textContent = `Menampilkan ${data.length} dari ${laporanViewData.length} transaksi`;
    }

    const infoTransaksiBottom = document.getElementById('infoTransaksiBottom');
    if (infoTransaksiBottom) {
        infoTransaksiBottom.textContent = `Menampilkan ${data.length} dari ${laporanViewData.length} transaksi`;
    }

    // Update pagination
    updateLaporanPagination(data.length);
}

// Fungsi untuk update pagination laporan
function updateLaporanPagination(totalItems) {
    const pagination = document.getElementById('pagination');
    if (!pagination) return;

    const totalPages = Math.ceil(totalItems / itemsPerPageLaporan);

    let paginationHTML = '';

    // Previous button
    paginationHTML += `
        <li class="page-item ${currentPageLaporan === 1 ? 'disabled' : ''}">
            <a href="#" class="page-link" onclick="changeLaporanPage(${currentPageLaporan - 1})">&laquo;</a>
        </li>
    `;

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        paginationHTML += `
            <li class="page-item ${i === currentPageLaporan ? 'active' : ''}">
                <a href="#" class="page-link" onclick="changeLaporanPage(${i})">${i}</a>
            </li>
        `;
    }

    // Next button
    paginationHTML += `
        <li class="page-item ${currentPageLaporan === totalPages ? 'disabled' : ''}">
            <a href="#" class="page-link" onclick="changeLaporanPage(${currentPageLaporan + 1})">&raquo;</a>
        </li>
    `;

    pagination.innerHTML = paginationHTML;
}

// Fungsi untuk change page laporan
function changeLaporanPage(page) {
    const totalPages = Math.ceil(laporanViewData.length / itemsPerPageLaporan);

    if (page < 1 || page > totalPages) return;

    currentPageLaporan = page;
    const startIndex = (page - 1) * itemsPerPageLaporan;
    const endIndex = startIndex + itemsPerPageLaporan;
    const paginatedData = laporanViewData.slice(startIndex, endIndex);

    renderLaporanTable(paginatedData);
}

// Fungsi untuk view detail transaksi
function viewDetail(id) {
    showNotification(`Melihat detail transaksi No. ${id}`, 'info');
}

// Set tanggal default
function setDefaultDates() {
    const tanggalMulai = document.getElementById('tanggalMulai');
    const tanggalSelesai = document.getElementById('tanggalSelesai');

    if (!tanggalMulai || !tanggalSelesai) return;

    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    tanggalMulai.value = formatDate(firstDay);
    tanggalSelesai.value = formatDate(today);
}

// Filter data laporan
function filterData() {
    const pilihBulanEl = document.getElementById('pilihBulan');
    const pilihTahunEl = document.getElementById('pilihTahun');
    const filterKasirEl = document.getElementById('filterKasir');
    const filterMetodeEl = document.getElementById('filterMetodeBayar');
    const searchEl = document.getElementById('searchTransaksi');

    // Halaman laporan versi sederhana tidak punya UI filter.
    // Jangan error; cukup tampilkan data yang ada.
    if (!pilihBulanEl || !pilihTahunEl || !filterKasirEl || !filterMetodeEl || !searchEl) {
        laporanViewData = transaksiData;
        currentPageLaporan = 1;
        renderLaporanTable(laporanViewData.slice(0, itemsPerPageLaporan));
        return;
    }

    const bulan = pilihBulanEl.value;
    const tahun = pilihTahunEl.value;
    const kasir = filterKasirEl.value;
    const metode = filterMetodeEl.value;
    const search = (searchEl.value || '').toLowerCase();

    let filtered = [...transaksiData];

    if (kasir) {
        filtered = filtered.filter(t => t.kasir === kasir);
    }

    if (metode) {
        filtered = filtered.filter(t => t.metode === metode);
    }

    if (search) {
        filtered = filtered.filter(t =>
            t.noTransaksi.toLowerCase().includes(search) ||
            t.kasir.toLowerCase().includes(search) ||
            t.item.toLowerCase().includes(search)
        );
    }

    // Hitung statistik
    const totalTransaksi = filtered.length;
    const totalPendapatan = filtered.reduce((sum, t) => sum + t.total, 0);
    const rataTransaksi = totalTransaksi > 0 ? Math.round(totalPendapatan / totalTransaksi) : 0;
    const totalItemTerjual = filtered.reduce((sum, t) => sum + (parseInt(t.jumlah_item) || 0), 0);

    // Update statistik
    const totalTransaksiEl = document.getElementById('totalTransaksi');
    const totalPendapatanEl = document.getElementById('totalPendapatan');
    const rataTransaksiEl = document.getElementById('rataTransaksi');
    const totalItemTerjualEl = document.getElementById('totalItemTerjual');
    const periodeInfo = document.getElementById('periodeInfo');
    const periodeInfo2 = document.getElementById('periodeInfo2');

    if (totalTransaksiEl) totalTransaksiEl.textContent = totalTransaksi;
    if (totalPendapatanEl) totalPendapatanEl.textContent = formatRupiah(totalPendapatan);
    if (rataTransaksiEl) rataTransaksiEl.textContent = formatRupiah(rataTransaksi);
    if (totalItemTerjualEl) totalItemTerjualEl.textContent = totalItemTerjual;

    if (periodeInfo) {
        periodeInfo.textContent = `Bulan ${bulan ? getMonthName(bulan) + ' ' : ''}${tahun}`;
    }
    if (periodeInfo2) {
        periodeInfo2.textContent = `Bulan ${bulan ? getMonthName(bulan) + ' ' : ''}${tahun}`;
    }

    // Reset ke halaman 1
    currentPageLaporan = 1;
    laporanViewData = filtered;
    renderLaporanTable(filtered.slice(0, itemsPerPageLaporan));

    showNotification(`Menampilkan ${filtered.length} transaksi`, 'success');
}

// Reset filter
function resetFilter() {
    const pilihBulan = document.getElementById('pilihBulan');
    const pilihTahun = document.getElementById('pilihTahun');
    const filterKasir = document.getElementById('filterKasir');
    const filterMetodeBayar = document.getElementById('filterMetodeBayar');
    const searchTransaksi = document.getElementById('searchTransaksi');

    if (pilihBulan) pilihBulan.value = '12';
    if (pilihTahun) pilihTahun.value = '2025';
    if (filterKasir) filterKasir.value = '';
    if (filterMetodeBayar) filterMetodeBayar.value = '';
    if (searchTransaksi) searchTransaksi.value = '';

    setDefaultDates();

    // Hitung ulang statistik berdasarkan semua data
    filterData();

    // Reset ke halaman 1
    currentPageLaporan = 1;
    laporanViewData = transaksiData;
    renderLaporanTable(laporanViewData.slice(0, itemsPerPageLaporan));

    showNotification('Filter telah direset', 'info');
}

// Export to Excel
function exportToExcel() {
    showNotification('Laporan berhasil diekspor ke Excel', 'success');
}

// Print laporan
function printLaporan() {
    window.print();
}

// Helper function untuk nama bulan
function getMonthName(monthNumber) {
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    return months[monthNumber - 1];
}

// Inisialisasi saat halaman laporan dimuat
document.addEventListener('DOMContentLoaded', function () {
    // Cek apakah kita di halaman laporan (API based)
    if (window.location.pathname.includes('laporan.php') ||
        document.querySelector('.sidebar-link[href="laporan.php"].active')) {

        // Render tabel awal
        fetch('get_daily_report.php')
            .then(response => response.json())
            .then(apiResponse => {
                if (apiResponse.status === 'success') {
                    transaksiData = apiResponse.data; // Simpan ke variabel global
                    laporanViewData = transaksiData;

                    // Render tabel awal
                    currentPageLaporan = 1;
                    renderLaporanTable(laporanViewData.slice(0, itemsPerPageLaporan));

                    // Set tanggal default
                    setDefaultDates();

                    // Update statistik awal (jika perlu)
                    filterData();
                } else {
                    const msg = apiResponse.message || 'Gagal mengambil data laporan';
                    showNotification(msg, 'error');
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                showNotification('Gagal mengambil data laporan', 'error');
            });

        // Event listeners
        const filterLaporanBtn = document.getElementById('filterLaporan');
        if (filterLaporanBtn) {
            filterLaporanBtn.addEventListener('click', filterData);
        }

        const resetFilterBtn = document.getElementById('resetFilter');
        if (resetFilterBtn) {
            resetFilterBtn.addEventListener('click', resetFilter);
        }

        const exportLaporanBtn = document.getElementById('exportLaporan');
        if (exportLaporanBtn) {
            exportLaporanBtn.addEventListener('click', exportToExcel);
        }

        const printLaporanBtn = document.getElementById('printLaporan');
        if (printLaporanBtn) {
            printLaporanBtn.addEventListener('click', printLaporan);
        }

        const btnSearch = document.getElementById('btnSearch');
        if (btnSearch) {
            btnSearch.addEventListener('click', filterData);
        }

        const searchTransaksi = document.getElementById('searchTransaksi');
        if (searchTransaksi) {
            searchTransaksi.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    filterData();
                }
            });
        }

        const refreshLaporanBtn = document.getElementById('refreshLaporan');
        if (refreshLaporanBtn) {
            refreshLaporanBtn.addEventListener('click', function () {
                currentPageLaporan = 1;
                renderLaporanTable(transaksiData.slice(0, itemsPerPageLaporan));
                showNotification('Data laporan telah direfresh', 'info');
            });
        }
    }
});