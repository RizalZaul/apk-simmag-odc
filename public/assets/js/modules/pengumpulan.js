/* =============================================================================
 * PENGUMPULAN.JS
 * JS Terpusat untuk Modul Manajemen Tugas - Pengumpulan
 * Hanya fungsi yang TIDAK mengandung PHP vars (base_url, csrf_hash)
 *
 * DEPENDS ON:
 *   window.BASE_URL — didefinisikan inline di main_pengumpulan.php
 *
 * SECTIONS:
 *   1. Utility
 *   2. Tab Loader
 *   3. Generic Table Init & Filter (dipakai Mandiri / Kelompok / Tim)
 *   4. Mandiri Events
 *   5. Kelompok Events
 *   6. Tim Events
 *   7. Detail Page — Item Review
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

function showLoading(title = 'Memproses...') {
    if (typeof Swal !== 'undefined') {
        Swal.fire({ title, allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    }
}

function getStatusBadgeClass(status) {
    const map = {
        'Done': 'badge-success',
        'Revisi': 'badge-warning',
        'Belum Diperiksa': 'badge-info',
        'Submit': 'badge-info',
        'Belum Dikirim': 'badge-gray',
    };
    return map[status] || 'badge-gray';
}


/* =============================================================================
 * 2. TAB LOADER
 * ============================================================================= */

function loadPengumpulanTab(tab) {
    const overlay = $('#loadingOverlay');
    const contentArea = $('#tabContentArea');

    const urlMap = {
        mandiri: window.BASE_URL + '/load-mandiri',
        kelompok: window.BASE_URL + '/load-kelompok',
        tim: window.BASE_URL + '/load-tim',
    };

    const url = urlMap[tab] || urlMap.mandiri;

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
                // Init flatpickr untuk semua tab
                const fpClass = tab === 'mandiri' ? '.flatpickr-mandiri'
                    : tab === 'kelompok' ? '.flatpickr-kelompok'
                        : '.flatpickr-tim';

                if (typeof flatpickr !== 'undefined' && $(fpClass).length) {
                    flatpickr(fpClass, { dateFormat: 'd-m-Y', allowInput: false, locale: 'id' });
                }

                // Init DataTable & events per tab
                if (tab === 'mandiri' && $('#tableMandiri').length) { initPengumpulanTable('#tableMandiri', 5); initMandiriEvents(); }
                if (tab === 'kelompok' && $('#tableKelompok').length) { initPengumpulanTable('#tableKelompok', 5); initKelompokEvents(); }
                if (tab === 'tim' && $('#tableTim').length) { initPengumpulanTable('#tableTim', 5); initTimEvents(); }
            }, 100);
        },
        error: function (xhr, status, error) {
            overlay.removeClass('active');

            let msg = 'Gagal memuat konten';
            if (status === 'timeout') msg = 'Request timeout (>10s).';
            else if (xhr.status === 404) msg = 'URL tidak ditemukan (404).';
            else if (xhr.status === 500) msg = 'Server error (500). Cek log server.';

            contentArea.html(`
                <div style="text-align:center; padding: 60px;">
                    <i class="fas fa-exclamation-triangle fa-3x"
                       style="color:var(--accent-red); margin-bottom:16px;"></i>
                    <h3>Error Loading Content</h3>
                    <p style="color:var(--text-muted)">${msg}</p>
                    <p style="color:#64748b; font-size:13px">Status: ${xhr.status} | ${status}</p>
                </div>
            `);

            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        },
    });
}


/* =============================================================================
 * 3. GENERIC TABLE INIT
 *    Dipakai oleh Mandiri, Kelompok, dan Tim — kolom aksi selalu kolom terakhir
 * ============================================================================= */

function initPengumpulanTable(tableSelector, aksiColumnIndex) {
    if (typeof $.fn.DataTable === 'undefined') { console.error('DataTables not available'); return; }
    if ($.fn.DataTable.isDataTable(tableSelector)) $(tableSelector).DataTable().destroy();

    try {
        const dt = $(tableSelector).DataTable({
            paging: true,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            searching: false,
            ordering: true,
            order: [[0, 'asc']],
            info: true,
            autoWidth: false,
            columnDefs: [
                { targets: aksiColumnIndex, orderable: false },
            ],
            language: {
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                infoFiltered: '(disaring dari _MAX_ total data)',
                emptyTable: 'Tidak ada data yang tersedia',
                zeroRecords: 'Tidak ada data yang cocok',
                paginate: {
                    first: 'Pertama', last: 'Terakhir',
                    next: 'Selanjutnya', previous: 'Sebelumnya',
                },
            },
            initComplete: () => console.log('DataTable initialized:', tableSelector),
        });

        // Simpan instance ke window agar bisa di-access jika perlu
        window['dt_' + tableSelector.replace('#', '')] = dt;
    } catch (e) {
        console.error('DataTable error:', tableSelector, e);
    }
}

/* Helper: Apply filter generik (nama + deadline) */
function _applyFilter(tableSelector, namaSelector, deadlineSelector) {
    const nama = $(namaSelector).val().toLowerCase();
    const deadline = $(deadlineSelector).val().toLowerCase();

    $(`${tableSelector} tbody tr`).each(function () {
        const namaMatch = !nama || $(this).find('.td-nama').text().toLowerCase().includes(nama);
        const deadlineMatch = !deadline || $(this).find('.td-deadline').text().toLowerCase().includes(deadline);
        $(this).toggle(namaMatch && deadlineMatch);
    });
}

/* Helper: Init toggle filter */
function _initFilterToggle(btnId, sectionId) {
    $(`#${btnId}`).off('click').on('click', function () {
        const section = $(`#${sectionId}`);
        const isShowing = section.hasClass('show');
        const icon = $(this).find('i');
        const text = $(this).find('.btn-text');

        if (isShowing) {
            section.removeClass('show');
            icon.removeClass('fa-arrow-left').addClass('fa-filter');
            text.text('Filter');
        } else {
            section.addClass('show');
            icon.removeClass('fa-filter').addClass('fa-arrow-left');
            text.text('Kembali');
        }
    });
}

/* Helper: Init reset filter */
function _initResetFilter(resetBtnId, fpInputId, namaInputId, tableSelector) {
    $(`#${resetBtnId}`).off('click').on('click', function () {
        $(`#${namaInputId}`).val('');

        const fpInstance = document.getElementById(fpInputId)?._flatpickr;
        if (fpInstance) fpInstance.clear();
        else $(`#${fpInputId}`).val('');

        $(`${tableSelector} tbody tr`).show();

        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'success', title: 'Filter Direset', timer: 1200, showConfirmButton: false });
        }
    });
}

/* Helper: Navigasi detail (shared semua tab) */
function _initDetailNavigation() {
    $(document).off('click', '.btn-detail-pengumpulan')
        .on('click', '.btn-detail-pengumpulan', function () {
            const url = $(this).data('url');
            if (url) window.location.href = url;
        });
}


/* =============================================================================
 * 4. MANDIRI EVENTS
 * ============================================================================= */

function initMandiriEvents() {
    _initFilterToggle('btnFilterMandiri', 'filterSectionMandiri');
    _initResetFilter('btnResetFilterMandiri', 'filterDeadlineMandiri', 'filterNamaMandiri', '#tableMandiri');

    $('#filterNamaMandiri').off('keyup').on('keyup', function () {
        _applyFilter('#tableMandiri', '#filterNamaMandiri', '#filterDeadlineMandiri');
    });
    $('#filterDeadlineMandiri').off('change').on('change', function () {
        _applyFilter('#tableMandiri', '#filterNamaMandiri', '#filterDeadlineMandiri');
    });

    _initDetailNavigation();
}


/* =============================================================================
 * 5. KELOMPOK EVENTS
 * ============================================================================= */

function initKelompokEvents() {
    _initFilterToggle('btnFilterKelompok', 'filterSectionKelompok');
    _initResetFilter('btnResetFilterKelompok', 'filterDeadlineKelompok', 'filterNamaKelompok', '#tableKelompok');

    $('#filterNamaKelompok').off('keyup').on('keyup', function () {
        _applyFilter('#tableKelompok', '#filterNamaKelompok', '#filterDeadlineKelompok');
    });
    $('#filterDeadlineKelompok').off('change').on('change', function () {
        _applyFilter('#tableKelompok', '#filterNamaKelompok', '#filterDeadlineKelompok');
    });

    _initDetailNavigation();
}


/* =============================================================================
 * 6. TIM EVENTS
 * ============================================================================= */

function initTimEvents() {
    _initFilterToggle('btnFilterTim', 'filterSectionTim');
    _initResetFilter('btnResetFilterTim', 'filterDeadlineTim', 'filterNamaTim', '#tableTim');

    $('#filterNamaTim').off('keyup').on('keyup', function () {
        _applyFilter('#tableTim', '#filterNamaTim', '#filterDeadlineTim');
    });
    $('#filterDeadlineTim').off('change').on('change', function () {
        _applyFilter('#tableTim', '#filterNamaTim', '#filterDeadlineTim');
    });

    _initDetailNavigation();
}


/* =============================================================================
 * 7. DETAIL PAGE — Item Review
 * ============================================================================= */

function initDetailReview(submitFn, backUrl) {
    window._submitReviewFn = submitFn;
    window._backUrl = backUrl;
}

/* ── Setujui ── */
window.setujuiItem = function (itemId) {
    if (typeof Swal === 'undefined') { _doSetujui(itemId); return; }

    Swal.fire({
        title: 'Setujui Item?',
        text: 'Status item akan diubah menjadi Done.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Setujui',
        cancelButtonText: 'Batal',
    }).then(result => { if (result.isConfirmed) _doSetujui(itemId); });
};

function _doSetujui(itemId) {
    showLoading('Menyetujui item...');
    window._submitReviewFn(itemId, 'setujui', '')
        .then(data => {
            if (data.success) {
                _updateItemUI(itemId, 'Done', '');
                Swal.fire({ icon: 'success', title: 'Item Disetujui!', text: data.message, timer: 1800, showConfirmButton: false });
                _checkOverallStatus();
            } else {
                showAlert('error', 'Gagal', data.message);
            }
        })
        .catch(() => showAlert('error', 'Error', 'Terjadi kesalahan koneksi.'));
}

/* ── Show / Hide Form Revisi ── */
window.showRevisiForm = function (itemId) {
    $(`#revisiForm_${itemId}`).slideDown(250);
    $(`#itemActions_${itemId}`).hide();
    $(`#revisiKomentar_${itemId}`).focus();
};

window.hideRevisiForm = function (itemId) {
    $(`#revisiForm_${itemId}`).slideUp(200);
    $(`#itemActions_${itemId}`).show();
    $(`#revisiKomentar_${itemId}`).val('');
};

/* ── Submit Revisi ── */
window.submitRevisi = function (itemId) {
    const komentar = $(`#revisiKomentar_${itemId}`).val().trim();

    if (!komentar) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'warning', title: 'Komentar Kosong', text: 'Masukkan catatan revisi sebelum mengirim.', confirmButtonColor: '#0f766e' });
        }
        $(`#revisiKomentar_${itemId}`).focus();
        return;
    }

    showLoading('Mengirim catatan revisi...');
    window._submitReviewFn(itemId, 'revisi', komentar)
        .then(data => {
            if (data.success) {
                _updateItemUI(itemId, 'Revisi', komentar);
                $(`#revisiForm_${itemId}`).slideUp(200);
                Swal.fire({ icon: 'success', title: 'Revisi Dikirim!', text: data.message, timer: 1800, showConfirmButton: false });
                _checkOverallStatus();
            } else {
                showAlert('error', 'Gagal', data.message);
            }
        })
        .catch(() => showAlert('error', 'Error', 'Terjadi kesalahan koneksi.'));
};

/* ── Update UI item ── */
function _updateItemUI(itemId, newStatus, komentar) {
    const badgeClass = getStatusBadgeClass(newStatus);
    const $badge = $(`#itemStatus_${itemId}`);

    $badge.removeClass('badge-success badge-warning badge-info badge-gray')
        .addClass(badgeClass)
        .text(newStatus);

    $(`#itemActions_${itemId}`).hide();

    if (komentar) {
        $(`#itemKomentarText_${itemId}`).text(komentar);
        $(`#itemKomentar_${itemId}`).slideDown(250);
    }
}

/* ── Hitung ulang overall status ── */
function _checkOverallStatus() {
    const statuses = [];
    $('[id^="itemStatus_"]').each(function () {
        statuses.push($(this).text().trim());
    });

    let overall;
    if (statuses.every(s => s === 'Done')) {
        overall = 'Done';
    } else if (statuses.some(s => s === 'Revisi')) {
        overall = 'Revisi';
    } else if (statuses.some(s => s === 'Submit')) {
        overall = 'Belum Diperiksa';
    } else {
        overall = 'Belum Dikirim';
    }

    const badgeClass = getStatusBadgeClass(overall);
    $('#overallStatusBadge')
        .removeClass('badge-success badge-warning badge-info badge-gray')
        .addClass(badgeClass)
        .text(overall);
}
