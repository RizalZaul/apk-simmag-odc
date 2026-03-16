<!-- Action Buttons -->
<div class="section-buttons">
    <button class="btn-custom btn-add" id="btnTambahPkl" onclick="window.location.href='<?= base_url('dashboard/manajemen-pkl/pkl/tambah') ?>'">
        <i class="fas fa-plus"></i>
        <span class="btn-text">Tambah</span>
    </button>
    <button class="btn-custom btn-filter-toggle" id="btnFilterPklAktif">
        <i class="fas fa-filter"></i>
        <span class="btn-text">Filter</span>
    </button>
</div>

<!-- Filter Section -->
<div class="filter-section" id="filterSectionPklAktif">
    <div class="filter-header">
        <h5 class="filter-title">
            <i class="fas fa-filter"></i>
            Filter PKL Aktif
        </h5>
        <button class="btn-reset" id="btnResetFilterPklAktif">
            <i class="fas fa-redo"></i>
            Reset
        </button>
    </div>

    <div class="filter-row-full">
        <div class="filter-group">
            <label><i class="fas fa-search"></i> Cari Nama PKL</label>
            <input type="text" id="filterNamaPklAktif" placeholder="Ketik nama PKL..." class="filter-input">
        </div>
    </div>

    <div class="filter-row-half">
        <div class="filter-group">
            <label><i class="fas fa-tags"></i> Kategori PKL</label>
            <select id="filterKategoriPklAktif" class="filter-input">
                <option value="">Semua Kategori</option>
                <option value="Mandiri">Mandiri</option>
                <option value="Instansi">Instansi</option>
            </select>
        </div>

        <div class="filter-group" id="filterGroupInstansiAktif">
            <label><i class="fas fa-building"></i> Nama Instansi</label>
            <select id="filterInstansiAktif" class="filter-input select2-filter">
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
            <!-- FIXED: removed duplicate class attribute -->
            <input type="text" id="filterTglMulaiAktif" class="filter-input" placeholder="Pilih tanggal">
        </div>
        <div class="filter-group">
            <label><i class="fas fa-calendar-check"></i> Tanggal Akhir</label>
            <!-- FIXED: removed duplicate class attribute -->
            <input type="text" id="filterTglAkhirAktif" class="filter-input" placeholder="Pilih tanggal">
        </div>
    </div>
</div>

<!-- Table -->
<div class="table-container">
    <table id="tablePklAktif" class="table table-hover" style="width:100%;">
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
    // INIT: DataTable for PKL Aktif
    // ==========================================
    function initPklAktifTable() {
        if (typeof $.fn.DataTable === 'undefined') return;

        if ($.fn.DataTable.isDataTable('#tablePklAktif')) {
            $('#tablePklAktif').DataTable().destroy();
        }

        try {
            window.tablePklAktif = $('#tablePklAktif').DataTable({
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
                    emptyTable: "Tidak ada data PKL Aktif",
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
                    // FIXED: functions are now defined at module scope, not nested inside here
                    initPklAktifSelect2();
                    initPklAktifFlatpickr();
                }
            });
        } catch (e) {
            // silent — DataTable errors are visible in browser console
        }
    }

    // ==========================================
    // INIT: Select2 — PKL Aktif Filter
    // FIXED: extracted from inside initPklAktifTable (was a nested function)
    // ==========================================
    function initPklAktifSelect2() {
        if (typeof $.fn.select2 === 'undefined') return;

        if ($('#filterInstansiAktif').hasClass('select2-hidden-accessible')) {
            $('#filterInstansiAktif').select2('destroy');
        }

        $('#filterInstansiAktif').select2({
            placeholder: 'Pilih Instansi',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#filterSectionPklAktif'),
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
    // INIT: Flatpickr — PKL Aktif Filter
    // FIXED: extracted from inside initPklAktifTable
    //        FIXED: was using locale: 'id' (requires locale pack); now uses inline object
    //        FIXED: was targeting wrong IDs (filterTglMulai/filterTglAkhir)
    // ==========================================
    // CATATAN: FLATPICKR_ID_LOCALE sudah didefinisikan di pkl.js (external)
    // Tidak perlu dideklarasikan ulang di sini — akan error "already declared"

    function initPklAktifFlatpickr() {
        if (typeof flatpickr === 'undefined') return;

        flatpickr('#filterTglMulaiAktif', {
            dateFormat: 'd-m-Y',
            allowInput: true,
            locale: FLATPICKR_ID_LOCALE
        });
        flatpickr('#filterTglAkhirAktif', {
            dateFormat: 'd-m-Y',
            allowInput: true,
            locale: FLATPICKR_ID_LOCALE
        });
    }

    // ==========================================
    // INIT: Event Handlers — PKL Aktif
    // ==========================================
    function initPklAktifEvents() {
        // Toggle filter panel
        $('#btnFilterPklAktif').off('click.pklaktif').on('click.pklaktif', function() {
            const $section = $('#filterSectionPklAktif');
            const isOpen = $section.hasClass('show');
            $section.toggleClass('show', !isOpen);
            $(this).find('i').toggleClass('fa-filter', isOpen).toggleClass('fa-arrow-left', !isOpen);
            $(this).find('.btn-text').text(isOpen ? 'Filter' : 'Kembali');
        });

        // Reset filter
        $('#btnResetFilterPklAktif').off('click.pklaktif').on('click.pklaktif', function() {
            $('#filterNamaPklAktif').val('');
            $('#filterKategoriPklAktif').val('');
            $('#filterInstansiAktif').val(null).trigger('change');
            if (typeof flatpickr !== 'undefined') {
                document.querySelector('#filterTglMulaiAktif')?._flatpickr?.clear();
                document.querySelector('#filterTglAkhirAktif')?._flatpickr?.clear();
            }
            $('#filterGroupInstansiAktif').removeClass('hidden');
            $('#tablePklAktif tbody tr').show();

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
        $('#filterNamaPklAktif').off('keyup.pklaktif').on('keyup.pklaktif', function() {
            const value = this.value.toLowerCase();
            $('#tablePklAktif tbody tr').each(function() {
                $(this).toggle($(this).data('nama').indexOf(value) > -1);
            });
        });

        // Filter by Kategori (toggle instansi panel visibility)
        $('#filterKategoriPklAktif').off('change.pklaktif').on('change.pklaktif', function() {
            const kategori = this.value;
            if (kategori === 'Mandiri') {
                $('#filterGroupInstansiAktif').addClass('hidden');
                $('#filterInstansiAktif').val(null).trigger('change');
            } else {
                $('#filterGroupInstansiAktif').removeClass('hidden');
            }

            filterPklAktifTable();
        });

        // Filter by Instansi (Select2)
        $('#filterInstansiAktif').off('change.pklaktif').on('change.pklaktif', function() {
            filterPklAktifTable();
        });

        // Filter by Tanggal
        $('#filterTglMulaiAktif, #filterTglAkhirAktif').off('change.pklaktif').on('change.pklaktif', function() {
            filterPklAktifTable();
        });
    }

    // ==========================================
    // FILTER TANGGAL — CLIENT-SIDE (SEMENTARA)
    // ==========================================
    // ⚠️  TODO: MIGRASI KE SERVER-SIDE SAAT INTEGRASI DATABASE REAL
    //
    // Saat ini filter tanggal bekerja CLIENT-SIDE:
    //   - Data sudah di-load semua ke browser via dummy
    //   - Filter hanya show/hide baris di DOM
    //
    // Setelah integrasi DB, ganti dengan AJAX ke controller:
    //
    //   // Di controller (ManajemenPkl.php), method loadPklAktif():
    //   $filterMulai = $this->request->getGet('tgl_mulai'); // format: Y-m-d
    //   $filterAkhir = $this->request->getGet('tgl_akhir'); // format: Y-m-d
    //
    //   if ($filterMulai && $filterAkhir) {
    //       // Logika OVERLAP: tampilkan PKL yang periodenya bertabrakan dengan filter
    //       // Overlap terjadi jika: tgl_mulai <= filter_akhir AND tgl_akhir >= filter_mulai
    //       $this->db->where('tgl_mulai <=', $filterAkhir)
    //                ->where('tgl_akhir >=', $filterMulai);
    //   } elseif ($filterMulai) {
    //       $this->db->where('tgl_akhir >=', $filterMulai);
    //   } elseif ($filterAkhir) {
    //       $this->db->where('tgl_mulai <=', $filterAkhir);
    //   }
    //
    //   // Jangan lupa kirim filter via AJAX di initPklAktifEvents():
    //   // loadPklSubTab('aktif', { tgl_mulai: filterMulai, tgl_akhir: filterAkhir })
    //
    // ⚠️  HAPUS seluruh blok filterPklAktifTable() di bawah ini setelah migrasi.
    // ==========================================

    // Combined row-visibility filter — SEMENTARA, lihat TODO di atas
    function filterPklAktifTable() {
        const nama = $('#filterNamaPklAktif').val().toLowerCase();
        const kategori = $('#filterKategoriPklAktif').val();
        const instansi = ($('#filterInstansiAktif').val() || '').toLowerCase();
        const tglMulai = $('#filterTglMulaiAktif').val();
        const tglAkhir = $('#filterTglAkhirAktif').val();

        // Parse d-m-Y → YYYY-MM-DD agar string comparison benar
        const parseDMY = function(str) {
            if (!str) return null;
            const p = str.split('-');
            return p.length === 3 ? p[2] + '-' + p[1] + '-' + p[0] : null;
        };
        const filterMulai = parseDMY(tglMulai);
        const filterAkhir = parseDMY(tglAkhir);

        $('#tablePklAktif tbody tr').each(function() {
            const $row = $(this);
            const rowMulai = $row.data('tgl-mulai'); // YYYY-MM-DD dari controller
            const rowAkhir = $row.data('tgl-akhir'); // YYYY-MM-DD dari controller
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