/**
 * assets/js/modules/pkl.js
 * Manajemen PKL — Shared Utilities & All Page Logic
 *
 * File ini TIDAK boleh mengandung PHP interpolation (<?= ?>).
 * Semua nilai dari PHP (URL, ID) dikirim lewat parameter fungsi
 * atau window.PKL_BASE_URL yang di-set inline di masing-masing view.
 *
 * Fungsi yang tersedia:
 *   - Shared    : showAlert, showToast, parseDate
 *   - main_pkl  : pklMainInit(baseUrl)
 *   - instansi  : instansiTambahInit(urls), instansiUbahInit(urls)
 *   - pkl_tambah: pklTambahInit(urls)
 *   - pkl_detail: pklDetailInit(pklId, urls)
 */
'use strict';

// =============================================================================
// SHARED CONSTANTS
// =============================================================================

const FLATPICKR_ID_LOCALE = {
    firstDayOfWeek: 1,
    weekdays: {
        shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
        longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
    },
    months: {
        shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
            'Agustus', 'September', 'Oktober', 'November', 'Desember']
    }
};

const REGEX_WA = /^08[0-9]{8,11}$/;
const REGEX_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


// =============================================================================
// SHARED UTILITY FUNCTIONS
// =============================================================================

function showAlert(icon, title, text) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({ icon, title, text, confirmButtonColor: '#0f766e' });
    } else {
        alert(title + ': ' + text);
    }
}

function showToast(icon, title, text) {
    if (typeof Swal === 'undefined') return;
    Swal.mixin({
        toast: true, position: 'top-end',
        showConfirmButton: false, timer: 3000, timerProgressBar: true
    }).fire({ icon, title, text });
}

function parseDate(dateStr) {
    const [d, m, y] = dateStr.split('-');
    return new Date(y, m - 1, d);
}


// =============================================================================
// MAIN PKL — Tab Loading
// =============================================================================

let _pklMainBaseUrl = '';

function pklMainInit(baseUrl) {
    _pklMainBaseUrl = baseUrl;
    loadTabContent('instansi');

    $('.tab-btn').on('click', function () {
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        loadTabContent($(this).data('tab'));
    });
}

window.detailPkl = function (id) {
    window.location.href = _pklMainBaseUrl + '/pkl/detail/' + id;
};

window.reloadCurrentTab = function () {
    loadTabContent($('.tab-btn.active').data('tab'));
};

function _reloadPklSection(targetSubTab) {
    window.__pklRestoreSubTab = targetSubTab || 'aktif';
    $('.tab-btn').removeClass('active');
    $('.tab-btn[data-tab="pkl"]').addClass('active');
    loadTabContent('pkl');
}

window.nonaktifkanPkl = function (idPkl, fromSubTab) {
    const subTab = fromSubTab
        || (typeof window.currentPklSubTab !== 'undefined' ? window.currentPklSubTab : 'aktif');

    Swal.fire({
        title: 'Nonaktifkan Akun?',
        html: 'Akun PKL ini akan dinonaktifkan.<br><small style="color:#64748b">Peserta tidak bisa login sampai diaktifkan kembali.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Nonaktifkan',
        cancelButtonText: 'Batal',
        reverseButtons: true,
    }).then(function (result) {
        if (!result.isConfirmed) return;

        Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url: _pklMainBaseUrl + '/pkl/nonaktifkan/' + idPkl,
            method: 'POST',
            success: function (res) {
                Swal.fire({
                    icon: 'success', title: 'Berhasil!',
                    text: res.message || 'Akun PKL berhasil dinonaktifkan.',
                    timer: 1500, showConfirmButton: false,
                }).then(function () { _reloadPklSection(subTab); });
            },
            error: function (xhr) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: xhr.responseJSON?.message || 'Terjadi kesalahan. Coba lagi.' });
            },
        });
    });
};

window.aktifkanPkl = function (idPkl) {
    Swal.fire({
        title: 'Aktifkan Akun?',
        html: 'Akun PKL ini akan diaktifkan kembali.<br><small style="color:#64748b">Peserta bisa login lagi setelah diaktifkan.</small>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Aktifkan',
        cancelButtonText: 'Batal',
        reverseButtons: true,
    }).then(function (result) {
        if (!result.isConfirmed) return;

        Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url: _pklMainBaseUrl + '/pkl/aktifkan/' + idPkl,
            method: 'POST',
            success: function (res) {
                Swal.fire({
                    icon: 'success', title: 'Berhasil!',
                    text: res.message || 'Akun PKL berhasil diaktifkan.',
                    timer: 1500, showConfirmButton: false,
                }).then(function () { _reloadPklSection('nonaktif'); });
            },
            error: function (xhr) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: xhr.responseJSON?.message || 'Terjadi kesalahan. Coba lagi.' });
            },
        });
    });
};

window.hapusPkl = function (idPkl) {
    const subTab = typeof window.currentPklSubTab !== 'undefined'
        ? window.currentPklSubTab : 'aktif';

    Swal.fire({
        title: 'Hapus Data PKL?',
        html: 'Data PKL ini akan dihapus permanen.<br><small style="color:#ef4444">Aksi ini tidak bisa dibatalkan.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
    }).then(function (result) {
        if (!result.isConfirmed) return;

        Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url: _pklMainBaseUrl + '/pkl/delete/' + idPkl,
            method: 'POST',
            success: function (res) {
                const title = res.kelompok_dihapus ? 'PKL & Kelompok Terhapus!' : 'PKL Terhapus!';
                Swal.fire({
                    icon: 'success', title, text: res.message,
                    timer: 2500, showConfirmButton: false,
                }).then(function () { _reloadPklSection(subTab); });
            },
            error: function (xhr) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: xhr.responseJSON?.message || 'Gagal menghapus data.' });
            },
        });
    });
};

function loadTabContent(tab) {
    const $overlay = $('#loadingOverlay');
    const $contentArea = $('#tabContentArea');
    const url = tab === 'instansi'
        ? _pklMainBaseUrl + '/load-instansi'
        : _pklMainBaseUrl + '/load-pkl';

    $overlay.addClass('active');
    $contentArea.html('');

    $.ajax({
        url, method: 'GET', timeout: 10000,
        success: function (response) {
            $contentArea.html(response);
            $overlay.removeClass('active');

            setTimeout(function () {
                if ($('#tableInstansi').length) {
                    if (typeof initInstansiTable === 'function') initInstansiTable();
                    if (typeof initInstansiEvents === 'function') initInstansiEvents();
                }
                if ($('.pkl-stats-container').length) {
                    if (typeof initPklSubTabs === 'function') initPklSubTabs();
                }
            }, 100);
        },
        error: function (xhr, status) {
            $overlay.removeClass('active');

            let msg = 'Gagal memuat konten';
            if (status === 'timeout') msg = 'Request timeout (>10s)';
            else if (xhr.status === 404) msg = 'Halaman tidak ditemukan (404)';
            else if (xhr.status === 500) msg = 'Server error (500)';

            $contentArea.html(`
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <h3 class="empty-state-title">Error Loading Content</h3>
                    <p class="empty-state-description">${msg}</p>
                </div>
            `);
            showAlert('error', 'Error', msg);
        }
    });
}


// =============================================================================
// INSTANSI TAMBAH
// =============================================================================

function instansiTambahInit(urls) {
    if (typeof $.fn.select2 !== 'undefined') {
        $('#kotaInstansi').select2(_buildKotaTagsConfig());
    }

    $('#formTambahInstansi').on('submit', function (e) {
        e.preventDefault();

        const kategori = $('#kategoriInstansi').val().trim();
        const nama = $('#namaInstansi').val().trim();
        const alamat = $('#alamatInstansi').val().trim();
        const kota = $('#kotaInstansi').val();

        if (!kategori || !nama || !alamat || !kota) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Semua field wajib diisi!', confirmButtonColor: '#0f766e' });
            return;
        }

        Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url: urls.store, method: 'POST',
            data: new FormData(this), processData: false, contentType: false,
            success: function (response) {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message || 'Instansi berhasil ditambahkan', timer: 2000, showConfirmButton: false });
                setTimeout(() => { window.location.href = urls.redirect; }, 2000);
            },
            error: function (xhr) {
                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Gagal menyimpan data', confirmButtonColor: '#ef4444' });
            }
        });
    });
}


// =============================================================================
// INSTANSI UBAH
// =============================================================================

function instansiUbahInit(urls, instansiId) {
    if (typeof $.fn.select2 !== 'undefined') {
        $('#kotaInstansi').select2(_buildKotaTagsConfig());
    }

    $('#formUbahInstansi').on('submit', function (e) {
        e.preventDefault();

        const kategori = $('#kategoriInstansi').val().trim();
        const nama = $('#namaInstansi').val().trim();
        const alamat = $('#alamatInstansi').val().trim();
        const kota = $('#kotaInstansi').val();

        if (!kategori || !nama || !alamat || !kota) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Semua field wajib diisi!', confirmButtonColor: '#0f766e' });
            return;
        }

        Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url: urls.update + '/' + instansiId, method: 'POST',
            data: new FormData(this), processData: false, contentType: false,
            success: function (response) {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message || 'Instansi berhasil diupdate', timer: 2000, showConfirmButton: false });
                setTimeout(() => { window.location.href = urls.redirect; }, 2000);
            },
            error: function (xhr) {
                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Gagal menyimpan perubahan', confirmButtonColor: '#ef4444' });
            }
        });
    });
}


// =============================================================================
// PKL TAMBAH — Form Multi-Step
// =============================================================================

let _pkl = {
    step: 1,
    totalSteps: 3,
    jumlahAnggota: 1,
    kategori: 'Mandiri',
    fpInstances: [],
    urls: {}
};

function pklTambahInit(urls) {
    _pkl.urls = urls;
    _pkl.step = 1;
    _pkl.jumlahAnggota = 1;
    _pkl.kategori = 'Mandiri';
    _pkl.fpInstances = [];

    _pklTambahInitFlatpickr();
    _pklTambahInitSelect2();
    _pklTambahInitEvents();
}

// ── Flatpickr ─────────────────────────────────────────────────────────────────
function _pklTambahInitFlatpickr() {
    if (typeof flatpickr === 'undefined') return;

    const today = new Date();
    const minMulai = new Date(today);
    const maxMulai = new Date(today);

    minMulai.setDate(today.getDate() - 7);
    maxMulai.setMonth(today.getMonth() + 6);

    function getMinAkhir(tglMulai) {
        const min = new Date(tglMulai);
        min.setDate(min.getDate() + 21);
        return min;
    }

    const fpMulaiInstansi = flatpickr('#tglMulaiInstansi', {
        dateFormat: 'd-M-Y', minDate: minMulai, maxDate: maxMulai,
        allowInput: false, locale: FLATPICKR_ID_LOCALE,
        onChange: function (selectedDates) {
            if (!selectedDates[0]) return;
            fpAkhirInstansi.set('minDate', getMinAkhir(selectedDates[0]));
            if (fpAkhirInstansi.selectedDates[0] &&
                fpAkhirInstansi.selectedDates[0] < getMinAkhir(selectedDates[0])) {
                fpAkhirInstansi.clear();
            }
        },
    });

    const fpAkhirInstansi = flatpickr('#tglAkhirInstansi', {
        dateFormat: 'd-M-Y', allowInput: false, locale: FLATPICKR_ID_LOCALE,
    });

    const fpMulaiMandiri = flatpickr('#tglMulaiMandiri', {
        dateFormat: 'd-M-Y', minDate: minMulai, maxDate: maxMulai,
        allowInput: false, locale: FLATPICKR_ID_LOCALE,
        onChange: function (selectedDates) {
            if (!selectedDates[0]) return;
            fpAkhirMandiri.set('minDate', getMinAkhir(selectedDates[0]));
            if (fpAkhirMandiri.selectedDates[0] &&
                fpAkhirMandiri.selectedDates[0] < getMinAkhir(selectedDates[0])) {
                fpAkhirMandiri.clear();
            }
        },
    });

    const fpAkhirMandiri = flatpickr('#tglAkhirMandiri', {
        dateFormat: 'd-M-Y', allowInput: false, locale: FLATPICKR_ID_LOCALE,
    });

    _pkl.fpInstances.push(fpMulaiInstansi, fpAkhirInstansi, fpMulaiMandiri, fpAkhirMandiri);
}

// ── Select2 ───────────────────────────────────────────────────────────────────
function _pklTambahInitSelect2() {
    if (typeof $.fn.select2 === 'undefined') return;

    $('#namaInstansi').select2({
        placeholder: 'Pilih atau Ketik Instansi Baru', allowClear: true, width: '100%', tags: true,
        createTag: function (params) {
            const term = $.trim(params.term);
            if (!term) return null;
            return { id: 'new_' + term, text: term, newTag: true };
        },
        templateResult: function (data) {
            if (data.loading) return data.text;
            if (data.newTag) return $('<span><i class="fas fa-plus-circle" style="color:#0f766e;margin-right:6px;"></i> Tambah: <strong>' + data.text + '</strong></span>');
            return data.text;
        }
    });

    $('#namaInstansi').on('change', function () {
        const val = $(this).val();
        if (val && val.toString().startsWith('new_')) {
            $('#newInstansiFields').slideDown();
        } else {
            $('#newInstansiFields').slideUp();
            $('#alamatInstansiBaru').val('');
            $('#kotaInstansiBaru').val('').trigger('change');
        }
    });

    $('#kotaInstansiBaru').select2(_buildKotaTagsConfig());
}

// ── Event Handlers ────────────────────────────────────────────────────────────
function _pklTambahInitEvents() {
    $('input[name="kategori_pkl"]').on('change', function () {
        _pkl.kategori = $(this).val();
        if (_pkl.kategori === 'Mandiri') {
            $('#mandiriFields').slideDown();
            $('#instansiFields').slideUp();
            _pkl.jumlahAnggota = 1;
        } else {
            $('#mandiriFields').slideUp();
            $('#instansiFields').slideDown();
            _pkl.jumlahAnggota = parseInt($('#jumlahAnggota').val()) || 1;
        }
    });

    $('#jumlahAnggota').on('input', function () {
        let v = parseInt($(this).val());
        if (isNaN(v) || v < 1) { $(this).val(1); _pkl.jumlahAnggota = 1; }
        else if (v > 10) { $(this).val(10); _pkl.jumlahAnggota = 10; }
        else { _pkl.jumlahAnggota = v; }
    });

    $('#kategoriInstansiPkl').on('change', function () {
        const kategoriDipilih = $(this).val();

        $('#namaInstansi').val(null).trigger('change');
        $('#newInstansiFields').slideUp();

        const kategoriMap = { 'Kuliah': 'kampus', 'SMK Sederajat': 'sekolah' };
        const dbVal = kategoriMap[kategoriDipilih] || '';

        $('#namaInstansi option').each(function () {
            const optVal = $(this).val();
            if (!optVal || optVal.toString().startsWith('new_')) {
                $(this).prop('disabled', false).show();
                return;
            }
            const optKategori = $(this).data('kategori');
            if (!dbVal || optKategori === dbVal) {
                $(this).prop('disabled', false).show();
            } else {
                $(this).prop('disabled', true).hide();
            }
        });

        $('#namaInstansi').trigger('change.select2');
    });

    $('#btnNextStep1').on('click', function () {
        if (_pklValidateStep1()) { _pklGenerateBiodata(); _pklGoToStep(2); }
    });
    $('#btnBackStep2').on('click', () => _pklGoToStep(1));
    $('#btnNextStep2').on('click', function () {
        if (_pklValidateStep2()) { _pklGenerateReview(); _pklGoToStep(3); }
    });
    $('#btnBackStep3').on('click', () => _pklGoToStep(2));
    $('#btnSubmitForm').on('click', () => _pklSubmit());
}

// ── Step Navigation ───────────────────────────────────────────────────────────
function _pklGoToStep(n) {
    if (n < 1 || n > _pkl.totalSteps) return;
    _pkl.step = n;

    $('.form-step').removeClass('active');
    $('.form-step[data-step="' + n + '"]').addClass('active');

    $('.progress-step').removeClass('active completed').each(function () {
        const s = parseInt($(this).data('step'));
        if (s < n) $(this).addClass('completed');
        else if (s === n) $(this).addClass('active');
    });

    $('#progressLineFill').css('width', ((n - 1) / (_pkl.totalSteps - 1)) * 100 + '%');
    $('html, body').animate({ scrollTop: 0 }, 300);
}

// ── Validasi Step 1 ───────────────────────────────────────────────────────────
function _pklValidateStep1() {
    const err = [];

    if (_pkl.kategori === 'Mandiri') {
        const s = $('#tglMulaiMandiri').val(), e = $('#tglAkhirMandiri').val();
        if (!s) err.push('Tanggal Mulai PKL wajib diisi');
        if (!e) err.push('Tanggal Akhir PKL wajib diisi');
        if (s && e && parseDate(s) >= parseDate(e)) err.push('Tanggal Akhir harus lebih besar dari Tanggal Mulai');
    } else {
        if (!$('#kategoriInstansiPkl').val()) err.push('Kategori Instansi wajib dipilih');
        if (!$('#namaInstansi').val()) err.push('Nama Instansi wajib dipilih');

        const instVal = ($('#namaInstansi').val() || '').toString();
        if (instVal.startsWith('new_')) {
            if (!$('#alamatInstansiBaru').val().trim()) err.push('Alamat Instansi wajib diisi');
            if (!$('#kotaInstansiBaru').val()) err.push('Kota Instansi wajib dipilih');
        }

        const wa = $('#noWaPembimbing').val().trim();
        if (!$('#namaPembimbing').val().trim()) err.push('Nama Pembimbing wajib diisi');
        if (!wa) err.push('No WA Pembimbing wajib diisi');
        else if (!REGEX_WA.test(wa)) err.push('Format No WA tidak valid (contoh: 081234567890)');

        const jml = parseInt($('#jumlahAnggota').val());
        if (!jml || jml < 1 || jml > 10) err.push('Jumlah Anggota harus antara 1–10');
        if (!$('#namaKelompokInstansi').val().trim()) err.push('Nama Kelompok wajib diisi');

        const s = $('#tglMulaiInstansi').val(), e = $('#tglAkhirInstansi').val();
        if (!s) err.push('Tanggal Mulai PKL wajib diisi');
        if (!e) err.push('Tanggal Akhir PKL wajib diisi');
        if (s && e && parseDate(s) >= parseDate(e)) err.push('Tanggal Akhir harus lebih besar dari Tanggal Mulai');
    }

    if (err.length) {
        Swal.fire({ icon: 'error', title: 'Validasi Gagal', html: '<div style="text-align:left;">' + err.map(m => '• ' + m).join('<br>') + '</div>', confirmButtonColor: '#ef4444' });
        return false;
    }
    return true;
}

// ── Validasi Step 2 ───────────────────────────────────────────────────────────
function _pklValidateStep2() {
    const err = [], emails = [];

    for (let i = 1; i <= _pkl.jumlahAnggota; i++) {
        const pre = 'Anggota ' + i + ': ';
        const wa = $('#noWa_' + i).val().trim();
        const mail = $('#email_' + i).val().trim().toLowerCase();

        if (!$('#namaLengkap_' + i).val().trim()) err.push(pre + 'Nama Lengkap wajib diisi');
        if (!$('#namaPanggilan_' + i).val().trim()) err.push(pre + 'Nama Panggilan wajib diisi');
        if (!$('#tempatLahir_' + i).val().trim()) err.push(pre + 'Tempat Lahir wajib diisi');
        if (!$('#tglLahir_' + i).val()) err.push(pre + 'Tanggal Lahir wajib diisi');
        if (!$('#alamat_' + i).val().trim()) err.push(pre + 'Alamat wajib diisi');

        if (!wa) err.push(pre + 'No WA wajib diisi');
        else if (!REGEX_WA.test(wa)) err.push(pre + 'Format No WA tidak valid (08xxxxxxxxxx)');

        if (!mail) err.push(pre + 'Email wajib diisi');
        else if (!REGEX_EMAIL.test(mail)) err.push(pre + 'Format email tidak valid');
        else if (emails.includes(mail)) err.push('Email duplikat: ' + mail);
        else emails.push(mail);

        if (!$('#jenisKelamin_' + i).val()) err.push(pre + 'Jenis Kelamin wajib dipilih');

        // [PERUBAHAN 1] Jurusan hanya wajib untuk PKL Instansi
        if (_pkl.kategori !== 'Mandiri' && !$('#jurusan_' + i).val().trim()) {
            err.push(pre + 'Jurusan wajib diisi');
        }
    }

    if (err.length) {
        Swal.fire({ icon: 'error', title: 'Validasi Gagal', html: '<div style="text-align:left;max-height:300px;overflow-y:auto;">' + err.map(m => '• ' + m).join('<br>') + '</div>', confirmButtonColor: '#ef4444' });
        return false;
    }
    return true;
}

// ── Generate Biodata Forms (Step 2) ──────────────────────────────────────────
function _pklGenerateBiodata() {
    const $accordion = $('#biodataAccordion').empty();
    // [PERUBAHAN 2] Jurusan hanya dirender untuk PKL Instansi
    const showJurusan = _pkl.kategori !== 'Mandiri';

    for (let i = 1; i <= _pkl.jumlahAnggota; i++) {
        const roleField = _pkl.jumlahAnggota > 1 ? `
            <div class="form-group-pkl">
                <label><i class="fas fa-user-tag"></i> Role dalam Kelompok</label>
                <select id="roleKelompok_${i}" disabled>
                    <option value="ketua"   ${i === 1 ? 'selected' : ''}>Ketua</option>
                    <option value="anggota" ${i > 1 ? 'selected' : ''}>Anggota</option>
                </select>
                <small class="form-hint">
                    <i class="fas fa-lock"></i>
                    ${i === 1 ? 'Anggota pertama otomatis menjadi Ketua' : 'Role Anggota tidak dapat diubah'}
                </small>
            </div>` : '';

        const jurusanField = showJurusan ? `
            <div class="form-group-pkl">
                <label><i class="fas fa-graduation-cap"></i> Jurusan <span class="required">*</span></label>
                <input type="text" id="jurusan_${i}" placeholder="Jurusan/Program Studi">
            </div>` : '';

        $accordion.append(`
            <div class="biodata-item ${i === 1 ? 'active' : ''}" data-index="${i}">
                <div class="biodata-header">
                    <div class="biodata-title">
                        <i class="fas fa-user"></i> Anggota ${i} ${i === 1 && _pkl.jumlahAnggota > 1 ? '(Ketua)' : ''}
                    </div>
                    <i class="fas fa-chevron-down biodata-toggle"></i>
                </div>
                <div class="biodata-content">
                    <div class="form-row">
                        <div class="form-group-pkl">
                            <label><i class="fas fa-id-card"></i> Nama Lengkap <span class="required">*</span></label>
                            <input type="text" id="namaLengkap_${i}" placeholder="Nama lengkap sesuai KTP">
                        </div>
                        <div class="form-group-pkl">
                            <label><i class="fas fa-user"></i> Nama Panggilan <span class="required">*</span></label>
                            <input type="text" id="namaPanggilan_${i}" placeholder="Nama panggilan">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group-pkl">
                            <label><i class="fas fa-map-marked-alt"></i> Tempat Lahir <span class="required">*</span></label>
                            <input type="text" id="tempatLahir_${i}" placeholder="Kota tempat lahir">
                        </div>
                        <div class="form-group-pkl">
                            <label><i class="fas fa-birthday-cake"></i> Tanggal Lahir <span class="required">*</span></label>
                            <input type="text" id="tglLahir_${i}" class="flatpickr-biodata" placeholder="Pilih tanggal">
                        </div>
                    </div>
                    <div class="form-group-pkl">
                        <label><i class="fas fa-home"></i> Alamat <span class="required">*</span></label>
                        <textarea id="alamat_${i}" placeholder="Alamat lengkap"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group-pkl">
                            <label><i class="fas fa-phone"></i> No WA <span class="required">*</span></label>
                            <input type="text" id="noWa_${i}" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="form-group-pkl">
                            <label><i class="fas fa-envelope"></i> Email <span class="required">*</span></label>
                            <input type="email" id="email_${i}" placeholder="email@example.com">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group-pkl">
                            <label><i class="fas fa-venus-mars"></i> Jenis Kelamin <span class="required">*</span></label>
                            <select id="jenisKelamin_${i}">
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        ${jurusanField}
                    </div>
                    ${roleField}
                </div>
            </div>
        `);
    }

    if (typeof flatpickr !== 'undefined') {
        $('.flatpickr-biodata').each(function () {
            flatpickr(this, { dateFormat: 'd-M-Y', maxDate: 'today', allowInput: false, locale: FLATPICKR_ID_LOCALE });
        });
    }

    $accordion.off('click.biodata', '.biodata-header').on('click.biodata', '.biodata-header', function () {
        $(this).parent('.biodata-item').toggleClass('active');
    });
}

// ── Generate Review (Step 3) ──────────────────────────────────────────────────
function _pklGenerateReview() {
    let kelompokHTML = '';

    if (_pkl.kategori === 'Mandiri') {
        kelompokHTML = `
            <div class="review-item"><span class="review-label">Kategori PKL:</span><span class="review-value">Mandiri</span></div>
            <div class="review-item"><span class="review-label">Tanggal Mulai:</span><span class="review-value">${$('#tglMulaiMandiri').val()}</span></div>
            <div class="review-item"><span class="review-label">Tanggal Akhir:</span><span class="review-value">${$('#tglAkhirMandiri').val()}</span></div>`;
    } else {
        const instVal = ($('#namaInstansi').val() || '').toString();
        let instText = $('#namaInstansi option:selected').text();
        if (instVal.startsWith('new_')) instText = instVal.replace('new_', '') + ' (Baru)';

        kelompokHTML = `
            <div class="review-item"><span class="review-label">Kategori PKL:</span><span class="review-value">Instansi</span></div>
            <div class="review-item"><span class="review-label">Kategori Instansi:</span><span class="review-value">${$('#kategoriInstansiPkl').val()}</span></div>
            <div class="review-item"><span class="review-label">Nama Instansi:</span><span class="review-value">${instText}</span></div>
            ${instVal.startsWith('new_') ? `
            <div class="review-item"><span class="review-label">Alamat Instansi:</span><span class="review-value">${$('#alamatInstansiBaru').val()}</span></div>
            <div class="review-item"><span class="review-label">Kota Instansi:</span><span class="review-value">${$('#kotaInstansiBaru option:selected').text()}</span></div>` : ''}
            <div class="review-item"><span class="review-label">Nama Pembimbing:</span><span class="review-value">${$('#namaPembimbing').val()}</span></div>
            <div class="review-item"><span class="review-label">No WA Pembimbing:</span><span class="review-value">${$('#noWaPembimbing').val()}</span></div>
            <div class="review-item"><span class="review-label">Jumlah Anggota:</span><span class="review-value">${_pkl.jumlahAnggota} orang</span></div>
            <div class="review-item"><span class="review-label">Nama Kelompok:</span><span class="review-value">${$('#namaKelompokInstansi').val()}</span></div>
            <div class="review-item"><span class="review-label">Tanggal Mulai:</span><span class="review-value">${$('#tglMulaiInstansi').val()}</span></div>
            <div class="review-item"><span class="review-label">Tanggal Akhir:</span><span class="review-value">${$('#tglAkhirInstansi').val()}</span></div>`;
    }

    $('#reviewKelompok').html(kelompokHTML);

    // [PERUBAHAN 3] Jurusan hanya muncul di review untuk PKL Instansi
    const showJurusan = _pkl.kategori !== 'Mandiri';
    let anggotaHTML = '';

    for (let i = 1; i <= _pkl.jumlahAnggota; i++) {
        const roleText = _pkl.jumlahAnggota > 1 ? ' (' + (i === 1 ? 'Ketua' : 'Anggota') + ')' : '';
        anggotaHTML += `
            <div class="review-anggota-card">
                <div class="review-anggota-name"><i class="fas fa-user"></i> ${$('#namaLengkap_' + i).val()}${roleText}</div>
                <div class="review-anggota-detail"><span class="review-label">Nama Panggilan:</span><span class="review-value">${$('#namaPanggilan_' + i).val()}</span></div>
                <div class="review-anggota-detail"><span class="review-label">Tempat, Tgl Lahir:</span><span class="review-value">${$('#tempatLahir_' + i).val()}, ${$('#tglLahir_' + i).val()}</span></div>
                <div class="review-anggota-detail"><span class="review-label">Alamat:</span><span class="review-value">${$('#alamat_' + i).val()}</span></div>
                <div class="review-anggota-detail"><span class="review-label">No WA:</span><span class="review-value">${$('#noWa_' + i).val()}</span></div>
                <div class="review-anggota-detail"><span class="review-label">Email:</span><span class="review-value">${$('#email_' + i).val()}</span></div>
                <div class="review-anggota-detail"><span class="review-label">Jenis Kelamin:</span><span class="review-value">${$('#jenisKelamin_' + i).val()}</span></div>
                ${showJurusan ? `<div class="review-anggota-detail"><span class="review-label">Jurusan:</span><span class="review-value">${$('#jurusan_' + i).val()}</span></div>` : ''}
            </div>`;
    }

    $('#reviewAnggota').html(anggotaHTML);
    $('#reviewJumlahAnggota').text(_pkl.jumlahAnggota);
}

// ── Submit ────────────────────────────────────────────────────────────────────
function _pklSubmit() {
    Swal.fire({
        title: 'Konfirmasi', text: 'Apakah data yang Anda masukkan sudah benar?', icon: 'question',
        showCancelButton: true, confirmButtonColor: '#0f766e', cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Simpan!', cancelButtonText: 'Periksa Lagi'
    }).then(result => {
        if (!result.isConfirmed) return;

        Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url: _pkl.urls.store, method: 'POST',
            data: JSON.stringify(_pklPrepareData()), contentType: 'application/json', processData: false,
            success: function () {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data PKL berhasil ditambahkan', timer: 2000, showConfirmButton: false });
                setTimeout(() => { window.location.href = _pkl.urls.redirect; }, 2000);
            },
            error: function (xhr) {
                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Gagal menyimpan data', confirmButtonColor: '#ef4444' });
            }
        });
    });
}

function _pklPrepareData() {
    const data = { kategori_pkl: _pkl.kategori, jumlah_anggota: _pkl.jumlahAnggota, anggota: [] };

    if (_pkl.kategori === 'Mandiri') {
        data.tgl_mulai = $('#tglMulaiMandiri').val();
        data.tgl_akhir = $('#tglAkhirMandiri').val();
    } else {
        data.kategori_instansi = $('#kategoriInstansiPkl').val();
        const instVal = ($('#namaInstansi').val() || '').toString();

        if (instVal.startsWith('new_')) {
            data.instansi_baru = { nama: instVal.replace('new_', ''), alamat: $('#alamatInstansiBaru').val(), kota: $('#kotaInstansiBaru').val() };
        } else {
            data.id_instansi = instVal;
        }

        data.nama_pembimbing = $('#namaPembimbing').val();
        data.no_wa_pembimbing = $('#noWaPembimbing').val();
        data.nama_kelompok = $('#namaKelompokInstansi').val();
        data.tgl_mulai = $('#tglMulaiInstansi').val();
        data.tgl_akhir = $('#tglAkhirInstansi').val();
    }

    for (let i = 1; i <= _pkl.jumlahAnggota; i++) {
        data.anggota.push({
            nama_lengkap: $('#namaLengkap_' + i).val(),
            nama_panggilan: $('#namaPanggilan_' + i).val(),
            tempat_lahir: $('#tempatLahir_' + i).val(),
            tgl_lahir: $('#tglLahir_' + i).val(),
            alamat: $('#alamat_' + i).val(),
            no_wa: $('#noWa_' + i).val(),
            email: $('#email_' + i).val(),
            jenis_kelamin: $('#jenisKelamin_' + i).val(),
            // [PERUBAHAN 4] Kirim null untuk Mandiri, isi dari input untuk Instansi
            jurusan: _pkl.kategori !== 'Mandiri' ? ($('#jurusan_' + i).val() || null) : null,
            role: _pkl.jumlahAnggota > 1 ? (i === 1 ? 'ketua' : 'anggota') : 'ketua'
        });
    }

    return data;
}


// =============================================================================
// PKL DETAIL — Edit Mode
// =============================================================================

let _detail = { isEdit: false, fpInstances: [], urls: {}, id: null };

function pklDetailInit(pklId, urls) {
    _detail.id = pklId;
    _detail.urls = urls;

    $('.anggota-header').on('click', function () {
        $(this).parent('.anggota-item').toggleClass('active');
    });

    $('#btnEdit').on('click', () => _detailEnterEdit());
    $('#btnCancel').on('click', () => _detailExitEdit());
    $('#btnSave').on('click', () => _detailSave());
}

function _detailEnterEdit() {
    _detail.isEdit = true;

    $('.view-anggota').hide();
    $('.edit-anggota').show();
    $('#viewInstansi').hide();
    $('#editInstansi').show();

    if (typeof flatpickr !== 'undefined') {
        $('.flatpickr-biodata').each(function () {
            _detail.fpInstances.push(
                flatpickr(this, { dateFormat: 'd-M-Y', maxDate: 'today', allowInput: false, locale: FLATPICKR_ID_LOCALE })
            );
        });
    }

    $('#btnEdit').hide();
    $('#btnCancel, #btnSave').show();
    $('.anggota-item').addClass('active');

    showToast('info', 'Mode Edit Aktif', 'Anda dapat mengubah data PKL sekarang');
}

function _detailExitEdit() {
    Swal.fire({
        title: 'Batalkan Perubahan?', text: 'Semua perubahan yang belum disimpan akan hilang', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Batalkan', cancelButtonText: 'Tidak'
    }).then(result => {
        if (!result.isConfirmed) return;

        _detail.isEdit = false;

        $('.view-anggota').show();
        $('.edit-anggota').hide();
        $('#viewInstansi').show();
        $('#editInstansi').hide();

        _detail.fpInstances.forEach(inst => inst.destroy());
        _detail.fpInstances = [];

        $('#btnEdit').show();
        $('#btnCancel, #btnSave').hide();
        $('.anggota-item').removeClass('active');
        $('.anggota-item:first').addClass('active');

        showToast('info', 'Perubahan Dibatalkan', 'Data kembali ke kondisi semula');
    });
}

function _detailSave() {
    const formData = _detailCollect();
    if (!_detailValidate(formData)) return;

    Swal.fire({
        title: 'Simpan Perubahan?', text: 'Pastikan semua data sudah benar', icon: 'question',
        showCancelButton: true, confirmButtonColor: '#0f766e', cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Simpan!', cancelButtonText: 'Periksa Lagi'
    }).then(result => {
        if (!result.isConfirmed) return;

        Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.ajax({
            url: _detail.urls.update + '/' + _detail.id, method: 'POST',
            data: JSON.stringify(formData), contentType: 'application/json',
            success: function () {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Perubahan berhasil disimpan', timer: 2000, showConfirmButton: false });
                setTimeout(() => location.reload(), 2000);
            },
            error: function (xhr) {
                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Gagal menyimpan perubahan', confirmButtonColor: '#ef4444' });
            }
        });
    });
}

function _detailCollect() {
    const data = { instansi: {}, anggota: [] };

    if ($('#editInstansi').length) {
        data.instansi = {
            nama_pembimbing: $('#editPembimbing').val().trim(),
            no_wa_pembimbing: $('#editNoWaPembimbing').val().trim()
        };
    }

    $('.anggota-item').each(function () {
        data.anggota.push({
            // BUG-01 FIX: kirim id_pkl bukan id_user.
            // BE updatePkl() baca $anggota['id_pkl'] untuk UPDATE tabel pkl.
            // data-id-pkl diset PHP di pkl_detail.php dari $anggota['id_pkl'].
            id_pkl: $(this).data('id-pkl'),
            nama_lengkap: $(this).find('.edit-nama-lengkap').val().trim(),
            nama_panggilan: $(this).find('.edit-nama-panggilan').val().trim(),
            tempat_lahir: $(this).find('.edit-tempat-lahir').val().trim(),
            tgl_lahir: $(this).find('.edit-tgl-lahir').val(),
            alamat: $(this).find('.edit-alamat').val().trim(),
            no_wa: $(this).find('.edit-no-wa').val().trim(),
            jenis_kelamin: $(this).find('.edit-jenis-kelamin').val(),
            jurusan: $(this).find('.edit-jurusan').val()?.trim() || null
        });
    });

    return data;
}

function _detailValidate(data) {
    const err = [];

    if (data.instansi && Object.keys(data.instansi).length > 0) {
        if (!data.instansi.nama_pembimbing) err.push('Nama Pembimbing wajib diisi');
        if (!data.instansi.no_wa_pembimbing) err.push('No WA Pembimbing wajib diisi');
        else if (!REGEX_WA.test(data.instansi.no_wa_pembimbing)) err.push('Format No WA Pembimbing tidak valid');
    }

    const isMandiri = !data.instansi || Object.keys(data.instansi).length === 0;

    data.anggota.forEach((a, idx) => {
        const pre = 'Anggota ' + (idx + 1) + ': ';
        if (!a.nama_lengkap) err.push(pre + 'Nama Lengkap wajib diisi');
        if (!a.nama_panggilan) err.push(pre + 'Nama Panggilan wajib diisi');
        if (!a.tempat_lahir) err.push(pre + 'Tempat Lahir wajib diisi');
        if (!a.tgl_lahir) err.push(pre + 'Tanggal Lahir wajib diisi');
        if (!a.alamat) err.push(pre + 'Alamat wajib diisi');
        if (!a.no_wa) err.push(pre + 'No WA wajib diisi');
        else if (!REGEX_WA.test(a.no_wa)) err.push(pre + 'Format No WA tidak valid');
        if (!a.jenis_kelamin) err.push(pre + 'Jenis Kelamin wajib dipilih');
        // Jurusan hanya wajib untuk Instansi
        if (!isMandiri && !a.jurusan) err.push(pre + 'Jurusan wajib diisi');
    });

    if (err.length) {
        Swal.fire({ icon: 'error', title: 'Validasi Gagal', html: '<div style="text-align:left;max-height:300px;overflow-y:auto;">' + err.map(m => '• ' + m).join('<br>') + '</div>', confirmButtonColor: '#ef4444' });
        return false;
    }
    return true;
}


// =============================================================================
// PRIVATE HELPERS
// =============================================================================

function _buildKotaTagsConfig() {
    return {
        tags: true, placeholder: 'Pilih atau Ketik Kota Baru', allowClear: true, width: '100%',
        createTag: function (params) {
            const term = $.trim(params.term);
            if (!term) return null;
            const cap = term.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase()).join(' ');
            return { id: cap, text: cap, newTag: true };
        },
        insertTag: function (data, tag) { data.unshift(tag); },
        templateResult: function (data) {
            if (data.loading) return data.text;
            if (data.newTag) return $('<span><i class="fas fa-plus-circle" style="color:#0f766e;margin-right:6px;"></i> Tambah: <strong>' + data.text + '</strong></span>');
            return data.text;
        },
        language: { noResults: () => 'Ketik untuk menambah kota baru', searching: () => 'Mencari...' }
    };
}