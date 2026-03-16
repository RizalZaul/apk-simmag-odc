<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/modules/data-modul.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="detail-modul-container">
    <div class="detail-section-modul">

        <!-- Header -->
        <div class="detail-header-modul">
            <h2 class="detail-title-modul">
                <i class="fas fa-info-circle"></i>
                Detail Modul
            </h2>
        </div>

        <!-- Nama Modul -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fas fa-book"></i>
                Nama Modul
            </div>
            <div class="detail-value large">
                <?= esc($modul['nama_modul']) ?>
            </div>
        </div>

        <!-- Kategori -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fas fa-tags"></i>
                Kategori
            </div>
            <div class="detail-value">
                <?= esc($modul['nama_kategori']) ?>
            </div>
        </div>

        <!-- Deskripsi -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fas fa-align-left"></i>
                Deskripsi
            </div>
            <div class="detail-value">
                <?= nl2br(esc($modul['ket_modul'])) ?>
            </div>
        </div>

        <!-- Modul (Link atau File) -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fas fa-folder-open"></i>
                Modul
            </div>

            <div class="modul-display">
                <?php if ($modul['tipe'] === 'link'): ?>

                    <!-- Link -->
                    <div class="modul-display-icon link">
                        <i class="fas fa-link"></i>
                    </div>
                    <div class="modul-display-name">Link Eksternal</div>
                    <div class="modul-display-info"><?= esc($modul['path']) ?></div>
                    <div class="modul-action-buttons">
                        <a href="<?= esc($modul['path']) ?>"
                            target="_blank"
                            class="btn-modul-action btn-open">
                            <i class="fas fa-external-link-alt"></i>
                            Buka di Tab Baru
                        </a>
                    </div>

                <?php else: ?>

                    <!-- File -->
                    <?php
                    $ext       = strtolower(pathinfo($modul['path'], PATHINFO_EXTENSION));
                    $icon      = match ($ext) {
                        'pdf'           => 'fa-file-pdf',
                        'docx', 'doc'  => 'fa-file-word',
                        'pptx', 'ppt'  => 'fa-file-powerpoint',
                        'xlsx', 'xls'  => 'fa-file-excel',
                        'zip', 'rar'   => 'fa-file-zipper',
                        default        => 'fa-file',
                    };
                    $iconClass = in_array($ext, ['docx', 'doc']) ? 'docx'
                        : (in_array($ext, ['pptx', 'ppt']) ? 'pptx'
                            : (in_array($ext, ['xlsx', 'xls']) ? 'xlsx'
                                : (in_array($ext, ['zip', 'rar']) ? 'zip'
                                    : (in_array($ext, ['pdf']) ? 'pdf' : 'file'))));

                    /*
                     * FIX BUG 3 — URL file salah & ukuran file tidak terbaca:
                     *
                     * SEBELUMNYA (SALAH):
                     *   $filePath     = base_url('assets/Modul/' . $modul['path']);
                     *   $fileFullPath = FCPATH . 'assets/Modul/' . $modul['path'];
                     *
                     * Masalah 1: File disimpan di writable/uploads/modul/ (di luar public/)
                     *   sehingga TIDAK bisa diakses langsung via URL base_url('assets/Modul/...').
                     *   URL tersebut akan menghasilkan 404 karena file tidak ada di public/assets/Modul/.
                     *
                     * Masalah 2: FCPATH . 'assets/Modul/' . $modul['path'] juga path yang salah
                     *   untuk cek ukuran file, sehingga filesize() selalu gagal → tampil "Unknown".
                     *
                     * SESUDAH (BENAR):
                     *   - Gunakan route controller downloadModul() untuk serve file.
                     *     Route: GET modul/file/(:num)          → inline (lihat di tab baru)
                     *            GET modul/file/(:num)/download  → attachment (unduh)
                     *   - Gunakan WRITEPATH . 'uploads/modul/' . $modul['path'] untuk cek ukuran.
                     *     WRITEPATH adalah path absolut ke folder writable/ di server.
                     */
                    $fileViewUrl     = base_url('dashboard/data-modul/modul/file/' . (int)$modul['id']);
                    $fileDownloadUrl = base_url('dashboard/data-modul/modul/file/' . (int)$modul['id'] . '/download');

                    // Path fisik untuk membaca ukuran file — di luar public/, tidak bisa via URL
                    $fileFullPath = WRITEPATH . 'uploads/modul/' . $modul['path'];
                    $fileSize     = is_file($fileFullPath) ? filesize($fileFullPath) : 0;
                    $fileSizeFmt  = $fileSize > 0
                        ? number_format($fileSize / 1024 / 1024, 2) . ' MB'
                        : 'Unknown';
                    ?>
                    <div class="modul-display-icon <?= $iconClass ?>">
                        <i class="fas <?= $icon ?>"></i>
                    </div>
                    <div class="modul-display-name"><?= esc($modul['path']) ?></div>
                    <div class="modul-display-info">
                        Ukuran: <?= $fileSizeFmt ?> &nbsp;|&nbsp; Format: <?= strtoupper($ext) ?>
                    </div>
                    <div class="modul-action-buttons">
                        <a href="<?= $fileViewUrl ?>"
                            target="_blank"
                            class="btn-modul-action btn-open">
                            <i class="fas fa-eye"></i>
                            Lihat File
                        </a>
                        <a href="<?= $fileDownloadUrl ?>"
                            class="btn-modul-action btn-download">
                            <i class="fas fa-download"></i>
                            Download
                        </a>
                    </div>

                <?php endif; ?>
            </div>
        </div>

        <!-- Tanggal Diubah -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fas fa-calendar-check"></i>
                Terakhir Diubah
            </div>
            <div class="detail-value">
                <?= $modul['tgl_diubah'] ? date('d M Y H:i', strtotime($modul['tgl_diubah'])) : '-' ?>
            </div>
        </div>

        <!-- Tanggal Dibuat -->
        <div class="detail-item">
            <div class="detail-label">
                <i class="fas fa-calendar-plus"></i>
                Tanggal Ditambahkan
            </div>
            <div class="detail-value">
                <?= $modul['tgl_dibuat'] ? date('d M Y H:i', strtotime($modul['tgl_dibuat'])) : '-' ?>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions-modul">
            <button type="button" class="btn-modul btn-kembali"
                onclick="window.location.href='<?= base_url('dashboard/data-modul') ?>'">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </button>
            <button type="button" class="btn-modul btn-ubah"
                onclick="window.location.href='<?= base_url('dashboard/data-modul/modul/ubah/' . $modul['id']) ?>'">
                <i class="fas fa-edit"></i>
                Ubah
            </button>
        </div>

    </div>
</div>

<?= $this->endSection() ?>