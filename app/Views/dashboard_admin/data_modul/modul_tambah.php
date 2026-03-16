<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/modules/data-modul.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="form-modul-container">
    <div class="form-section-modul">

        <!-- Form Header -->
        <div class="form-header-modul">
            <h2 class="form-title-modul">
                <i class="fas fa-plus-circle"></i>
                Tambah Modul Baru
            </h2>
        </div>

        <!-- Form -->
        <form id="formTambahModul" enctype="multipart/form-data">

            <!--
                [BARU] CSRF token disisipkan sebagai hidden field.
                Saat FormData dibuat dari form ini, token ikut terbawa otomatis.
                Tanpa ini, CI4 menolak POST request dan memunculkan 404/403.
            -->
            <?= csrf_field() ?>

            <!-- Nama Modul -->
            <div class="form-group-modul">
                <label>
                    <i class="fas fa-book"></i>
                    Nama Modul <span class="required">*</span>
                </label>
                <input type="text" id="namaModul" name="nama_modul"
                    placeholder="Masukkan nama modul" required>
            </div>

            <!-- Kategori Modul — Select2 -->
            <div class="form-group-modul">
                <label>
                    <i class="fas fa-tags"></i>
                    Kategori Modul <span class="required">*</span>
                </label>
                <select id="kategoriModul" name="id_kat_m"
                    class="select2-kategori-modul" style="width:100%;" required>
                    <option value="">Pilih Kategori</option>
                    <?php foreach ($kategori_list as $kat): ?>
                        <option value="<?= $kat['id'] ?>">
                            <?= esc($kat['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Deskripsi -->
            <div class="form-group-modul">
                <label>
                    <i class="fas fa-align-left"></i>
                    Deskripsi <span class="required">*</span>
                </label>
                <textarea id="ketModul" name="ket_modul"
                    placeholder="Masukkan deskripsi modul (maksimal 500 karakter)"
                    maxlength="500" required></textarea>
                <div class="char-counter">
                    <span id="charCount">0</span> / 500 karakter
                </div>
            </div>

            <!-- Tipe Modul -->
            <div class="form-group-modul">
                <label>
                    <i class="fas fa-list"></i>
                    Tipe Modul <span class="required">*</span>
                </label>
                <div class="radio-group-modul">
                    <div class="radio-option-modul">
                        <input type="radio" id="tipeLink" name="tipe" value="link" checked>
                        <label for="tipeLink">
                            <i class="fas fa-link"></i> Link
                        </label>
                    </div>
                    <div class="radio-option-modul">
                        <input type="radio" id="tipeFile" name="tipe" value="file">
                        <label for="tipeFile">
                            <i class="fas fa-file"></i> File
                        </label>
                    </div>
                </div>
            </div>

            <!-- Modul Link (Conditional) -->
            <div class="form-group-modul conditional-field show" id="fieldLink">
                <label>
                    <i class="fas fa-link"></i>
                    URL Link <span class="required">*</span>
                </label>
                <input type="url" id="modulLink" name="modul_link"
                    placeholder="https://example.com/modul">
                <div class="error-message" id="linkError"></div>
            </div>

            <!-- Modul File (Conditional) -->
            <div class="form-group-modul conditional-field" id="fieldFile">
                <label>
                    <i class="fas fa-file-upload"></i>
                    Upload File <span class="required">*</span>
                </label>

                <div class="file-upload-area" id="fileUploadArea">
                    <input type="file" id="modulFile" name="modul_file"
                        accept=".pdf,.docx,.doc,.pptx,.ppt,.xlsx,.xls,.zip,.rar">
                    <div class="file-upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="file-upload-text">Drag &amp; Drop file di sini</div>
                    <div class="file-upload-hint">atau klik untuk memilih file</div>
                    <div class="file-upload-limits">
                        <strong>Maksimal:</strong> 300 MB<br>
                        <strong>Format:</strong> PDF, DOCX, PPTX, XLSX, ZIP, RAR
                    </div>
                </div>

                <!-- File terpilih -->
                <div class="file-selected-info" id="fileSelectedInfo">
                    <div class="file-info-header">
                        <div class="file-info-name">
                            <i class="fas fa-file"></i>
                            <span id="fileName">-</span>
                        </div>
                        <button type="button" class="file-info-remove" id="btnRemoveFile">
                            <i class="fas fa-times"></i> Hapus
                        </button>
                    </div>
                    <div class="file-info-details">
                        Ukuran: <span id="fileSize">-</span>
                    </div>
                </div>

                <div class="error-message" id="fileError"></div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions-modul">
                <button type="button" class="btn-modul btn-kembali"
                    onclick="window.location.href='<?= base_url('dashboard/data-modul') ?>'">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </button>
                <button type="submit" class="btn-modul btn-tambah">
                    <i class="fas fa-save"></i>
                    Tambah
                </button>
            </div>

        </form>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="<?= base_url('assets/js/modules/modul.js') ?>"></script>
<script>
    $(document).ready(function() {
        modulTambahInit({
            store: '<?= base_url('dashboard/data-modul/modul/store') ?>',
            back: '<?= base_url('dashboard/data-modul') ?>',
        });
    });
</script>
<?= $this->endSection() ?>