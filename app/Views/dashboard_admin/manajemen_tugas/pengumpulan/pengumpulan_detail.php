<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/modules/pengumpulan.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
function statusBadgeClass(string $status): string
{
    return match ($status) {
        'Done'            => 'badge-success',
        'Revisi'          => 'badge-warning',
        'Belum Diperiksa' => 'badge-info',
        'Submit'          => 'badge-info',
        default           => 'badge-gray',
    };
}

function fileIconData(string $path): array
{
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    return match (true) {
        in_array($ext, ['pdf'])          => ['fa-file-pdf',        'pdf'],
        in_array($ext, ['doc', 'docx'])  => ['fa-file-word',       'docx'],
        in_array($ext, ['ppt', 'pptx'])  => ['fa-file-powerpoint', 'pptx'],
        in_array($ext, ['xls', 'xlsx'])  => ['fa-file-excel',      'xlsx'],
        in_array($ext, ['zip', 'rar'])   => ['fa-file-zipper',     'zip'],
        default                          => ['fa-file',            'file'],
    };
}

$p    = $pengumpulan;
$type = $p['type'] ?? 'mandiri';
?>

<div class="pengumpulan-detail-container">

    <!-- ── BACK BUTTON ── -->
    <div class="detail-back-bar">
        <button class="btn-back-detail"
            onclick="window.location.href='<?= base_url('dashboard/manajemen-tugas/pengumpulan') ?>'">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Pengumpulan
        </button>
    </div>

    <!-- ── INFO CARD ── -->
    <div class="detail-info-card">

        <div class="detail-info-header">
            <div class="detail-info-title">
                <?php if ($type === 'mandiri'): ?>
                    <i class="fas fa-user"></i> Informasi Pengumpulan — Mandiri
                <?php elseif ($type === 'kelompok'): ?>
                    <i class="fas fa-users"></i> Informasi Pengumpulan — Kelompok PKL
                <?php else: ?>
                    <i class="fas fa-user-friends"></i> Informasi Pengumpulan — Tim
                <?php endif; ?>
            </div>
            <span class="badge <?= statusBadgeClass($p['status']) ?> badge-lg" id="overallStatusBadge">
                <?= esc($p['status']) ?>
            </span>
        </div>

        <div class="detail-info-grid">

            <!-- Identitas (berbeda per tipe) -->
            <div class="detail-info-item">
                <div class="detail-info-label">
                    <?php if ($type === 'mandiri'): ?>
                        <i class="fas fa-user"></i> Pengirim
                    <?php elseif ($type === 'kelompok'): ?>
                        <i class="fas fa-users"></i> Nama Kelompok PKL
                    <?php else: ?>
                        <i class="fas fa-user-friends"></i> Nama Tim
                    <?php endif; ?>
                </div>
                <div class="detail-info-value">
                    <?php if ($type === 'mandiri'): ?>
                        <?= esc($p['nama_pengirim']) ?>
                    <?php elseif ($type === 'kelompok'): ?>
                        <?= esc($p['nama_kelompok']) ?>
                    <?php else: ?>
                        <?= esc($p['nama_tim']) ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Kategori Tugas -->
            <div class="detail-info-item">
                <div class="detail-info-label">
                    <i class="fas fa-tags"></i> Kategori Tugas
                </div>
                <div class="detail-info-value"><?= esc($p['kategori_tugas']) ?></div>
            </div>

            <!-- Nama Tugas -->
            <div class="detail-info-item">
                <div class="detail-info-label">
                    <i class="fas fa-tasks"></i> Nama Tugas
                </div>
                <div class="detail-info-value"><?= esc($p['nama_tugas']) ?></div>
            </div>

            <!-- Deadline -->
            <div class="detail-info-item">
                <div class="detail-info-label">
                    <i class="fas fa-calendar-check"></i> Deadline
                </div>
                <div class="detail-info-value"><?= esc($p['deadline']) ?></div>
            </div>

            <!-- Tanggal Dikirim -->
            <div class="detail-info-item">
                <div class="detail-info-label">
                    <i class="fas fa-paper-plane"></i> Tanggal Dikirim
                </div>
                <div class="detail-info-value">
                    <?= $p['waktu_pengumpulan']
                        ? esc($p['waktu_pengumpulan'])
                        : '<span class="text-muted">Belum dikirim</span>' ?>
                </div>
            </div>

            <!-- Deskripsi Tugas -->
            <div class="detail-info-item full-width">
                <div class="detail-info-label">
                    <i class="fas fa-align-left"></i> Deskripsi Tugas
                </div>
                <div class="detail-info-value detail-deskripsi">
                    <?= nl2br(esc($p['deskripsi_tugas'])) ?>
                </div>
            </div>

        </div>
    </div><!-- /.detail-info-card -->


    <!-- ── ANGGOTA CARD (kelompok / tim saja) ── -->
    <?php if ($type !== 'mandiri' && !empty($p['anggota'])): ?>
        <div class="detail-anggota-card">

            <div class="detail-anggota-header">
                <div class="detail-anggota-title">
                    <?php if ($type === 'kelompok'): ?>
                        <i class="fas fa-users"></i> Anggota Kelompok PKL
                    <?php else: ?>
                        <i class="fas fa-user-friends"></i> Anggota Tim
                    <?php endif; ?>
                    <span class="items-count-badge"><?= count($p['anggota']) ?> Orang</span>
                </div>
            </div>

            <div class="anggota-list">
                <?php foreach ($p['anggota'] as $idx => $nama): ?>
                    <div class="anggota-item">
                        <div class="anggota-number"><?= $idx + 1 ?></div>
                        <div class="anggota-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="anggota-nama"><?= esc($nama) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    <?php endif; ?>


    <!-- ── ITEMS SECTION ── -->
    <div class="detail-items-section">

        <div class="detail-items-header">
            <div class="detail-items-title">
                <i class="fas fa-folder-open"></i>
                Hasil Tugas
                <span class="items-count-badge"><?= count($p['items']) ?> Item</span>
            </div>
        </div>

        <div class="items-list" id="itemsList">

            <?php foreach ($p['items'] as $idx => $item): ?>

                <?php
                $itemNum         = $idx + 1;
                $itemLabel       = 'Item ' . $itemNum;
                $itemStatusClass = statusBadgeClass($item['status']);
                $canReview       = $item['status'] === 'Submit';
                $hasContent      = !empty($item['path']);
                $isLink          = $item['tipe'] === 'link';
                [$fileIcon, $fileIconClass] = ($hasContent && !$isLink)
                    ? fileIconData($item['path'])
                    : ['fa-file', 'file'];
                ?>

                <div class="item-card" id="itemCard_<?= $item['id'] ?>">

                    <!-- Item Header -->
                    <div class="item-card-header">
                        <div class="item-number-badge"><?= $itemNum ?></div>
                        <div class="item-name"><?= $itemLabel ?></div>
                        <span class="badge <?= $itemStatusClass ?> item-status-badge"
                            id="itemStatus_<?= $item['id'] ?>">
                            <?= esc($item['status']) ?>
                        </span>
                    </div>

                    <!-- Item Body -->
                    <div class="item-card-body">

                        <!-- Konten: Link atau File -->
                        <?php if ($hasContent): ?>
                            <div class="item-file-display">
                                <?php if ($isLink): ?>
                                    <div class="item-file-icon link-icon">
                                        <i class="fas fa-link"></i>
                                    </div>
                                    <div class="item-file-info">
                                        <div class="item-file-name">Link Eksternal</div>
                                        <div class="item-file-path"><?= esc($item['path']) ?></div>
                                    </div>
                                    <a href="<?= esc($item['path']) ?>"
                                        target="_blank"
                                        class="btn-item-action btn-open-link">
                                        <i class="fas fa-external-link-alt"></i>
                                        Buka
                                    </a>
                                <?php else: ?>
                                    <div class="item-file-icon <?= $fileIconClass ?>-icon">
                                        <i class="fas <?= $fileIcon ?>"></i>
                                    </div>
                                    <div class="item-file-info">
                                        <div class="item-file-name"><?= esc($item['path']) ?></div>
                                        <div class="item-file-path">
                                            <?= strtoupper(pathinfo($item['path'], PATHINFO_EXTENSION)) ?> File
                                        </div>
                                    </div>
                                    <a href="<?= base_url('dashboard/manajemen-tugas/pengumpulan/file/' . urlencode($item['path'])) ?>"
                                        target="_blank"
                                        class="btn-item-action btn-open-link">
                                        <i class="fas fa-eye"></i>
                                        Lihat
                                    </a>
                                    <a href="<?= base_url('dashboard/manajemen-tugas/pengumpulan/file/' . urlencode($item['path'])) ?>"
                                        download
                                        class="btn-item-action btn-download">
                                        <i class="fas fa-download"></i>
                                        Unduh
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="item-no-file">
                                <i class="fas fa-inbox"></i>
                                <span>Belum ada <?= $isLink ? 'link' : 'file' ?> yang dikumpulkan</span>
                            </div>
                        <?php endif; ?>

                        <!-- Komentar / Catatan Revisi -->
                        <?php if (!empty($item['komentar'])): ?>
                            <div class="item-komentar" id="itemKomentar_<?= $item['id'] ?>">
                                <div class="item-komentar-label">
                                    <i class="fas fa-comment-dots"></i>
                                    Catatan Revisi
                                </div>
                                <div class="item-komentar-text"><?= esc($item['komentar']) ?></div>
                            </div>
                        <?php else: ?>
                            <div class="item-komentar" id="itemKomentar_<?= $item['id'] ?>" style="display:none;">
                                <div class="item-komentar-label">
                                    <i class="fas fa-comment-dots"></i>
                                    Catatan Revisi
                                </div>
                                <div class="item-komentar-text" id="itemKomentarText_<?= $item['id'] ?>"></div>
                            </div>
                        <?php endif; ?>

                        <!-- Review Actions (hanya jika status Submit) -->
                        <div class="item-actions" id="itemActions_<?= $item['id'] ?>"
                            <?= $canReview ? '' : 'style="display:none;"' ?>>
                            <button class="btn-setujui"
                                onclick="setujuiItem(<?= $item['id'] ?>)">
                                <i class="fas fa-check"></i>
                                Setujui
                            </button>
                            <button class="btn-revisi-req"
                                onclick="showRevisiForm(<?= $item['id'] ?>)">
                                <i class="fas fa-undo"></i>
                                Minta Revisi
                            </button>
                        </div>

                        <!-- Form Revisi Inline -->
                        <div class="revisi-form" id="revisiForm_<?= $item['id'] ?>" style="display:none;">
                            <div class="revisi-form-label">
                                <i class="fas fa-comment-alt"></i>
                                Komentar untuk PKL
                            </div>
                            <textarea id="revisiKomentar_<?= $item['id'] ?>"
                                class="revisi-textarea"
                                placeholder="Tuliskan catatan atau perbaikan yang perlu dilakukan..."
                                rows="3"></textarea>
                            <div class="revisi-form-actions">
                                <button class="btn-revisi-submit"
                                    onclick="submitRevisi(<?= $item['id'] ?>)">
                                    <i class="fas fa-paper-plane"></i>
                                    Kirim Revisi
                                </button>
                                <button class="btn-revisi-cancel"
                                    onclick="hideRevisiForm(<?= $item['id'] ?>)">
                                    <i class="fas fa-times"></i>
                                    Batal
                                </button>
                            </div>
                        </div>

                    </div><!-- /.item-card-body -->
                </div><!-- /.item-card -->

            <?php endforeach; ?>

        </div><!-- /#itemsList -->
    </div><!-- /.detail-items-section -->

</div><!-- /.pengumpulan-detail-container -->

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="<?= base_url('assets/js/modules/pengumpulan.js') ?>"></script>
<script>
    const REVIEW_URL = '<?= base_url('dashboard/manajemen-tugas/pengumpulan/review-item') ?>';
    const CSRF_NAME = '<?= csrf_token() ?>';
    const CSRF_HASH = '<?= csrf_hash() ?>';
    const BACK_URL = '<?= base_url('dashboard/manajemen-tugas/pengumpulan') ?>';

    function submitReviewToServer(itemId, aksi, komentar = '') {
        const payload = {
            aksi,
            komentar
        };
        payload[CSRF_NAME] = CSRF_HASH;
        return fetch(`${REVIEW_URL}/${itemId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        }).then(r => r.json());
    }

    $(document).ready(function() {
        initDetailReview(submitReviewToServer, BACK_URL);
    });
</script>
<?= $this->endSection() ?>