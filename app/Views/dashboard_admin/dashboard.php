<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<style>
  /* ── Progress bar tugas dashboard ── */
  .tugas-progress {
    width: 100%;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid rgba(0, 0, 0, .06);
  }

  .tugas-progress-info {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: var(--text-muted, #64748b);
    margin-bottom: 5px;
  }

  .tugas-progress-bar {
    width: 100%;
    height: 6px;
    background: #e2e8f0;
    border-radius: 99px;
    overflow: hidden;
  }

  .tugas-progress-fill {
    height: 100%;
    border-radius: 99px;
    transition: width .4s ease;
    min-width: 2px;
  }

  .tugas-item {
    flex-wrap: wrap;
  }

  .tugas-left {
    flex: 1 1 auto;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
  <h1>Dashboard</h1>
  <p class="page-subtitle">Ringkasan data sistem PKL</p>
</div>

<!-- ================= STATISTIK PKL ================= -->
<div class="dashboard-stats">

  <!-- Aktif: kelompok_pkl.status = 'aktif' -->
  <div class="stat-card card-primary">
    <div class="stat-icon">
      <i class="fas fa-briefcase"></i>
    </div>
    <div class="stat-content">
      <h4>PKL Aktif</h4>
      <p class="stat-number"><?= $pklAktif ?? 0 ?></p>
      <span class="stat-label">Kelompok berjalan</span>
    </div>
  </div>

  <!-- Selesai: kelompok_pkl.status = 'selesai' -->
  <div class="stat-card card-success">
    <div class="stat-icon">
      <i class="fas fa-check-circle"></i>
    </div>
    <div class="stat-content">
      <h4>PKL Selesai</h4>
      <p class="stat-number"><?= $pklSelesai ?? 0 ?></p>
      <span class="stat-label">Kelompok selesai</span>
    </div>
  </div>

  <!-- NonAktif: user pkl dinonaktifkan, tapi kelompoknya masih aktif -->
  <div class="stat-card card-warning">
    <div class="stat-icon">
      <i class="fas fa-pause-circle"></i>
    </div>
    <div class="stat-content">
      <h4>PKL Non-Aktif</h4>
      <p class="stat-number"><?= $pklNonAktif ?? 0 ?></p>
      <span class="stat-label">Peserta dibekukan</span>
    </div>
  </div>

</div>

<!-- ================= MODUL ================= -->
<div class="dashboard-section">
  <div class="section-header">
    <h3><i class="fas fa-book"></i> Modul Pembelajaran</h3>
    <a href="<?= base_url('dashboard/data-modul') ?>" class="btn-link">
      Lihat Semua <i class="fas fa-arrow-right"></i>
    </a>
  </div>

  <div class="modul-wrapper">
    <?php if (! empty($modulList)): ?>
      <?php foreach ($modulList as $modul): ?>
        <div class="modul-card card-success">

          <div class="modul-icon stat-icon">
            <i class="fas fa-book-open"></i>
          </div>

          <div class="modul-body">
            <h4><?= esc($modul['nama']) ?></h4>
            <span class="modul-kategori"><?= esc($modul['kategori']) ?></span>
          </div>

          <div class="modul-footer">
            <a href="<?= base_url('dashboard/data-modul/modul/detail/' . (int) $modul['id']) ?>" class="btn-modul">
              Lihat Detail
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center text-muted">Belum ada modul tersedia</p>
    <?php endif; ?>
  </div>
</div>

<!-- ================= TUGAS ================= -->
<div class="dashboard-section">
  <div class="section-header">
    <h3><i class="fas fa-tasks"></i> Tugas Aktif — Perlu Perhatian</h3>
    <a href="<?= base_url('dashboard/manajemen-tugas/penugasan') ?>" class="btn-link">
      Lihat Semua <i class="fas fa-arrow-right"></i>
    </a>
  </div>

  <div class="tugas-list">
    <?php if (! empty($tugasList)): ?>
      <?php foreach ($tugasList as $tugas): ?>
        <?php
        $priorityConfig = [
          'high'   => ['label' => 'Mendesak', 'class' => 'danger'],
          'medium' => ['label' => 'Segera',   'class' => 'warning'],
          'low'    => ['label' => 'Normal',   'class' => 'info'],
        ];
        $p      = $priorityConfig[$tugas['priority']] ?? $priorityConfig['low'];
        $persen = (int) ($tugas['persen'] ?? 0);
        $barColor = match ($tugas['priority']) {
          'high'   => '#ef4444',
          'medium' => '#f59e0b',
          default  => '#0d9488',
        };
        ?>
        <div class="tugas-item priority-<?= esc($tugas['priority']) ?>">

          <!-- Baris atas: judul + badge + tombol -->
          <div class="tugas-left">
            <div class="tugas-info">
              <i class="fa-regular fa-file-lines"></i>
              <div class="tugas-text">
                <span class="tugas-title"><?= esc($tugas['judul']) ?></span>
                <span class="tugas-meta">
                  <i class="far fa-clock"></i> <?= esc($tugas['deadline_label']) ?>
                  &nbsp;·&nbsp;
                  <i class="fas fa-hourglass-half"></i> <?= esc($tugas['sisa_label']) ?>
                  &nbsp;·&nbsp;
                  <i class="fas fa-tag"></i> <?= esc($tugas['kategori']) ?>
                </span>
              </div>
            </div>
          </div>
          <div class="tugas-right">
            <span class="tugas-status status-<?= $p['class'] ?>">
              <?= $p['label'] ?>
            </span>
            <a href="<?= base_url('dashboard/manajemen-tugas/tugas/detail/' . (int) $tugas['id']) ?>"
              class="btn-action" title="Detail Tugas">
              <i class="fas fa-eye"></i>
            </a>
          </div>

          <!-- Baris bawah: progress bar -->
          <div class="tugas-progress">
            <div class="tugas-progress-info">
              <span><?= (int) $tugas['sudah_kumpul'] ?>/<?= (int) $tugas['total_sasaran'] ?> sudah mengumpulkan</span>
              <span><?= $persen ?>%</span>
            </div>
            <div class="tugas-progress-bar">
              <div class="tugas-progress-fill"
                style="width: <?= $persen ?>%; background: <?= $barColor ?>;"></div>
            </div>
          </div>

        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="empty-state">
        <i class="fas fa-check-circle fa-3x" style="color: #0d9488;"></i>
        <p>Semua tugas aktif sudah selesai 🎉</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<?= $this->endSection() ?>