<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="page-header">
  <h1>Dashboard</h1>
  <p class="page-subtitle">Selamat datang, <?= session()->get('nama') ?? 'Siswa PKL' ?>!</p>
</div>

<!-- ================= STATISTIK TUGAS ================= -->
<div class="dashboard-stats">
  <div class="stat-card card-primary">
    <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
    <div class="stat-content">
      <h4>Total Tugas</h4>
      <p class="stat-number"><?= $totalTugas ?? 0 ?></p>
      <span class="stat-label">Tugas yang diberikan</span>
    </div>
  </div>
  <div class="stat-card card-success">
    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
    <div class="stat-content">
      <h4>Tugas Selesai</h4>
      <p class="stat-number"><?= $tugasSelesai ?? 0 ?></p>
      <span class="stat-label">Telah dikumpulkan</span>
    </div>
  </div>
  <div class="stat-card card-warning">
    <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
    <div class="stat-content">
      <h4>Tugas Pending</h4>
      <p class="stat-number"><?= $tugasPending ?? 0 ?></p>
      <span class="stat-label">Belum dikerjakan</span>
    </div>
  </div>
</div>

<!-- ================= MODUL PEMBELAJARAN ================= -->
<div class="dashboard-section">
  <div class="section-header">
    <h3><i class="fas fa-book"></i> Modul Pembelajaran Saya</h3>
    <a href="<?= base_url('pkl/data-modul') ?>" class="btn-link">Lihat Semua <i class="fas fa-arrow-right"></i></a>
  </div>
  <div class="modul-wrapper">
    <?php if (!empty($modulList)): ?>
      <?php foreach ($modulList as $modul): ?>
        <div class="modul-card">
          <div class="modul-icon bg-<?= $modul['color'] ?>">
            <i class="fas <?= $modul['icon'] ?>"></i>
          </div>
          <div class="modul-body">
            <h4><?= esc($modul['nama']) ?></h4>
            <div class="progress-bar">
              <div class="progress-fill" style="width: <?= $modul['progress'] ?>%"></div>
            </div>
            <span class="progress-text"><?= $modul['progress'] ?>% Selesai (<?= $modul['selesai'] ?>/<?= $modul['total_materi'] ?> Materi)</span>
          </div>
          <div class="modul-footer bg-<?= $modul['color'] ?>">
            <a href="<?= base_url('pkl/data-modul/kategori/' . $modul['id']) ?>" class="btn-modul">Lanjutkan Belajar</a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="empty-state">
        <i class="fas fa-book-open fa-2x"></i>
        <p>Belum ada modul tersedia</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- ================= TUGAS TERBARU ================= -->
<div class="dashboard-section">
  <div class="section-header">
    <h3><i class="fas fa-tasks"></i> Tugas Terbaru</h3>
    <a href="<?= base_url('pkl/manajemen-tugas') ?>" class="btn-link">Lihat Semua <i class="fas fa-arrow-right"></i></a>
  </div>
  <div class="tugas-list">
    <?php if (!empty($tugasTerbaru)): ?>
      <?php foreach ($tugasTerbaru as $tugas): ?>
        <div class="tugas-item priority-<?= $tugas['priority'] ?>">
          <div class="tugas-left">
            <div class="tugas-info">
              <i class="fa-regular fa-file-lines"></i>
              <div class="tugas-text">
                <span class="tugas-title"><?= esc($tugas['judul']) ?></span>
                <span class="tugas-meta">
                  <i class="far fa-clock"></i> Deadline: <?= esc($tugas['deadline']) ?>
                  <span class="divider">•</span>
                  <i class="fas fa-tag"></i> <?= esc($tugas['kategori']) ?>
                </span>
              </div>
            </div>
          </div>
          <div class="tugas-right">
            <span class="badge badge-<?= $tugas['mode'] === 'Individu' ? 'primary' : 'info' ?>">
              <i class="fas fa-<?= $tugas['mode'] === 'Individu' ? 'user' : 'users' ?>"></i>
              <?= esc($tugas['mode']) ?>
            </span>
            <a href="<?= base_url('pkl/manajemen-tugas/detail/' . $tugas['id']) ?>" class="btn-view-tugas">
              <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="empty-state">
        <i class="fas fa-inbox fa-3x"></i>
        <p>Belum ada tugas</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
  /* PAGE HEADER */
  .page-header {
    background: #fff;
    border-radius: 10px;
    padding: 20px 24px;
    margin-bottom: 24px;
    border-left: 4px solid #0f766e;
    box-shadow: 0 1px 6px rgba(0,0,0,0.06);
  }
  .page-header h1 {
    margin: 0 0 4px 0;
    font-size: 20px;
    font-weight: 700;
    color: #1a1a1a;
  }
  .page-subtitle {
    margin: 0;
    font-size: 14px;
    color: #666;
  }

  /* TUGAS LIST */
  .tugas-list { display: flex; flex-direction: column; gap: 12px; }

  .tugas-item {
    background: #f8fafc;
    border-radius: 10px;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-left: 4px solid #e0e0e0;
    transition: all 0.3s ease;
  }

  .tugas-item:hover {
    background: #f0fdfa;
    border-left-color: #20a8a8;
    transform: translateX(5px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  }

  .tugas-item.priority-high   { border-left-color: #e74c3c; }
  .tugas-item.priority-medium { border-left-color: #f39c12; }
  .tugas-item.priority-low    { border-left-color: #95a5a6; }

  .tugas-left { flex: 1; display: flex; align-items: center; gap: 15px; }

  .tugas-info { display: flex; align-items: center; gap: 12px; flex: 1; }
  .tugas-info > i { font-size: 22px; color: #20a8a8; flex-shrink: 0; }

  .tugas-text { display: flex; flex-direction: column; gap: 5px; }

  .tugas-title { font-weight: 600; color: #1a1a1a; font-size: 14.5px; }

  .tugas-meta {
    font-size: 12px; color: #666;
    display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
  }
  .tugas-meta i { font-size: 11px; }
  .divider { color: #ddd; }

  .tugas-right { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }

  .badge {
    padding: 5px 12px; border-radius: 20px;
    font-size: 12px; font-weight: 500;
    display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;
  }
  .badge-primary { background: #e3f2fd; color: #1976d2; }
  .badge-info    { background: #f3e5f5; color: #7b1fa2; }

  .btn-view-tugas {
    width: 34px; height: 34px; border-radius: 8px;
    background: #20a8a8; color: white;
    display: flex; align-items: center; justify-content: center;
    text-decoration: none; transition: all 0.3s ease; font-size: 13px;
  }
  .btn-view-tugas:hover { background: #17c3b2; transform: scale(1.1); }

  .empty-state {
    text-align: center;
    padding: 50px 20px;
    color: #999;
    grid-column: 1 / -1;
    width: 100%;
  }
  .empty-state i { margin-bottom: 15px; color: #ddd; display: block; }
  .empty-state p { font-size: 15px; margin: 0; }

  @media (max-width: 768px) {
    .tugas-item { flex-direction: column; align-items: flex-start; gap: 14px; }
    .tugas-right { width: 100%; justify-content: space-between; }
  }
</style>
<?= $this->endSection() ?>