<?= $this->extend('layouts/dashboard_layout') ?>
<?= $this->section('content') ?>

<?php
/*
 * DETAIL TUGAS — PKL
 *
 * [FIX BUG 1] Tipe item sekarang 'file' | 'link' (bukan 'text' | 'link').
 *             Modal menggunakan FormData multipart, bukan JSON.
 *             File yang diizinkan: .pdf .docx .doc .pptx .ppt .xlsx .xls .zip .rar
 *             Serve file via route: pkl/manajemen-tugas/file/{filename}
 *
 * [FIX BUG 2] Saat status 'Revisi': modal hanya menampilkan slot untuk
 *             item yang berstatus 'Revisi', disertai data lama + catatan admin.
 *             Submit hanya mengirim pengganti item revisi (bukan semua item).
 *
 * [FIX BUG 3] "Tugas ini tidak ditugaskan kepada Anda" sudah diperbaiki
 *             di PklTugasController & PengumpulanTugasModel — view ini tidak
 *             perlu perubahan untuk bug 3, tapi comment lama diperbarui.
 */

// ── Field dari $tugas (TugasModel::getFormattedDetail) ────────────────────
$judul           = $tugas['nama_tugas']       ?? '-';
$kategori        = $tugas['nama_kategori']    ?? $tugas['kategori_tugas'] ?? '-';
$deskripsi       = $tugas['deskripsi']        ?? '-';
$deadlineFmt     = $tugas['deadline_fmt']     ?? '-';
$deadlineTs      = ! empty($tugas['deadline']) ? (int) strtotime($tugas['deadline']) * 1000 : 0;
$tglDibuat       = $tugas['tgl_dibuat']       ?? '-';
$tglDiubah       = $tugas['tgl_diubah']       ?? '-';
$tugasId         = (int) ($tugas['id_tugas']  ?? $tugas['id'] ?? 0);
$targetJumlah    = max(1, (int) ($tugas['target_jumlah'] ?? 1));
$isLewatDeadline = (bool) ($tugas['is_lewat_deadline'] ?? false);
$modePengumpulan = $tugas['mode_pengumpulan'] ?? 'individu';

// ── Status dari $pengumpulan ──────────────────────────────────────────────
$status    = $pengumpulan['status']          ?? 'Belum Dikirim';
$tglKumpul = $pengumpulan['tgl_pengumpulan'] ?? null;

$statusMap = [
  'Done'            => ['class' => 'status-done',    'label' => 'Selesai'],
  'Revisi'          => ['class' => 'status-revisi',  'label' => 'Revisi'],
  'Belum Diperiksa' => ['class' => 'status-proses',  'label' => 'Belum Diperiksa'],
  'Belum Dikirim'   => ['class' => 'status-belum',   'label' => 'Belum Dikirim'],
];
$statusInfo = $statusMap[$status] ?? ['class' => 'status-belum', 'label' => $status];

$canSubmit = $pengumpulan !== null
  && in_array($status, ['Belum Dikirim', 'Revisi'], true);

$items = $items ?? [];

// [FIX BUG 2] Filter item yang perlu direvisi
$itemsRevisi  = array_values(array_filter($items, fn($i) => $i['status'] === 'Revisi'));
$revisiCount  = count($itemsRevisi);

// Jumlah slot modal:
//   - Status Revisi   → hanya tampilkan slot sebanyak item revisi
//   - Status lainnya  → tampilkan semua target_jumlah slot
$slotsModal = ($status === 'Revisi') ? max(1, $revisiCount) : $targetJumlah;

// Ekstensi yang diizinkan (sinkron dengan controller & modul)
$allowedExt     = ['pdf', 'docx', 'doc', 'pptx', 'ppt', 'xlsx', 'xls', 'zip', 'rar'];
$acceptAttr     = '.' . implode(',.', $allowedExt);
$allowedExtList = implode(', ', $allowedExt);
?>

<!-- BREADCRUMB -->
<nav class="breadcrumb">
  <a href="<?= base_url('pkl/manajemen-tugas') ?>">
    <i class="fas fa-th-list"></i> Manajemen Tugas
  </a>
  <i class="fas fa-chevron-right sep"></i>
  <span><?= esc($judul) ?></span>
</nav>

<!-- HERO -->
<div class="dt-hero">
  <div class="dt-hero-icon">
    <i class="fas fa-file-upload"></i>
  </div>
  <div class="dt-hero-text">
    <h1><?= esc($judul) ?></h1>
    <div class="dt-hero-meta">
      <span class="meta-chip"><i class="fas fa-tag"></i> <?= esc($kategori) ?></span>
      <span class="meta-chip">
        <i class="fas fa-<?= $modePengumpulan === 'kelompok' ? 'users' : 'user' ?>"></i>
        <?= $modePengumpulan === 'kelompok' ? 'Kelompok' : 'Individu' ?>
      </span>
      <span class="status-pill <?= $statusInfo['class'] ?>"><?= $statusInfo['label'] ?></span>
    </div>
  </div>
</div>

<!-- TIME BOX -->
<div class="dt-time-box <?= $isLewatDeadline ? 'is-late' : '' ?>">
  <div class="dt-time-row">
    <i class="fas fa-calendar-plus"></i>
    <span><strong>Ditambahkan:</strong> <?= esc($tglDibuat) ?></span>
  </div>
  <div class="dt-time-row">
    <i class="fas fa-calendar-times"></i>
    <span><strong>Deadline:</strong> <?= esc($deadlineFmt) ?></span>
    <?php if ($isLewatDeadline): ?>
      <span class="overdue-tag"><i class="fas fa-exclamation-triangle"></i> Melewati deadline</span>
    <?php endif; ?>
  </div>
</div>

<!-- DESKRIPSI -->
<?php if (! empty($deskripsi) && $deskripsi !== '-'): ?>
  <div class="dt-desc-box">
    <p><?= nl2br(esc($deskripsi)) ?></p>
  </div>
<?php endif; ?>

<!-- TOMBOL PENGUMPULAN -->
<div class="dt-submit-bar">
  <?php if ($pengumpulan === null): ?>
    <div class="alert-not-assigned">
      <i class="fas fa-info-circle"></i>
      Tugas ini tidak ditugaskan kepada Anda.
    </div>
  <?php elseif ($canSubmit): ?>
    <button class="btn-submit-main" onclick="openSubmitModal()">
      <i class="fas fa-<?= $status === 'Revisi' ? 'redo' : 'plus-circle' ?>"></i>
      <?= $status === 'Revisi' ? 'Kirim Ulang (Revisi)' : 'Kumpulkan Tugas' ?>
    </button>
    <?php if ($status === 'Revisi'): ?>
      <span class="revisi-inline-hint">
        <i class="fas fa-info-circle"></i>
        <?= $revisiCount ?> jawaban perlu direvisi
      </span>
    <?php endif; ?>
  <?php else: ?>
    <button class="btn-submit-main is-locked" disabled>
      <i class="fas fa-lock"></i>
      <?= $status === 'Done' ? 'Tugas Sudah Selesai' : 'Sedang Diperiksa' ?>
    </button>
  <?php endif; ?>
</div>

<!-- STATUS TABLE -->
<h2 class="dt-section-heading">Status Pengumpulan</h2>
<div class="dt-status-table">

  <div class="dt-row">
    <div class="dt-row-label">Status</div>
    <div class="dt-row-value">
      <span class="chip chip-<?= match ($status) {
                                'Done'            => 'green',
                                'Revisi'          => 'yellow',
                                'Belum Diperiksa' => 'blue',
                                default           => 'gray',
                              } ?>">
        <i class="fas fa-<?= match ($status) {
                            'Done'            => 'check-circle',
                            'Revisi'          => 'redo',
                            'Belum Diperiksa' => 'clock',
                            default           => 'minus-circle',
                          } ?>"></i>
        <?= $statusInfo['label'] ?>
      </span>
    </div>
  </div>

  <div class="dt-row">
    <div class="dt-row-label">Waktu Pengumpulan</div>
    <div class="dt-row-value">
      <?php if ($tglKumpul): ?>
        <i class="fas fa-calendar-check" style="color:#20a8a8"></i>
        <?= esc(date('d M Y, H:i', strtotime($tglKumpul))) ?>
      <?php else: ?>
        <span class="text-muted">Belum dikumpulkan</span>
      <?php endif; ?>
    </div>
  </div>

  <div class="dt-row">
    <div class="dt-row-label">Jumlah Jawaban</div>
    <div class="dt-row-value">
      <span><?= count($items) ?> / <?= $targetJumlah ?> item</span>
    </div>
  </div>

  <div class="dt-row">
    <div class="dt-row-label">Sisa Waktu</div>
    <div class="dt-row-value <?= $isLewatDeadline ? 'text-danger' : '' ?>">
      <?php if ($isLewatDeadline): ?>
        <i class="fas fa-clock"></i> Sudah melewati deadline
      <?php elseif ($deadlineTs > 0): ?>
        <i class="fas fa-clock"></i> <span id="countdown">Menghitung...</span>
      <?php else: ?>
        <span class="text-muted">-</span>
      <?php endif; ?>
    </div>
  </div>

  <div class="dt-row last">
    <div class="dt-row-label">Terakhir Diubah</div>
    <div class="dt-row-value"><?= esc($tglDiubah) ?></div>
  </div>

</div>

<!-- JAWABAN SAYA -->
<?php if (! empty($items)): ?>
  <h2 class="dt-section-heading" style="margin-top:28px">Jawaban Saya</h2>
  <div class="items-list">
    <?php foreach ($items as $idx => $item): ?>
      <?php
      $itemStatusMap = [
        'Submit'        => ['class' => 'item-dikirim', 'label' => 'Terkirim',  'icon' => 'fa-paper-plane'],
        'Revisi'        => ['class' => 'item-revisi',  'label' => 'Revisi',    'icon' => 'fa-redo'],
        'Done'          => ['class' => 'item-done',    'label' => 'Diterima',  'icon' => 'fa-check-circle'],
        'Belum Dikirim' => ['class' => 'item-belum',   'label' => 'Draft',     'icon' => 'fa-file'],
      ];
      $iStatus = $itemStatusMap[$item['status']] ?? ['class' => 'item-belum', 'label' => $item['status'], 'icon' => 'fa-file'];
      $isTipe  = $item['tipe'] ?? 'text';
      ?>
      <div class="item-card <?= $item['status'] === 'Revisi' ? 'item-card-revisi' : '' ?>">
        <div class="item-header">
          <span class="item-num">Jawaban <?= $idx + 1 ?></span>
          <span class="item-tipe-badge tipe-<?= $isTipe ?>">
            <?php if ($isTipe === 'link'): ?>
              <i class="fas fa-link"></i> LINK
            <?php elseif ($isTipe === 'file'): ?>
              <i class="fas fa-file-alt"></i> FILE
            <?php else: ?>
              <i class="fas fa-align-left"></i> <?= strtoupper($isTipe) ?>
            <?php endif; ?>
          </span>
          <span class="item-status-badge <?= $iStatus['class'] ?>">
            <i class="fas <?= $iStatus['icon'] ?>"></i> <?= $iStatus['label'] ?>
          </span>
        </div>

        <div class="item-data">
          <?php if ($isTipe === 'link'): ?>
            <a href="<?= esc($item['data']) ?>" target="_blank" class="item-link">
              <i class="fas fa-external-link-alt"></i> <?= esc($item['data']) ?>
            </a>
          <?php elseif ($isTipe === 'file'): ?>
            <a href="<?= base_url('pkl/manajemen-tugas/file/' . urlencode($item['data'])) ?>"
              target="_blank" class="item-link">
              <i class="fas fa-download"></i> <?= esc($item['data']) ?>
            </a>
          <?php else: ?>
            <p><?= nl2br(esc($item['data'])) ?></p>
          <?php endif; ?>
        </div>

        <?php if (! empty($item['komentar'])): ?>
          <div class="item-komentar">
            <div class="komentar-label">
              <i class="fas fa-comment-dots"></i> Catatan dari Admin
            </div>
            <p><?= nl2br(esc($item['komentar'])) ?></p>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- FOOTER -->
<div class="dt-foot-actions">
  <a href="<?= base_url('pkl/manajemen-tugas') ?>" class="btn-foot btn-back">
    <i class="fas fa-arrow-left"></i> Kembali
  </a>
</div>


<!-- ================================================================
     MODAL PENGUMPULAN TUGAS
     [FIX BUG 1] Tipe item: 'file' | 'link'. Submit via FormData.
     [FIX BUG 2] Revisi: tampilkan hanya slot item yang perlu direvisi.
     ================================================================ -->
<?php if ($canSubmit): ?>
  <div class="m-overlay" id="submitModal">
    <div class="m-box">
      <div class="m-head">
        <div class="m-head-left">
          <div class="m-head-icon"><i class="fas fa-upload"></i></div>
          <span><?= $status === 'Revisi' ? 'Kirim Ulang Jawaban' : 'Kumpulkan Tugas' ?></span>
        </div>
        <button class="m-close" type="button" onclick="closeSubmitModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="m-body">

        <!-- Info tugas (readonly) -->
        <div class="m-field-row">
          <div class="m-field">
            <label>Judul Tugas</label>
            <input type="text" value="<?= esc($judul) ?>" readonly>
          </div>
          <div class="m-field">
            <label>Deadline</label>
            <input type="text" value="<?= esc($deadlineFmt) ?>" readonly>
          </div>
        </div>

        <!-- [FIX BUG 2] Revisi notice -->
        <?php if ($status === 'Revisi'): ?>
          <div class="revisi-notice">
            <i class="fas fa-redo"></i>
            <div>
              <strong>Ada <?= $revisiCount ?> jawaban yang perlu direvisi.</strong>
              Isi ulang hanya slot di bawah yang bertanda revisi.
              Jawaban yang sudah diterima admin tidak perlu dikirim ulang.
            </div>
          </div>
        <?php endif; ?>

        <!-- Format file yang diizinkan -->
        <div class="file-hint-bar">
          <i class="fas fa-info-circle"></i>
          Format file: <strong><?= $allowedExtList ?></strong> &nbsp;·&nbsp; Maks 300 MB
        </div>

        <!-- Slot jawaban -->
        <div id="slotsContainer">
          <?php for ($i = 0; $i < $slotsModal; $i++): ?>
            <?php
            // Jika revisi: ambil data item revisi ke-i untuk tampilkan konteks
            $prevItem = $itemsRevisi[$i] ?? null;
            ?>
            <div class="submit-slot" data-slot="<?= $i ?>">
              <div class="slot-header">
                <span class="slot-label">
                  Jawaban <?= $i + 1 ?>
                  <?php if ($status === 'Revisi'): ?>
                    <span class="slot-revisi-tag"><i class="fas fa-redo"></i> Revisi</span>
                  <?php endif; ?>
                </span>
                <!-- [FIX BUG 1] Opsi tipe: file / link -->
                <select class="slot-tipe" onchange="onTipeChange(this)">
                  <option value="file">File</option>
                  <option value="link">Link URL</option>
                </select>
              </div>

              <!-- [FIX BUG 2] Tampilkan konteks jawaban sebelumnya saat revisi -->
              <?php if ($prevItem): ?>
                <div class="slot-prev-info">
                  <div class="slot-prev-label">
                    <i class="fas fa-history"></i> Jawaban sebelumnya (<?= esc($prevItem['tipe']) ?>):
                  </div>
                  <div class="slot-prev-data">
                    <?php if ($prevItem['tipe'] === 'link'): ?>
                      <a href="<?= esc($prevItem['data']) ?>" target="_blank">
                        <i class="fas fa-external-link-alt"></i> <?= esc($prevItem['data']) ?>
                      </a>
                    <?php elseif ($prevItem['tipe'] === 'file'): ?>
                      <i class="fas fa-file-alt"></i> <?= esc($prevItem['data']) ?>
                    <?php else: ?>
                      <?= esc(mb_substr($prevItem['data'], 0, 120)) ?><?= mb_strlen($prevItem['data']) > 120 ? '...' : '' ?>
                    <?php endif; ?>
                  </div>
                  <?php if (! empty($prevItem['komentar'])): ?>
                    <div class="slot-prev-komentar">
                      <i class="fas fa-comment-dots"></i>
                      <strong>Catatan admin:</strong> <?= esc($prevItem['komentar']) ?>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

              <div class="slot-input-wrap">
                <!-- Input file (default tampil) -->
                <div class="slot-file-wrap">
                  <label class="slot-file-label">
                    <input type="file"
                      class="slot-file-input"
                      accept="<?= $acceptAttr ?>"
                      onchange="onFileChange(this)">
                    <span class="slot-file-ui">
                      <i class="fas fa-cloud-upload-alt"></i>
                      <span class="slot-file-text">Klik untuk memilih file</span>
                      <span class="slot-file-hint"><?= $allowedExtList ?> · maks 300 MB</span>
                    </span>
                  </label>
                  <div class="slot-file-selected" style="display:none"></div>
                </div>

                <!-- Input URL (tersembunyi, tampil saat pilih Link) -->
                <input type="url"
                  class="slot-url-input"
                  placeholder="https://..."
                  style="display:none">
              </div>
            </div>
          <?php endfor; ?>
        </div>

        <div class="m-actions">
          <button type="button" class="btn-foot btn-back" onclick="closeSubmitModal()">
            Batal
          </button>
          <button type="button" class="btn-foot btn-kirim" id="btnKirimTugas"
            onclick="submitTugas(<?= $tugasId ?>)">
            <i class="fas fa-paper-plane"></i> Kirim Tugas
          </button>
        </div>

      </div>
    </div>
  </div>
<?php endif; ?>


<!-- TOAST -->
<?php if (session()->getFlashdata('success')): ?>
  <div class="toast" id="toast">
    <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
  </div>
<?php endif; ?>

<?= $this->endSection() ?>


<?= $this->section('styles') ?>
<style>
  /* BREADCRUMB */
  .breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #94a3b8;
    margin-bottom: 22px;
    flex-wrap: wrap;
  }

  .breadcrumb a {
    color: #20a8a8;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .breadcrumb a:hover {
    text-decoration: underline;
  }

  .breadcrumb .sep {
    font-size: 10px;
  }

  /* HERO */
  .dt-hero {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 18px;
  }

  .dt-hero-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: linear-gradient(135deg, #20a8a8, #17c3b2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
  }

  .dt-hero-text h1 {
    font-size: 21px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 10px 0;
    line-height: 1.3;
  }

  .dt-hero-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .meta-chip {
    background: #f1f5f9;
    color: #64748b;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .status-pill {
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 12.5px;
    font-weight: 700;
  }

  .status-belum {
    background: #fff7ed;
    color: #c2410c;
  }

  .status-proses {
    background: #eff6ff;
    color: #1d4ed8;
  }

  .status-done {
    background: #f0fdf4;
    color: #15803d;
  }

  .status-revisi {
    background: #fef3c7;
    color: #92400e;
  }

  /* TIME BOX */
  .dt-time-box {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 20px;
    margin-bottom: 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .dt-time-box.is-late {
    background: #fff9f9;
    border-color: #fecaca;
  }

  .dt-time-row {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13.5px;
    color: #475569;
    flex-wrap: wrap;
  }

  .dt-time-row i {
    color: #94a3b8;
    width: 16px;
    text-align: center;
  }

  .dt-time-box.is-late .dt-time-row:last-child {
    color: #dc2626;
  }

  .dt-time-box.is-late .dt-time-row:last-child i {
    color: #dc2626;
  }

  .overdue-tag {
    background: #fef2f2;
    color: #dc2626;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  /* DESC */
  .dt-desc-box {
    background: #f8fafc;
    border-left: 4px solid #20a8a8;
    border-radius: 0 8px 8px 0;
    padding: 12px 16px;
    margin-bottom: 20px;
    font-size: 14px;
    color: #475569;
    line-height: 1.7;
  }

  /* NOT ASSIGNED ALERT */
  .alert-not-assigned {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fff7ed;
    border: 1.5px solid #fed7aa;
    border-radius: 10px;
    padding: 14px 18px;
    color: #c2410c;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 24px;
  }

  .alert-not-assigned i {
    font-size: 18px;
  }

  /* SUBMIT BAR */
  .dt-submit-bar {
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }

  .btn-submit-main {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 24px;
    background: #20a8a8;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.2s;
  }

  .btn-submit-main:hover {
    background: #17c3b2;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(32, 168, 168, 0.3);
  }

  .btn-submit-main.is-locked {
    background: #94a3b8;
    cursor: not-allowed;
    opacity: 0.8;
  }

  .btn-submit-main.is-locked:hover {
    transform: none;
    box-shadow: none;
  }

  .revisi-inline-hint {
    font-size: 13px;
    color: #92400e;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  /* SECTION HEADING */
  .dt-section-heading {
    font-size: 17px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 12px 0;
  }

  /* STATUS TABLE */
  .dt-status-table {
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 28px;
  }

  .dt-row {
    display: grid;
    grid-template-columns: 200px 1fr;
    border-bottom: 1px solid #f1f5f9;
  }

  .dt-row.last {
    border-bottom: none;
  }

  .dt-row-label {
    padding: 14px 18px;
    background: #f8fafc;
    font-size: 13.5px;
    font-weight: 600;
    color: #334155;
    display: flex;
    align-items: flex-start;
  }

  .dt-row-value {
    padding: 14px 18px;
    font-size: 14px;
    color: #475569;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
  }

  .chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12.5px;
    font-weight: 600;
  }

  .chip-green {
    background: #f0fdf4;
    color: #15803d;
  }

  .chip-yellow {
    background: #fef3c7;
    color: #92400e;
  }

  .chip-blue {
    background: #eff6ff;
    color: #1d4ed8;
  }

  .chip-gray {
    background: #f1f5f9;
    color: #64748b;
  }

  .text-danger {
    color: #dc2626;
    font-weight: 600;
    font-size: 13.5px;
  }

  .text-muted {
    color: #94a3b8;
  }

  /* ITEMS LIST */
  .items-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-bottom: 28px;
  }

  .item-card {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
  }

  .item-card-revisi {
    border-color: #fde68a;
    background: #fffef5;
  }

  .item-header {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    padding: 12px 18px;
    background: #f1f5f9;
    border-bottom: 1px solid #e2e8f0;
  }

  .item-card-revisi .item-header {
    background: #fef9e7;
  }

  .item-num {
    font-size: 13px;
    font-weight: 700;
    color: #334155;
  }

  .item-tipe-badge {
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .tipe-text {
    background: #e0f7f7;
    color: #0d5f5f;
  }

  .tipe-link {
    background: #d1fae5;
    color: #065f46;
  }

  .tipe-file {
    background: #dbeafe;
    color: #1e40af;
  }

  .item-status-badge {
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-left: auto;
  }

  .item-dikirim {
    background: #eff6ff;
    color: #1d4ed8;
  }

  .item-revisi {
    background: #fef3c7;
    color: #92400e;
  }

  .item-done {
    background: #f0fdf4;
    color: #15803d;
  }

  .item-belum {
    background: #f1f5f9;
    color: #64748b;
  }

  .item-data {
    padding: 14px 18px;
  }

  .item-data p {
    margin: 0;
    font-size: 14px;
    color: #334155;
    line-height: 1.7;
    word-break: break-word;
  }

  .item-link {
    color: #20a8a8;
    text-decoration: none;
    font-size: 13.5px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    word-break: break-all;
  }

  .item-link:hover {
    text-decoration: underline;
  }

  .item-komentar {
    margin: 0 18px 14px;
    padding: 12px 14px;
    background: #fffbeb;
    border: 1.5px solid #fde68a;
    border-radius: 8px;
  }

  .komentar-label {
    font-size: 11.5px;
    font-weight: 700;
    color: #92400e;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .item-komentar p {
    margin: 0;
    font-size: 13.5px;
    color: #78350f;
    line-height: 1.6;
  }

  /* FOOT ACTIONS */
  .dt-foot-actions {
    display: flex;
    gap: 10px;
  }

  .btn-foot {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: all 0.2s;
    font-family: inherit;
  }

  .btn-back {
    background: #f1f5f9;
    color: #334155;
    border: 1.5px solid #e2e8f0;
  }

  .btn-back:hover {
    background: #e2e8f0;
  }

  .btn-kirim {
    background: #1e293b;
    color: white;
  }

  .btn-kirim:hover {
    background: #334155;
  }

  /* MODAL */
  .m-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  .m-overlay.show {
    display: flex;
  }

  .m-box {
    background: white;
    width: 100%;
    max-width: 560px;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.18);
    animation: popUp 0.25s ease;
    max-height: 90vh;
    overflow-y: auto;
  }

  .m-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 22px;
    background: linear-gradient(135deg, #20a8a8, #17c3b2);
    color: white;
  }

  .m-head-left {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    font-weight: 700;
  }

  .m-head-icon {
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
  }

  .m-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 7px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    transition: background 0.2s;
  }

  .m-close:hover {
    background: rgba(255, 255, 255, 0.35);
  }

  .m-body {
    padding: 22px 24px;
  }

  .m-field {
    margin-bottom: 14px;
  }

  .m-field-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
  }

  .m-field label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
  }

  .m-field input[type="text"] {
    width: 100%;
    padding: 9px 13px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13.5px;
    font-family: inherit;
    color: #334155;
    outline: none;
    box-sizing: border-box;
  }

  .m-field input[readonly] {
    background: #f8fafc;
    color: #64748b;
  }

  /* Revisi notice */
  .revisi-notice {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    background: #fffbeb;
    border: 1.5px solid #fde68a;
    border-radius: 8px;
    padding: 13px 14px;
    font-size: 13px;
    color: #92400e;
    margin-bottom: 14px;
  }

  .revisi-notice i {
    flex-shrink: 0;
    margin-top: 2px;
    font-size: 15px;
  }

  /* File hint bar */
  .file-hint-bar {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 8px;
    padding: 8px 13px;
    font-size: 12px;
    color: #0369a1;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  /* Slot */
  .submit-slot {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    margin-bottom: 12px;
    overflow: hidden;
  }

  .slot-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    background: #f1f5f9;
    border-bottom: 1px solid #e2e8f0;
  }

  .slot-label {
    font-size: 13px;
    font-weight: 700;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .slot-revisi-tag {
    background: #fef3c7;
    color: #92400e;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 3px;
  }

  .slot-tipe {
    padding: 5px 10px;
    border: 1.5px solid #e2e8f0;
    border-radius: 7px;
    font-size: 12.5px;
    font-family: inherit;
    color: #334155;
    background: white;
    cursor: pointer;
    outline: none;
  }

  .slot-tipe:focus {
    border-color: #20a8a8;
  }

  /* Konteks jawaban sebelumnya (revisi) */
  .slot-prev-info {
    margin: 10px 14px 0;
    padding: 10px 12px;
    background: #f0fdf4;
    border: 1px solid #a7f3d0;
    border-radius: 8px;
    font-size: 12.5px;
    color: #065f46;
  }

  .slot-prev-label {
    font-weight: 700;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .slot-prev-data {
    word-break: break-all;
    margin-bottom: 4px;
  }

  .slot-prev-data a {
    color: #059669;
  }

  .slot-prev-komentar {
    margin-top: 6px;
    padding-top: 6px;
    border-top: 1px solid #a7f3d0;
    color: #92400e;
    font-size: 12px;
    display: flex;
    align-items: flex-start;
    gap: 5px;
  }

  /* Slot input area */
  .slot-input-wrap {
    padding: 12px 14px;
  }

  /* File upload */
  .slot-file-label {
    display: block;
    cursor: pointer;
  }

  .slot-file-label input[type="file"] {
    display: none;
  }

  .slot-file-ui {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 20px 16px;
    border: 2px dashed #cbd5e1;
    border-radius: 8px;
    background: white;
    text-align: center;
    transition: all 0.2s;
    color: #64748b;
  }

  .slot-file-ui:hover {
    border-color: #20a8a8;
    color: #20a8a8;
    background: #f0fdfa;
  }

  .slot-file-ui i {
    font-size: 22px;
    color: #94a3b8;
  }

  .slot-file-ui:hover i {
    color: #20a8a8;
  }

  .slot-file-text {
    font-size: 13px;
    font-weight: 600;
  }

  .slot-file-hint {
    font-size: 11px;
    color: #94a3b8;
  }

  .slot-file-selected {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    background: #f0fdf4;
    border: 1.5px solid #a7f3d0;
    border-radius: 8px;
    font-size: 13px;
    color: #15803d;
  }

  .slot-file-selected .file-name {
    font-weight: 600;
    word-break: break-all;
    flex: 1;
  }

  .slot-file-selected .file-size {
    font-size: 11.5px;
    color: #047857;
    white-space: nowrap;
  }

  .slot-file-remove {
    background: none;
    border: none;
    color: #dc2626;
    cursor: pointer;
    padding: 2px 5px;
    border-radius: 4px;
  }

  .slot-file-remove:hover {
    background: #fef2f2;
  }

  /* Link input */
  .slot-url-input {
    width: 100%;
    padding: 9px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13.5px;
    font-family: inherit;
    color: #334155;
    outline: none;
    box-sizing: border-box;
    transition: border-color 0.2s;
  }

  .slot-url-input:focus {
    border-color: #20a8a8;
  }

  .m-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
  }

  /* TOAST */
  .toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: #15803d;
    color: white;
    padding: 13px 18px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    z-index: 9999;
    animation: popUp 0.3s ease;
  }

  @keyframes popUp {
    from {
      opacity: 0;
      transform: translateY(10px)
    }

    to {
      opacity: 1;
      transform: translateY(0)
    }
  }

  @media (max-width: 640px) {
    .dt-row {
      grid-template-columns: 1fr;
    }

    .dt-row-label {
      padding: 10px 16px;
      font-size: 12px;
      border-bottom: 1px solid #f1f5f9;
    }

    .dt-row-value {
      padding: 10px 16px;
    }

    .m-field-row {
      grid-template-columns: 1fr;
    }

    .dt-hero-text h1 {
      font-size: 17px;
    }

    .dt-foot-actions {
      flex-direction: column;
    }

    .dt-submit-bar {
      flex-direction: column;
      align-items: flex-start;
    }
  }
</style>
<?= $this->endSection() ?>


<?= $this->section('javascript') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // ── CSRF ─────────────────────────────────────────────────────────
  const csrfToken = '<?= csrf_hash() ?>';
  const csrfHeader = '<?= csrf_header() ?>';

  // ── URL ───────────────────────────────────────────────────────────
  const SUBMIT_URL = '<?= base_url('pkl/manajemen-tugas/submit/' . $tugasId) ?>';

  // ── Modal ──────────────────────────────────────────────────────────
  function openSubmitModal() {
    document.getElementById('submitModal')?.classList.add('show');
    // Init drag-drop setiap kali modal dibuka (slot sudah di-render PHP)
    setTimeout(initDragDrop, 60);
  }

  function closeSubmitModal() {
    document.getElementById('submitModal')?.classList.remove('show');
  }

  document.querySelectorAll('.m-overlay').forEach(m => {
    m.addEventListener('click', e => {
      if (e.target === m) m.classList.remove('show');
    });
  });

  // ── Slot tipe toggle (File ↔ Link) ─────────────────────────────────
  function onTipeChange(sel) {
    const slot = sel.closest('.submit-slot');
    const fileWrap = slot.querySelector('.slot-file-wrap');
    const urlInp = slot.querySelector('.slot-url-input');

    if (sel.value === 'link') {
      fileWrap.style.display = 'none';
      urlInp.style.display = 'block';
      urlInp.focus();
    } else {
      fileWrap.style.display = 'block';
      urlInp.style.display = 'none';
    }
  }

  // ── File selection display (dipanggil dari onchange & drag-drop) ───
  function onFileChange(input) {
    const slot = input.closest('.submit-slot');
    const fileUI = slot.querySelector('.slot-file-ui');
    const selected = slot.querySelector('.slot-file-selected');
    const file = input.files[0];
    if (!file) return;

    const sizeMB = (file.size / 1024 / 1024).toFixed(2);
    fileUI.style.display = 'none';
    selected.style.display = 'flex';
    selected.innerHTML = `
      <i class="fas fa-file-alt"></i>
      <span class="file-name">${escHtml(file.name)}</span>
      <span class="file-size">${sizeMB} MB</span>
      <button class="slot-file-remove" type="button" onclick="clearFile(this)" title="Hapus">
        <i class="fas fa-times"></i>
      </button>`;
  }

  function clearFile(btn) {
    const slot = btn.closest('.submit-slot');
    const fileUI = slot.querySelector('.slot-file-ui');
    const selected = slot.querySelector('.slot-file-selected');
    const input = slot.querySelector('.slot-file-input');
    input.value = '';
    fileUI.style.display = 'block';
    selected.style.display = 'none';
    selected.innerHTML = '';
    fileUI.classList.remove('drag-over');
    fileUI.style.borderColor = '';
  }

  function escHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  // ── Drag & Drop ────────────────────────────────────────────────────
  function initDragDrop() {
    document.querySelectorAll('.submit-slot').forEach(slot => {
      const fileUI = slot.querySelector('.slot-file-ui');
      const input = slot.querySelector('.slot-file-input');
      if (!fileUI || !input || fileUI._ddInited) return;
      fileUI._ddInited = true; // hindari double-bind

      fileUI.addEventListener('dragenter', e => {
        e.preventDefault();
        fileUI.classList.add('drag-over');
      });
      fileUI.addEventListener('dragover', e => {
        e.preventDefault(); // wajib supaya drop diizinkan
        fileUI.classList.add('drag-over');
      });
      fileUI.addEventListener('dragleave', e => {
        if (!fileUI.contains(e.relatedTarget)) {
          fileUI.classList.remove('drag-over');
        }
      });
      fileUI.addEventListener('drop', e => {
        e.preventDefault();
        fileUI.classList.remove('drag-over');
        const file = e.dataTransfer?.files?.[0];
        if (!file) return;

        try {
          // DataTransfer API — assign file ke hidden input
          const dt = new DataTransfer();
          dt.items.add(file);
          input.files = dt.files;
          onFileChange(input);
        } catch (_) {
          fileUI.style.borderColor = '#ef4444';
          const hint = fileUI.querySelector('.slot-file-hint');
          if (hint) hint.textContent = 'Drag & drop tidak didukung browser ini, gunakan tombol pilih.';
          setTimeout(() => {
            fileUI.style.borderColor = '';
            if (hint) hint.textContent = '<?= $allowedExtList ?> · maks 300 MB';
          }, 3000);
        }
      });
    });
  }

  // ── Submit via FormData ────────────────────────────────────────────
  // [FIX KEY NAMING] Flat keys: slot_count / slot_N_tipe / slot_N_data / file_N
  // Bracket notation items[N][file] TIDAK kompatibel dengan CI4 getFile()
  // karena PHP menyusun $_FILES bracket dalam struktur multidimensi yang berbeda.
  // Flat key 'file_0', 'file_1' dst dijamin dibaca CI4 getFile('file_0') dengan benar.
  async function submitTugas(tugasId) {
    const slots = document.querySelectorAll('.submit-slot');
    const formData = new FormData();
    let valid = true;

    // Kirim jumlah slot agar PHP tahu berapa iterasi yang dilakukan
    formData.append('slot_count', slots.length);

    slots.forEach((slot, idx) => {
      const tipe = slot.querySelector('.slot-tipe').value;
      formData.append(`slot_${idx}_tipe`, tipe);

      if (tipe === 'link') {
        const urlInput = slot.querySelector('.slot-url-input');
        const url = (urlInput?.value ?? '').trim();
        if (!url) {
          valid = false;
          if (urlInput) urlInput.style.borderColor = '#ef4444';
          return;
        }
        if (urlInput) urlInput.style.borderColor = '';
        formData.append(`slot_${idx}_data`, url);

      } else {
        // tipe = file — gunakan flat key 'file_N' (BUKAN items[N][file])
        const fileInput = slot.querySelector('.slot-file-input');
        const fileUI = slot.querySelector('.slot-file-ui');

        if (!fileInput?.files?.[0]) {
          valid = false;
          // Kembalikan fileUI ke visible (mungkin sudah hidden) supaya border error kelihatan
          if (fileUI) {
            fileUI.style.display = 'block';
            fileUI.style.borderColor = '#ef4444';
          }
          return;
        }
        if (fileUI) {
          fileUI.style.display = 'none';
          fileUI.style.borderColor = '';
        }
        formData.append(`file_${idx}`, fileInput.files[0]); // flat key, aman untuk CI4
      }
    });

    if (!valid) {
      Swal.fire({
        icon: 'warning',
        title: 'Belum Lengkap',
        text: 'Mohon lengkapi semua slot jawaban sebelum mengirim.',
        confirmButtonColor: '#20a8a8',
      });
      return;
    }

    const btn = document.getElementById('btnKirimTugas');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
    }

    try {
      const res = await fetch(SUBMIT_URL, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          [csrfHeader]: csrfToken,
          // JANGAN set Content-Type — biarkan browser set sendiri beserta boundary multipart
        },
        body: formData,
      });

      const data = await res.json();

      if (data.success) {
        closeSubmitModal();
        await Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: data.message || 'Tugas berhasil dikumpulkan.',
          timer: 1800,
          showConfirmButton: false,
        });
        window.location.reload();
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: data.message || 'Terjadi kesalahan saat mengirim.',
          confirmButtonColor: '#20a8a8',
        });
      }
    } catch (_) {
      Swal.fire({
        icon: 'error',
        title: 'Kesalahan Jaringan',
        text: 'Tidak dapat terhubung ke server. Coba lagi.',
        confirmButtonColor: '#20a8a8',
      });
    } finally {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Tugas';
      }
    }
  }

  // ── Countdown deadline ────────────────────────────────────────────
  (function() {
    const deadlineTs = <?= $deadlineTs ?>;
    const el = document.getElementById('countdown');
    if (!deadlineTs || !el) return;
    const target = new Date(deadlineTs);

    function tick() {
      const diff = target - Date.now();
      if (diff <= 0) {
        el.textContent = 'Sudah melewati deadline';
        return;
      }
      const d = Math.floor(diff / 86400000);
      const h = Math.floor((diff % 86400000) / 3600000);
      const mn = Math.floor((diff % 3600000) / 60000);
      el.textContent = (d ? d + ' hari ' : '') + h + ' jam ' + mn + ' menit lagi';
      setTimeout(tick, 60000);
    }
    tick();
  })();

  // ── Auto hide toast ───────────────────────────────────────────────
  setTimeout(() => {
    const t = document.getElementById('toast');
    if (t) {
      t.style.transition = 'opacity .5s';
      t.style.opacity = '0';
      setTimeout(() => t.remove(), 500);
    }
  }, 3000);
</script>
<?= $this->endSection() ?>