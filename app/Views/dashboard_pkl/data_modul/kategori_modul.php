<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/modules/pkl-kategori-detail.css') ?>">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
  <div class="breadcrumb">
    <a href="<?= base_url('pkl/data-modul') ?>"><i class="fas fa-arrow-left"></i> Kembali</a>
  </div>
  <h1><?= esc($kategori['nama']) ?></h1>
  <p class="page-subtitle">Daftar modul dalam kategori ini</p>
</div>

<!-- SEARCH -->
<div class="search-section">
  <div class="search-wrapper">
    <div class="search-box">
      <i class="fas fa-search"></i>
      <input type="text" id="searchTable" placeholder="Cari modul..." class="search-input">
    </div>
    <button type="button" id="resetSearch" class="btn-reset">
      <i class="fas fa-rotate-left"></i> Reset
    </button>
  </div>
</div>

<!-- TABEL -->
<div class="table-container">
  <table id="modulTable" style="width:100%">
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Modul</th>
        <th>Kategori</th>
        <th>Modul</th>
        <th>Tanggal Diubah</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($modulList)): ?>
        <?php foreach ($modulList as $index => $modul): ?>
          <tr>
            <td><?= $index + 1 ?></td>
            <td><strong><?= esc($modul['nama']) ?></strong></td>
            <td><span class="kategori-badge"><?= esc($kategori['nama']) ?></span></td>
            <td>
              <?php if ($modul['tipe'] === 'link'): ?>
                <a href="<?= esc($modul['path']) ?>" target="_blank" class="modul-link">
                  <i class="fas fa-link text-success"></i> Buka Link
                </a>
              <?php else: ?>
                <?php
                  $ext  = strtolower(pathinfo($modul['path'], PATHINFO_EXTENSION));
                  $icon = match($ext) {
                      'pdf'        => 'fa-file-pdf text-danger',
                      'doc','docx' => 'fa-file-word text-primary',
                      'ppt','pptx' => 'fa-file-powerpoint text-warning',
                      'xls','xlsx' => 'fa-file-excel text-success',
                      'zip','rar'  => 'fa-file-zipper text-secondary',
                      default      => 'fa-file text-muted',
                  };
                ?>
                <a href="<?= base_url('pkl/data-modul/file/' . $modul['id']) ?>" target="_blank" class="modul-link">
                  <i class="fas <?= $icon ?>"></i> <?= esc($modul['path']) ?>
                </a>
              <?php endif; ?>
            </td>
            <td><?= esc($modul['tanggal_diubah']) ?></td>
            <td>
              <div class="action-buttons">
                <a href="<?= base_url('pkl/data-modul/detail/' . $modul['id']) ?>"
                   class="btn-action btn-detail" title="Detail">
                  <i class="fas fa-eye"></i>
                </a>
                <?php if ($modul['tipe'] === 'file'): ?>
                  <a href="<?= base_url('pkl/data-modul/file/' . $modul['id'] . '/download') ?>"
                     class="btn-action btn-download" title="Download">
                    <i class="fas fa-download"></i>
                  </a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      <!-- Jika kosong, DataTables yang handle via emptyTable di JS -->
    </tbody>
  </table>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url('assets/js/modules/pkl-kategori-detail.js') ?>"></script>
<?= $this->endSection() ?>