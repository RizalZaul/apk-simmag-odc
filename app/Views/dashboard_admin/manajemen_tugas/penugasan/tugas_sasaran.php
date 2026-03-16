<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/modules/tugas.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="sasaran-container">

    <!-- Header -->
    <div class="sasaran-header">
        <button class="btn-back-link" id="btnBackToForm">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Form Ketentuan
        </button>
        <h1 class="sasaran-title">
            <i class="fas fa-crosshairs"></i>
            Pilih Sasaran Tugas
        </h1>
    </div>

    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step-item">
            <div class="step-circle done"><i class="fas fa-check" style="font-size:14px"></i></div>
            <span class="step-label done">Ketentuan Tugas</span>
        </div>
        <div class="step-line done"></div>
        <div class="step-item">
            <div class="step-circle active">2</div>
            <span class="step-label active">Pilih Sasaran</span>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="sasaran-tabs">
        <button class="tab-sasaran active" data-tab="mandiri" id="tabMandiri">
            <i class="fas fa-user"></i>
            Mandiri
            <span class="tab-count" id="countMandiri">0</span>
        </button>
        <button class="tab-sasaran" data-tab="kelompok" id="tabKelompok">
            <i class="fas fa-users"></i>
            Kelompok PKL
            <span class="tab-count" id="countKelompok">0</span>
        </button>
        <button class="tab-sasaran" data-tab="tim" id="tabTim">
            <i class="fas fa-user-friends"></i>
            Tim Tugas
            <span class="tab-count" id="countTim">0</span>
        </button>
    </div>

    <!-- Mode hint — ditampilkan/disembunyikan via JS berdasarkan mode_pengumpulan -->
    <div id="modeHintBar" style="display:none; margin-bottom:var(--space-md);
        padding:10px 16px; border-radius:8px; font-size:13px; font-weight:500;">
    </div>

    <!-- Content Area -->
    <div class="sasaran-content" id="sasaranContent">
        <div class="content-section-header">
            <h3 class="content-section-title" id="contentTitle">
                <i class="fas fa-user"></i> PKL Mandiri
            </h3>
            <button class="btn-filter-sasaran" id="btnFilterSasaran">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>

        <!-- Filter (hidden by default) -->
        <div class="filter-sasaran" id="filterSasaran">
            <div class="filter-sasaran-grid">
                <div class="filter-group-sasaran">
                    <label id="filterLabel1"><i class="fas fa-search"></i> Cari Nama</label>
                    <input type="text" id="filterNamaSasaran" placeholder="Ketik nama...">
                </div>
                <div class="filter-group-sasaran">
                    <label><i class="fas fa-calendar"></i> Tgl Mulai</label>
                    <input type="text" id="filterTglMulai" placeholder="dd-mm-yyyy" class="flatpickr-sasaran">
                </div>
                <div class="filter-group-sasaran">
                    <label><i class="fas fa-calendar-check"></i> Tgl Akhir</label>
                    <input type="text" id="filterTglAkhir" placeholder="dd-mm-yyyy" class="flatpickr-sasaran">
                </div>
            </div>
            <div style="text-align:right; margin-top:var(--space-sm);">
                <button class="btn-reset-sasaran" id="btnResetFilterSasaran">
                    <i class="fas fa-redo"></i> Reset Filter
                </button>
            </div>
        </div>

        <!-- Body (Dynamic Content) -->
        <div class="content-body" id="contentBody">
            <div style="text-align:center; padding:40px;">
                <i class="fas fa-spinner fa-spin fa-2x" style="color:var(--primary);"></i>
                <p style="margin-top:10px; color:var(--text-muted);">Memuat data...</p>
            </div>
        </div>
    </div>

</div>

<!-- Bottom Actions -->
<div class="sasaran-actions">
    <button class="btn-sasaran-action btn-sasaran-back" id="btnBackToFormBottom">
        <i class="fas fa-arrow-left"></i>
        Kembali
    </button>
    <div style="display:flex; align-items:center; gap:var(--space-lg);">
        <span id="selectedInfoBar"
            style="font-size:var(--font-size-sm); color:var(--text-secondary); font-weight:600;">
            Belum ada yang dipilih
        </span>
        <button class="btn-sasaran-action btn-sasaran-send" id="btnKirim" disabled>
            <i class="fas fa-paper-plane"></i>
            Kirim Tugas
        </button>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="<?= base_url('assets/js/modules/tugas.js') ?>"></script>
<script>
    // ================================================================
    // TAMBAHAN BARU — window variables untuk API endpoints
    // (tugas.js sudah tidak pakai getDummyXxx, semua dari API)
    // ================================================================
    window.SASARAN_URL = '<?= base_url('dashboard/manajemen-tugas/sasaran') ?>';
    window.TIM_URL = '<?= base_url('dashboard/manajemen-tugas/tim') ?>';
    window.PKL_MEMBER_URL = '<?= base_url('dashboard/manajemen-tugas/pkl/members') ?>';
    window.CSRF_TOKEN = '<?= csrf_hash() ?>';

    // ================================================================
    // INLINE JS — hanya yang mengandung PHP vars (base_url, csrf_hash)
    // Semua fungsi sasaran (initTabs, renderMandiri, renderKelompok,
    // renderTim, initTableEvents, initTimFormEvents, initFilterSasaran,
    // initFlatpickrSasaran, getDummyXxx) sudah ada di tugas.js
    // ================================================================

    $(document).ready(function() {
        // Guard: harus ada form data dari step 1
        const formData = sessionStorage.getItem('tugasFormData');
        if (!formData) {
            window.location.href = '<?= base_url('dashboard/manajemen-tugas/tugas/tambah') ?>';
            return;
        }

        const fd = JSON.parse(formData);
        const modePengumpulan = (fd.model_pengumpulan || 'individu').toLowerCase();

        // ── Sesuaikan tab berdasarkan mode pengumpulan ────────────────
        if (modePengumpulan === 'kelompok') {
            // Sembunyikan tab Mandiri — mode kelompok tidak bisa ke individu
            $('#tabMandiri').hide();

            // Aktifkan tab Kelompok sebagai default
            $('.tab-sasaran').removeClass('active');
            $('#tabKelompok').addClass('active');

            // Tampilkan hint
            $('#modeHintBar').css({
                'background': '#fef3c7',
                'border': '1px solid #f59e0b',
                'color': '#92400e'
            }).html(`
                <i class="fas fa-info-circle" style="margin-right:6px; color:#f59e0b;"></i>
                Mode pengumpulan <strong>Kelompok</strong> — sasaran hanya dapat dipilih dari
                <strong>Kelompok PKL</strong> atau <strong>Tim Tugas</strong>.
            `).show();

            // Load tab kelompok sebagai default
            initTabs();
            initBackButtonsSasaran();
            initFilterSasaran();
            initFlatpickrSasaran();
            loadTabData('kelompok');

        } else {
            // Mode individu — semua tab tersedia
            // Tampilkan hint info expand
            $('#modeHintBar').css({
                'background': '#f0fdfa',
                'border': '1px solid #0d9488',
                'color': '#134e4a'
            }).html(`
                <i class="fas fa-info-circle" style="margin-right:6px; color:#0d9488;"></i>
                Mode pengumpulan <strong>Individu</strong> — jika memilih Kelompok PKL atau Tim Tugas,
                setiap anggota akan mendapatkan tugas secara <strong>individual</strong>.
            `).show();

            initTabs();
            initBackButtonsSasaran();
            initFilterSasaran();
            initFlatpickrSasaran();
            loadTabData('mandiri');
        }

        // Tombol Kirim — mengandung base_url + csrf_hash → tetap inline
        $('#btnKirim').on('click', function() {
            if (selectedItems.length === 0) return;

            const formData = JSON.parse(sessionStorage.getItem('tugasFormData') || '{}');
            const typeNames = {
                mandiri: 'PKL Mandiri',
                kelompok: 'Kelompok PKL',
                tim: 'Tim Tugas'
            };
            const typeName = typeNames[activeTab];
            const namaList = selectedItems.map((item, i) => `${i + 1}. ${item.name}`).join('<br>');

            Swal.fire({
                title: '📋 Konfirmasi Kirim Tugas',
                html: `
                <div style="text-align:left; font-size:14px;">
                    <div style="background:#f0fdfa; border-radius:8px; padding:12px; margin-bottom:12px;">
                        <p style="margin:4px 0"><b>Nama Tugas:</b> ${formData.nama_tugas || '-'}</p>
                        <p style="margin:4px 0"><b>Kategori:</b> ${formData.kategori_tugas || '-'}</p>
                        <p style="margin:4px 0"><b>Deadline:</b> ${formData.deadline || '-'}</p>
                        <p style="margin:4px 0"><b>Model:</b> ${formData.model_pengumpulan || '-'}</p>
                    </div>
                    <div style="border:2px solid var(--primary); border-radius:8px; padding:12px;">
                        <p style="margin:0 0 8px; font-weight:700; color:var(--primary)">
                            🎯 Sasaran: ${typeName} (${selectedItems.length})
                        </p>
                        <div style="max-height:150px; overflow-y:auto; font-size:13px; line-height:1.8;">
                            ${namaList}
                        </div>
                    </div>
                </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f766e',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-paper-plane"></i> Ya, Kirim!',
                cancelButtonText: 'Batal',
                width: '500px'
            }).then(result => {
                if (result.isConfirmed) submitTugas();
            });
        });
    });

    // ----------------------------------------------------------------
    // initBackButtonsSasaran — mengandung base_url
    // ----------------------------------------------------------------
    function initBackButtonsSasaran() {
        $('#btnBackToForm, #btnBackToFormBottom').on('click', function() {
            window.location.href = '<?= base_url('dashboard/manajemen-tugas/tugas/tambah') ?>';
        });
    }

    // ----------------------------------------------------------------
    // submitTugas — mengandung base_url + csrf_hash
    // ----------------------------------------------------------------
    function submitTugas() {
        const formData = JSON.parse(sessionStorage.getItem('tugasFormData') || '{}');

        const payload = {
            ...formData,
            sasaran_type: activeTab,
            sasaran_items: selectedItems.map(i => i.id)
        };

        Swal.fire({
            title: 'Mengirim Tugas...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: '<?= base_url('dashboard/manajemen-tugas/tugas/store') ?>',
            method: 'POST',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                sessionStorage.removeItem('tugasFormData');
                sessionStorage.removeItem('tugasSasaran');

                Swal.fire({
                    icon: 'success',
                    title: 'Tugas Berhasil Dikirim!',
                    html: `<b>${selectedItems.length}</b> penerima tugas telah ditambahkan.`,
                    confirmButtonColor: '#0f766e',
                    confirmButtonText: 'Kembali ke List'
                }).then(() => {
                    window.location.href = '<?= base_url('/dashboard/manajemen-tugas/penugasan') ?>';
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan server',
                    confirmButtonColor: '#0f766e'
                });
            }
        });
    }
</script>
<?= $this->endSection() ?>