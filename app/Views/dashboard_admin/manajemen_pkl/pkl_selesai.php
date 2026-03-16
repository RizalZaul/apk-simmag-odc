<!-- Action Buttons (no Tambah — selesai is read-only) -->
<div class="section-buttons">
    <button class="btn-custom btn-filter-toggle" id="btnFilterPklSelesai">
        <i class="fas fa-filter"></i>
        <span class="btn-text">Filter</span>
    </button>
</div>

<!-- Filter Section -->
<div class="filter-section" id="filterSectionPklSelesai">
    <div class="filter-header">
        <h5 class="filter-title">
            <i class="fas fa-filter"></i>
            Filter PKL Selesai
        </h5>
        <button class="btn-reset" id="btnResetFilterPklSelesai">
            <i class="fas fa-redo"></i>
            Reset
        </button>
    </div>

    <div class="filter-row-full">
        <div class="filter-group">
            <label><i class="fas fa-search"></i> Cari Nama PKL</label>
            <input type="text" id="filterNamaPklSelesai" placeholder="Ketik nama PKL..." class="filter-input">
        </div>
    </div>

    <div class="filter-row-half">
        <div class="filter-group">
            <label><i class="fas fa-tags"></i> Kategori PKL</label>
            <select id="filterKategoriPklSelesai" class="filter-input">
                <option value="">Semua Kategori</option>
                <option value="Mandiri">Mandiri</option>
                <option value="Instansi">Instansi</option>
            </select>
        </div>

        <div class="filter-group" id="filterGroupInstansiSelesai">
            <label><i class="fas fa-building"></i> Nama Instansi</label>
            <select id="filterInstansiSelesai" class="filter-input select2-filter">
                <option value="">Semua Instansi</option>
                <?php foreach ($instansi_list as $instansi): ?>
                    <option value="<?= esc($instansi['nama_instansi']) ?>">
                        <?= esc($instansi['nama_instansi']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="filter-row-half">
        <div class="filter-group">
            <label><i class="fas fa-calendar-alt"></i> Tanggal Mulai</label>
            <!-- FIXED: removed duplicate class attr; FIXED: unique IDs (was filterTglMulai, conflicted with Aktif) -->
            <input type="text" id="filterTglMulaiSelesai" class="filter-input" placeholder="Pilih tanggal">
        </div>
        <div class="filter-group">
            <label><i class="fas fa-calendar-check"></i> Tanggal Akhir</label>
            <!-- FIXED: was filterTglAkhir, conflicted with Aktif -->
            <input type="text" id="filterTglAkhirSelesai" class="filter-input" placeholder="Pilih tanggal">
        </div>
    </div>
</div>

<!-- Table -->
<div class="table-container">
    <table id="tablePklSelesai" class="table table-hover" style="width:100%;">
        <thead>
            <tr>
                <th style="width:5%;">No</th>
                <th style="width:22%;">Nama</th>
                <th style="width:13%;">Kategori</th>
                <th style="width:20%;">Instansi</th>
                <th style="width:13%;">Tgl Mulai</th>
                <th style="width:13%;">Tgl Akhir</th>
                <th style="width:14%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pkl_list as $index => $pkl): ?>
                <?php $isMandiri = $pkl['kategori_pkl'] === 'Mandiri'; ?>
                <tr data-nama="<?= strtolower(esc($pkl['nama_lengkap'])) ?>"
                    data-kategori="<?= esc($pkl['kategori_pkl']) ?>"
                    data-instansi="<?= $isMandiri ? '' : strtolower(esc($pkl['nama_instansi'])) ?>"
                    data-tgl-mulai="<?= esc($pkl['tgl_mulai']) ?>"
                    data-tgl-akhir="<?= esc($pkl['tgl_akhir']) ?>">
                    <td><?= $index + 1 ?></td>
                    <td class="text-left"><?= esc($pkl['nama_lengkap']) ?></td>
                    <td><?= esc($pkl['kategori_pkl']) ?></td>
                    <td><?= $isMandiri ? '-' : esc($pkl['nama_instansi']) ?></td>
                    <td><?= date('d-m-Y', strtotime($pkl['tgl_mulai'])) ?></td>
                    <td><?= date('d-m-Y', strtotime($pkl['tgl_akhir'])) ?></td>
                    <td>
                        <!-- FIX Bug 2: was id_pkl, harus id_kelompok -->
                        <button class="btn-action btn-edit" onclick="detailPkl(<?= $pkl['id_kelompok'] ?>)" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action btn-warning" onclick="nonaktifkanPkl(<?= $pkl['id_pkl'] ?>)" title="Nonaktifkan Akun"
                            style="background:var(--status-warning);color:#fff;">
                            <i class="fas fa-user-slash"></i>
                        </button>
                        <button class="btn-action btn-delete" onclick="hapusPkl(<?= $pkl['id_pkl'] ?>)" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // ==========================================
    // INIT: DataTable — PKL Selesai
    // ==========================================
    function initPklSelesaiTable() {
        if (typeof $.fn.DataTable === 'undefined') return;

        if ($.fn.DataTable.isDataTable('#tablePklSelesai')) {
            $('#tablePklSelesai').DataTable().destroy();
        }

        try {
            window.tablePklSelesai = $('#tablePklSelesai').DataTable({
                paging: true,
                pageLength: 10,
                lengthMenu: [
                    [5, 10, 25, 50],
                    [5, 10, 25, 50]
                ],
                searching: false,
                ordering: true,
                order: [
                    [0, 'asc']
                ],
                info: true,
                autoWidth: false,
                language: {
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    emptyTable: "Tidak ada data PKL Selesai",
                    zeroRecords: "Tidak ada data yang cocok",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                columnDefs: [{
                        targets: [1, 2, 3],
                        orderable: true
                    },
                    {
                        targets: 6,
                        orderable: false
                    }
                ],
                initComplete: function() {
                    initPklSelesaiSelect2();
                    initPklSelesaiFlatpickr();
                }
            });
        } catch (e) {
            // silent
        }
    }

    // ==========================================
    // INIT: Select2 — PKL Selesai Filter
    // ==========================================
    function initPklSelesaiSelect2() {
        if (typeof $.fn.select2 === 'undefined') return;

        if ($('#filterInstansiSelesai').hasClass('select2-hidden-accessible')) {
            $('#filterInstansiSelesai').select2('destroy');
        }

        $('#filterInstansiSelesai').select2({
            placeholder: 'Pilih Instansi',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#filterSectionPklSelesai'),
            language: {
                noResults: function() {
                    return 'Instansi tidak ditemukan';
                },
                searching: function() {
                    return 'Mencari...';
                }
            }
        });
    }

    // ==========================================
    // INIT: Flatpickr — PKL Selesai Filter
    // FIXED: was using locale: 'id'; now inline locale object
    // ==========================================
    function initPklSelesaiFlatpickr() {
        if (typeof flatpickr === 'undefined') return;

        // FLATPICKR_ID_LOCALE didefinisikan di pkl.js (external)
        flatpickr('#filterTglMulaiSelesai', {
            dateFormat: 'd-m-Y',
            allowInput: true,
            locale: FLATPICKR_ID_LOCALE
        });
        flatpickr('#filterTglAkhirSelesai', {
            dateFormat: 'd-m-Y',
            allowInput: true,
            locale: FLATPICKR_ID_LOCALE
        });
    }

    // ==========================================
    // INIT: Event Handlers — PKL Selesai
    // ==========================================
    function initPklSelesaiEvents() {
        // Toggle filter panel
        $('#btnFilterPklSelesai').off('click.pklselesai').on('click.pklselesai', function() {
            const $section = $('#filterSectionPklSelesai');
            const isOpen = $section.hasClass('show');
            $section.toggleClass('show', !isOpen);
            $(this).find('i').toggleClass('fa-filter', isOpen).toggleClass('fa-arrow-left', !isOpen);
            $(this).find('.btn-text').text(isOpen ? 'Filter' : 'Kembali');
        });

        // Reset filter
        $('#btnResetFilterPklSelesai').off('click.pklselesai').on('click.pklselesai', function() {
            $('#filterNamaPklSelesai').val('');
            $('#filterKategoriPklSelesai').val('');
            $('#filterInstansiSelesai').val(null).trigger('change');
            document.querySelector('#filterTglMulaiSelesai')?._flatpickr?.clear();
            document.querySelector('#filterTglAkhirSelesai')?._flatpickr?.clear();
            $('#filterGroupInstansiSelesai').removeClass('hidden');
            $('#tablePklSelesai tbody tr').show();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Filter Direset',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });

        // Filter by Nama
        $('#filterNamaPklSelesai').off('keyup.pklselesai').on('keyup.pklselesai', function() {
            const value = this.value.toLowerCase();
            $('#tablePklSelesai tbody tr').each(function() {
                $(this).toggle($(this).data('nama').indexOf(value) > -1);
            });
        });

        // Filter by Kategori
        $('#filterKategoriPklSelesai').off('change.pklselesai').on('change.pklselesai', function() {
            if (this.value === 'Mandiri') {
                $('#filterGroupInstansiSelesai').addClass('hidden');
                $('#filterInstansiSelesai').val(null).trigger('change');
            } else {
                $('#filterGroupInstansiSelesai').removeClass('hidden');
            }
            filterPklSelesaiTable();
        });

        // Filter by Instansi
        $('#filterInstansiSelesai').off('change.pklselesai').on('change.pklselesai', function() {
            filterPklSelesaiTable();
        });

        // Filter by Tanggal
        $('#filterTglMulaiSelesai, #filterTglAkhirSelesai').off('change.pklselesai').on('change.pklselesai', function() {
            filterPklSelesaiTable();
        });
    }

    // ==========================================
    // FILTER TANGGAL — CLIENT-SIDE (SEMENTARA)
    // ==========================================
    // ⚠️  TODO: MIGRASI KE SERVER-SIDE SAAT INTEGRASI DATABASE REAL
    //
    // Setelah integrasi DB, ganti dengan query di controller (loadPklSelesai()):
    //   $this->db->where('tgl_mulai <=', $filterAkhir)
    //            ->where('tgl_akhir >=', $filterMulai);
    //
    // Logika: OVERLAP — tampilkan PKL yang periodenya bertabrakan dengan filter.
    // ⚠️  HAPUS blok filterPklSelesaiTable() di bawah ini setelah migrasi.
    // ==========================================

    function filterPklSelesaiTable() {
        const nama = $('#filterNamaPklSelesai').val().toLowerCase();
        const kategori = $('#filterKategoriPklSelesai').val();
        const instansi = ($('#filterInstansiSelesai').val() || '').toLowerCase();
        const tglMulai = $('#filterTglMulaiSelesai').val();
        const tglAkhir = $('#filterTglAkhirSelesai').val();

        const parseDMY = function(str) {
            if (!str) return null;
            const p = str.split('-');
            return p.length === 3 ? p[2] + '-' + p[1] + '-' + p[0] : null;
        };
        const filterMulai = parseDMY(tglMulai);
        const filterAkhir = parseDMY(tglAkhir);

        $('#tablePklSelesai tbody tr').each(function() {
            const $row = $(this);
            const rowMulai = $row.data('tgl-mulai');
            const rowAkhir = $row.data('tgl-akhir');
            let visible = true;

            if (nama && $row.data('nama').indexOf(nama) === -1) visible = false;
            if (kategori && $row.data('kategori') !== kategori) visible = false;
            if (instansi && $row.data('instansi').indexOf(instansi) === -1) visible = false;

            // Logika OVERLAP: tgl_mulai_row <= filter_akhir AND tgl_akhir_row >= filter_mulai
            if (filterMulai && rowAkhir < filterMulai) visible = false;
            if (filterAkhir && rowMulai > filterAkhir) visible = false;

            $row.toggle(visible);
        });
    }

</script>