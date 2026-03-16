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
                <i class="fas fa-edit"></i>
                Ubah Instansi
            </h2>
        </div>

        <!-- Form -->
        <form id="formUbahInstansi">

            <input type="hidden" id="instansiId" name="id_instansi" value="<?= $instansi['id'] ?>">

            <!-- Kategori Instansi -->
            <div class="form-group-instansi">
                <label>
                    <i class="fas fa-tags"></i>
                    Kategori Instansi <span class="required">*</span>
                </label>
                <select id="kategoriInstansi" name="kategori_instansi" required>
                    <option value="">Pilih Kategori</option>
                    <option value="SMK Sederajat" <?= $instansi['kategori_label'] === 'SMK Sederajat' ? 'selected' : '' ?>>SMK Sederajat</option>
                    <option value="Kuliah" <?= $instansi['kategori_label'] === 'Kuliah'        ? 'selected' : '' ?>>Kuliah</option>
                </select>
            </div>

            <!-- Nama Instansi -->
            <div class="form-group-instansi">
                <label>
                    <i class="fas fa-building"></i>
                    Nama Instansi <span class="required">*</span>
                </label>
                <input type="text" id="namaInstansi" name="nama_instansi"
                    placeholder="Masukkan nama instansi"
                    value="<?= esc($instansi['nama_instansi']) ?>" required>
            </div>

            <!-- Alamat -->
            <div class="form-group-instansi">
                <label>
                    <i class="fas fa-map-marker-alt"></i>
                    Alamat <span class="required">*</span>
                </label>
                <textarea id="alamatInstansi" name="alamat"
                    placeholder="Masukkan alamat lengkap instansi"
                    required><?= esc($instansi['alamat']) ?></textarea>
            </div>

            <!-- Kota -->
            <div class="form-group-instansi">
                <label>
                    <i class="fas fa-city"></i>
                    Kota <span class="required">*</span>
                </label>
                <select id="kotaInstansi" name="kota" class="select2-kota" required>
                    <option value="">Pilih Kota</option>
                    <?php foreach ($kota_list as $kota): ?>
                        <option value="<?= esc($kota) ?>" <?= $instansi['kota'] === $kota ? 'selected' : '' ?>>
                            <?= esc($kota) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Form Actions -->
            <div class="form-actions-instansi">
                <button type="button" class="btn-instansi btn-kembali"
                    onclick="window.location.href='<?= base_url('dashboard/manajemen-pkl') ?>'">
                    <i class="fas fa-arrow-left"></i>
                    Batal
                </button>
                <button type="submit" class="btn-instansi btn-simpan">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
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
        instansiUbahInit({
                update: '<?= base_url('dashboard/manajemen-pkl/instansi/update') ?>',
                redirect: '<?= base_url('dashboard/manajemen-pkl') ?>'
            },
            '<?= $instansi['id'] ?>'
        );
    });
</script>
<?= $this->endSection() ?>