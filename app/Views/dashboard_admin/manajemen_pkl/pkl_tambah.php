<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<!-- Manajemen PKL Module CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/modules/manajemen-pkl.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="form-pkl-container">
    <!-- Progress Bar -->
    <div class="progress-container">
        <div class="progress-steps">
            <div class="progress-line">
                <div class="progress-line-fill" id="progressLineFill"></div>
            </div>

            <div class="progress-step active" data-step="1">
                <div class="progress-circle">1</div>
                <span class="progress-label">Data Kelompok</span>
            </div>

            <div class="progress-step" data-step="2">
                <div class="progress-circle">2</div>
                <span class="progress-label">Biodata Anggota</span>
            </div>

            <div class="progress-step" data-step="3">
                <div class="progress-circle">3</div>
                <span class="progress-label">Konfirmasi</span>
            </div>
        </div>
    </div>

    <!-- Form Steps -->
    <form id="formTambahPkl">

        <!-- ==================== STEP 1: DATA KELOMPOK ==================== -->
        <div class="form-step active" data-step="1">
            <div class="form-section-pkl">
                <div class="form-section-header">
                    <h3 class="form-section-title">
                        <i class="fas fa-users"></i>
                        Data Kelompok PKL
                    </h3>
                    <p class="form-section-subtitle">Isi informasi kelompok PKL dengan lengkap</p>
                </div>

                <!-- Kategori PKL -->
                <div class="form-group-pkl">
                    <label>
                        <i class="fas fa-tags"></i>
                        Kategori PKL <span class="required">*</span>
                    </label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="kategoriMandiri" name="kategori_pkl" value="Mandiri" checked>
                            <label for="kategoriMandiri">Mandiri</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="kategoriInstansi" name="kategori_pkl" value="Instansi">
                            <label for="kategoriInstansi">Instansi</label>
                        </div>
                    </div>
                </div>

                <!-- MANDIRI FIELDS (Default Shown) -->
                <div id="mandiriFields">
                    <div class="form-row">
                        <div class="form-group-pkl">
                            <label>
                                <i class="fas fa-calendar-alt"></i>
                                Tanggal Mulai PKL <span class="required">*</span>
                            </label>
                            <input type="text" id="tglMulaiMandiri" class="flatpickr-date" placeholder="Pilih tanggal">
                        </div>
                        <div class="form-group-pkl">
                            <label>
                                <i class="fas fa-calendar-check"></i>
                                Tanggal Akhir PKL <span class="required">*</span>
                            </label>
                            <input type="text" id="tglAkhirMandiri" class="flatpickr-date" placeholder="Pilih tanggal">
                        </div>
                    </div>
                </div>

                <!-- INSTANSI FIELDS (Hidden by Default) -->
                <div id="instansiFields" style="display: none;">
                    <!-- Kategori Instansi -->
                    <div class="form-group-pkl">
                        <label>
                            <i class="fas fa-building"></i>
                            Kategori Instansi <span class="required">*</span>
                        </label>
                        <select id="kategoriInstansiPkl">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Kuliah">Kuliah</option>
                            <option value="SMK Sederajat">SMK Sederajat</option>
                        </select>
                    </div>

                    <!-- Nama Instansi (Select2 with Tags) -->
                    <div class="form-group-pkl">
                        <label>
                            <i class="fas fa-university"></i>
                            Nama Instansi <span class="required">*</span>
                        </label>
                        <select id="namaInstansi" class="select2-instansi">
                            <option value="">-- Pilih atau Ketik Instansi Baru --</option>
                            <?php foreach ($instansi_list as $instansi): ?>
                                <option value="<?= $instansi['id'] ?>"
                                    data-kategori="<?= $instansi['kategori_instansi'] ?>"
                                    data-nama="<?= esc($instansi['nama_instansi']) ?>"
                                    data-alamat="<?= esc($instansi['alamat']) ?>"
                                    data-kota="<?= esc($instansi['kota']) ?>">
                                    <?= esc($instansi['nama_instansi']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Ketik nama instansi baru jika tidak ada dalam pilihan
                        </small>
                    </div>

                    <!-- New Instansi Fields (Hidden, shown when user types new) -->
                    <div id="newInstansiFields" style="display: none;">
                        <div class="form-row">
                            <div class="form-group-pkl">
                                <label>
                                    <i class="fas fa-map-marker-alt"></i>
                                    Alamat Instansi <span class="required">*</span>
                                </label>
                                <textarea id="alamatInstansiBaru" placeholder="Alamat lengkap instansi"></textarea>
                            </div>
                            <div class="form-group-pkl">
                                <label>
                                    <i class="fas fa-city"></i>
                                    Kota Instansi <span class="required">*</span>
                                </label>
                                <select id="kotaInstansiBaru" class="select2-kota-pkl">
                                    <option value="">-- Pilih atau Ketik Kota --</option>
                                    <?php foreach ($kota_list as $kota): ?>
                                        <option value="<?= $kota ?>"><?= $kota ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Nama Pembimbing -->
                    <div class="form-row">
                        <div class="form-group-pkl">
                            <label>
                                <i class="fas fa-chalkboard-teacher"></i>
                                Nama Pembimbing <span class="required">*</span>
                            </label>
                            <input type="text" id="namaPembimbing" placeholder="Nama pembimbing instansi">
                        </div>
                        <div class="form-group-pkl">
                            <label>
                                <i class="fas fa-phone"></i>
                                No WA Pembimbing <span class="required">*</span>
                            </label>
                            <input type="text" id="noWaPembimbing" placeholder="08xxxxxxxxxx">
                        </div>
                    </div>

                    <!-- Jumlah Anggota + Nama Kelompok -->
                    <div class="form-row">
                        <div class="form-group-pkl">
                            <label>
                                <i class="fas fa-users"></i>
                                Jumlah Anggota <span class="required">*</span>
                            </label>
                            <input type="number" id="jumlahAnggota" min="1" max="10" value="1" placeholder="1-10">
                        </div>
                        <div class="form-group-pkl">
                            <label>
                                <i class="fas fa-flag"></i>
                                Nama Kelompok <span class="required">*</span>
                            </label>
                            <input type="text" id="namaKelompokInstansi" placeholder="Contoh: Tim ITM">
                        </div>
                    </div>

                    <!-- Tanggal Mulai + Akhir -->
                    <div class="form-row">
                        <div class="form-group-pkl">
                            <label>
                                <i class="fas fa-calendar-alt"></i>
                                Tanggal Mulai PKL <span class="required">*</span>
                            </label>
                            <input type="text" id="tglMulaiInstansi" class="flatpickr-date" placeholder="Pilih tanggal">
                        </div>
                        <div class="form-group-pkl">
                            <label>
                                <i class="fas fa-calendar-check"></i>
                                Tanggal Akhir PKL <span class="required">*</span>
                            </label>
                            <input type="text" id="tglAkhirInstansi" class="flatpickr-date" placeholder="Pilih tanggal">
                        </div>
                    </div>

                </div>
            </div>

            <!-- Navigation -->
            <div class="form-navigation">
                <button type="button" class="btn-pkl btn-back" onclick="window.location.href='<?= base_url('dashboard/manajemen-pkl') ?>'">
                    <i class="fas fa-times"></i>
                    Batal
                </button>
                <button type="button" class="btn-pkl btn-next" id="btnNextStep1">
                    Lanjut ke Biodata
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- ==================== STEP 2: BIODATA ANGGOTA ==================== -->
        <div class="form-step" data-step="2">
            <div class="form-section-pkl">
                <div class="form-section-header">
                    <h3 class="form-section-title">
                        <i class="fas fa-id-card"></i>
                        Biodata Anggota PKL
                    </h3>
                    <p class="form-section-subtitle">Isi biodata setiap anggota kelompok PKL</p>
                </div>

                <!-- Biodata Accordion (Dynamic) -->
                <div class="biodata-accordion" id="biodataAccordion">
                    <!-- Will be generated dynamically by JavaScript -->
                </div>
            </div>

            <!-- Navigation -->
            <div class="form-navigation">
                <button type="button" class="btn-pkl btn-back" id="btnBackStep2">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </button>
                <button type="button" class="btn-pkl btn-next" id="btnNextStep2">
                    Lanjut ke Konfirmasi
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- ==================== STEP 3: KONFIRMASI ==================== -->
        <div class="form-step" data-step="3">
            <div class="form-section-pkl">
                <div class="form-section-header">
                    <h3 class="form-section-title">
                        <i class="fas fa-check-circle"></i>
                        Konfirmasi Data PKL
                    </h3>
                    <p class="form-section-subtitle">Periksa kembali data sebelum disimpan</p>
                </div>

                <!-- Review Data Kelompok -->
                <div class="review-section">
                    <h4 class="review-title">
                        <i class="fas fa-users"></i>
                        Data Kelompok
                    </h4>
                    <div id="reviewKelompok">
                        <!-- Will be filled by JavaScript -->
                    </div>
                </div>

                <!-- Review Data Anggota -->
                <div class="review-section">
                    <h4 class="review-title">
                        <i class="fas fa-id-card"></i>
                        Data Anggota (<span id="reviewJumlahAnggota">0</span> orang)
                    </h4>
                    <div id="reviewAnggota">
                        <!-- Will be filled by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="form-navigation">
                <button type="button" class="btn-pkl btn-back" id="btnBackStep3">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </button>
                <button type="button" class="btn-pkl btn-submit" id="btnSubmitForm">
                    <i class="fas fa-save"></i>
                    Simpan Data PKL
                </button>
            </div>
        </div>

    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="<?= base_url('assets/js/modules/pkl.js') ?>"></script>
<script>
    $(document).ready(function() {
        pklTambahInit({
            store: '<?= base_url('dashboard/manajemen-pkl/pkl/store') ?>',
            redirect: '<?= base_url('dashboard/manajemen-pkl') ?>'
        });
    });
</script>
<?= $this->endSection() ?>