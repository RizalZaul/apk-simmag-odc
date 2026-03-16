<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/modules/tugas.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="tambah-tugas-container">

    <!-- Header -->
    <div class="tambah-header">
        <button class="btn-back-link" id="btnBackToDetail">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Detail Tugas
        </button>
        <h1 class="tambah-title">
            <i class="fas fa-pen"></i>
            Ubah Tugas
        </h1>
    </div>

    <!-- Form Ubah -->
    <div class="form-section-tugas">
        <div class="form-section-header">
            <h3 class="form-section-title">
                <i class="fas fa-clipboard-list"></i>
                Ketentuan Tugas
            </h3>
        </div>
        <div class="form-section-body">
            <form id="formUbahTugas" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" id="tugasId" value="<?= (int) $tugas['id'] ?>">

                <div class="form-grid">

                    <!-- Editor (readonly) -->
                    <div class="form-group-tugas">
                        <label><i class="fas fa-user"></i> Editor</label>
                        <input type="text"
                            value="<?= esc($tugas['editor'] ?? session('nama_lengkap') ?? 'Admin') ?>"
                            class="readonly-field"
                            readonly>
                    </div>

                    <!-- Nama Tugas -->
                    <div class="form-group-tugas">
                        <label>
                            <i class="fas fa-tasks"></i>
                            Nama Tugas <span class="required">*</span>
                        </label>
                        <input type="text"
                            id="namaTugasUbah"
                            name="nama_tugas"
                            value="<?= esc($tugas['nama_tugas']) ?>"
                            placeholder="Masukkan nama tugas..."
                            required>
                    </div>

                    <!-- Kategori Tugas -->
                    <div class="form-group-tugas">
                        <label>
                            <i class="fas fa-tags"></i>
                            Kategori Tugas <span class="required">*</span>
                        </label>

                        <?php
                        // Tentukan mode tugas saat ini dari kategori yang terpilih
                        $currentMode = '';
                        foreach (($kategori_list ?? []) as $kat) {
                            if ((int)$kat['id'] === (int)$tugas['id_kat_tugas']) {
                                $currentMode = $kat['mode']; // 'individu' | 'kelompok'
                                break;
                            }
                        }
                        ?>
                        <select id="kategoriTugasUbah" name="kategori_id" style="width:100%;" required>
                            <option value="">-- Pilih Kategori Tugas --</option>
                            <?php foreach (($kategori_list ?? []) as $kat): ?>
                                <?php if ($kat['mode'] !== $currentMode) continue; // hide beda mode 
                                ?>
                                <option value="<?= $kat['id'] ?>"
                                    data-mode="<?= esc(ucfirst($kat['mode'])) ?>"
                                    <?= (int)$kat['id'] === (int)$tugas['id_kat_tugas'] ? 'selected' : '' ?>>
                                    <?= esc($kat['nama_kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    </div>

                    <!-- Mode Pengumpulan — auto-fill dari kategori -->
                    <div class="form-group-tugas">
                        <label>
                            <i class="fas fa-layer-group"></i>
                            Mode Pengumpulan
                        </label>
                        <select id="modelPengumpulanUbah" name="model_pengumpulan" disabled>
                            <option value="">— Dipilih otomatis dari kategori —</option>
                            <option value="Individu">Individu</option>
                            <option value="Kelompok">Kelompok</option>
                        </select>
                        <small style="color:var(--text-muted); font-size:11px; margin-top:4px; display:block;">
                            <i class="fas fa-info-circle"></i>
                            Mode ditentukan otomatis sesuai kategori tugas yang dipilih
                        </small>
                    </div>

                    <!-- Deadline -->
                    <div class="form-group-tugas">
                        <label>
                            <i class="fas fa-calendar-times"></i>
                            Deadline <span class="required">*</span>
                        </label>
                        <input type="text"
                            id="deadlineUbah"
                            name="deadline"
                            value="<?= esc($tugas['deadline_fmt'] ?? '') ?>"
                            placeholder="Pilih tanggal deadline..."
                            class="flatpickr-input"
                            required
                            readonly>
                    </div>

                    <!-- Jumlah Target -->
                    <div class="form-group-tugas">
                        <label>
                            <i class="fas fa-bullseye"></i>
                            Jumlah Target <span class="required">*</span>
                        </label>
                        <input type="number"
                            id="jumlahTargetUbah"
                            name="jumlah_target"
                            value="<?= esc($tugas['target_jumlah'] ?? 1) ?>"
                            placeholder="Contoh: 10"
                            min="1"
                            required>
                    </div>

                    <!-- Deskripsi -->
                    <div class="form-group-tugas full-width">
                        <label>
                            <i class="fas fa-align-left"></i>
                            Deskripsi <span class="required">*</span>
                        </label>
                        <textarea id="deskripsiUbah"
                            name="deskripsi"
                            placeholder="Tuliskan deskripsi tugas secara detail..."
                            required><?= esc($tugas['deskripsi'] ?? '') ?></textarea>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Sasaran Tugas (read-only, tidak bisa diubah di sini) -->
    <div class="form-section-tugas">
        <div class="form-section-header">
            <h3 class="form-section-title">
                <i class="fas fa-crosshairs"></i>
                Sasaran Tugas
            </h3>
            <small style="color:var(--text-white); font-size:12px;">
                <i class="fas fa-info-circle"></i>
                Sasaran tugas tidak dapat diubah setelah tugas dibuat
            </small>
        </div>
        <div class="form-section-body">
            <div id="sasaranUbahArea">
                <div style="text-align:center; padding:30px;">
                    <i class="fas fa-spinner fa-spin fa-2x" style="color:var(--primary);"></i>
                    <p style="margin-top:10px; color:var(--text-muted);">Memuat data sasaran...</p>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Bottom Actions -->
<div class="tambah-actions">
    <button class="btn-tugas btn-tugas-back" id="btnBatalUbah">
        <i class="fas fa-times"></i>
        Batal
    </button>
    <button class="btn-tugas btn-tugas-next" id="btnSimpanUbah">
        <i class="fas fa-save"></i>
        Simpan Perubahan
    </button>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="<?= base_url('assets/js/modules/tugas.js') ?>"></script>
<script>
    const UBAH_TUGAS_API_URL = '<?= base_url('dashboard/manajemen-tugas/tugas/update') ?>';
    const DETAIL_TUGAS_URL = '<?= base_url('dashboard/manajemen-tugas/tugas/detail') ?>';
    const SASARAN_API_URL = '<?= base_url('dashboard/manajemen-tugas/tugas') ?>';
    const TUGAS_ID = <?= (int) $tugas['id'] ?>;

    $(document).ready(function() {
        // Init flatpickr untuk deadline
        // [FIX] Gunakan deadline_fmt (d-m-Y) dari model sebagai value HTML.
        // flatpickr dengan dateFormat:'d-m-Y' akan parse "20-06-2025" dengan benar.
        if (typeof flatpickr !== 'undefined') {
            flatpickr('#deadlineUbah', {
                dateFormat: 'd-m-Y',
                allowInput: false,
                disableMobile: true,
                locale: 'id'
            });
        }

        // Init Select2 untuk kategori
        if (typeof $.fn.select2 !== 'undefined') {
            $('#kategoriTugasUbah').select2({
                placeholder: '-- Pilih Kategori Tugas --',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: () => 'Kategori tidak ditemukan',
                    searching: () => 'Mencari...'
                }
            });
        }

        // Auto-fill mode pengumpulan dari kategori yang dipilih
        $('#kategoriTugasUbah').on('change', function() {
            const mode = $(this).find('option:selected').data('mode') || '';
            $('#modelPengumpulanUbah').val(mode);
        });

        // Trigger sekali saat load untuk mengisi mode dari kategori yang sudah terpilih
        $('#kategoriTugasUbah').trigger('change');

        // Tombol kembali ke detail
        $('#btnBackToDetail, #btnBatalUbah').on('click', function() {
            window.location.href = DETAIL_TUGAS_URL + '/' + TUGAS_ID;
        });

        // Tombol simpan
        $('#btnSimpanUbah').on('click', function() {
            submitUbahTugas();
        });

        // Load sasaran (read-only)
        loadSasaranUbah(TUGAS_ID);
    });

    // ----------------------------------------------------------------
    // submitUbahTugas — AJAX update ke controller
    // ----------------------------------------------------------------
    function submitUbahTugas() {
        const namaTugas = $('#namaTugasUbah').val().trim();
        const kategoriId = $('#kategoriTugasUbah').val();
        const deadline = $('#deadlineUbah').val().trim();
        const jumlahTarget = $('#jumlahTargetUbah').val().trim();
        const deskripsi = $('#deskripsiUbah').val().trim();

        // Validasi
        if (!namaTugas) {
            showToast('warning', 'Nama Tugas tidak boleh kosong!');
            $('#namaTugasUbah').focus();
            return;
        }
        if (!kategoriId) {
            showToast('warning', 'Kategori Tugas harus dipilih!');
            return;
        }
        if (!deadline) {
            showToast('warning', 'Deadline tidak boleh kosong!');
            return;
        }
        if (!jumlahTarget || parseInt(jumlahTarget) < 1) {
            showToast('warning', 'Jumlah Target harus diisi dengan angka positif!');
            $('#jumlahTargetUbah').focus();
            return;
        }
        if (!deskripsi) {
            showToast('warning', 'Deskripsi tidak boleh kosong!');
            $('#deskripsiUbah').focus();
            return;
        }

        Swal.fire({
            title: 'Menyimpan perubahan...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: UBAH_TUGAS_API_URL + '/' + TUGAS_ID,
            method: 'POST',
            contentType: 'application/json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: JSON.stringify({
                nama_tugas: namaTugas,
                kategori_id: kategoriId,
                deadline: deadline,
                jumlah_target: jumlahTarget,
                deskripsi: deskripsi
            }),
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message || 'Tugas berhasil diperbarui.',
                    confirmButtonColor: '#0f766e',
                    confirmButtonText: 'Lihat Detail'
                }).then(() => {
                    window.location.href = DETAIL_TUGAS_URL + '/' + TUGAS_ID;
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan server.',
                    confirmButtonColor: '#0f766e'
                });
            }
        });
    }

    // ----------------------------------------------------------------
    // loadSasaranUbah — tampil read-only, sama dengan tugas_detail.php
    // ----------------------------------------------------------------
    function loadSasaranUbah(idTugas) {
        $.ajax({
            url: SASARAN_API_URL + '/' + idTugas + '/sasaran',
            method: 'GET',
            timeout: 10000,
            success: function(res) {
                renderSasaranUbah(res.data || []);
            },
            error: function() {
                $('#sasaranUbahArea').html(`
                    <div style="text-align:center; padding:30px; color:var(--text-muted);">
                        <i class="fas fa-info-circle fa-2x" style="margin-bottom:10px;"></i>
                        <p>Data sasaran belum tersedia atau gagal dimuat.</p>
                    </div>
                `);
            }
        });
    }

    function renderSasaranUbah(data) {
        if (!data || data.length === 0) {
            $('#sasaranUbahArea').html(`
                <div style="text-align:center; padding:30px; color:var(--text-muted);">
                    <i class="fas fa-users-slash fa-2x" style="margin-bottom:10px;"></i>
                    <p>Belum ada sasaran untuk tugas ini.</p>
                </div>
            `);
            return;
        }

        const tipeLabel = {
            'individu': {
                icon: 'fa-user',
                badge: 'badge-mandiri',
                label: 'PKL Mandiri'
            },
            'kelompok': {
                icon: 'fa-users',
                badge: 'badge-kelompok',
                label: 'Kelompok PKL'
            },
            'tim_tugas': {
                icon: 'fa-user-friends',
                badge: 'badge-tim',
                label: 'Tim Tugas'
            }
        };

        // Group by tipe
        const grouped = {};
        data.forEach(row => {
            if (!grouped[row.target_tipe]) grouped[row.target_tipe] = [];
            grouped[row.target_tipe].push(row);
        });

        let html = '';
        Object.entries(grouped).forEach(([tipe, rows]) => {
            const info = tipeLabel[tipe] || {
                icon: 'fa-question',
                badge: '',
                label: tipe
            };
            html += `
                <div style="margin-bottom:var(--space-lg);">
                    <div style="display:flex; align-items:center; gap:var(--space-sm); margin-bottom:var(--space-md);">
                        <span class="sasaran-type-badge ${info.badge}" style="font-size:13px;">
                            <i class="fas ${info.icon}"></i> ${info.label}
                        </span>
                        <span style="font-size:13px; color:var(--text-muted);">${rows.length} sasaran</span>
                    </div>
                    <div class="table-container">
                        <table class="table table-hover" style="width:100%;">
                            <thead>
                                <tr>
                                    <th style="width:5%;">No</th>
                                    <th>Nama</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
            `;
            rows.forEach((row, i) => {
                html += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${row.nama_sasaran || '-'}</td>
                        <td style="color:var(--text-muted); font-size:13px;">${row.keterangan || '-'}</td>
                    </tr>
                `;
            });
            html += `</tbody></table></div></div>`;
        });

        $('#sasaranUbahArea').html(html);
    }
</script>
<?= $this->endSection() ?>