<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/modules/tugas.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="tambah-tugas-container">

    <!-- Header -->
    <div class="tambah-header">
        <button class="btn-back-link" id="btnBackToList">
            <i class="fas fa-arrow-left"></i>
            Kembali ke List Tugas
        </button>
        <h1 class="tambah-title">
            <i class="fas fa-plus-circle"></i>
            Tambah Tugas Baru
        </h1>
    </div>

    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step-item">
            <div class="step-circle active" id="step1Circle">1</div>
            <span class="step-label active" id="step1Label">Ketentuan Tugas</span>
        </div>
        <div class="step-line" id="stepLine1"></div>
        <div class="step-item">
            <div class="step-circle inactive" id="step2Circle">2</div>
            <span class="step-label" id="step2Label">Pilih Sasaran</span>
        </div>
    </div>

    <!-- Form Ketentuan -->
    <div class="form-section-tugas">
        <div class="form-section-header">
            <h3 class="form-section-title">
                <i class="fas fa-clipboard-list"></i>
                Ketentuan Tugas
            </h3>
        </div>
        <div class="form-section-body">
            <form id="formTambahTugas" novalidate>
                <?= csrf_field() ?>

                <div class="form-grid">
                    <!-- Editor (readonly) -->
                    <div class="form-group-tugas">
                        <label><i class="fas fa-user"></i> Editor</label>
                        <input type="text"
                            id="editor"
                            name="editor"
                            value="<?= esc(session('nama_lengkap') ?? 'Admin') ?>"
                            class="readonly-field"
                            readonly>
                    </div>

                    <!-- Nama Tugas -->
                    <div class="form-group-tugas">
                        <label>
                            <i class="fas fa-tasks"></i>
                            Nama Tugas <span class="required">*</span>
                        </label>
                        <input type="text"
                            id="namaTugas"
                            name="nama_tugas"
                            placeholder="Masukkan nama tugas..."
                            required>
                    </div>

                    <!-- Kategori Tugas — Select2 -->
                    <div class="form-group-tugas">
                        <label>
                            <i class="fas fa-tags"></i>
                            Kategori Tugas <span class="required">*</span>
                        </label>
                        <select id="kategoriTugas" name="kategori_tugas" style="width:100%;" required>
                            <option value="">-- Pilih Kategori Tugas --</option>
                            <?php foreach (($kategori_list ?? []) as $kat): ?>
                                <option value="<?= $kat['id'] ?>"
                                    data-nama="<?= esc($kat['nama_kategori']) ?>"
                                    data-mode="<?= esc(ucfirst($kat['mode'])) ?>">
                                    <?= esc($kat['nama_kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Model Pengumpulan — auto-fill dari kategori, tidak bisa diubah manual -->
                    <div class="form-group-tugas">
                        <label>
                            <i class="fas fa-layer-group"></i>
                            Mode Pengumpulan
                        </label>
                        <select id="modelPengumpulan" name="model_pengumpulan" disabled>
                            <option value="">— Dipilih otomatis dari kategori —</option>
                            <option value="Individu">Individu</option>
                            <option value="Kelompok">Kelompok</option>
                        </select>
                        <small style="color:var(--text-muted); font-size:11px; margin-top:4px; display:block;">
                            <i class="fas fa-info-circle"></i>
                            Mode ditentukan otomatis sesuai kategori tugas yang dipilih
                        </small>
                    </div>

                    <!-- Deadline -->
                    <div class="form-group-tugas">
                        <label>
                            <i class="fas fa-calendar-times"></i>
                            Deadline <span class="required">*</span>
                        </label>
                        <input type="text"
                            id="deadline"
                            name="deadline"
                            placeholder="Pilih tanggal deadline..."
                            class="flatpickr-input"
                            required
                            readonly>
                    </div>

                    <!-- Jumlah Target -->
                    <div class="form-group-tugas">
                        <label>
                            <i class="fas fa-bullseye"></i>
                            Jumlah Target <span class="required">*</span>
                        </label>
                        <input type="number"
                            id="jumlahTarget"
                            name="jumlah_target"
                            placeholder="Contoh: 10"
                            min="1"
                            required>
                    </div>

                    <!-- Deskripsi -->
                    <div class="form-group-tugas full-width">
                        <label>
                            <i class="fas fa-align-left"></i>
                            Deskripsi <span class="required">*</span>
                        </label>
                        <textarea id="deskripsi"
                            name="deskripsi"
                            placeholder="Tuliskan deskripsi tugas secara detail..."
                            required></textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sasaran Preview -->
    <div class="form-section-tugas">
        <div class="form-section-header">
            <h3 class="form-section-title">
                <i class="fas fa-crosshairs"></i>
                Sasaran Tugas
            </h3>
        </div>
        <div class="form-section-body">
            <div class="sasaran-preview" id="sasaranPreview">
                <!-- Empty State -->
                <div class="sasaran-empty-text" id="sasaranEmpty">
                    <i class="fas fa-users-slash"></i>
                    <span>Belum ada sasaran dipilih</span>
                    <span style="font-size:12px; color:var(--text-muted);">
                        Klik "Tambah Pilih Sasaran" untuk memilih penerima tugas
                    </span>
                </div>

                <!-- Summary (shown after selection) -->
                <div class="sasaran-summary" id="sasaranSummary">
                    <div id="sasaranTypeBadge"></div>
                    <div class="sasaran-count" id="sasaranCount"></div>
                    <div class="sasaran-names" id="sasaranNames"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Bottom Actions -->
<div class="tambah-actions">
    <button class="btn-tugas btn-tugas-back" id="btnBackToList2">
        <i class="fas fa-arrow-left"></i>
        Kembali
    </button>
    <button class="btn-tugas btn-tugas-next" id="btnNextSasaran">
        <i class="fas fa-crosshairs"></i>
        Tambah Pilih Sasaran
        <i class="fas fa-arrow-right"></i>
    </button>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="<?= base_url('assets/js/modules/tugas.js') ?>"></script>
<script>
    // ================================================================
    // INLINE JS — hanya yang mengandung PHP vars (base_url, session)
    // Fungsi murni: initFlatpickrTambah, initSelect2Tugas, validateForm,
    // saveFormToSession, restoreSasaranFromSession, updateSasaranPreview
    // semuanya sudah ada di tugas.js
    // ================================================================

    // State page-level sasaran
    window._selectedSasaran = {
        type: null,
        items: [],
        names: [],
        total: 0
    };

    $(document).ready(function() {
        initFlatpickrTambah();
        initSelect2Tugas();
        initBackButtonsTambah();
        initNextButton();
        restoreSasaranFromSession();
    });

    // ----------------------------------------------------------------
    // initBackButtonsTambah — mengandung base_url
    // ----------------------------------------------------------------
    function initBackButtonsTambah() {
        $('#btnBackToList, #btnBackToList2').on('click', function() {
            const formHasData = checkFormHasData();

            if (formHasData) {
                Swal.fire({
                    title: 'Tinggalkan Halaman?',
                    text: 'Data yang sudah diisi akan hilang. Yakin ingin kembali?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Kembali',
                    cancelButtonText: 'Tetap di Sini'
                }).then((result) => {
                    if (result.isConfirmed) {
                        sessionStorage.removeItem('tugasSasaran');
                        sessionStorage.removeItem('tugasFormData');
                        window.location.href = '<?= base_url('dashboard/manajemen-tugas/penugasan') ?>';
                    }
                });
            } else {
                window.location.href = '<?= base_url('dashboard/manajemen-tugas/penugasan') ?>';
            }
        });
    }

    // ----------------------------------------------------------------
    // initNextButton — mengandung base_url
    // ----------------------------------------------------------------
    function initNextButton() {
        $('#btnNextSasaran').on('click', function() {
            if (!validateForm()) return;
            saveFormToSession();
            window.location.href = '<?= base_url('dashboard/manajemen-tugas/tugas/pilih-sasaran') ?>';
        });
    }
</script>
<?= $this->endSection() ?>