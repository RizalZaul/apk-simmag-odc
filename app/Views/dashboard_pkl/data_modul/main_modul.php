<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/modules/pkl-data-modul.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
  <h1>Data Modul</h1>
  <p class="page-subtitle">Modul pembelajaran PKL</p>
</div>

<!-- TOOLBAR -->
<div class="modul-toolbar">
  <div class="search-box">
    <i class="fas fa-search"></i>
    <input type="text" id="searchModul" placeholder="Cari kategori..." class="search-input">
  </div>
  <div class="modul-count-badge">
    <i class="fas fa-layer-group"></i>
    <?= count($kategoriList ?? []) ?> Kategori
  </div>
  <button type="button" id="resetSearch" class="btn-reset">
    <i class="fas fa-rotate-left"></i> Reset
  </button>
</div>

<!-- SECTION LABEL -->
<div class="section-label">
  <i class="fas fa-th-large"></i> Semua Kategori
</div>

<!-- GRID -->
<div class="kategori-modul-grid" id="kategoriGrid">
  <?php if (!empty($kategoriList)): ?>
    <?php foreach($kategoriList as $kategori): ?>
      <a href="<?= base_url('pkl/data-modul/kategori/' . $kategori['id']) ?>" class="kategori-card">
        <div class="kategori-icon bg-<?= $kategori['color'] ?>">
          <i class="fas fa-layer-group"></i>
        </div>
        <div class="kategori-content">
          <h3><?= esc($kategori['nama']) ?></h3>
          <div class="kategori-meta">
            <p class="kategori-count">
              <i class="fas fa-book"></i> <?= $kategori['total_modul'] ?> Modul
            </p>
            <span class="kategori-pill">
              <i class="fas fa-check-circle"></i> Aktif
            </span>
          </div>
        </div>
        <div class="kategori-arrow">
          <i class="fas fa-chevron-right"></i>
        </div>
      </a>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="empty-state">
      <i class="fas fa-folder-open fa-3x"></i>
      <p>Belum ada kategori modul</p>
    </div>
  <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="<?= base_url('assets/js/modules/pkl-data-modul.js') ?>"></script>
<?= $this->endSection() ?>