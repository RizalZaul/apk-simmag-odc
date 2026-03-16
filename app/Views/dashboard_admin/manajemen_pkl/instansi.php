<!-- Action Buttons -->
<div class="section-buttons">
    <button class="btn-custom btn-add" id="btnTambahInstansi" onclick="window.location.href='<?= base_url('dashboard/manajemen-pkl/instansi/tambah') ?>'">
        <i class="fas fa-plus"></i>
        <span class="btn-text">Tambah</span>
    </button>
    <button class="btn-custom btn-filter-toggle" id="btnFilterInstansi">
        <i class="fas fa-filter"></i>
        <span class="btn-text">Filter</span>
    </button>
</div>

<!-- Filter Section -->
<div class="filter-section" id="filterSectionInstansi">
    <div class="filter-header">
        <h5 class="filter-title">
            <i class="fas fa-filter"></i>
            Filter Data Instansi
        </h5>
        <button class="btn-reset" id="btnResetFilterInstansi">
            <i class="fas fa-redo"></i>
            Reset
        </button>
    </div>

    <!-- Baris 1: Search Nama Instansi (Full Width) -->
    <div class="filter-row-full">
        <div class="filter-group">
            <label>
                <i class="fas fa-search"></i>
                Cari Nama Instansi
            </label>
            <input type="text" id="filterNamaInstansi" placeholder="Ketik nama instansi..." class="filter-input">
        </div>
    </div>

    <!-- Baris 2: Kategori + Kota (50% + 50%) -->
    <div class="filter-row-half">
        <div class="filter-group">
            <label>
                <i class="fas fa-tags"></i>
                Kategori
            </label>
            <select id="filterKategoriInstansi" class="filter-input">
                <option value="">Semua Kategori</option>
                <option value="SMK Sederajat">SMK Sederajat</option>
                <option value="Kuliah">Kuliah</option>
            </select>
        </div>

        <div class="filter-group">
            <label>
                <i class="fas fa-map-marker-alt"></i>
                Kota
            </label>
            <select id="filterKotaInstansi" class="filter-input select2-filter">
                <option value="">Semua Kota</option>
                <?php foreach ($kota_list as $kota): ?>
                    <option value="<?= esc($kota) ?>"><?= esc($kota) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

    </div>
</div>

<!-- Table -->
<div class="table-container">
    <table id="tableInstansi" class="table table-hover" style="width: 100%;">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Nama Instansi</th>
                <th style="width: 15%;">Kategori Instansi</th>
                <th style="width: 35%;">Alamat</th>
                <th style="width: 15%;">Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($instansi_list as $index => $item): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td style="text-align: left; padding-left: 20px;">
                        <?= esc($item['nama_instansi']) ?>
                    </td>
                    <td><?= esc($item['kategori_label']) ?></td>
                    <td style="text-align: left; padding-left: 20px;">
                        <?= esc($item['alamat']) ?>, <?= esc($item['kota']) ?>
                    </td>
                    <td>
                        <button class="btn-action btn-edit"
                            onclick="window.location.href='<?= base_url('dashboard/manajemen-pkl/instansi/ubah/' . $item['id']) ?>'"
                            title="Ubah">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn-action btn-delete"
                            onclick="hapusInstansi(<?= $item['id'] ?>)"
                            title="Hapus">
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
    // INIT: DataTable for Instansi
    // ==========================================
    function initInstansiTable() {
        if (typeof $.fn.DataTable === 'undefined') {
            console.error('DataTables not available');
            return;
        }

        if ($.fn.DataTable.isDataTable('#tableInstansi')) {
            $('#tableInstansi').DataTable().destroy();
        }

        try {
            window.tableInstansi = $('#tableInstansi').DataTable({
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
                language: {
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    emptyTable: "Tidak ada data yang tersedia",
                    zeroRecords: "Tidak ada data yang cocok",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                autoWidth: false,
                columnDefs: [{
                        targets: [1, 3],
                        orderable: true,
                        className: 'text-left'
                    },
                    {
                        targets: 4,
                        orderable: false
                    }
                ],
            });
        } catch (error) {
            console.error('Error initializing DataTable Instansi:', error);
        }
    }

    // ==========================================
    // INIT: Select2 for Instansi Filters
    // ==========================================
    function initInstansiSelect2() {
        // Destroy existing Select2 jika ada
        if ($('#filterKotaInstansi').hasClass('select2-hidden-accessible')) {
            $('#filterKotaInstansi').select2('destroy');
        }

        // Init Select2
        $('#filterKotaInstansi').select2({
            placeholder: 'Pilih Kota',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#filterSectionInstansi'),
            language: {
                noResults: function() {
                    return "Kota tidak ditemukan";
                },
                searching: function() {
                    return "Mencari...";
                }
            }
        });
    }

    // ==========================================
    // INIT: Event Handlers for Instansi
    // ==========================================
    function initInstansiEvents() {
        // Toggle Filter
        $('#btnFilterInstansi').off('click').on('click', function() {
            const filterSection = $('#filterSectionInstansi');
            const icon = $(this).find('i');
            const text = $(this).find('.btn-text');

            if (filterSection.hasClass('show')) {
                filterSection.removeClass('show');
                icon.removeClass('fa-arrow-left').addClass('fa-filter');
                text.text('Filter');
            } else {
                filterSection.addClass('show');
                icon.removeClass('fa-filter').addClass('fa-arrow-left');
                text.text('Kembali');
            }
        });

        // Ganti ketiga handler filter yang lama dengan ini:

        function _applyInstansiFilter() {
            const nama = $('#filterNamaInstansi').val().toLowerCase();
            const kategori = $('#filterKategoriInstansi').val().toLowerCase();
            const kota = ($('#filterKotaInstansi').val() || '').toLowerCase();

            $('#tableInstansi tbody tr').each(function() {
                const namaMatch = !nama || $(this).find('td:eq(1)').text().toLowerCase().includes(nama);
                const kategoriMatch = !kategori || $(this).find('td:eq(2)').text().toLowerCase().includes(kategori);
                const kotaMatch = !kota || $(this).find('td:eq(3)').text().toLowerCase().includes(kota);
                $(this).toggle(namaMatch && kategoriMatch && kotaMatch);
            });
        }

        // Filter by Nama
        $('#filterNamaInstansi').off('keyup').on('keyup', _applyInstansiFilter);

        // Filter by Kategori
        $('#filterKategoriInstansi').off('change').on('change', _applyInstansiFilter);

        // Filter by Kota (Select2)
        $('#filterKotaInstansi').off('change').on('change', function() {
            // Manual fix rendered text Select2 (tetap dipertahankan)
            setTimeout(function() {
                const selectedText = $('#filterKotaInstansi').find('option:selected').text();
                const $rendered = $('#filterKotaInstansi').next('.select2-container').find('.select2-selection__rendered');
                if (selectedText && selectedText !== 'Semua Kota') {
                    $rendered.text(selectedText).attr('title', selectedText);
                } else {
                    $rendered.html('<span class="select2-selection__placeholder">Pilih Kota</span>').attr('title', 'Pilih Kota');
                }
            }, 10);

            _applyInstansiFilter();
        });

        // Reset Filter
        $('#btnResetFilterInstansi').off('click').on('click', function() {
            $('#filterNamaInstansi').val('');
            $('#filterKategoriInstansi').val('');
            $('#filterKotaInstansi').val(null).trigger('change');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Filter Direset',
                    timer: 1500,
                    showConfirmButton: false,
                    timerProgressBar: true
                });
            }
        });

    }

    // ==========================================
    // GLOBAL FUNCTIONS
    // ==========================================
    window.hapusInstansi = function(id) {
        if (typeof Swal === 'undefined') {
            if (!confirm('Yakin ingin menghapus instansi ini?')) return;
            performDeleteInstansi(id);
        } else {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Yakin ingin menghapus instansi ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (result.isConfirmed) {
                    performDeleteInstansi(id);
                }
            });
        }
    }

    function performDeleteInstansi(id) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Menghapus...',
                text: 'Sedang menghapus data',
                allowOutsideClick: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });
        }

        $.ajax({
            url: '<?= base_url("dashboard/manajemen-pkl/instansi/delete") ?>/' + id,
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

                setTimeout(function() {
                    loadTabContent('instansi');
                }, 2000);
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