<!-- Action Buttons (no Tambah — nonaktif is read-only) -->
<div class="section-buttons">
    <button class="btn-custom btn-filter-toggle" id="btnFilterPklNonaktif">
        <i class="fas fa-filter"></i>
        <span class="btn-text">Filter</span>
    </button>
</div>

<!-- Filter Section -->
<div class="filter-section" id="filterSectionPklNonaktif">
    <div class="filter-header">
        <h5 class="filter-title">
            <i class="fas fa-filter"></i>
            Filter PKL Non-Aktif
        </h5>
        <button class="btn-reset" id="btnResetFilterPklNonaktif">
            <i class="fas fa-redo"></i>
            Reset
        </button>
    </div>

    <div class="filter-row-full">
        <div class="filter-group">
            <label><i class="fas fa-search"></i> Cari Nama PKL</label>
            <input type="text" id="filterNamaPklNonaktif" placeholder="Ketik nama PKL..." class="filter-input">
        </div>
    </div>

    <div class="filter-row-half">
        <div class="filter-group">
            <label><i class="fas fa-tags"></i> Kategori PKL</label>
            <select id="filterKategoriPklNonaktif" class="filter-input">
                <option value="">Semua Kategori</option>
                <option value="Mandiri">Mandiri</option>
                <option value="Instansi">Instansi</option>
            </select>
        </div>

        <div class="filter-group" id="filterGroupInstansiNonaktif">
            <label><i class="fas fa-building"></i> Nama Instansi</label>
            <select id="filterInstansiNonaktif" class="filter-input select2-filter">
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
            <input type="text" id="filterTglMulaiNonaktif" class="filter-input" placeholder="Pilih tanggal">
        </div>
        <div class="filter-group">
            <label><i class="fas fa-calendar-check"></i> Tanggal Akhir</label>
            <input type="text" id="filterTglAkhirNonaktif" class="filter-input" placeholder="Pilih tanggal">
        </div>
    </div>

    <!-- Filter ekstra khusus Non-Aktif -->
    <div class="filter-row-full">
        <div class="filter-group">
            <label><i class="fas fa-info-circle"></i> Status Kelompok</label>
            <select id="filterStatusKelompokNonaktif" class="filter-input">
                <option value="">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="selesai">Selesai</option>
                <option value="nonaktif">Non-Aktif</option>
            </select>
        </div>
    </div>
</div>

<!-- Table -->
<div class="table-container">
    <table id="tablePklNonaktif" class="table table-hover" style="width:100%;">
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
                    data-tgl-akhir="<?= esc($pkl['tgl_akhir']) ?>"
                    data-status-kelompok="<?= esc($pkl['status_kelompok']) ?>">
                    <td><?= $index + 1 ?></td>
                    <td class="text-left"><?= esc($pkl['nama_lengkap']) ?></td>
                    <td><?= esc($pkl['kategori_pkl']) ?></td>
                    <td><?= $isMandiri ? '-' : esc($pkl['nama_instansi']) ?></td>
                    <td><?= $pkl['tgl_mulai'] ? date('d-m-Y', strtotime($pkl['tgl_mulai'])) : '-' ?></td>
                    <td><?= $pkl['tgl_akhir'] ? date('d-m-Y', strtotime($pkl['tgl_akhir'])) : '-' ?></td>
                    <td>
                        <!-- FIX Bug 2: was id_pkl, harus id_kelompok -->
                        <button class="btn-action btn-edit" onclick="detailPkl(<?= $pkl['id_kelompok'] ?>)" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action btn-success" onclick="aktifkanPkl(<?= $pkl['id_pkl'] ?>)" title="Aktifkan Akun"
                            style="background:var(--status-success);color:#fff;">
                            <i class="fas fa-user-check"></i>
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
    // INIT: DataTable — PKL Non-Aktif
    // ==========================================
    function initPklNonaktifTable() {
        if (typeof $.fn.DataTable === 'undefined') return;

        if ($.fn.DataTable.isDataTable('#tablePklNonaktif')) {
            $('#tablePklNonaktif').DataTable().destroy();
        }

        try {
            window.tablePklNonaktif = $('#tablePklNonaktif').DataTable({
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
                    emptyTable: "Tidak ada data PKL Non-Aktif",
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
                    initPklNonaktifSelect2();
                    initPklNonaktifFlatpickr();
                }
            });
        } catch (e) {
            // silent
        }
    }

    // ==========================================
    // INIT: Select2 — PKL Non-Aktif Filter
    // ==========================================
    function initPklNonaktifSelect2() {
        if (typeof $.fn.select2 === 'undefined') return;

        if ($('#filterInstansiNonaktif').hasClass('select2-hidden-accessible')) {
            $('#filterInstansiNonaktif').select2('destroy');
        }

        $('#filterInstansiNonaktif').select2({
            placeholder: 'Pilih Instansi',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#filterSectionPklNonaktif'),
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
    // INIT: Flatpickr — PKL Non-Aktif Filter
    // ==========================================
    function initPklNonaktifFlatpickr() {
        if (typeof flatpickr === 'undefined') return;

        const locale = {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
            },
            months: {
                shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            }
        };

        flatpickr('#filterTglMulaiNonaktif', {
            dateFormat: 'd-m-Y',
            allowInput: true,
            locale: FLATPICKR_ID_LOCALE
        });

        flatpickr('#filterTglAkhirNonaktif', {
            dateFormat: 'd-m-Y',
            allowInput: true,
            locale: FLATPICKR_ID_LOCALE
        });

    }

    // ==========================================
    // INIT: Event Handlers — PKL Non-Aktif
    // ==========================================
    function initPklNonaktifEvents() {
        // Toggle filter panel
        $('#btnFilterPklNonaktif').off('click.pklnonaktif').on('click.pklnonaktif', function() {
            const $section = $('#filterSectionPklNonaktif');
            const isOpen = $section.hasClass('show');
            $section.toggleClass('show', !isOpen);
            $(this).find('i').toggleClass('fa-filter', isOpen).toggleClass('fa-arrow-left', !isOpen);
            $(this).find('.btn-text').text(isOpen ? 'Filter' : 'Kembali');
        });

        // Reset filter
        $('#btnResetFilterPklNonaktif').off('click.pklnonaktif').on('click.pklnonaktif', function() {
            $('#filterNamaPklNonaktif').val('');
            $('#filterKategoriPklNonaktif').val('');
            $('#filterInstansiNonaktif').val(null).trigger('change');
            document.querySelector('#filterTglMulaiNonaktif')?._flatpickr?.clear();
            document.querySelector('#filterTglAkhirNonaktif')?._flatpickr?.clear();
            $('#filterStatusKelompokNonaktif').val('');
            $('#filterGroupInstansiNonaktif').removeClass('hidden');
            $('#tablePklNonaktif tbody tr').show();

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
        $('#filterNamaPklNonaktif').off('keyup.pklnonaktif').on('keyup.pklnonaktif', function() {
            filterPklNonaktifTable();
        });

        // Filter by Kategori
        $('#filterKategoriPklNonaktif').off('change.pklnonaktif').on('change.pklnonaktif', function() {
            if (this.value === 'Mandiri') {
                $('#filterGroupInstansiNonaktif').addClass('hidden');
                $('#filterInstansiNonaktif').val(null).trigger('change');
            } else {
                $('#filterGroupInstansiNonaktif').removeClass('hidden');
            }
            filterPklNonaktifTable();
        });

        // Filter by Instansi
        $('#filterInstansiNonaktif').off('change.pklnonaktif').on('change.pklnonaktif', function() {
            filterPklNonaktifTable();
        });

        // Filter by Tanggal
        $('#filterTglMulaiNonaktif, #filterTglAkhirNonaktif').off('change.pklnonaktif').on('change.pklnonaktif', function() {
            filterPklNonaktifTable();
        });

        // Filter by Status Kelompok (ekstra untuk Non-Aktif)
        $('#filterStatusKelompokNonaktif').off('change.pklnonaktif').on('change.pklnonaktif', function() {
            filterPklNonaktifTable();
        });
    }

    function filterPklNonaktifTable() {
        const nama = $('#filterNamaPklNonaktif').val().toLowerCase();
        const kategori = $('#filterKategoriPklNonaktif').val();
        const instansi = ($('#filterInstansiNonaktif').val() || '').toLowerCase();
        const tglMulai = $('#filterTglMulaiNonaktif').val();
        const tglAkhir = $('#filterTglAkhirNonaktif').val();
        const status = $('#filterStatusKelompokNonaktif').val();

        // Parse d-m-Y → YYYY-MM-DD untuk string comparison
        const parseDMY = function(str) {
            if (!str) return null;
            const p = str.split('-');
            return p.length === 3 ? p[2] + '-' + p[1] + '-' + p[0] : null;
        };
        const filterMulai = parseDMY(tglMulai);
        const filterAkhir = parseDMY(tglAkhir);

        $('#tablePklNonaktif tbody tr').each(function() {
            const $row = $(this);
            const rowMulai = $row.data('tgl-mulai'); // YYYY-MM-DD
            const rowAkhir = $row.data('tgl-akhir'); // YYYY-MM-DD
            let visible = true;

            if (nama && $row.data('nama').indexOf(nama) === -1) visible = false;
            if (kategori && $row.data('kategori') !== kategori) visible = false;
            if (instansi && $row.data('instansi').indexOf(instansi) === -1) visible = false;
            if (status && $row.data('status-kelompok') !== status) visible = false;

            // FIX Bug 4: gunakan OVERLAP (sama dengan tab Aktif & Selesai)
            // PKL tampil jika periodenya bertabrakan dengan rentang filter:
            //   PKL.tgl_akhir >= filter_mulai  (PKL belum berakhir saat filter mulai)
            //   PKL.tgl_mulai <= filter_akhir  (PKL sudah mulai sebelum filter berakhir)
            if (filterMulai && rowAkhir < filterMulai) visible = false; // PKL selesai sebelum filter mulai
            if (filterAkhir && rowMulai > filterAkhir) visible = false; // PKL mulai setelah filter berakhir

            $row.toggle(visible);
        });
    }
</script>