<!-- Action Buttons + Inline Search -->
<div class="section-buttons">
    <div class="inline-search-wrapper">
        <i class="fas fa-search inline-search-icon"></i>
        <input type="text" id="filterNamaKategori"
            placeholder="Cari nama kategori..."
            class="inline-search-input filter-input">
    </div>
    <button class="btn-reset-inline" id="btnResetKategoriTugas" title="Reset pencarian">
        <i class="fas fa-redo"></i>
        Reset
    </button>
    <button class="btn-custom btn-add" id="btnTambah">
        <i class="fas fa-plus"></i>
        <span class="btn-text">Tambah</span>
    </button>
</div>

<!-- Form Section (Tambah / Edit) -->
<div class="form-section" id="formSection">
    <div class="form-header">
        <h5 class="form-title">
            <i class="fas fa-edit"></i>
            <span id="formTitle">Tambah Kategori Tugas</span>
        </h5>
    </div>

    <form id="formKategori">
        <input type="hidden" id="kategoriId">

        <div class="form-group">
            <label>
                <i class="fas fa-tag"></i>
                Nama Kategori <span class="required">*</span>
            </label>
            <input type="text" id="namaKategori" placeholder="Masukkan nama kategori" required>
        </div>

        <div class="form-group">
            <label>
                <i class="fas fa-users"></i>
                Mode Pengumpulan <span class="required">*</span>
            </label>
            <div class="radio-group">
                <div class="radio-option">
                    <input type="radio" id="modeIndividu" name="mode_pengumpulan" value="individu" checked>
                    <label for="modeIndividu">Individu</label>
                </div>
                <div class="radio-option">
                    <input type="radio" id="modeKelompok" name="mode_pengumpulan" value="kelompok">
                    <label for="modeKelompok">Kelompok</label>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn-cancel" id="btnBatal">
                <i class="fas fa-times"></i> Batal
            </button>
            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
    </form>
</div>

<!-- Table -->
<div class="table-container">
    <table id="tableKategori" class="table table-hover" style="width:100%;">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width:35%;">Nama Kategori</th>
                <th style="width:20%;">Mode Pengumpulan</th>
                <th style="width:15%;">Tgl Dibuat</th>
                <th style="width:15%;">Tgl Diubah</th>
                <th style="width:10%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($kategori_list as $index => $item): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($item['nama_kategori']) ?></td>
                    <td><?= esc(ucfirst($item['mode_pengumpulan'])) ?></td>
                    <td><?= esc($item['tgl_dibuat']) ?></td>
                    <td><?= esc($item['tgl_diubah']) ?></td>
                    <td>
                        <button class="btn-action btn-edit" onclick="editKategori(<?= $item['id'] ?>)" title="Edit">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn-action btn-delete" onclick="hapusKategori(<?= $item['id'] ?>)" title="Hapus">
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
    // Fungsi murni JS (initKategoriTable) sudah ada di tugas.js
    // dan dipanggil oleh loadTabContent() di main_penugasan.php
    // ================================================================

    /**
     * initKategoriEvents — dipanggil dari loadTabContent() di tugas.js
     * Mengandung base_url → tetap inline
     */
    function initKategoriEvents() {
        // Search filter
        $('#filterNamaKategori').off('keyup').on('keyup', function() {
            const val = this.value.toLowerCase();
            $('#tableKategori tbody tr').each(function() {
                $(this).toggle($(this).find('td:eq(1)').text().toLowerCase().includes(val));
            });
        });

        // Reset Inline Search
        $('#btnResetKategoriTugas').off('click').on('click', function() {
            $('#filterNamaKategori').val('').trigger('keyup');
            showToast('success', 'Pencarian direset');
        });

        // Toggle form Tambah
        $('#btnTambah').off('click').on('click', function() {
            const formSection = $('#formSection');
            const icon = $(this).find('i');
            const text = $(this).find('.btn-text');

            if (formSection.hasClass('show')) {
                formSection.removeClass('show');
                icon.removeClass('fa-arrow-left').addClass('fa-plus');
                text.text('Tambah');
                $('#formKategori')[0].reset();
            } else {
                formSection.addClass('show');
                icon.removeClass('fa-plus').addClass('fa-arrow-left');
                text.text('Kembali');
                $('#formTitle').text('Tambah Kategori Tugas');
                $('#formKategori')[0].reset();
                $('#kategoriId').val('');
            }
        });

        // Batal
        $('#btnBatal').off('click').on('click', function() {
            $('#formSection').removeClass('show');
            $('#formKategori')[0].reset();
            $('#btnTambah').find('i').removeClass('fa-arrow-left').addClass('fa-plus');
            $('#btnTambah').find('.btn-text').text('Tambah');
        });

        // Submit form — base_url ada di sini
        $('#formKategori').off('submit').on('submit', function(e) {
            e.preventDefault();

            const id = $('#kategoriId').val();
            const namaKategori = $('#namaKategori').val();
            const modePengumpulan = $('input[name="mode_pengumpulan"]:checked').val();

            if (!namaKategori) {
                (typeof Swal !== 'undefined') ?
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Nama kategori harus diisi!'
                }): alert('Nama kategori harus diisi!');
                return;
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            }

            const url = id ?
                '<?= base_url('dashboard/manajemen-tugas/kategori/update') ?>/' + id :
                '<?= base_url('dashboard/manajemen-tugas/kategori/store') ?>';

            $.ajax({
                url,
                method: 'POST',
                data: {
                    nama_kategori: namaKategori,
                    mode_pengumpulan: modePengumpulan
                },
                success: function(response) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message || 'Kategori berhasil disimpan',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                    $('#formSection').removeClass('show');
                    $('#formKategori')[0].reset();
                    $('#btnTambah').find('i').removeClass('fa-arrow-left').addClass('fa-plus');
                    $('#btnTambah').find('.btn-text').text('Tambah');
                    setTimeout(() => loadTabContent('kategori'), 2000);
                },
                error: function(xhr) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Gagal menyimpan data'
                        });
                    }
                }
            });
        });
    }

    /** editKategori — mengandung base_url */
    window.editKategori = function(id) {
        $('#formSection').addClass('show');
        $('#formTitle').text('Edit Kategori Tugas');
        $('#btnTambah').find('i').removeClass('fa-plus').addClass('fa-arrow-left');
        $('#btnTambah').find('.btn-text').text('Kembali');

        $.ajax({
            url: '<?= base_url('dashboard/manajemen-tugas/kategori') ?>/' + id,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const d = response.data;
                    $('#kategoriId').val(d.id);
                    $('#namaKategori').val(d.nama_kategori);
                    $('input[name="mode_pengumpulan"][value="' + d.mode_pengumpulan + '"]').prop('checked', true);
                }
            },
            error: function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat data kategori'
                    });
                }
            }
        });

        $('html, body').animate({
            scrollTop: $('#formSection').offset().top - 100
        }, 500);
    };

    /** hapusKategori + performDelete — mengandung base_url */
    window.hapusKategori = function(id) {
        if (typeof Swal === 'undefined') {
            if (!confirm('Yakin ingin menghapus kategori ini?')) return;
            performDelete(id);
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Yakin ingin menghapus kategori ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed) performDelete(id);
        });
    };

    function performDelete(id) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Menghapus...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }

        $.ajax({
            url: '<?= base_url('dashboard/manajemen-tugas/kategori/delete') ?>/' + id,
            method: 'POST',
            success: function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: 'Data berhasil dihapus',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                setTimeout(() => loadTabContent('kategori'), 2000);
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