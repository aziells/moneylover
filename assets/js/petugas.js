document.addEventListener('DOMContentLoaded', function () {
    loadPetugas();

    const modal = document.getElementById('modalPetugas');
    const btnTambah = document.getElementById('btnTambahPetugas');
    const closeBtn = document.getElementById('closeModal');
    const btnBatal = document.getElementById('btnBatal');
    const formPetugas = document.getElementById('formPetugas');
    const modalTitle = document.getElementById('modalTitle');
    const passwordHint = document.getElementById('passwordHint');

    // Buka Modal Tambah
    btnTambah.onclick = function () {
        formPetugas.reset();
        document.getElementById('petugasId').value = '';
        modalTitle.innerHTML = '<i class="fas fa-user-plus"></i> <span>Tambah Petugas</span>';
        passwordHint.style.display = 'none';
        document.getElementById('password').required = true;
        openModal('modalPetugas');
    }

    // Tutup Modal
    if (closeBtn) {
        closeBtn.onclick = function () {
            closeModal('modalPetugas');
        }
    }

    btnBatal.onclick = function () {
        closeModal('modalPetugas');
    }

    // Click outside handled globally in script.js

    // Submit Form
    formPetugas.onsubmit = function (e) {
        e.preventDefault();
        const id = document.getElementById('petugasId').value;
        const aksi = id ? 'edit' : 'tambah';
        const formData = new FormData(formPetugas);
        formData.append('aksi', aksi);

        fetch('../proses/proses_petugas.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeModal('modalPetugas');
                    loadPetugas();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan pada server', 'error');
            });
    }
});

function loadPetugas() {
    fetch('../proses/proses_petugas.php?aksi=tampil')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderTable(data.data);
            }
        });
}

function renderTable(data) {
    const tbody = document.getElementById('petugasTableBody');
    tbody.innerHTML = '';

    data.forEach((p, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${index + 1}</td>
            <td>${p.nama}</td>
            <td>${p.username}</td>
            <td>${p.email}</td>
            <td><span class="badge ${p.role === 'admin' ? 'badge-primary' : 'badge-success'}">${p.role}</span></td>
            <td>
                <button class="btn btn-sm btn-outline-info" onclick="editPetugas(${p.id_user})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="hapusPetugas(${p.id_user})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function editPetugas(id) {
    fetch(`../proses/proses_petugas.php?aksi=detail&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const p = data.data;
                document.getElementById('petugasId').value = p.id_user;
                document.getElementById('nama').value = p.nama;
                document.getElementById('username').value = p.username;
                document.getElementById('email').value = p.email;
                document.getElementById('password').value = '';
                document.getElementById('password').required = false;
                document.getElementById('role').value = p.role;

                document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-edit"></i> <span>Edit Petugas</span>';
                document.getElementById('passwordHint').style.display = 'block';
                openModal('modalPetugas');
            }
        });
}

function hapusPetugas(id) {
    if (confirm('Apakah Anda yakin ingin menghapus petugas ini?')) {
        const formData = new FormData();
        formData.append('aksi', 'hapus');
        formData.append('id', id);

        fetch('../proses/proses_petugas.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    loadPetugas();
                } else {
                    showNotification(data.message, 'error');
                }
            });
    }
}
