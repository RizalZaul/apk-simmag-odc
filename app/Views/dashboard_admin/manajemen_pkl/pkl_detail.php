<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<!-- Manajemen PKL Module CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/modules/manajemen-pkl.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="detail-pkl-container">

    <!-- Header Section -->
    <div class="detail-header">
        <div class="detail-header-left">
            <a href="<?= base_url('dashboard/manajemen-pkl') ?>" class="btn-back-link">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Data PKL
            </a>
            <h2 class="detail-title">
                <i class="fas fa-clipboard-list"></i>
                Detail PKL #<?= str_pad($pkl['id_kelompok'], 3, '0', STR_PAD_LEFT) ?>
            </h2>
        </div>
        <div class="detail-header-right">
            <span class="badge badge-<?= strtolower($pkl['status_kelompok']) ?>">
                <?= ucfirst($pkl['status_kelompok']) ?>
            </span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="detail-content">

        <!-- ==================== SECTION 1: INFORMASI KELOMPOK ==================== -->
        <div class="detail-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-users"></i>
                    Informasi Umum
                </h3>
            </div>
            <div class="section-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Kategori PKL</span>
                        <span class="detail-value">
                            <?= $pkl['kategori_pkl'] ?>
                            <span class="badge badge-info"><?= $pkl['kategori_pkl'] ?></span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status Kelompok</span>
                        <span class="detail-value">
                            <span class="badge badge-<?= strtolower($pkl['status_kelompok']) ?>">
                                <?= ucfirst($pkl['status_kelompok']) ?>
                            </span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tanggal Mulai</span>
                        <span class="detail-value">
                            <i class="fas fa-calendar-alt"></i>
                            <?= $pkl['tgl_mulai'] ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tanggal Akhir</span>
                        <span class="detail-value">
                            <i class="fas fa-calendar-check"></i>
                            <?= $pkl['tgl_akhir'] ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Durasi PKL</span>
                        <span class="detail-value">
                            <i class="fas fa-hourglass-half"></i>
                            <?= $pkl['durasi'] ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Jumlah Anggota</span>
                        <span class="detail-value">
                            <i class="fas fa-user-friends"></i>
                            <?= count($pkl['anggota']) ?> Orang
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== SECTION 2: BIODATA INSTANSI (CONDITIONAL) ==================== -->
        <?php if ($pkl['kategori_pkl'] === 'Instansi' && isset($pkl['instansi'])): ?>
            <div class="detail-section" id="sectionInstansi">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-building"></i>
                        Biodata Instansi
                    </h3>
                </div>
                <div class="section-body">
                    <!-- View Mode -->
                    <div class="detail-grid" id="viewInstansi">
                        <div class="detail-item">
                            <span class="detail-label">Kategori Instansi</span>
                            <span class="detail-value"><?= $pkl['instansi']['kategori_instansi'] ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Nama Instansi</span>
                            <span class="detail-value">
                                <strong><?= $pkl['instansi']['nama_instansi'] ?></strong>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Nama Kelompok</span>
                            <span class="detail-value">
                                <i class="fas fa-flag"></i>
                                <?= $pkl['nama_kelompok'] ?>
                            </span>
                        </div>
                        <div class="detail-item detail-item-full">
                            <span class="detail-label">Alamat Instansi</span>
                            <span class="detail-value"><?= $pkl['instansi']['alamat'] ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Kota</span>
                            <span class="detail-value">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= $pkl['instansi']['kota'] ?>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Nama Pembimbing</span>
                            <span class="detail-value" id="viewPembimbing">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <?= $pkl['instansi']['nama_pembimbing'] ?>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">No WA Pembimbing</span>
                            <span class="detail-value" id="viewNoWaPembimbing">
                                <i class="fas fa-phone"></i>
                                <?= $pkl['instansi']['no_wa_pembimbing'] ?>
                            </span>
                        </div>
                    </div>

                    <!-- Edit Mode (Hidden by default) -->
                    <div class="edit-form" id="editInstansi" style="display: none;">
                        <div class="form-row">
                            <div class="form-group-detail">
                                <label>Kategori Instansi</label>
                                <input type="text" value="<?= $pkl['instansi']['kategori_instansi'] ?>" readonly class="readonly-field">
                            </div>
                            <div class="form-group-detail">
                                <label>Nama Instansi</label>
                                <input type="text" value="<?= $pkl['instansi']['nama_instansi'] ?>" readonly class="readonly-field">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group-detail">
                                <label>Nama Kelompok</label>
                                <input type="text" value="<?= $pkl['nama_kelompok'] ?>" readonly class="readonly-field">
                            </div>
                            <div class="form-group-detail">
                                <label>Kota</label>
                                <input type="text" value="<?= $pkl['instansi']['kota'] ?>" readonly class="readonly-field">
                            </div>
                        </div>
                        <div class="form-group-detail">
                            <label>Alamat Instansi</label>
                            <textarea readonly class="readonly-field"><?= $pkl['instansi']['alamat'] ?></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group-detail">
                                <label>Nama Pembimbing <span class="required">*</span></label>
                                <input type="text" id="editPembimbing" value="<?= $pkl['instansi']['nama_pembimbing'] ?>">
                            </div>
                            <div class="form-group-detail">
                                <label>No WA Pembimbing <span class="required">*</span></label>
                                <input type="text" id="editNoWaPembimbing" value="<?= $pkl['instansi']['no_wa_pembimbing'] ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
        <?php endif; ?>

        <!-- ==================== SECTION 3: DAFTAR ANGGOTA ==================== -->
        <div class="detail-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="fas fa-user-friends"></i>
                    Daftar Anggota (<?= count($pkl['anggota']) ?> Orang)
                </h3>
            </div>
            <div class="section-body">
                <div class="anggota-accordion" id="anggotaAccordion">
                    <?php foreach ($pkl['anggota'] as $index => $anggota): ?>
                        <div class="anggota-item <?= $index === 0 ? 'active' : '' ?>"
                            data-id="<?= $anggota['id_user'] ?>"
                            data-id-pkl="<?= $anggota['id_pkl'] ?>">
                            <div class="anggota-header">
                                <div class="anggota-title">
                                    <i class="fas fa-user-circle"></i>
                                    <span class="anggota-name">
                                        Anggota <?= $index + 1 ?> - <?= $anggota['nama_lengkap'] ?>
                                        <?php if (strtolower($anggota['role_kelompok']) === 'ketua'): ?>
                                            <span class="badge badge-ketua">Ketua</span>
                                        <?php else: ?>
                                            <span class="badge badge-anggota">Anggota</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <i class="fas fa-chevron-down anggota-toggle"></i>
                            </div>
                            <div class="anggota-content">
                                <!-- View Mode -->
                                <div class="detail-grid view-anggota">
                                    <div class="detail-item">
                                        <span class="detail-label">Nama Lengkap</span>
                                        <span class="detail-value"><?= $anggota['nama_lengkap'] ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Nama Panggilan</span>
                                        <span class="detail-value"><?= $anggota['nama_panggilan'] ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Tempat Lahir</span>
                                        <span class="detail-value"><?= $anggota['tempat_lahir'] ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Tanggal Lahir</span>
                                        <span class="detail-value">
                                            <i class="fas fa-birthday-cake"></i>
                                            <?= $anggota['tgl_lahir'] ?>
                                        </span>
                                    </div>
                                    <div class="detail-item detail-item-full">
                                        <span class="detail-label">Alamat</span>
                                        <span class="detail-value"><?= $anggota['alamat'] ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">No WA</span>
                                        <span class="detail-value">
                                            <i class="fas fa-phone"></i>
                                            <?= $anggota['no_wa'] ?>
                                        </span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Email</span>
                                        <span class="detail-value">
                                            <i class="fas fa-envelope"></i>
                                            <?= $anggota['email'] ?>
                                        </span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Jenis Kelamin</span>
                                        <span class="detail-value">
                                            <i class="fas fa-<?= strtolower($anggota['jenis_kelamin']) === 'laki-laki' ? 'mars' : 'venus' ?>"></i>
                                            <?= $anggota['jenis_kelamin'] ?>
                                        </span>
                                    </div>

                                    <?php if ($pkl['kategori_pkl'] === 'Instansi'): ?>
                                        <div class="detail-item">
                                            <span class="detail-label">Jurusan</span>
                                            <span class="detail-value">
                                                <i class="fas fa-graduation-cap"></i>
                                                <?= $anggota['jurusan'] ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="detail-item">
                                        <span class="detail-label">Status</span>
                                        <span class="detail-value">
                                            <span class="badge badge-<?= strtolower($anggota['status_user']) ?>">
                                                <?= ucfirst($anggota['status_user']) ?>
                                            </span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Edit Mode (Hidden by default) -->
                                <div class="edit-form edit-anggota" style="display: none;">
                                    <div class="form-row">
                                        <div class="form-group-detail">
                                            <label>Nama Lengkap <span class="required">*</span></label>
                                            <input type="text" class="edit-nama-lengkap" value="<?= $anggota['nama_lengkap'] ?>">
                                        </div>
                                        <div class="form-group-detail">
                                            <label>Nama Panggilan <span class="required">*</span></label>
                                            <input type="text" class="edit-nama-panggilan" value="<?= $anggota['nama_panggilan'] ?>">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group-detail">
                                            <label>Tempat Lahir <span class="required">*</span></label>
                                            <input type="text" class="edit-tempat-lahir" value="<?= $anggota['tempat_lahir'] ?>">
                                        </div>
                                        <div class="form-group-detail">
                                            <label>Tanggal Lahir <span class="required">*</span></label>
                                            <input type="text" class="edit-tgl-lahir flatpickr-biodata" value="<?= $anggota['tgl_lahir'] ?>">
                                        </div>
                                    </div>
                                    <div class="form-group-detail">
                                        <label>Alamat <span class="required">*</span></label>
                                        <textarea class="edit-alamat"><?= $anggota['alamat'] ?></textarea>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group-detail">
                                            <label>No WA <span class="required">*</span></label>
                                            <input type="text" class="edit-no-wa" value="<?= $anggota['no_wa'] ?>">
                                        </div>
                                        <div class="form-group-detail">
                                            <label>Email</label>
                                            <input type="email" value="<?= $anggota['email'] ?>" readonly class="readonly-field">
                                            <small class="form-hint">Email tidak dapat diubah</small>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group-detail">
                                            <label>Jenis Kelamin <span class="required">*</span></label>
                                            <select class="edit-jenis-kelamin">
                                                <option value="Laki-laki" <?= $anggota['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                                <option value="Perempuan" <?= $anggota['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                            </select>
                                        </div>

                                        <?php if ($pkl['kategori_pkl'] === 'Instansi'): ?>
                                            <div class="form-group-detail">
                                                <label>Jurusan <span class="required">*</span></label>
                                                <input type="text" class="edit-jurusan" value="<?= $anggota['jurusan'] ?>">
                                            </div>
                                        <?php endif; ?>


                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Action Buttons (Fixed Bottom) -->
    <div class="detail-actions">
        <div class="actions-left">
            <a href="<?= base_url('dashboard/manajemen-pkl') ?>" class="btn-detail btn-back" id="btnKembali">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
        <div class="actions-right">
            <button type="button" class="btn-detail btn-edit" id="btnEdit">
                <i class="fas fa-edit"></i>
                Edit PKL
            </button>
            <button type="button" class="btn-detail btn-cancel" id="btnCancel" style="display: none;">
                <i class="fas fa-times"></i>
                Batal
            </button>
            <button type="button" class="btn-detail btn-save" id="btnSave" style="display: none;">
                <i class="fas fa-save"></i>
                Simpan Perubahan
            </button>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="<?= base_url('assets/js/modules/pkl.js') ?>"></script>
<script>
    $(document).ready(function() {
        pklDetailInit(
            <?= $pkl['id_kelompok'] ?>, {
                update: '<?= base_url('dashboard/manajemen-pkl/pkl/update') ?>'
            }
        );
    });
</script>
<?= $this->endSection() ?>