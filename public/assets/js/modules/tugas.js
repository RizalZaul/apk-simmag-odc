/* =============================================================================
 * TUGAS.JS
 * JS Terpusat untuk Modul Manajemen Tugas - Penugasan
 * Hanya fungsi yang TIDAK mengandung PHP vars (base_url, csrf_hash, session)
 *
 * Fungsi dengan PHP vars tetap inline di masing-masing view.
 * =============================================================================
 *
 * DEPENDS ON (didefinisikan inline di view):
 *   window.BASE_URL       — dari main_penugasan.php
 *   window.SASARAN_URL    — dari tugas_sasaran.php (tambah baru)
 *   window.TIM_URL        — dari tugas_sasaran.php (tambah baru)
 *   window.PKL_MEMBER_URL — dari tugas_sasaran.php (tambah baru)
 *
 * SECTIONS:
 *   1.  Utility
 *   2.  Main Penugasan — Tab Loader
 *   3.  Kategori Tugas — DataTable
 *   4.  Tugas — DataTable + Events + Filter
 *   5.  Tugas Tambah — Form, Select2, Flatpickr, Session
 *   6.  Tugas Sasaran — Tabs, Render, Checkbox, Tim, Filter, API Data
 * ============================================================================= */


/* =============================================================================
 * 1. UTILITY
 * ============================================================================= */

function showAlert(icon, title, text) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({ icon, title, text, confirmButtonColor: '#0f766e' });
    } else {
        alert(title + ': ' + text);
    }
}

/**
 * showToast — SweetAlert2 Toast, muncul di pojok kanan atas.
 * @param {string} icon    'success' | 'error' | 'warning' | 'info'
 * @param {string} title   Pesan singkat yang ditampilkan
 * @param {number} timer   Durasi tampil dalam ms (default 2000)
 */
function showToast(icon = 'success', title = '', timer = 2000) {
    if (typeof Swal === 'undefined') { console.info('[Toast]', icon, title); return; }
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    Toast.fire({ icon, title });
}


/* =============================================================================
 * 2. MAIN PENUGASAN — Tab Loader
 * ============================================================================= */

function loadTabContent(tab) {
    const overlay = $('#loadingOverlay');
    const contentArea = $('#tabContentArea');

    const url = tab === 'kategori'
        ? window.BASE_URL + '/load-kategori'
        : window.BASE_URL + '/load-tugas';

    overlay.addClass('active');
    contentArea.html('');

    $.ajax({
        url,
        method: 'GET',
        timeout: 10000,
        success: function (response) {
            contentArea.html(response);
            overlay.removeClass('active');

            setTimeout(function () {
                if ($('#tableKategori').length) {
                    initKategoriTable();
                    initKategoriEvents();
                }
                if ($('#tableTugas').length) {
                    initTugasTable();
                    initTugasEvents();
                }
                if ($('.datepicker').length && typeof flatpickr !== 'undefined') {
                    $('.datepicker').flatpickr({ dateFormat: 'd-m-Y', locale: 'id' });
                }
            }, 100);
        },
        error: function (xhr, status, error) {
            overlay.removeClass('active');

            let msg = 'Unknown error';
            if (status === 'timeout') msg = 'Request timeout (>10s).';
            else if (xhr.status === 404) msg = 'URL not found (404): ' + url;
            else if (xhr.status === 500) msg = 'Server error (500). Cek log server.';
            else msg = error || 'Gagal memuat konten';

            contentArea.html(`
                <div style="text-align:center; padding: 60px;">
                    <i class="fas fa-exclamation-triangle fa-3x" style="color:var(--accent-red); margin-bottom:16px;"></i>
                    <h3>Error Loading Content</h3>
                    <p style="color:var(--text-muted)">${msg}</p>
                    <p style="color:#64748b; font-size:13px">Status: ${xhr.status} | ${status}</p>
                </div>
            `);

            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        }
    });
}


/* =============================================================================
 * 3. KATEGORI TUGAS — DataTable Init
 * ============================================================================= */

function initKategoriTable() {
    if (typeof $.fn.DataTable === 'undefined') return;
    if ($.fn.DataTable.isDataTable('#tableKategori')) $('#tableKategori').DataTable().destroy();

    try {
        window.tableKategori = $('#tableKategori').DataTable({
            paging: true, pageLength: 10,
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            searching: false, ordering: true, order: [[0, 'asc']], info: true, autoWidth: false,
            language: {
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                emptyTable: 'Tidak ada data yang tersedia',
                zeroRecords: 'Tidak ada data yang cocok',
                paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' }
            }
        });
    } catch (e) { console.error('DataTable Kategori error:', e); }
}


/* =============================================================================
 * 4. TUGAS — DataTable Init + Events + Filter
 * ============================================================================= */

function initTugasTable() {
    if (typeof $.fn.DataTable === 'undefined') return;
    if ($.fn.DataTable.isDataTable('#tableTugas')) $('#tableTugas').DataTable().destroy();

    try {
        window.tableTugas = $('#tableTugas').DataTable({
            paging: true, pageLength: 10,
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            searching: false, ordering: true, order: [[0, 'asc']], info: true, autoWidth: false,
            columnDefs: [
                { targets: 2, orderable: true, className: 'text-left' },
                { targets: 6, orderable: false }
            ],
            language: {
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                emptyTable: 'Tidak ada data yang tersedia',
                zeroRecords: 'Tidak ada data yang cocok',
                paginate: { first: 'Pertama', last: 'Terakhir', next: 'Selanjutnya', previous: 'Sebelumnya' }
            }
        });
    } catch (e) { console.error('DataTable Tugas error:', e); }
}

function initTugasEvents() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('#filterKategoriTugas').select2({
            placeholder: 'Semua Kategori', allowClear: true, width: '100%',
            language: { noResults: () => 'Kategori tidak ditemukan', searching: () => 'Mencari...' }
        });
    }

    if (typeof flatpickr !== 'undefined') {
        flatpickr('#filterDeadlineTugas', { dateFormat: 'd-m-Y', locale: 'id' });
    }

    $('#btnFilterTugas').off('click').on('click', function () {
        const filterSection = $('#filterSectionTugas');
        const icon = $(this).find('i');
        const text = $(this).find('.btn-text');

        if (filterSection.hasClass('show')) {
            filterSection.removeClass('show');
            icon.removeClass('fa-arrow-left').addClass('fa-filter');
            text.text('Filter');
        } else {
            filterSection.addClass('show');
            icon.removeClass('fa-filter').addClass('fa-arrow-left');
            text.text('Kembali');
        }
    });

    $('#btnResetFilterTugas').off('click').on('click', function () {
        $('#filterNamaTugas').val('');
        $('#filterKategoriTugas').val('').trigger('change');
        if ($('#filterDeadlineTugas')[0]?._flatpickr) {
            $('#filterDeadlineTugas')[0]._flatpickr.clear();
        } else {
            $('#filterDeadlineTugas').val('');
        }
        $('#tableTugas tbody tr').show();

        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'success', title: 'Filter Direset', timer: 1500, showConfirmButton: false });
        }
    });

    $('#filterNamaTugas').off('keyup').on('keyup', () => _applyTugasFilter());
    $('#filterKategoriTugas').off('change').on('change', () => _applyTugasFilter());
    $('#filterDeadlineTugas').off('change').on('change', () => _applyTugasFilter());
}

function _applyTugasFilter() {
    const nama = $('#filterNamaTugas').val().toLowerCase();
    const kategori = ($('#filterKategoriTugas').val() || '').toLowerCase();
    const deadline = $('#filterDeadlineTugas').val().toLowerCase();

    $('#tableTugas tbody tr').each(function () {
        const namaMatch = !nama || $(this).find('td:eq(2)').text().toLowerCase().includes(nama);
        const kategoriMatch = !kategori || $(this).find('td:eq(3)').text().toLowerCase().includes(kategori);
        const deadlineMatch = !deadline || $(this).find('td:eq(5)').text().toLowerCase().includes(deadline);
        $(this).toggle(namaMatch && kategoriMatch && deadlineMatch);
    });
}

window.detailTugas = function (id) {
    // window.TUGAS_DETAIL_URL di-set inline di tugas.php (mengandung base_url)
    window.location.href = window.TUGAS_DETAIL_URL + '/' + id;
};

/* hapusTugas memanggil performDeleteTugas() yang didefinisikan inline */
window.hapusTugas = function (id) {
    if (typeof Swal === 'undefined') {
        if (!confirm('Yakin ingin menghapus tugas ini?')) return;
        performDeleteTugas(id);
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Yakin ingin menghapus tugas ini? Semua sasaran terkait juga akan terhapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) performDeleteTugas(id);
    });
};


/* =============================================================================
 * 5. TUGAS TAMBAH — Form Init, Validasi, Session
 * ============================================================================= */

function initFlatpickrTambah() {
    if (typeof flatpickr === 'undefined') return;
    flatpickr('#deadline', { dateFormat: 'd-m-Y', allowInput: false, disableMobile: true, locale: 'id' });
}

function initSelect2Tugas() {
    if (typeof $.fn.select2 === 'undefined') return;

    $('#kategoriTugas').select2({
        placeholder: '-- Pilih Kategori Tugas --', allowClear: true, width: '100%',
        language: { noResults: () => 'Kategori tidak ditemukan', searching: () => 'Mencari...' }
    });

    $('#kategoriTugas').on('change', function () {
        const selected = $(this).find('option:selected');
        const mode = selected.data('mode') || '';

        if (mode) {
            // Auto-fill mode pengumpulan dari data-mode kategori yang dipilih
            $('#modelPengumpulan').val(mode);
            $(this).next('.select2-container').find('.select2-selection').css('border-color', '');
        } else {
            // Reset ke placeholder jika kategori dikosongkan
            $('#modelPengumpulan').val('');
        }
    });
}

function checkFormHasData() {
    return $('#namaTugas').val().trim() !== ''
        || !!$('#kategoriTugas').val()
        || $('#deskripsi').val().trim() !== ''
        || (window._selectedSasaran && window._selectedSasaran.total > 0);
}

function validateForm() {
    const namaTugas = $('#namaTugas').val()?.trim();
    if (!namaTugas) {
        $('#namaTugas').css('border-color', 'var(--error)').focus();
        Swal.fire({ icon: 'warning', title: 'Field Wajib Diisi', text: 'Nama Tugas tidak boleh kosong!', confirmButtonColor: '#0f766e' });
        return false;
    }
    $('#namaTugas').css('border-color', '');

    const kategoriVal = $('#kategoriTugas').val();
    if (!kategoriVal) {
        $('#kategoriTugas').next('.select2-container').find('.select2-selection').css('border-color', 'var(--error)');
        Swal.fire({ icon: 'warning', title: 'Field Wajib Diisi', text: 'Kategori Tugas harus dipilih!', confirmButtonColor: '#0f766e' });
        return false;
    }
    $('#kategoriTugas').next('.select2-container').find('.select2-selection').css('border-color', '');

    const modelVal = $('#modelPengumpulan').val()?.trim();
    if (!modelVal) {
        Swal.fire({ icon: 'warning', title: 'Field Wajib Diisi', text: 'Pilih Kategori Tugas terlebih dahulu untuk menentukan mode pengumpulan!', confirmButtonColor: '#0f766e' });
        $('#kategoriTugas').next('.select2-container').find('.select2-selection').css('border-color', 'var(--error)');
        return false;
    }
    $('#kategoriTugas').next('.select2-container').find('.select2-selection').css('border-color', '');

    const deadlineVal = $('#deadline').val()?.trim();
    if (!deadlineVal) {
        $('#deadline').css('border-color', 'var(--error)').focus();
        Swal.fire({ icon: 'warning', title: 'Field Wajib Diisi', text: 'Deadline tidak boleh kosong!', confirmButtonColor: '#0f766e' });
        return false;
    }
    $('#deadline').css('border-color', '');

    const jumlahTarget = $('#jumlahTarget').val()?.trim();
    if (!jumlahTarget || parseInt(jumlahTarget) < 1) {
        $('#jumlahTarget').css('border-color', 'var(--error)').focus();
        Swal.fire({ icon: 'warning', title: 'Field Wajib Diisi', text: 'Jumlah Target harus diisi dengan angka positif!', confirmButtonColor: '#0f766e' });
        return false;
    }
    $('#jumlahTarget').css('border-color', '');

    const deskripsiVal = $('#deskripsi').val()?.trim();
    if (!deskripsiVal) {
        $('#deskripsi').css('border-color', 'var(--error)').focus();
        Swal.fire({ icon: 'warning', title: 'Field Wajib Diisi', text: 'Deskripsi tidak boleh kosong!', confirmButtonColor: '#0f766e' });
        return false;
    }
    $('#deskripsi').css('border-color', '');

    return true;
}

function saveFormToSession() {
    const formData = {
        editor: $('#editor').val(),
        nama_tugas: $('#namaTugas').val(),
        kategori_id: $('#kategoriTugas').val(),
        kategori_tugas: $('#kategoriTugas option:selected').text().trim(),
        model_pengumpulan: $('#modelPengumpulan').val(),
        deadline: $('#deadline').val(),
        jumlah_target: $('#jumlahTarget').val(),
        deskripsi: $('#deskripsi').val()
    };
    sessionStorage.setItem('tugasFormData', JSON.stringify(formData));
}

function restoreSasaranFromSession() {
    const savedSasaran = sessionStorage.getItem('tugasSasaran');
    if (savedSasaran) {
        window._selectedSasaran = JSON.parse(savedSasaran);
        updateSasaranPreview();
    }

    const savedForm = sessionStorage.getItem('tugasFormData');
    if (savedForm) {
        const fd = JSON.parse(savedForm);
        $('#namaTugas').val(fd.nama_tugas || '');
        $('#modelPengumpulan').val(fd.model_pengumpulan || '');
        $('#deskripsi').val(fd.deskripsi || '');
        $('#jumlahTarget').val(fd.jumlah_target || '');

        if (fd.kategori_id) {
            $('#kategoriTugas').val(fd.kategori_id).trigger('change');
        }
        setTimeout(() => {
            if (fd.deadline) $('#deadline').val(fd.deadline);
        }, 100);
    }
}

function updateSasaranPreview() {
    const selectedSasaran = window._selectedSasaran;
    if (!selectedSasaran || !selectedSasaran.total) return;

    const typeBadges = {
        mandiri: { class: 'badge-mandiri', icon: 'fa-user', label: 'PKL Mandiri' },
        kelompok: { class: 'badge-kelompok', icon: 'fa-users', label: 'Kelompok PKL' },
        tim: { class: 'badge-tim', icon: 'fa-user-friends', label: 'Tim Tugas' }
    };
    const badge = typeBadges[selectedSasaran.type];

    $('#sasaranTypeBadge').html(`
        <span class="sasaran-type-badge ${badge.class}">
            <i class="fas ${badge.icon}"></i> ${badge.label}
        </span>
    `);
    $('#sasaranCount').text(`${selectedSasaran.total} penerima tugas dipilih`);
    $('#sasaranNames').html(selectedSasaran.names.map(n => `• ${n}`).join('<br>'));
    $('#sasaranEmpty').hide();
    $('#sasaranSummary').addClass('show');
    $('#sasaranPreview').addClass('has-data');
}


/* =============================================================================
 * 6. TUGAS SASARAN — Tabs, Render, Checkbox, Tim Form, Filter, API Data
 * ============================================================================= */

/* ---------- STATE ---------- */
let activeTab = 'mandiri';
let selectedItems = [];
let allData = { mandiri: [], kelompok: [], tim: [] };
let buatTimMode = false;
let newTimMembers = [];

/* ---------- TABS ---------- */
function initTabs() {
    $('.tab-sasaran').on('click', function () {
        if (buatTimMode) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Selesaikan dulu pembuatan Tim Tugas!', confirmButtonColor: '#0f766e' });
            return;
        }

        const tab = $(this).data('tab');
        if (tab === activeTab) return;

        if (selectedItems.length > 0) {
            Swal.fire({
                title: 'Ganti Tab?',
                text: 'Pilihan sebelumnya akan direset. Lanjutkan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Ganti',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) { selectedItems = []; switchTab(tab); }
            });
        } else {
            switchTab(tab);
        }
    });
}

function switchTab(tab) {
    activeTab = tab;
    $('.tab-sasaran').removeClass('active');
    $(`.tab-sasaran[data-tab="${tab}"]`).addClass('active');

    const filterLabels = { mandiri: 'Cari Nama PKL', kelompok: 'Cari Nama Kelompok', tim: 'Cari Nama Tim' };
    $('#filterLabel1').html(`<i class="fas fa-search"></i> ${filterLabels[tab]}`);

    const titles = {
        mandiri: { icon: 'fa-user', text: 'PKL Mandiri' },
        kelompok: { icon: 'fa-users', text: 'Kelompok PKL' },
        tim: { icon: 'fa-user-friends', text: 'Tim Tugas' }
    };
    const t = titles[tab];
    $('#contentTitle').html(`<i class="fas ${t.icon}"></i> ${t.text}`);

    resetFilter();
    $('#filterSasaran').removeClass('show');
    loadTabData(tab);
}

/* ---------- LOADING ---------- */
function showLoading() {
    $('#contentBody').html(`
        <div style="text-align:center; padding: 40px;">
            <i class="fas fa-spinner fa-spin fa-2x" style="color: var(--primary);"></i>
            <p style="margin-top: 10px; color: var(--text-muted);">Memuat data...</p>
        </div>
    `);
}

/* ---------- LOAD DATA DARI API ---------- */
function loadTabData(tab) {
    showLoading();

    // URL map — window.SASARAN_URL dan window.TIM_URL didefinisikan inline di tugas_sasaran.php
    const urlMap = {
        mandiri: window.SASARAN_URL + '/mandiri',
        kelompok: window.SASARAN_URL + '/kelompok',
        tim: window.TIM_URL + '/list'
    };

    $.ajax({
        url: urlMap[tab],
        method: 'GET',
        timeout: 10000,
        success: function (res) {
            const data = res.data || [];
            if (tab === 'mandiri') renderMandiri(data);
            if (tab === 'kelompok') renderKelompok(data);
            if (tab === 'tim') renderTim(data);
        },
        error: function (xhr) {
            const msg = xhr.responseJSON?.message || 'Gagal memuat data';
            $('#contentBody').html(`
                <div style="text-align:center; padding:40px;">
                    <i class="fas fa-exclamation-triangle fa-2x" style="color:var(--accent-red);"></i>
                    <p style="margin-top:10px; color:var(--text-muted);">${msg}</p>
                </div>
            `);
        }
    });
}

/* ---------- RENDER: MANDIRI ---------- */
function renderMandiri(data) {
    allData.mandiri = data;
    $('#countMandiri').text(data.length);

    let html = `
        <div class="selected-bar">
            <div class="selected-bar-text">
                <i class="fas fa-check-circle"></i>
                Dipilih: <span class="selected-bar-count" id="selectedCount">0</span> orang
            </div>
            <button class="btn-clear-selection" id="btnClearSelection">
                <i class="fas fa-times"></i> Hapus Pilihan
            </button>
        </div>
        <table class="table-sasaran" id="tableMandiri">
            <thead>
                <tr>
                    <th><input type="checkbox" class="custom-checkbox" id="checkAll"></th>
                    <th>NO</th>
                    <th>Nama Lengkap</th>
                    <th>Jenis Kelamin</th>
                    <th>Tgl Mulai</th>
                    <th>Tgl Akhir</th>
                </tr>
            </thead>
            <tbody>
    `;

    if (data.length === 0) {
        html += `<tr><td colspan="6" style="text-align:center; padding:30px; color:var(--text-muted);">
                    Tidak ada data PKL mandiri aktif
                 </td></tr>`;
    } else {
        data.forEach((item, i) => {
            html += `
                <tr class="sasaran-row" data-id="${item.id}" data-name="${item.nama}">
                    <td><input type="checkbox" class="custom-checkbox row-check" data-id="${item.id}" data-name="${item.nama}"></td>
                    <td>${i + 1}</td>
                    <td class="td-name"><strong>${item.nama}</strong></td>
                    <td>${item.jk}</td>
                    <td>${item.tgl_mulai}</td>
                    <td>${item.tgl_akhir}</td>
                </tr>
            `;
        });
    }

    html += `</tbody></table>`;
    $('#contentBody').html(html);
    initTableEvents();
}

/* ---------- RENDER: KELOMPOK ---------- */
function renderKelompok(data) {
    allData.kelompok = data;
    $('#countKelompok').text(data.length);

    let html = `
        <div class="selected-bar">
            <div class="selected-bar-text">
                <i class="fas fa-check-circle"></i>
                Dipilih: <span class="selected-bar-count" id="selectedCount">0</span> kelompok
            </div>
            <button class="btn-clear-selection" id="btnClearSelection">
                <i class="fas fa-times"></i> Hapus Pilihan
            </button>
        </div>
        <table class="table-sasaran" id="tableKelompok">
            <thead>
                <tr>
                    <th><input type="checkbox" class="custom-checkbox" id="checkAll"></th>
                    <th>NO</th>
                    <th>Nama Kelompok</th>
                    <th>Jumlah Anggota</th>
                    <th>Tgl Mulai</th>
                    <th>Tgl Akhir</th>
                </tr>
            </thead>
            <tbody>
    `;

    if (data.length === 0) {
        html += `<tr><td colspan="6" style="text-align:center; padding:30px; color:var(--text-muted);">
                    Tidak ada data kelompok aktif
                 </td></tr>`;
    } else {
        data.forEach((item, i) => {
            html += `
                <tr class="sasaran-row" data-id="${item.id}" data-name="${item.nama}">
                    <td><input type="checkbox" class="custom-checkbox row-check" data-id="${item.id}" data-name="${item.nama}"></td>
                    <td>${i + 1}</td>
                    <td class="td-name">
                        <strong>${item.nama}</strong><br>
                        <small style="color:var(--text-muted)">${item.instansi}</small>
                    </td>
                    <td><span class="badge-instansi-sm"><i class="fas fa-users"></i> ${item.jumlah} anggota</span></td>
                    <td>${item.tgl_mulai}</td>
                    <td>${item.tgl_akhir}</td>
                </tr>
            `;
        });
    }

    html += `</tbody></table>`;
    $('#contentBody').html(html);
    initTableEvents();
}

/* ---------- RENDER: TIM ---------- */
function renderTim(data, highlightNewId = null) {
    allData.tim = data;
    $('#countTim').text(data.length);

    let html = `
        <div class="tim-actions">
            <button class="btn-buat-tim" id="btnBuatTim">
                <i class="fas fa-plus"></i> Buat Tim Tugas Baru
            </button>
        </div>

        <div class="form-buat-tim" id="formBuatTim">
            <div class="form-buat-tim-title">
                <i class="fas fa-plus-circle"></i> Buat Tim Tugas Baru
            </div>
            <div style="margin-bottom: var(--space-lg)">
                <label class="form-buat-tim-label">
                    Nama Tim <span style="color:var(--error)">*</span>
                </label>
                <input type="text" id="namaTim" class="form-buat-tim-input"
                       placeholder="Contoh: Team Backend PKL ITS...">
            </div>
            <div class="form-buat-tim-grid" style="margin-bottom: var(--space-md)">
                <div>
                    <label class="form-buat-tim-label">
                        <i class="fas fa-search"></i> Cari Nama Anggota
                    </label>
                    <input type="text" id="filterTimMember" class="form-buat-tim-input" placeholder="Ketik nama...">
                </div>
                <div>
                    <label class="form-buat-tim-label">
                        <i class="fas fa-filter"></i> Kategori PKL
                    </label>
                    <select id="filterTimKategori" class="form-buat-tim-input">
                        <option value="">Semua Kategori</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="Instansi">Dari Instansi</option>
                    </select>
                </div>
            </div>
            <div style="border:2px solid var(--primary); border-radius:var(--radius-md); overflow:hidden; margin-bottom:var(--space-md)">
                <table class="table-sasaran" id="tableTimMembers">
                    <thead>
                        <tr>
                            <th><input type="checkbox" class="custom-checkbox" id="checkAllTim"></th>
                            <th>NO</th>
                            <th>Nama Lengkap</th>
                            <th>Kategori PKL</th>
                            <th>Kelompok/Mandiri</th>
                        </tr>
                    </thead>
                    <tbody id="timMemberBody">
                        <tr><td colspan="5" style="text-align:center; padding:20px;">
                            <i class="fas fa-spinner fa-spin"></i> Memuat data anggota...
                        </td></tr>
                    </tbody>
                </table>
            </div>
            <div id="timMemberSelected"
                 style="font-size:var(--font-size-sm); color:var(--primary); font-weight:600; margin-bottom:var(--space-md)">
                0 anggota dipilih
            </div>
            <div class="form-buat-tim-actions">
                <button class="btn-tim-batal" id="btnBatalBuatTim">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button class="btn-tim-simpan" id="btnSimpanTim">
                    <i class="fas fa-save"></i>
                    <span id="simpanTimLabel">Simpan Tim</span>
                </button>
            </div>
        </div>

        <div class="selected-bar">
            <div class="selected-bar-text">
                <i class="fas fa-check-circle"></i>
                Dipilih: <span class="selected-bar-count" id="selectedCount">0</span> tim
            </div>
            <button class="btn-clear-selection" id="btnClearSelection">
                <i class="fas fa-times"></i> Hapus Pilihan
            </button>
        </div>

        <table class="table-sasaran" id="tableTim">
            <thead>
                <tr>
                    <th><input type="checkbox" class="custom-checkbox" id="checkAll"></th>
                    <th>NO</th>
                    <th>Nama Tim</th>
                    <th>Jumlah Anggota</th>
                    <th>Tgl Dibuat</th>
                    <th>Dipakai di</th>
                </tr>
            </thead>
            <tbody>
    `;

    if (data.length === 0) {
        html += `<tr><td colspan="6" style="text-align:center; padding:30px; color:var(--text-muted);">
                    Belum ada tim tugas. Buat tim baru di atas.
                 </td></tr>`;
    } else {
        data.forEach((item, i) => {
            const isNew = item.id === highlightNewId;
            html += `
                <tr class="sasaran-row${isNew ? ' selected-row' : ''}"
                    data-id="${item.id}" data-name="${item.nama}">
                    <td><input type="checkbox" class="custom-checkbox row-check"
                               data-id="${item.id}" data-name="${item.nama}"
                               ${isNew ? 'checked' : ''}></td>
                    <td>${i + 1}</td>
                    <td class="td-name">
                        <strong>${item.nama}</strong><br>
                        <small style="color:var(--text-muted)">${item.anggota_preview}</small>
                    </td>
                    <td>${item.jumlah} anggota</td>
                    <td>${item.tgl_dibuat}</td>
                    <td><span style="color:var(--text-muted); font-size:12px">${item.dipakai} tugas</span></td>
                </tr>
            `;
        });
    }

    html += `</tbody></table>`;
    $('#contentBody').html(html);
    initTableEvents();
    initTimFormEvents();

    if (highlightNewId) {
        const newTim = data.find(t => t.id === highlightNewId);
        if (newTim) {
            selectedItems = [{ id: newTim.id, name: newTim.nama, type: 'tim' }];
            updateSelectedUI();
        }
    }
}

/* ---------- TABLE EVENTS (Checkbox) ---------- */
function initTableEvents() {
    selectedItems.forEach(item => {
        $(`.row-check[data-id="${item.id}"]`).prop('checked', true);
        $(`.sasaran-row[data-id="${item.id}"]`).addClass('selected-row');
    });
    updateSelectedUI();

    $(document).off('click', '.sasaran-row').on('click', '.sasaran-row', function (e) {
        if ($(e.target).hasClass('custom-checkbox') || buatTimMode) return;
        const cb = $(this).find('.row-check');
        cb.prop('checked', !cb.prop('checked')).trigger('change');
    });

    $(document).off('change', '.row-check').on('change', '.row-check', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const row = $(this).closest('tr');

        if ($(this).is(':checked')) {
            if (!selectedItems.find(i => i.id == id)) {
                selectedItems.push({ id, name, type: activeTab });
            }
            row.addClass('selected-row');
        } else {
            selectedItems = selectedItems.filter(i => i.id != id);
            row.removeClass('selected-row');
        }
        updateSelectedUI();
        syncSelectAll();
    });

    $(document).off('change', '#checkAll').on('change', '#checkAll', function () {
        const checked = $(this).is(':checked');
        $('.row-check').each(function () { $(this).prop('checked', checked).trigger('change'); });
    });

    $(document).off('click', '#btnClearSelection').on('click', '#btnClearSelection', function () {
        selectedItems = [];
        $('.row-check').prop('checked', false);
        $('.sasaran-row').removeClass('selected-row');
        $('#checkAll').prop('checked', false);
        updateSelectedUI();
    });
}

function syncSelectAll() {
    const total = $('.row-check').length;
    const checked = $('.row-check:checked').length;
    $('#checkAll').prop('checked', total > 0 && total === checked);
}

function updateSelectedUI() {
    const count = selectedItems.length;
    $('#selectedCount').text(count);

    const labels = { mandiri: 'orang', kelompok: 'kelompok', tim: 'tim' };
    const label = labels[activeTab] || 'item';

    if (count > 0) {
        $('#selectedInfoBar').html(`
            <span style="color:var(--primary)">
                <i class="fas fa-check-circle"></i>
                <strong>${count}</strong> ${label} dipilih
            </span>
        `);
        $('#btnKirim').prop('disabled', false);
    } else {
        $('#selectedInfoBar').text('Belum ada yang dipilih');
        $('#btnKirim').prop('disabled', true);
    }
}

/* ---------- TIM FORM EVENTS ---------- */
function initTimFormEvents() {
    $(document).off('click', '#btnBuatTim').on('click', '#btnBuatTim', function () {
        buatTimMode = true;
        $('#formBuatTim').addClass('show');
        $(this).prop('disabled', true);
        newTimMembers = [];
        _loadPklMembersForTim();
    });

    $(document).off('click', '#btnBatalBuatTim').on('click', '#btnBatalBuatTim', function () {
        buatTimMode = false;
        $('#formBuatTim').removeClass('show');
        $('#btnBuatTim').prop('disabled', false);
        $('#namaTim').val('');
        newTimMembers = [];
    });

    $(document).off('keyup', '#filterTimMember').on('keyup', '#filterTimMember', function () {
        const val = this.value.toLowerCase();
        $('#timMemberBody tr').filter(function () {
            $(this).toggle($(this).find('td:eq(2)').text().toLowerCase().includes(val));
        });
    });

    $(document).off('change', '#filterTimKategori').on('change', '#filterTimKategori', function () {
        const val = this.value.toLowerCase();
        if (!val) { $('#timMemberBody tr').show(); return; }
        $('#timMemberBody tr').filter(function () {
            $(this).toggle($(this).find('td:eq(3)').text().toLowerCase().includes(val));
        });
    });

    $(document).off('change', '#checkAllTim').on('change', '#checkAllTim', function () {
        const checked = $(this).is(':checked');
        $('#timMemberBody .tim-member-check').each(function () {
            $(this).prop('checked', checked).trigger('change');
        });
    });

    $(document).off('change', '.tim-member-check').on('change', '.tim-member-check', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        if ($(this).is(':checked')) {
            if (!newTimMembers.find(m => m.id == id)) newTimMembers.push({ id, name });
        } else {
            newTimMembers = newTimMembers.filter(m => m.id != id);
        }
        $('#timMemberSelected').text(`${newTimMembers.length} anggota dipilih`);
    });

    $(document).off('click', '#btnSimpanTim').on('click', '#btnSimpanTim', function () {
        const namaTim = $('#namaTim').val().trim();

        if (!namaTim) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Nama tim wajib diisi!', confirmButtonColor: '#0f766e' });
            return;
        }
        if (newTimMembers.length < 2) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih minimal 2 anggota!', confirmButtonColor: '#0f766e' });
            return;
        }

        Swal.fire({ title: 'Menyimpan Tim...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        // Kirim ke API (window.TIM_URL didefinisikan inline di tugas_sasaran.php)
        $.ajax({
            url: window.TIM_URL + '/store',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                nama_tim: namaTim,
                anggota: newTimMembers.map(m => m.id)
            }),
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN || '' },
            success: function (res) {
                buatTimMode = false;
                $('#formBuatTim').removeClass('show');
                $('#btnBuatTim').prop('disabled', false);
                $('#namaTim').val('');
                newTimMembers = [];

                Swal.fire({
                    icon: 'success',
                    title: 'Tim Berhasil Dibuat!',
                    text: `${namaTim} dengan ${res.data?.jumlah || 0} anggota`,
                    timer: 1500,
                    showConfirmButton: false
                });

                // Reload list tim dan highlight tim baru
                setTimeout(() => {
                    $.get(window.TIM_URL + '/list', function (response) {
                        renderTim(response.data || [], res.data?.id || null);
                    });
                }, 1600);
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Gagal membuat tim',
                    confirmButtonColor: '#0f766e'
                });
            }
        });
    });
}

/**
 * Load daftar PKL untuk form buat tim (dari API)
 */
function _loadPklMembersForTim() {
    // window.PKL_MEMBER_URL didefinisikan inline di tugas_sasaran.php
    $.ajax({
        url: window.PKL_MEMBER_URL,
        method: 'GET',
        timeout: 10000,
        success: function (res) {
            renderTimMemberTable(res.data || []);
        },
        error: function () {
            $('#timMemberBody').html(`
                <tr><td colspan="5" style="text-align:center; padding:20px; color:var(--accent-red);">
                    Gagal memuat data anggota
                </td></tr>
            `);
        }
    });
}

function renderTimMemberTable(members) {
    if (!members || members.length === 0) {
        $('#timMemberBody').html(`
            <tr><td colspan="5" style="text-align:center; padding:20px; color:var(--text-muted);">
                Tidak ada data anggota PKL
            </td></tr>
        `);
        return;
    }

    let html = '';
    members.forEach((m, i) => {
        const badgeClass = m.kategori === 'Mandiri' ? 'badge-mandiri-sm' : 'badge-instansi-sm';
        html += `
            <tr>
                <td><input type="checkbox" class="custom-checkbox tim-member-check"
                           data-id="${m.id}" data-name="${m.nama}"></td>
                <td>${i + 1}</td>
                <td class="td-name">${m.nama}</td>
                <td><span class="${badgeClass}">${m.kategori}</span></td>
                <td>${m.kelompok}</td>
            </tr>
        `;
    });
    $('#timMemberBody').html(html);
}

/* ---------- FILTER SASARAN ---------- */
function initFilterSasaran() {
    $('#btnFilterSasaran').on('click', function () {
        $('#filterSasaran').toggleClass('show');
        const showing = $('#filterSasaran').hasClass('show');
        $(this).html(showing
            ? '<i class="fas fa-times"></i> Tutup Filter'
            : '<i class="fas fa-filter"></i> Filter'
        );
    });

    $(document).on('keyup', '#filterNamaSasaran', function () {
        const val = this.value.toLowerCase();
        $('.sasaran-row').filter(function () {
            $(this).toggle($(this).find('.td-name').text().toLowerCase().includes(val));
        });
    });

    $('#btnResetFilterSasaran').on('click', resetFilter);
}

function resetFilter() {
    $('#filterNamaSasaran').val('');
    $('#filterTglMulai').val('');
    $('#filterTglAkhir').val('');
    $('.sasaran-row').show();
}

function initFlatpickrSasaran() {
    if (typeof flatpickr === 'undefined') return;
    flatpickr('.flatpickr-sasaran', { dateFormat: 'd-m-Y', allowInput: false, locale: 'id' });
}