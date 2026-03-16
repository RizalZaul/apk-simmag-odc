<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('content') ?>

<div class="page-header">
  <h1>Manajemen Tugas</h1>
  <p class="page-subtitle">Kelola tugas individu dan kelompok Anda</p>
</div>

<?php
/*
 * [FIX BUG 3]
 * Sebelumnya view mengabaikan $tugasList dari PklTugasController::index()
 * dan langsung mendefinisikan dummy fallback ($modulIndividu, $modulKelompok).
 *
 * Sekarang:
 *  - Gunakan $tugasList yang dikirim controller (flat list dari TugasModel::getTugasListForPkl)
 *  - Split berdasarkan field `mode` ('Individu' / 'Kelompok')
 *  - Tampil sebagai flat list (opsi A) sesuai konfirmasi
 *
 * Keys tersedia di setiap item $tugasList:
 *   id, nama, deskripsi, deadline, deadline_raw, kategori, mode,
 *   status, id_pengumpulan, sudah_dikumpulkan, is_lewat_deadline, priority
 *
 * Status dari backend (PengumpulanTugasModel::SQL_STATUS):
 *   'Belum Dikirim' | 'Belum Diperiksa' | 'Revisi' | 'Done'
 */

$tugasList     = $tugasList ?? [];
$tugasIndividu = array_values(array_filter($tugasList, fn($t) => $t['mode'] === 'Individu'));
$tugasKelompok = array_values(array_filter($tugasList, fn($t) => $t['mode'] === 'Kelompok'));

/**
 * Mapping status backend → CSS class + label tampilan.
 * Mengikuti nilai persis dari PengumpulanTugasModel::SQL_STATUS.
 */
function getStatusBadge(string $status): array
{
  return match ($status) {
    'Done'            => ['class' => 'badge-done',    'label' => 'Selesai'],
    'Revisi'          => ['class' => 'badge-revisi',  'label' => 'Revisi'],
    'Belum Diperiksa' => ['class' => 'badge-proses',  'label' => 'Menunggu'],
    default           => ['class' => 'badge-belum',   'label' => 'Belum'],
  };
}
?>

<div class="mt-wrapper">

  <!-- TAB NAV -->
  <div class="mt-tab-nav">
    <button class="mt-tab-btn active" data-tab="individu">
      <i class="fas fa-user"></i> Tugas Individu
      <span class="mt-badge"><?= count($tugasIndividu) ?></span>
    </button>
    <button class="mt-tab-btn" data-tab="kelompok">
      <i class="fas fa-users"></i> Tugas Kelompok
      <span class="mt-badge"><?= count($tugasKelompok) ?></span>
    </button>
  </div>

  <!-- ===== TAB INDIVIDU ===== -->
  <div class="mt-tab-content active" id="tab-individu">

    <div class="mt-search-bar">
      <i class="fas fa-search"></i>
      <input type="text" id="searchIndividu" placeholder="Cari tugas...">
    </div>

    <div class="tugas-list" id="listIndividu">
      <?php if (! empty($tugasIndividu)): ?>
        <?php foreach ($tugasIndividu as $t): ?>
          <?php
          $badge  = getStatusBadge($t['status']);
          $isLate = (bool) ($t['is_lewat_deadline'] ?? false);
          $prio   = $t['priority'] ?? 'low';
          ?>
          <a href="<?= base_url('pkl/manajemen-tugas/detail/' . $t['id']) ?>"
            class="tugas-item priority-<?= $prio ?> <?= $isLate ? 'is-late' : '' ?>">

            <div class="tugas-left">
              <div class="tugas-icon-wrap">
                <i class="fas <?= $t['status'] === 'Done' ? 'fa-check-circle' : ($isLate ? 'fa-exclamation-circle' : 'fa-file-lines') ?>"></i>
              </div>
              <div class="tugas-info">
                <span class="tugas-judul"><?= esc($t['nama']) ?></span>
                <span class="tugas-meta">
                  <i class="far fa-clock"></i> <?= esc($t['deadline']) ?>
                  <span class="dot">•</span>
                  <i class="fas fa-tag"></i> <?= esc($t['kategori']) ?>
                  <?php if ($isLate): ?>
                    <span class="late-tag"><i class="fas fa-exclamation-triangle"></i> Lewat deadline</span>
                  <?php endif; ?>
                </span>
              </div>
            </div>

            <div class="tugas-right">
              <span class="status-badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span>
              <i class="fas fa-chevron-right tugas-arrow"></i>
            </div>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-inbox fa-3x"></i>
          <p>Belum ada tugas individu</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ===== TAB KELOMPOK ===== -->
  <div class="mt-tab-content" id="tab-kelompok">

    <div class="mt-search-bar">
      <i class="fas fa-search"></i>
      <input type="text" id="searchKelompok" placeholder="Cari tugas...">
    </div>

    <div class="tugas-list" id="listKelompok">
      <?php if (! empty($tugasKelompok)): ?>
        <?php foreach ($tugasKelompok as $t): ?>
          <?php
          $badge  = getStatusBadge($t['status']);
          $isLate = (bool) ($t['is_lewat_deadline'] ?? false);
          $prio   = $t['priority'] ?? 'low';
          ?>
          <a href="<?= base_url('pkl/manajemen-tugas/detail/' . $t['id']) ?>"
            class="tugas-item priority-<?= $prio ?> <?= $isLate ? 'is-late' : '' ?>">

            <div class="tugas-left">
              <div class="tugas-icon-wrap kelompok">
                <i class="fas <?= $t['status'] === 'Done' ? 'fa-check-circle' : ($isLate ? 'fa-exclamation-circle' : 'fa-users') ?>"></i>
              </div>
              <div class="tugas-info">
                <span class="tugas-judul"><?= esc($t['nama']) ?></span>
                <span class="tugas-meta">
                  <i class="far fa-clock"></i> <?= esc($t['deadline']) ?>
                  <span class="dot">•</span>
                  <i class="fas fa-tag"></i> <?= esc($t['kategori']) ?>
                  <?php if ($isLate): ?>
                    <span class="late-tag"><i class="fas fa-exclamation-triangle"></i> Lewat deadline</span>
                  <?php endif; ?>
                </span>
              </div>
            </div>

            <div class="tugas-right">
              <span class="status-badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span>
              <i class="fas fa-chevron-right tugas-arrow"></i>
            </div>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-users fa-3x"></i>
          <p>Belum ada tugas kelompok</p>
        </div>
      <?php endif; ?>
    </div>
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
    box-shadow: 0 1px 6px rgba(0, 0, 0, 0.06);
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

  /* WRAPPER */
  .mt-wrapper {
    background: white;
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
  }

  /* TABS */
  .mt-tab-nav {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
  }

  .mt-tab-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 13px 20px;
    border: none;
    border-radius: 10px;
    background: #f1f5f9;
    color: #64748b;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    font-family: inherit;
  }

  .mt-tab-btn:hover {
    background: #e2e8f0;
  }

  .mt-tab-btn.active {
    background: linear-gradient(135deg, #20a8a8, #17c3b2);
    color: white;
    box-shadow: 0 4px 12px rgba(32, 168, 168, 0.3);
  }

  .mt-badge {
    background: rgba(255, 255, 255, 0.25);
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
  }

  .mt-tab-btn:not(.active) .mt-badge {
    background: #20a8a8;
    color: white;
  }

  .mt-tab-content {
    display: none;
  }

  .mt-tab-content.active {
    display: block;
  }

  /* SEARCH */
  .mt-search-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 11px 16px;
    margin-bottom: 16px;
    transition: border-color 0.2s;
  }

  .mt-search-bar:focus-within {
    border-color: #20a8a8;
  }

  .mt-search-bar i {
    color: #94a3b8;
    font-size: 14px;
    flex-shrink: 0;
  }

  .mt-search-bar input {
    border: none;
    background: transparent;
    outline: none;
    width: 100%;
    font-size: 14px;
    color: #334155;
    font-family: inherit;
  }

  .mt-search-bar input::placeholder {
    color: #94a3b8;
  }

  /* TUGAS LIST */
  .tugas-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .tugas-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 18px;
    border-radius: 12px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-left: 4px solid #20a8a8;
    text-decoration: none;
    color: inherit;
    transition: all 0.25s;
    cursor: pointer;
  }

  .tugas-item:hover {
    background: #f0fdfa;
    border-color: #a7f3d0;
    border-left-color: #20a8a8;
    transform: translateX(4px);
    box-shadow: 0 2px 10px rgba(32, 168, 168, 0.1);
  }

  /* Priority border colors */
  .tugas-item.priority-high {
    border-left-color: #ef4444;
  }

  .tugas-item.priority-medium {
    border-left-color: #f59e0b;
  }

  .tugas-item.priority-low {
    border-left-color: #20a8a8;
  }

  /* Late override */
  .tugas-item.is-late {
    background: #fff9f9;
    border-color: #fecaca;
    border-left-color: #ef4444 !important;
  }

  .tugas-item.is-late:hover {
    background: #fef2f2;
  }

  /* Left section */
  .tugas-left {
    display: flex;
    align-items: center;
    gap: 14px;
    flex: 1;
    min-width: 0;
  }

  .tugas-icon-wrap {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: linear-gradient(135deg, #20a8a8, #17c3b2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
  }

  .tugas-icon-wrap.kelompok {
    background: linear-gradient(135deg, #9b59b6, #8e44ad);
  }

  .tugas-item.is-late .tugas-icon-wrap {
    background: linear-gradient(135deg, #ef4444, #dc2626);
  }

  .tugas-info {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .tugas-judul {
    font-size: 14.5px;
    font-weight: 700;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .tugas-item.is-late .tugas-judul {
    color: #dc2626;
  }

  .tugas-meta {
    font-size: 12px;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
  }

  .tugas-meta i {
    font-size: 11px;
  }

  .dot {
    color: #ddd;
  }

  .late-tag {
    background: #fef2f2;
    color: #dc2626;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 3px;
  }

  /* Right section */
  .tugas-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
    margin-left: 10px;
  }

  .status-badge {
    padding: 4px 11px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 700;
    white-space: nowrap;
  }

  .badge-done {
    background: #dcfce7;
    color: #15803d;
  }

  .badge-revisi {
    background: #fef3c7;
    color: #92400e;
  }

  .badge-proses {
    background: #eff6ff;
    color: #1d4ed8;
  }

  .badge-belum {
    background: #fff7ed;
    color: #c2410c;
  }

  .tugas-arrow {
    color: #cbd5e1;
    font-size: 12px;
    transition: transform 0.2s;
  }

  .tugas-item:hover .tugas-arrow {
    color: #20a8a8;
    transform: translateX(3px);
  }

  /* EMPTY STATE */
  .empty-state {
    text-align: center;
    padding: 50px 20px;
    color: #94a3b8;
  }

  .empty-state i {
    margin-bottom: 14px;
    color: #e2e8f0;
    display: block;
  }

  .empty-state p {
    font-size: 15px;
    margin: 0;
  }

  @media (max-width: 640px) {
    .tugas-item {
      flex-direction: column;
      align-items: flex-start;
      gap: 12px;
    }

    .tugas-right {
      width: 100%;
      justify-content: space-between;
      margin-left: 0;
    }
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
  // ===== TABS =====
  document.querySelectorAll('.mt-tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.mt-tab-btn').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.mt-tab-content').forEach(c => c.classList.remove('active'));
      this.classList.add('active');
      document.getElementById('tab-' + this.dataset.tab).classList.add('active');
    });
  });

  // ===== SEARCH — filter berdasarkan nama tugas di setiap list =====
  function initSearch(inputId, listId) {
    const input = document.getElementById(inputId);
    const list = document.getElementById(listId);
    if (!input || !list) return;

    input.addEventListener('input', function() {
      const kw = this.value.trim().toLowerCase();
      let ada = false;

      list.querySelectorAll('.tugas-item').forEach(item => {
        const nama = item.querySelector('.tugas-judul')?.textContent.toLowerCase() ?? '';
        const tampil = kw === '' || nama.includes(kw);
        item.style.display = tampil ? '' : 'none';
        if (tampil) ada = true;
      });

      // Empty-search feedback
      let emptyEl = list.querySelector('.search-empty');
      if (!ada && kw !== '') {
        if (!emptyEl) {
          emptyEl = document.createElement('div');
          emptyEl.className = 'search-empty empty-state';
          emptyEl.innerHTML = '<i class="fas fa-search fa-2x"></i><p>Tugas tidak ditemukan</p>';
          list.appendChild(emptyEl);
        }
      } else {
        emptyEl?.remove();
      }
    });
  }

  initSearch('searchIndividu', 'listIndividu');
  initSearch('searchKelompok', 'listKelompok');
</script>
<?= $this->endSection() ?>