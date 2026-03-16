<!-- =====================================================
     PARTIAL VIEW: data_modul.php
     Diload via AJAX oleh _loadModulTab('modul')
     Tidak ada <style> atau <script> di sini —
     CSS → data-modul.css | JS → modul.js
     ===================================================== -->

<!-- Action Buttons -->
<div class="section-buttons">
    <button class="btn-custom btn-add"
        onclick="window.location.href='<?= base_url('dashboard/data-modul/modul/tambah') ?>'">
        <i class="fas fa-plus"></i>
        <span class="btn-text">Tambah</span>
    </button>
    <button class="btn-custom btn-filter-toggle" id="btnFilterModul">
        <i class="fas fa-filter"></i>
        <span class="btn-text">Filter</span>
    </button>
</div>

<!-- Filter Section (Nama + Kategori) -->
<div class="filter-section" id="filterSectionModul">
    <div class="filter-header">
        <h5 class="filter-title">
            <i class="fas fa-filter"></i>
            Filter Data Modul
        </h5>
        <button class="btn-reset" id="btnResetFilterModul">
            <i class="fas fa-redo"></i>
            Reset
        </button>
    </div>

    <div class="filter-row-half">
        <!-- Cari Nama Modul -->
        <div class="filter-group">
            <label>
                <i class="fas fa-search"></i>
                Cari Nama Modul
            </label>
            <input type="text" id="filterNamaModul"
                placeholder="Ketik nama modul..."
                class="filter-input">
        </div>

        <!-- Kategori — Select2 -->
        <div class="filter-group">
            <label>
                <i class="fas fa-tags"></i>
                Kategori
            </label>
            <?php
            $kategoriUnik = array_unique(array_column($modul_list, 'nama_kategori'));
            sort($kategoriUnik);
            ?>
            <select id="filterKategoriModul" class="filter-input select2-kategori-filter" style="width:100%;">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategoriUnik as $kat): ?>
                    <option value="<?= esc($kat) ?>"><?= esc($kat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<!-- Table -->
<div class="table-container">
    <table id="tableDataModul" class="table table-hover" style="width: 100%;">
        <thead>
            <tr>
                <th style="width:  4%;">No</th>
                <th style="width: 27%;">Nama Modul</th>
                <th style="width: 18%;">Kategori</th>
                <th style="width: 20%;">Modul</th>
                <th style="width: 18%;">Tanggal Diubah</th>
                <th style="width: 13%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($modul_list as $index => $item): ?>
                <?php
                $ext  = strtolower(pathinfo($item['path'], PATHINFO_EXTENSION));
                $icon = match ($ext) {
                    'pdf'           => 'fa-file-pdf',
                    'docx', 'doc'  => 'fa-file-word',
                    'pptx', 'ppt'  => 'fa-file-powerpoint',
                    'xlsx', 'xls'  => 'fa-file-excel',
                    'zip', 'rar'   => 'fa-file-zipper',
                    default        => 'fa-file',
                };
                ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td style="text-align: left; padding-left: 20px;">
                        <?= esc($item['nama_modul']) ?>
                    </td>
                    <td><?= esc($item['nama_kategori']) ?></td>
                    <td style="text-align: left; padding-left: 20px;">
                        <?php if ($item['tipe'] === 'link'): ?>
                            <a href="<?= esc($item['path']) ?>"
                                target="_blank"
                                class="modul-link">
                                <i class="fas fa-link"></i>
                                <span>Buka Link</span>
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('dashboard/data-modul/modul/file/' . $item['id']) ?>"
                                target="_blank"
                                class="modul-link modul-file <?= $ext ?>">
                                <i class="fas <?= $icon ?>"></i>
                                <span><?= esc($item['path']) ?></span>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($item['tgl_diubah']) ?></td>
                    <td>
                        <button class="btn-action btn-detail"
                            onclick="window.location.href='<?= base_url('dashboard/data-modul/modul/detail/' . $item['id']) ?>'"
                            title="Detail">
                            <i class="fas fa-eye"></i>
                        </button>

                        <!--
                        [DIUBAH] URL hapus dikirim langsung dari PHP sebagai argumen kedua.
                        Sebelum: hapusModul(<?= $item['id'] ?>)
                                 → bergantung pada _modulBaseUrl yang bisa kosong saat page refresh
                        Sesudah: hapusModul(<?= $item['id'] ?>, 'URL_LENGKAP')
                                 → URL selalu absolut, tidak bergantung variabel JS global
                    -->
                        <button class="btn-action btn-delete"
                            onclick="hapusModul(<?= $item['id'] ?>, '<?= base_url('dashboard/data-modul') ?>')"
                            title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>