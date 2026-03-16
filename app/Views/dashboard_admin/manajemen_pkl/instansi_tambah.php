<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<!-- Manajemen PKL Module CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/modules/manajemen-pkl.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="form-instansi-container">
    <div class="form-section-instansi">

        <!-- Form Header -->
        <div class="form-header-instansi">
            <h2 class="form-title-instansi">
                <i class="fas fa-plus-circle"></i>
                Tambah Instansi Baru
            </h2>
        </div>

        <!-- Form -->
        <form id="formTambahInstansi">

            <!-- Kategori Instansi -->
            <div class="form-group-instansi">
                <label>
                    <i class="fas fa-tags"></i>
                    Kategori Instansi <span class="required">*</span>
                </label>
                <select id="kategoriInstansi" name="kategori_instansi" required>
                    <option value="">Pilih Kategori</option>
                    <option value="SMK Sederajat">SMK Sederajat</option>
                    <option value="Kuliah">Kuliah</option>
                </select>
            </div>

            <!-- Nama Instansi -->
            <div class="form-group-instansi">
                <label>
                    <i class="fas fa-building"></i>
                    Nama Instansi <span class="required">*</span>
                </label>
                <input type="text" id="namaInstansi" name="nama_instansi" placeholder="Masukkan nama instansi" required>
            </div>

            <!-- Alamat -->
            <div class="form-group-instansi">
                <label>
                    <i class="fas fa-map-marker-alt"></i>
                    Alamat <span class="required">*</span>
                </label>
                <textarea id="alamatInstansi" name="alamat" placeholder="Masukkan alamat lengkap instansi" required></textarea>
            </div>

            <!-- Kota (Select2 with Tags) -->
            <div class="form-group-instansi">
                <label>
                    <i class="fas fa-city"></i>
                    Kota <span class="required">*</span>
                </label>
                <select id="kotaInstansi" name="kota" class="select2-kota" required>
                    <option value="">Pilih atau Ketik Kota Baru</option>
                    <?php foreach ($kota_list as $kota): ?>
                        <option value="<?= esc($kota) ?>"><?= esc($kota) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="form-hint">
                    <i class="fas fa-info-circle"></i>
                    Ketik nama kota baru jika tidak ada dalam pilihan
                </small>
            </div>

            <!-- Form Actions -->
            <div class="form-actions-instansi">
                <button type="button" class="btn-instansi btn-kembali" onclick="window.location.href='<?= base_url('dashboard/manajemen-pkl') ?>'">
                    <i class="fas fa-arrow-left"></i>
                    Batal
                </button>
                <button type="submit" class="btn-instansi btn-tambah">
                    <i class="fas fa-save"></i>
                    Tambah
                </button>
            </div>

        </form>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="<?= base_url('assets/js/modules/pkl.js') ?>"></script>
<script>
    $(document).ready(function() {
        instansiTambahInit({
            store: '<?= base_url('dashboard/manajemen-pkl/instansi/store') ?>',
            redirect: '<?= base_url('dashboard/manajemen-pkl') ?>'
        });
    });
</script>
<?= $this->endSection() ?>