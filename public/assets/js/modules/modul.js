/**
 * assets/js/modules/modul.js
 * Data Modul — Shared Utilities & All Page Logic
 *
 * File ini TIDAK boleh mengandung PHP interpolation (<?= ?>).
 * Semua nilai dari PHP (URL, data modul) dikirim lewat parameter fungsi.
 *
 * Fungsi yang tersedia:
 *   - Main      : modulMainInit(baseUrl)
 *   - Kategori  : initKategoriModulTable(), initKategoriModulEvents()
 *   - Data Modul: initDataModulTable(), initDataModulEvents()
 *   - Tambah    : modulTambahInit(urls)
 *   - Ubah      : modulUbahInit(urls, modulData)
 */

'use strict';

// =============================================================================
// SHARED CONSTANTS
// =============================================================================

const DT_LANG_ID_MODUL = {
    lengthMenu: 'Tampilkan _MENU_ data',
    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
    infoFiltered: '(disaring dari _MAX_ total data)',
    emptyTable: 'Tidak ada data yang tersedia',
    zeroRecords: 'Tidak ada data yang cocok',
    paginate: {
        first: 'Pertama',
        last: 'Terakhir',
        next: 'Selanjutnya',
        previous: 'Sebelumnya',
    },
};

const ALLOWED_FILE_EXTS = ['pdf', 'docx', 'doc', 'pptx', 'ppt', 'xlsx', 'xls', 'zip', 'rar'];
const MAX_FILE_SIZE_BYTES = 300 * 1024 * 1024; // 300 MB

/** Config Select2 standar untuk dropdown kategori */
function _select2Config(placeholder) {
    return {
        placeholder: placeholder,
        allowClear: true,
        width: '100%',
        language: {
            noResults: () => 'Kategori tidak ditemukan',
            searching: () => 'Mencari...',
        },
    };
}


// =============================================================================
// MODULE MAIN — Tab Loading (main_modul.php)
// =============================================================================

let _modulBaseUrl = '';

function modulMainInit(baseUrl) {
    _modulBaseUrl = baseUrl;
    _loadModulTab('kategori');

    $('.modul-tab-btn').on('click', function () {
        $('.modul-tab-btn').removeClass('active');
        $(this).addClass('active');
        _loadModulTab($(this).data('target'));
    });
}

window.reloadCurrentModulTab = function () {
    const tab = $('.modul-tab-btn.active').data('target') || 'kategori';
    _loadModulTab(tab);
};

function _loadModulTab(tab) {
    const $overlay = $('#loadingOverlay');
    const $contentArea = $('#tabContentArea');
    const url = tab === 'kategori'
        ? _modulBaseUrl + '/load-kategori'
        : _modulBaseUrl + '/load-modul';

    $overlay.addClass('active');
    $contentArea.html('');

    $.ajax({
        url, method: 'GET', timeout: 10000,
        success: function (response) {
            $contentArea.html(response);
            $overlay.removeClass('active');

            setTimeout(function () {
                if (tab === 'kategori') {
                    if (typeof initKategoriModulTable === 'function') initKategoriModulTable();
                    if (typeof initKategoriModulEvents === 'function') initKategoriModulEvents();
                } else {
                    if (typeof initDataModulTable === 'function') initDataModulTable();
                    if (typeof initDataModulEvents === 'function') initDataModulEvents();
                }
            }, 100);
        },

        error: function (xhr, status) {
            $overlay.removeClass('active');

            let msg = 'Gagal memuat konten';
            if (status === 'timeout') {
                msg = 'Request timeout (>10s)';
            } else if (xhr.status === 404) {
                msg = 'Halaman tidak ditemukan (404)';
            } else if (xhr.status === 500) {
                // ── Coba baca pesan error asli dari server ──────────────
                try {
                    const json = JSON.parse(xhr.responseText);
                    msg = json.message || 'Server error (500)';
                } catch (e) {
                    msg = e;
                }
            }

            $contentArea.html(`
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <h3 class="empty-state-title">Gagal Memuat</h3>
                    <p class="empty-state-description">${msg}</p>
                </div>
            `);

            _showAlert('error', 'Error', msg);
        },

    });
}


// =============================================================================
// KATEGORI MODUL — Table + Events
// =============================================================================

function initKategoriModulTable() {
    if (typeof $.fn.DataTable === 'undefined') return;
    if ($.fn.DataTable.isDataTable('#tableKategoriModul')) {
        $('#tableKategoriModul').DataTable().destroy();
    }

    try {
        window.tableKategoriModul = $('#tableKategoriModul').DataTable({
            paging: true,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            searching: false,
            ordering: true,
            order: [[0, 'asc']],
            info: true,
            autoWidth: false,
            language: DT_LANG_ID_MODUL,
        });
    } catch (err) {
        console.error('[modul.js] initKategoriModulTable:', err);
    }
}

function initKategoriModulEvents() {

    // ---- Inline Search (langsung filter baris tabel) ----
    $('#filterNamaKategoriModul').off('keyup').on('keyup', function () {
        const val = this.value.toLowerCase();
        $('#tableKategoriModul tbody tr').each(function () {
            $(this).toggle($(this).find('td:eq(1)').text().toLowerCase().includes(val));
        });
    });

    // ---- Toggle Form (Tambah) ----
    $('#btnTambahKategoriModul').off('click').on('click', function () {
        const $formSection = $('#formSectionKategoriModul');
        const isOpen = $formSection.hasClass('show');

        $formSection.toggleClass('show', !isOpen);

        if (!isOpen) {
            $('#formTitleKategoriModul').text('Tambah Kategori Modul');
            $('#formKategoriModul')[0].reset();
            $('#kategoriModulId').val('');
        }

        $(this).find('i').toggleClass('fa-plus', isOpen).toggleClass('fa-arrow-left', !isOpen);
        $(this).find('.btn-text').text(isOpen ? 'Tambah' : 'Kembali');
    });

    // ---- Batal / Close Form ----
    $('#btnBatalKategoriModul').off('click').on('click', _closeKategoriForm);

    // Reset Inline Search
    $('#btnResetKategoriModul').off('click').on('click', function () {
        $('#filterNamaKategoriModul').val('').trigger('keyup');
    });

    // ---- Submit Form ----
    $('#formKategoriModul').off('submit').on('submit', function (e) {
        e.preventDefault();

        const id = $('#kategoriModulId').val();
        const namaKategori = $('#namaKategoriModul').val().trim();

        if (!namaKategori) {
            _showAlert('warning', 'Perhatian', 'Nama kategori harus diisi!');
            return;
        }

        _showLoading('Menyimpan...');

        const url = id
            ? _modulBaseUrl + '/kategori/update/' + id
            : _modulBaseUrl + '/kategori/store';

        $.ajax({
            url, method: 'POST',
            data: { nama_kategori: namaKategori },
            success: function (response) {
                _showToast('success', 'Berhasil!', response.message || 'Kategori berhasil disimpan');
                _closeKategoriForm();
                setTimeout(window.reloadCurrentModulTab, 1500);
            },
            error: function (xhr) {
                _showAlert('error', 'Error', xhr.responseJSON?.message || 'Gagal menyimpan data');
            },
        });
    });
}

function _closeKategoriForm() {
    $('#formSectionKategoriModul').removeClass('show');
    $('#formKategoriModul')[0].reset();
    $('#kategoriModulId').val('');
    $('#btnTambahKategoriModul').find('i').removeClass('fa-arrow-left').addClass('fa-plus');
    $('#btnTambahKategoriModul').find('.btn-text').text('Tambah');
}

window.editKategoriModul = function (id) {
    $('#formSectionKategoriModul').addClass('show');
    $('#formTitleKategoriModul').text('Edit Kategori Modul');
    $('#btnTambahKategoriModul').find('i').removeClass('fa-plus').addClass('fa-arrow-left');
    $('#btnTambahKategoriModul').find('.btn-text').text('Kembali');

    $.ajax({
        url: _modulBaseUrl + '/kategori/' + id,
        method: 'GET',
        success: function (response) {
            if (response.success) {
                $('#kategoriModulId').val(response.data.id);
                $('#namaKategoriModul').val(response.data.nama_kategori);
            } else {
                _showAlert('error', 'Error', 'Gagal memuat data kategori');
            }
        },
        error: function () {
            _showAlert('error', 'Error', 'Gagal memuat data kategori');
        },
    });

    $('html, body').animate({ scrollTop: $('#formSectionKategoriModul').offset().top - 100 }, 400);
};

window.hapusKategoriModul = function (id) {
    if (typeof Swal === 'undefined') {
        if (confirm('Yakin ingin menghapus kategori ini?')) _deleteKategoriModul(id);
        return;
    }
    Swal.fire({
        title: 'Konfirmasi Hapus', text: 'Yakin ingin menghapus kategori ini?',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#ef4444', cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal',
    }).then(function (result) {
        if (result.isConfirmed) _deleteKategoriModul(id);
    });
};

function _deleteKategoriModul(id) {
    _showLoading('Menghapus...');
    $.ajax({
        url: _modulBaseUrl + '/kategori/delete/' + id, method: 'POST',
        success: function (response) {
            _showToast('success', 'Terhapus!', response.message || 'Kategori berhasil dihapus');
            setTimeout(window.reloadCurrentModulTab, 1500);
        },
        error: function (xhr) {
            _showAlert('error', 'Error', xhr.responseJSON?.message || 'Gagal menghapus data');
        },
    });
}


// =============================================================================
// DATA MODUL — Table + Events
// =============================================================================

function initDataModulTable() {
    if (typeof $.fn.DataTable === 'undefined') return;
    if ($.fn.DataTable.isDataTable('#tableDataModul')) {
        $('#tableDataModul').DataTable().destroy();
    }

    try {
        window.tableDataModul = $('#tableDataModul').DataTable({
            paging: true,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            searching: false,
            ordering: true,
            order: [[0, 'asc']],
            info: true,
            autoWidth: false,
            language: DT_LANG_ID_MODUL,
            columnDefs: [
                { targets: [1, 3], orderable: true },
                { targets: 5, orderable: false },
            ],
        });
    } catch (err) {
        console.error('[modul.js] initDataModulTable:', err);
    }
}

function initDataModulEvents() {

    // ---- Select2 untuk filter Kategori ----
    if (typeof $.fn.select2 !== 'undefined') {
        $('#filterKategoriModul').select2(
            _select2Config('Semua Kategori')
        );
    }

    // ---- Toggle Filter Section ----
    $('#btnFilterModul').off('click').on('click', function () {
        const $filterSection = $('#filterSectionModul');
        const isOpen = $filterSection.hasClass('show');

        $filterSection.toggleClass('show', !isOpen);
        $(this).find('i').toggleClass('fa-filter', isOpen).toggleClass('fa-arrow-left', !isOpen);
        $(this).find('.btn-text').text(isOpen ? 'Filter' : 'Kembali');
    });

    // ---- Reset Filter ----
    $('#btnResetFilterModul').off('click').on('click', function () {
        $('#filterNamaModul').val('');
        $('#filterKategoriModul').val('').trigger('change');
        $('#tableDataModul tbody tr').show();
        _showToast('success', 'Filter Direset');
    });

    // ---- Filter by Nama ----
    $('#filterNamaModul').off('keyup').on('keyup', function () {
        _applyDataModulFilter();
    });

    // ---- Filter by Kategori (Select2) ----
    // ---- Filter by Kategori (Select2) ----
    $('#filterKategoriModul').off('change').on('change', function () {

        // Manual fix rendered text Select2 (sama seperti filterKotaInstansi)
        setTimeout(function () {
            const selectedText = $('#filterKategoriModul').find('option:selected').text();
            const $rendered = $('#filterKategoriModul').next('.select2-container')
                .find('.select2-selection__rendered');
            if (selectedText && selectedText !== 'Semua Kategori') {
                $rendered.text(selectedText).attr('title', selectedText);
            } else {
                $rendered.html('<span class="select2-selection__placeholder">Semua Kategori</span>')
                    .attr('title', 'Semua Kategori');
            }
        }, 10);

        _applyDataModulFilter();
    });
}

/** Terapkan semua filter aktif sekaligus ke baris tabel */
function _applyDataModulFilter() {
    const nama = $('#filterNamaModul').val().toLowerCase();
    const kategori = $('#filterKategoriModul').val().toLowerCase();

    $('#tableDataModul tbody tr').each(function () {
        const namaMatch = !nama || $(this).find('td:eq(1)').text().toLowerCase().includes(nama);
        const kategoriMatch = !kategori || $(this).find('td:eq(2)').text().toLowerCase().includes(kategori);
        $(this).toggle(namaMatch && kategoriMatch);
    });
}

// =============================================================================
// [DIUBAH] hapusModul — terima baseUrl sebagai argumen kedua
//
// Sebelum:
//   window.hapusModul = function (id) { ... _deleteModul(id) }
//   function _deleteModul(id) { url: _modulBaseUrl + '/modul/delete/' + id }
//
//   Masalah: _modulBaseUrl kosong jika modulMainInit belum terpanggil
//            (contoh: setelah page refresh atau direct URL access).
//            Akibatnya URL menjadi relatif → ERR_CONNECTION_REFUSED.
//
// Sesudah:
//   window.hapusModul = function (id, baseUrl) { ... _deleteModul(id, baseUrl) }
//   function _deleteModul(id, baseUrl) { url: (baseUrl || _modulBaseUrl) + '/modul/delete/' + id }
//
//   URL selalu absolut karena dikirim langsung dari PHP di onclick tombol.
// =============================================================================

window.hapusModul = function (id, baseUrl) {
    if (typeof Swal === 'undefined') {
        if (confirm('Yakin ingin menghapus modul ini?')) _deleteModul(id, baseUrl);
        return;
    }
    Swal.fire({
        title: 'Konfirmasi Hapus', text: 'Yakin ingin menghapus modul ini?',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#ef4444', cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal',
    }).then(function (result) {
        if (result.isConfirmed) _deleteModul(id, baseUrl);
    });
};

function _deleteModul(id, baseUrl) {
    // Gunakan baseUrl dari argumen (dikirim PHP), fallback ke _modulBaseUrl
    const base = baseUrl || _modulBaseUrl;

    _showLoading('Menghapus...');
    $.ajax({
        url: base + '/modul/delete/' + id, method: 'POST',
        success: function (response) {
            _showToast('success', 'Terhapus!', response.message || 'Modul berhasil dihapus');
            setTimeout(window.reloadCurrentModulTab, 1500);
        },
        error: function (xhr) {
            _showAlert('error', 'Error', xhr.responseJSON?.message || 'Gagal menghapus data');
        },
    });
}


// =============================================================================
// MODUL TAMBAH (modul_tambah.php)
// Dipanggil: modulTambahInit(urls)
// urls = { store: '...', back: '...' }
// =============================================================================

function modulTambahInit(urls) {
    let selectedFile = null;

    // ---- Select2 untuk Kategori ----
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2-kategori-modul').select2(
            _select2Config('Pilih Kategori')
        );
    }

    _initCharCounter('#ketModul', '#charCount', 500);
    _initTipeToggle(null);

    const $fileUploadArea = $('#fileUploadArea');
    const $fileInput = $('#modulFile');

    _initFileUpload($fileUploadArea, $fileInput, {
        onSelect: function (file) {
            selectedFile = file;
            _displayFileInfo(file);
        },
        onClear: function () {
            selectedFile = null;
            $('#fileSelectedInfo').removeClass('show');
            $('#fileError').removeClass('show').text('');
        },
    });

    // ---- Form Submit ----
    $('#formTambahModul').on('submit', function (e) {
        e.preventDefault();

        const namaModul = $('#namaModul').val().trim();
        const kategoriModul = $('#kategoriModul').val();
        const ketModul = $('#ketModul').val().trim();
        const tipe = $('input[name="tipe"]:checked').val();

        if (!namaModul || !kategoriModul || !ketModul) {
            _showAlert('warning', 'Perhatian', 'Semua field wajib diisi!');
            return;
        }

        if (tipe === 'link') {
            const link = $('#modulLink').val().trim();
            if (!link) { _showAlert('warning', 'Perhatian', 'URL Link harus diisi!'); return; }
            if (!link.startsWith('https://')) { _showAlert('warning', 'URL Tidak Valid', 'URL harus dimulai dengan https://'); return; }
        } else {
            if (!selectedFile) { _showAlert('warning', 'Perhatian', 'File harus dipilih!'); return; }
        }

        _showLoading('Menyimpan...');

        // [FIX] Bangun FormData manual dan sisipkan selectedFile secara eksplisit.
        //
        // MASALAH: new FormData(this) membaca file dari elemen <input type="file"> di DOM.
        //   - Choose file  → file masuk ke <input> → FormData membawa file ✓
        //   - Drag & drop  → file hanya ada di variabel selectedFile (JS),
        //                    <input> tetap KOSONG → FormData tidak membawa file →
        //                    server menerima request tanpa file → $file->isValid() = false
        //                    → 400 Bad Request ✗
        //
        // SOLUSI: Setelah FormData dibuat, timpa field 'modul_file' dengan selectedFile
        //   menggunakan formData.set() agar drag & drop dan choose file diperlakukan sama.
        const formData = new FormData(this);
        if (selectedFile) {
            formData.set('modul_file', selectedFile, selectedFile.name);
        }

        $.ajax({
            url: urls.store, method: 'POST',
            data: formData, processData: false, contentType: false,
            success: function (response) {
                _showToast('success', 'Berhasil!', response.message || 'Modul berhasil ditambahkan');
                setTimeout(function () { window.location.href = urls.back; }, 1500);
            },
            error: function (xhr) {
                _showAlert('error', 'Error', xhr.responseJSON?.message || 'Gagal menyimpan data');
            },
        });
    });
}


// =============================================================================
// MODUL UBAH (modul_ubah.php)
// Dipanggil: modulUbahInit(urls, modulData)
// urls      = { update: '...', back: '...' }
// modulData = { id: ..., tipe: '...' }
// =============================================================================

function modulUbahInit(urls, modulData) {
    let selectedFile = null;
    let fileReplaced = false;

    // ---- Select2 untuk Kategori ----
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2-kategori-modul').select2(
            _select2Config('Pilih Kategori')
        );
    }

    _initCharCounter('#ketModul', '#charCount', 500);
    _initTipeToggle(modulData.tipe, function () {
        selectedFile = null;
        fileReplaced = false;
        $('#fileSelectedInfo').removeClass('show');
        $('#fileError').removeClass('show').text('');
    });

    const $fileUploadArea = $('#fileUploadArea');
    const $fileInput = $('#modulFile');

    _initFileUpload($fileUploadArea, $fileInput, {
        onSelect: function (file) {
            selectedFile = file;
            fileReplaced = true;
            _displayFileInfo(file);
        },
        onClear: function () {
            selectedFile = null;
            fileReplaced = false;
            $('#fileSelectedInfo').removeClass('show');
            $('#fileError').removeClass('show').text('');
        },
    });

    // ---- Form Submit ----
    $('#formUbahModul').on('submit', function (e) {
        e.preventDefault();

        const namaModul = $('#namaModul').val().trim();
        const kategoriModul = $('#kategoriModul').val();
        const ketModul = $('#ketModul').val().trim();
        const tipe = $('input[name="tipe"]:checked').val();

        if (!namaModul || !kategoriModul || !ketModul) {
            _showAlert('warning', 'Perhatian', 'Semua field wajib diisi!');
            return;
        }

        if (tipe === 'link') {
            const link = $('#modulLink').val().trim();
            if (!link) { _showAlert('warning', 'Perhatian', 'URL Link harus diisi!'); return; }
            if (!link.startsWith('https://')) { _showAlert('warning', 'URL Tidak Valid', 'URL harus dimulai dengan https://'); return; }
        } else {
            const needFile = modulData.tipe !== 'file' || fileReplaced;
            if (needFile && !selectedFile) { _showAlert('warning', 'Perhatian', 'File harus dipilih!'); return; }
        }

        _showLoading('Menyimpan perubahan...');

        // [FIX] Sisipkan selectedFile secara eksplisit ke FormData.
        // Alasan sama dengan modulTambahInit: drag & drop tidak mengisi <input type="file">,
        // sehingga new FormData(form) tidak akan membawa file ke server.
        const formData = new FormData(this);
        if (fileReplaced) formData.set('file_replaced', '1');
        if (fileReplaced && selectedFile) {
            formData.set('modul_file', selectedFile, selectedFile.name);
        }

        $.ajax({
            url: urls.update + '/' + modulData.id, method: 'POST',
            data: formData, processData: false, contentType: false,
            success: function (response) {
                _showToast('success', 'Berhasil!', response.message || 'Modul berhasil diperbarui');
                setTimeout(function () { window.location.href = urls.back; }, 1500);
            },
            error: function (xhr) {
                _showAlert('error', 'Error', xhr.responseJSON?.message || 'Gagal menyimpan perubahan');
            },
        });
    });
}


// =============================================================================
// PRIVATE HELPERS
// =============================================================================

function _initCharCounter(textareaSelector, counterSelector, max) {
    const $textarea = $(textareaSelector);
    const $counter = $(counterSelector);

    const initialLen = $textarea.val().length;
    $counter.text(initialLen);
    _updateCounterColor($counter, initialLen, max);

    $textarea.on('input', function () {
        const len = $(this).val().length;
        $counter.text(len);
        _updateCounterColor($counter, len, max);
    });
}

function _updateCounterColor($counter, len, max) {
    $counter.parent()
        .toggleClass('danger', len > max * 0.9)
        .toggleClass('warning', len > max * 0.8 && len <= max * 0.9);
}

function _initTipeToggle(oldTipe, onChange) {
    // [FIX] Paksa hapus required dari file input sejak awal.
    // Drag & drop menyimpan file di selectedFile (JS), bukan di <input type="file">.
    // Jika required aktif, browser validasi native HTML5 saat submit: menemukan input
    // kosong → coba fokus ke input tersembunyi → "An invalid form control is not focusable".
    // Validasi sudah dihandle manual di JS (if (!selectedFile)), jadi required tidak perlu.
    $('#modulFile').prop('required', false).removeAttr('required');

    $('input[name="tipe"]').on('change', function () {
        const tipe = $(this).val();

        if (tipe === 'link') {
            $('#fieldLink').addClass('show');
            $('#fieldFile').removeClass('show');
            $('#modulLink').prop('required', true);
            // [FIX] Jangan set required pada file input
        } else {
            $('#fieldLink').removeClass('show');
            $('#fieldFile').addClass('show');
            $('#modulLink').prop('required', false);
            // [FIX] Jangan set required pada file input — validasi ada di JS
        }

        if (typeof onChange === 'function') onChange(tipe);
    });
}

function _initFileUpload($area, $input, callbacks) {
    // [FIX] Hentikan propagasi klik dari $input ke $area.
    // Tanpa ini: klik $area → $input.click() → event bubble ke $area
    // → loop tak terbatas → Maximum call stack size exceeded
    $input.on('click', function (e) { e.stopPropagation(); });

    $area.on('click', function (e) {
        if ($(e.target).closest('#btnRemoveFile').length) return;
        $input.click();
    });

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function (ev) {
        $area[0].addEventListener(ev, function (e) { e.preventDefault(); e.stopPropagation(); }, false);
    });

    ['dragenter', 'dragover'].forEach(function (ev) {
        $area[0].addEventListener(ev, function () { $area.addClass('dragover'); }, false);
    });

    ['dragleave', 'drop'].forEach(function (ev) {
        $area[0].addEventListener(ev, function () { $area.removeClass('dragover'); }, false);
    });

    $area[0].addEventListener('drop', function (e) {
        const files = e.dataTransfer.files;
        if (files.length > 0) _handleFile(files[0], callbacks);
    }, false);

    $input.on('change', function () {
        if (this.files.length > 0) _handleFile(this.files[0], callbacks);
    });

    $('#btnRemoveFile').on('click', function (e) {
        e.stopPropagation();
        $input.val('');
        callbacks.onClear();
    });
}

function _handleFile(file, callbacks) {
    const ext = file.name.split('.').pop().toLowerCase();

    $('#fileError').removeClass('show').text('');

    if (!ALLOWED_FILE_EXTS.includes(ext)) {
        const msg = 'Format tidak didukung. Gunakan: PDF, DOCX, PPTX, XLSX, ZIP, RAR';
        $('#fileError').addClass('show').text(msg);
        _showAlert('error', 'File Tidak Valid', msg);
        callbacks.onClear();
        return;
    }

    if (file.size > MAX_FILE_SIZE_BYTES) {
        const msg = 'Ukuran file melebihi batas maksimal 300 MB';
        $('#fileError').addClass('show').text(msg);
        _showAlert('error', 'File Terlalu Besar', msg);
        callbacks.onClear();
        return;
    }

    callbacks.onSelect(file);
}

function _displayFileInfo(file) {
    $('#fileName').text(file.name);
    $('#fileSize').text(_formatFileSize(file.size));
    $('#fileSelectedInfo').addClass('show');
}

function _formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const units = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return (bytes / Math.pow(k, i)).toFixed(2) + ' ' + units[i];
}

function _showAlert(icon, title, text) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({ icon, title, text, confirmButtonColor: '#0f766e' });
    } else {
        alert(title + ': ' + text);
    }
}

function _showToast(icon, title, text) {
    if (typeof Swal === 'undefined') return;
    Swal.mixin({
        toast: true, position: 'top-end',
        showConfirmButton: false, timer: 2500, timerProgressBar: true,
    }).fire({ icon, title, text });
}

function _showLoading(title) {
    if (typeof Swal === 'undefined') return;
    Swal.fire({ title, allowOutsideClick: false, didOpen: () => Swal.showLoading() });
}