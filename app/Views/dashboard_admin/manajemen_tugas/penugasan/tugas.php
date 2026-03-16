<!-- Action Buttons -->
<div class="section-buttons">
    <button class="btn-custom btn-add" id="btnTambahTugas" onclick="tambahTugas()" title="Tambah Tugas Baru">
        <i class="fas fa-plus"></i>
        <span class="btn-text">Tambah</span>
    </button>
    <button class="btn-custom btn-filter-toggle" id="btnFilterTugas">
        <i class="fas fa-filter"></i>
        <span class="btn-text">Filter</span>
    </button>
</div>

<!-- Filter Section -->
<div class="filter-section" id="filterSectionTugas">
    <div class="filter-header">
        <h5 class="filter-title">
            <i class="fas fa-filter"></i>
            Filter Data Tugas
        </h5>
        <button class="btn-reset" id="btnResetFilterTugas">
            <i class="fas fa-redo"></i> Reset
        </button>
    </div>

    <div class="filter-row-full">
        <div class="filter-group">
            <label><i class="fas fa-search"></i> Cari Nama Tugas</label>
            <input type="text" id="filterNamaTugas" placeholder="Ketik nama tugas...">
        </div>
    </div>

    <div class="filter-row-half">
        <div class="filter-group">
            <label><i class="fas fa-tags"></i> Kategori Tugas</label>
            <select id="filterKategoriTugas" style="width:100%;">
                <option value="">Semua Kategori</option>
                <?php
                $kategoriUnik = array_unique(array_column($tugas_list, 'kategori_tugas'));
                sort($kategoriUnik);
                foreach ($kategoriUnik as $kat):
                ?>
                    <option value="<?= esc($kat) ?>"><?= esc($kat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label><i class="fas fa-calendar-times"></i> Deadline</label>
            <input type="text" id="filterDeadlineTugas" placeholder="Pilih tanggal deadline" class="datepicker">
        </div>
    </div>
</div>

<!-- Table -->
<div class="table-container">
    <table id="tableTugas" class="table table-hover" style="width:100%;">
        <thead>
            <tr>
                <th style="width: 4%;">NO</th>
                <th style="width:12%;">Editor</th>
                <th style="width:28%;">Nama Tugas</th>
                <th style="width:13%;">Kategori Tugas</th>
                <th style="width:13%;">Mode Pengumpulan</th>
                <th style="width:12%;">Deadline</th>
                <th style="width:13%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tugas_list as $index => $item): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($item['editor']) ?></td>
                    <td style="text-align:left; padding-left:20px;"><?= esc($item['nama_tugas']) ?></td>
                    <td><?= esc($item['kategori_tugas']) ?></td>
                    <td><?= esc($item['mode_pengumpulan']) ?></td>
                    <td><?= esc($item['deadline']) ?></td>
                    <td>
                        <button class="btn-action btn-edit" onclick="detailTugas(<?= $item['id'] ?>)" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action btn-delete" onclick="hapusTugas(<?= $item['id'] ?>)" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // ================================================================
    // INLINE JS — hanya fungsi yang mengandung PHP vars (base_url)
    // initTugasTable(), initTugasEvents(), _applyTugasFilter(),
    // detailTugas(), hapusTugas() sudah ada di tugas.js
    // ================================================================

    /** URL vars untuk tugas.js (mengandung base_url) */
    window.TUGAS_DETAIL_URL = '<?= base_url('dashboard/manajemen-tugas/tugas/detail') ?>';
    window.TUGAS_UBAH_URL   = '<?= base_url('dashboard/manajemen-tugas/tugas/ubah') ?>';

    /** tambahTugas — mengandung base_url */
    window.tambahTugas = function() {
        window.location.href = '<?= base_url('dashboard/manajemen-tugas/tugas/tambah') ?>';
    };

    /** performDeleteTugas — mengandung base_url, dipanggil oleh hapusTugas() di tugas.js */
    function performDeleteTugas(id) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Menghapus...',
                text: 'Sedang menghapus data',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }

        $.ajax({
            url: '<?= base_url('dashboard/manajemen-tugas/tugas/delete') ?>/' + id,
            method: 'POST',
            success: function(response) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: 'Data berhasil dihapus',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                setTimeout(() => loadTabContent('tugas'), 2000);
            },
            error: function(xhr) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Gagal menghapus data'
                    });
                }
            }
        });
    }
</script>