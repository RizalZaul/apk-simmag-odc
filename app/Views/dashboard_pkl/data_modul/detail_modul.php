<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<style>
/* ===============================================================
   DETAIL MODUL (PKL) - Inline Styles
   =============================================================== */

.detail-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

/* Header card */
.detail-card-header {
  background: linear-gradient(135deg, #0d4f4f 0%, #0f766e 60%, #14b8a6 100%);
  padding: 28px 32px;
  display: flex;
  align-items: center;
  gap: 14px;
  position: relative;
  overflow: hidden;
}
.detail-card-header::after {
  content: '';
  position: absolute;
  top: -40px; right: -40px;
  width: 160px; height: 160px;
  border-radius: 50%;
  background: rgba(255,255,255,0.06);
}
.detail-card-header h2 {
  margin: 0;
  font-size: 20px;
  font-weight: 700;
  color: white;
  letter-spacing: -0.2px;
}
.detail-card-header > i {
  font-size: 22px;
  color: rgba(255,255,255,0.85);
  z-index: 1;
}

/* Body */
.detail-card-body { padding: 28px 32px; }

/* Detail item */
.detail-item { margin-bottom: 18px; }

.detail-item label {
  display: block;
  font-size: 11.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #64748b;
  margin-bottom: 6px;
}

.detail-item p {
  margin: 0;
  padding: 13px 16px;
  background: #f8fafc;
  border: 1.5px solid #e2e8f0;
  border-radius: 8px;
  font-size: 14px;
  color: #1e293b;
  font-weight: 500;
  line-height: 1.5;
}

/* Grid 2 kolom untuk tanggal */
.detail-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 18px;
}

/* File / Link box */
.file-box {
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  padding: 24px 20px;
  text-align: center;
  background: #f8fafc;
  transition: all 0.25s ease;
}
.file-box:hover {
  border-color: #14b8a6;
  box-shadow: 0 0 0 3px rgba(20,184,166,0.08);
}

/* Ikon tipe */
.file-box .box-icon {
  font-size: 40px;
  display: block;
  margin-bottom: 10px;
}
.icon-pdf   { color: #ef4444; }
.icon-word  { color: #2563eb; }
.icon-ppt   { color: #f59e0b; }
.icon-xlsx  { color: #22c55e; }
.icon-zip   { color: #8b5cf6; }
.icon-link  { color: #0f766e; }
.icon-file  { color: #64748b; }

/* Label tipe badge */
.tipe-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 12px;
}
.tipe-badge.badge-file { background: #fef3c7; color: #92400e; }
.tipe-badge.badge-link { background: #d1fae5; color: #065f46; }

.file-box .path-text {
  margin: 0 0 16px 0;
  font-size: 13px;
  color: #475569;
  font-weight: 500;
  word-break: break-all;
}

/* Tombol aksi file */
.file-actions {
  display: flex;
  gap: 10px;
  justify-content: center;
  flex-wrap: wrap;
}

.btn-view {
  background: #0f766e;
  color: white;
  padding: 9px 20px;
  border-radius: 8px;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.25s ease;
}
.btn-view:hover {
  background: #0d5c55;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(15,118,110,0.3);
  color: white;
}

.btn-download {
  background: #334155;
  color: white;
  padding: 9px 20px;
  border-radius: 8px;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.25s ease;
}
.btn-download:hover {
  background: #1e293b;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(51,65,85,0.3);
  color: white;
}

/* Footer */
.detail-footer {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  padding: 20px 32px 28px;
  border-top: 1.5px solid #e2e8f0;
}

.btn-back {
  background: #f1f5f9;
  color: #475569;
  padding: 10px 22px;
  border-radius: 8px;
  text-decoration: none;
  font-size: 13.5px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  border: 1.5px solid #cbd5e1;
  transition: all 0.25s ease;
}
.btn-back:hover {
  background: #e2e8f0;
  border-color: #94a3b8;
  color: #334155;
}

/* Responsive */
@media (max-width: 576px) {
  .detail-card-header { padding: 22px 20px; }
  .detail-card-body   { padding: 20px; }
  .detail-grid-2      { grid-template-columns: 1fr; }
  .detail-footer      { padding: 16px 20px 22px; }
  .btn-back           { width: 100%; justify-content: center; }
  .file-actions       { flex-direction: column; }
  .btn-view, .btn-download { justify-content: center; }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
  /*
   * Tentukan ikon & label berdasarkan tipe dan ekstensi.
   * Logika sama persis dengan versi admin.
   */
  if ($modul['tipe'] === 'link') {
      $iconClass  = 'fas fa-link box-icon icon-link';
      $badgeClass = 'badge-link';
      $badgeText  = 'Link URL';
  } else {
      $ext = strtolower(pathinfo($modul['path'], PATHINFO_EXTENSION));
      $iconClass = match($ext) {
          'pdf'         => 'fas fa-file-pdf box-icon icon-pdf',
          'doc', 'docx' => 'fas fa-file-word box-icon icon-word',
          'ppt', 'pptx' => 'fas fa-file-powerpoint box-icon icon-ppt',
          'xls', 'xlsx' => 'fas fa-file-excel box-icon icon-xlsx',
          'zip', 'rar'  => 'fas fa-file-zipper box-icon icon-zip',
          default       => 'fas fa-file box-icon icon-file',
      };
      $badgeClass = 'badge-file';
      $badgeText  = 'File ' . strtoupper($ext);
  }

  /*
   * [FIX] URL file modul untuk PKL.
   *
   * SALAH (versi lama):
   *   base_url('uploads/modul/' . $modul['path'])
   *   → File ada di writable/uploads/modul/ (di luar public/),
   *     TIDAK bisa diakses langsung via URL — akan menghasilkan 404.
   *
   *   base_url('dashboard/data-modul/download/' . $modul['id'])
   *   → Route tidak ada di Routes.php, dan 'dashboard' dilindungi filter admin.
   *
   * BENAR (sekarang):
   *   base_url('pkl/data-modul/file/' . $modul['id'])
   *   → Route PKL yang mengarah ke DataModulController::downloadModul()
   *     dengan filter 'auth:pkl' — aman dan benar.
   */
  $fileViewUrl     = base_url('pkl/data-modul/file/' . (int)$modul['id']);
  $fileDownloadUrl = base_url('pkl/data-modul/file/' . (int)$modul['id'] . '/download');
?>

<div class="detail-card">

  <!-- ===== HEADER ===== -->
  <div class="detail-card-header">
    <i class="fas fa-book"></i>
    <h2>Detail Modul</h2>
  </div>

  <!-- ===== BODY ===== -->
  <div class="detail-card-body">

    <!-- Nama Modul -->
    <div class="detail-item">
      <label><i class="fas fa-book-open"></i> Nama Modul</label>
      <p><?= esc($modul['nama_modul']) ?></p>
    </div>

    <!-- Kategori -->
    <div class="detail-item">
      <label><i class="fas fa-tag"></i> Kategori</label>
      <p><?= esc($modul['kategori']) ?></p>
    </div>

    <!-- Keterangan / Deskripsi -->
    <div class="detail-item">
      <label><i class="fas fa-align-left"></i> Deskripsi</label>
      <p><?= esc($modul['ket_modul']) ?></p>
    </div>

    <!-- Tanggal (2 kolom) -->
    <div class="detail-grid-2">
      <div class="detail-item" style="margin-bottom:0">
        <label><i class="fas fa-calendar-plus"></i> Tanggal Dibuat</label>
        <p><?= esc($modul['tgl_dibuat']) ?></p>
      </div>
      <div class="detail-item" style="margin-bottom:0">
        <label><i class="fas fa-calendar-check"></i> Tanggal Diubah</label>
        <p><?= esc($modul['tgl_diubah']) ?></p>
      </div>
    </div>

    <!-- Modul: Link atau File -->
    <div class="detail-item">
      <label><i class="fas fa-paperclip"></i> Modul</label>

      <div class="file-box">
        <i class="<?= $iconClass ?>"></i>
        <span class="tipe-badge <?= $badgeClass ?>"><?= $badgeText ?></span>
        <p class="path-text"><?= esc($modul['path']) ?></p>

        <div class="file-actions">
          <?php if ($modul['tipe'] === 'link'): ?>
            <a href="<?= esc($modul['path']) ?>" target="_blank" class="btn-view">
              <i class="fas fa-external-link-alt"></i> Buka Link
            </a>
          <?php else: ?>
            <!--
              [FIX] Gunakan route PKL (pkl/data-modul/file/:id) bukan URL langsung.
              File disimpan di writable/ dan hanya bisa diakses lewat controller.
            -->
            <a href="<?= $fileViewUrl ?>" target="_blank" class="btn-view">
              <i class="fas fa-eye"></i> Lihat File
            </a>
            <a href="<?= $fileDownloadUrl ?>" class="btn-download">
              <i class="fas fa-download"></i> Download
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div><!-- /detail-card-body -->

  <!-- ===== FOOTER ===== -->
  <!--
    [FIX] Tombol "Ubah" dihapus — PKL tidak punya akses edit modul.
    Footer hanya berisi tombol Kembali ke halaman kategori.
  -->
  <div class="detail-footer">
    <a href="<?= base_url('pkl/data-modul/kategori/' . $modul['id_kat_m']) ?>" class="btn-back">
      <i class="fas fa-arrow-left"></i> Kembali
    </a>
  </div>

</div>

<?= $this->endSection() ?>