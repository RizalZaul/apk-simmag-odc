<!-- =====================================================
     PARTIAL VIEW: hasil_tugas.php
     Tab Mandiri — Diload via AJAX oleh loadPengumpulanTab('mandiri')
     Tidak ada <style> atau <script> di sini —
     CSS → pengumpulan.css | JS → pengumpulan.js
     ===================================================== -->

<!-- Action Bar -->
<div class="section-buttons">
    <button class="btn-custom btn-filter-toggle" id="btnFilterMandiri">
        <i class="fas fa-filter"></i>
        <span class="btn-text">Filter</span>
    </button>
</div>

<!-- Filter Section (hidden by default) -->
<div class="filter-section" id="filterSectionMandiri">
    <div class="filter-header">
        <h5 class="filter-title">
            <i class="fas fa-filter"></i>
            Filter Pengumpulan Mandiri
        </h5>
        <button class="btn-reset" id="btnResetFilterMandiri">
            <i class="fas fa-redo"></i>
            Reset
        </button>
    </div>

    <div class="filter-row-half">
        <!-- Cari Nama PKL -->
        <div class="filter-group">
            <label>
                <i class="fas fa-search"></i>
                Nama PKL
            </label>
            <input type="text"
                id="filterNamaMandiri"
                placeholder="Ketik nama PKL..."
                class="filter-input">
        </div>

        <!-- Filter Deadline -->
        <div class="filter-group">
            <label>
                <i class="fas fa-calendar-alt"></i>
                Deadline
            </label>
            <input type="text"
                id="filterDeadlineMandiri"
                placeholder="dd-mm-yyyy"
                class="filter-input flatpickr-mandiri"
                readonly>
        </div>
    </div>
</div>

<!-- Table -->
<div class="table-container">
    <table id="tableMandiri" class="table table-hover" style="width: 100%;">
        <thead>
            <tr>
                <th style="width:  5%;">No</th>
                <th style="width: 20%;">Nama Lengkap</th>
                <th style="width: 20%;">Nama Tugas</th>
                <th style="width: 16%;">Waktu Pengumpulan</th>
                <th style="width: 16%;">Deadline</th>
                <th style="width: 16%;">Status</th>
                <th style="width: 7%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pengumpulan_list as $index => $item): ?>
                <?php
                $statusClass = match ($item['status']) {
                    'Done'            => 'badge-success',
                    'Revisi'          => 'badge-warning',
                    'Belum Diperiksa' => 'badge-info',
                    default           => 'badge-gray',   // Belum Dikirim
                };
                ?>
                <tr class="pengumpulan-row">
                    <td><?= $index + 1 ?></td>
                    <td class="td-nama" style="text-align: left; padding-left: 20px;">
                        <?= esc($item['nama_pengirim']) ?>
                    </td>
                    <td class="td-waktu">
                        <?php if ($item['nama_tugas']): ?>
                            <?= esc($item['nama_tugas']) ?>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="td-waktu">
                        <?php if ($item['waktu_pengumpulan']): ?>
                            <?= esc($item['waktu_pengumpulan']) ?>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="td-deadline"><?= esc($item['deadline']) ?></td>
                    <td>
                        <span class="badge <?= $statusClass ?>">
                            <?= esc($item['status']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="aksi">
                            <button class="btn-action btn-edit btn-detail-pengumpulan"
                                data-url="<?= base_url('dashboard/manajemen-tugas/pengumpulan/detail/' . $item['id']) ?>"
                                title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>