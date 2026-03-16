<!-- =====================================================
     PARTIAL VIEW: hasil_tugas_kelompok.php
     Tab Kelompok PKL — Diload via AJAX
     CSS → pengumpulan.css | JS → pengumpulan.js
     ===================================================== -->

<!-- Action Bar -->
<div class="section-buttons">
    <button class="btn-custom btn-filter-toggle" id="btnFilterKelompok">
        <i class="fas fa-filter"></i>
        <span class="btn-text">Filter</span>
    </button>
</div>

<!-- Filter Section -->
<div class="filter-section" id="filterSectionKelompok">
    <div class="filter-header">
        <h5 class="filter-title">
            <i class="fas fa-filter"></i>
            Filter Pengumpulan Kelompok
        </h5>
        <button class="btn-reset" id="btnResetFilterKelompok">
            <i class="fas fa-redo"></i>
            Reset
        </button>
    </div>

    <div class="filter-row-half">
        <!-- Cari Nama Kelompok -->
        <div class="filter-group">
            <label>
                <i class="fas fa-search"></i>
                Nama Kelompok PKL
            </label>
            <input type="text"
                   id="filterNamaKelompok"
                   placeholder="Ketik nama kelompok..."
                   class="filter-input">
        </div>

        <!-- Filter Deadline -->
        <div class="filter-group">
            <label>
                <i class="fas fa-calendar-alt"></i>
                Deadline
            </label>
            <input type="text"
                   id="filterDeadlineKelompok"
                   placeholder="dd-mm-yyyy"
                   class="filter-input flatpickr-kelompok"
                   readonly>
        </div>
    </div>
</div>

<!-- Table -->
<div class="table-container">
    <table id="tableKelompok" class="table table-hover" style="width: 100%;">
        <thead>
            <tr>
                <th style="width:  5%;">No</th>
                <th style="width: 18%;">Nama Kelompok PKL</th>
                <th style="width: 20%;">Nama Tugas</th>
                <th style="width: 16%;">Waktu Pengumpulan</th>
                <th style="width: 16%;">Deadline</th>
                <th style="width: 16%;">Status</th>
                <th style="width:  9%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pengumpulan_list as $index => $item): ?>
                <?php
                    $statusClass = match($item['status']) {
                        'Done'            => 'badge-success',
                        'Revisi'          => 'badge-warning',
                        'Belum Diperiksa' => 'badge-info',
                        default           => 'badge-gray',
                    };
                ?>
                <tr class="pengumpulan-row">
                    <td><?= $index + 1 ?></td>
                    <td class="td-nama" style="text-align: left; padding-left: 20px;">
                        <?= esc($item['nama_kelompok']) ?>
                    </td>
                    <td><?= esc($item['nama_tugas']) ?></td>
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
