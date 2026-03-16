<!-- =====================================================
     PARTIAL VIEW: kategori_modul.php
     Diload via AJAX oleh _loadModulTab('kategori')
     Tidak ada <style> atau <script> di sini —
     CSS → data-modul.css | JS → modul.js
     ===================================================== -->

<!-- Action Buttons + Inline Search -->
<div class="section-buttons">
    <div class="inline-search-wrapper">
        <i class="fas fa-search inline-search-icon"></i>
        <input type="text" id="filterNamaKategoriModul"
            placeholder="Cari nama kategori..."
            class="inline-search-input filter-input">
    </div>
    <button class="btn-reset-inline" id="btnResetKategoriModul" title="Reset pencarian">
        <i class="fas fa-redo"></i>
        Reset
    </button>
    <button class="btn-custom btn-add" id="btnTambahKategoriModul">
        <i class="fas fa-plus"></i>
        <span class="btn-text">Tambah</span>
    </button>
</div>

<!-- Form Section (Tambah / Edit) -->
<div class="form-section" id="formSectionKategoriModul">
    <div class="form-header">
        <h5 class="form-title">
            <i class="fas fa-edit"></i>
            <span id="formTitleKategoriModul">Tambah Kategori Modul</span>
        </h5>
    </div>

    <form id="formKategoriModul">
        <input type="hidden" id="kategoriModulId">

        <div class="form-group">
            <label>
                <i class="fas fa-tag"></i>
                Nama Kategori <span class="required">*</span>
            </label>
            <input type="text" id="namaKategoriModul"
                placeholder="Masukkan nama kategori"
                required>
        </div>

        <div class="form-actions">
            <button type="button" class="btn-cancel" id="btnBatalKategoriModul">
                <i class="fas fa-times"></i>
                Batal
            </button>
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i>
                Simpan
            </button>
        </div>
    </form>
</div>

<!-- Table -->
<div class="table-container">
    <table id="tableKategoriModul" class="table table-hover" style="width: 100%;">
        <thead>
            <tr>
                <th style="width:  5%;">No</th>
                <th style="width: 40%;">Nama Kategori</th>
                <th style="width: 20%;">Tanggal Dibuat</th>
                <th style="width: 20%;">Tanggal Diubah</th>
                <th style="width: 15%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($kategori_list as $index => $item): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td style="text-align: left; padding-left: 20px;">
                        <?= esc($item['nama_kategori']) ?>
                    </td>

                    <td><?= $item['tgl_dibuat'] ? date('d M Y H:i', strtotime($item['tgl_dibuat'])) : '-' ?></td>
                    <td><?= $item['tgl_diubah'] ? date('d M Y H:i', strtotime($item['tgl_diubah'])) : '-' ?></td>

                    <td>
                        <button class="btn-action btn-edit"
                            onclick="editKategoriModul(<?= $item['id'] ?>)"
                            title="Edit">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn-action btn-delete"
                            onclick="hapusKategoriModul(<?= $item['id'] ?>)"
                            title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>