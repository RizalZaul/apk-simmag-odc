<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/modules/tugas.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="tambah-tugas-container">

    <!-- Header -->
    <div class="tambah-header">
        <button class="btn-back-link" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i>
            Kembali ke List Tugas
        </button>
        <h1 class="tambah-title">
            <i class="fas fa-eye"></i>
            Detail Tugas
        </h1>
    </div>

    <!-- Info Tugas -->
    <div class="form-section-tugas">
        <div class="form-section-header">
            <h3 class="form-section-title">
                <i class="fas fa-clipboard-list"></i>
                Informasi Tugas
            </h3>
            <a href="<?= base_url('dashboard/manajemen-tugas/tugas/ubah/' . $tugas['id']) ?>"
               class="btn-tugas btn-tugas-next" style="text-decoration:none;">
                <i class="fas fa-pen"></i>
                Edit Tugas
            </a>
        </div>
        <div class="form-section-body">
            <div class="form-grid">

                <!-- Editor -->
                <div class="form-group-tugas">
                    <label><i class="fas fa-user"></i> Editor</label>
                    <input type="text" value="<?= esc($tugas['editor']) ?>" class="readonly-field" readonly>
                </div>

                <!-- Nama Tugas -->
                <div class="form-group-tugas">
                    <label><i class="fas fa-tasks"></i> Nama Tugas</label>
                    <input type="text" value="<?= esc($tugas['nama_tugas']) ?>" class="readonly-field" readonly>
                </div>

                <!-- Kategori Tugas -->
                <div class="form-group-tugas">
                    <label><i class="fas fa-tags"></i> Kategori Tugas</label>
                    <input type="text" value="<?= esc($tugas['kategori_tugas']) ?>" class="readonly-field" readonly>
                </div>

                <!-- Mode Pengumpulan -->
                <div class="form-group-tugas">
                    <label><i class="fas fa-layer-group"></i> Mode Pengumpulan</label>
                    <input type="text" value="<?= esc($tugas['mode_pengumpulan']) ?>" class="readonly-field" readonly>
                </div>

                <!-- Deadline -->
                <div class="form-group-tugas">
                    <label><i class="fas fa-calendar-times"></i> Deadline</label>
                    <input type="text" value="<?= esc($tugas['deadline']) ?>" class="readonly-field" readonly>
                </div>

                <!-- Jumlah Target -->
                <div class="form-group-tugas">
                    <label><i class="fas fa-bullseye"></i> Jumlah Target</label>
                    <input type="text" value="<?= esc($tugas['target_jumlah'] ?? '-') ?>" class="readonly-field" readonly>
                </div>

                <!-- Tanggal Dibuat -->
                <div class="form-group-tugas">
                    <label><i class="fas fa-calendar-plus"></i> Tanggal Dibuat</label>
                    <input type="text" value="<?= esc($tugas['tgl_dibuat']) ?>" class="readonly-field" readonly>
                </div>

                <!-- Deskripsi -->
                <div class="form-group-tugas full-width">
                    <label><i class="fas fa-align-left"></i> Deskripsi</label>
                    <textarea class="readonly-field" readonly rows="4"><?= esc($tugas['deskripsi'] ?? '-') ?></textarea>
                </div>

            </div>
        </div>
    </div>

    <!-- Sasaran Tugas -->
    <div class="form-section-tugas">
        <div class="form-section-header">
            <h3 class="form-section-title">
                <i class="fas fa-crosshairs"></i>
                Sasaran Tugas
            </h3>
        </div>
        <div class="form-section-body">
            <div id="sasaranDetailArea">
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
    <button class="btn-tugas btn-tugas-back" onclick="window.history.back()">
        <i class="fas fa-arrow-left"></i>
        Kembali
    </button>
    <a href="<?= base_url('dashboard/manajemen-tugas/tugas/ubah/' . $tugas['id']) ?>"
       class="btn-tugas btn-tugas-next" style="text-decoration:none;">
        <i class="fas fa-pen"></i>
        Edit Tugas
    </a>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="<?= base_url('assets/js/modules/tugas.js') ?>"></script>
<script>
    const SASARAN_URL = '<?= base_url('dashboard/manajemen-tugas/sasaran') ?>';
    const ID_TUGAS    = <?= (int) $tugas['id'] ?>;

    $(document).ready(function () {
        loadSasaranDetail(ID_TUGAS);
    });

    function loadSasaranDetail(idTugas) {
        $.ajax({
            url: '<?= base_url('dashboard/manajemen-tugas/tugas') ?>/' + idTugas + '/sasaran',
            method: 'GET',
            timeout: 10000,
            success: function (res) {
                renderSasaran(res.data || []);
            },
            error: function () {
                $('#sasaranDetailArea').html(`
                    <div style="text-align:center; padding:30px; color:var(--text-muted);">
                        <i class="fas fa-info-circle fa-2x" style="margin-bottom:10px;"></i>
                        <p>Data sasaran belum tersedia atau gagal dimuat.</p>
                    </div>
                `);
            }
        });
    }

    function renderSasaran(data) {
        if (!data || data.length === 0) {
            $('#sasaranDetailArea').html(`
                <div style="text-align:center; padding:30px; color:var(--text-muted);">
                    <i class="fas fa-users-slash fa-2x" style="margin-bottom:10px;"></i>
                    <p>Belum ada sasaran untuk tugas ini.</p>
                </div>
            `);
            return;
        }

        const tipeLabel = {
            'individu':  { icon: 'fa-user',         badge: 'badge-mandiri',  label: 'PKL Mandiri' },
            'kelompok':  { icon: 'fa-users',         badge: 'badge-kelompok', label: 'Kelompok PKL' },
            'tim_tugas': { icon: 'fa-user-friends',  badge: 'badge-tim',      label: 'Tim Tugas' }
        };

        // Group by tipe
        const grouped = {};
        data.forEach(row => {
            if (!grouped[row.target_tipe]) grouped[row.target_tipe] = [];
            grouped[row.target_tipe].push(row);
        });

        let html = '';
        Object.entries(grouped).forEach(([tipe, rows]) => {
            const info = tipeLabel[tipe] || { icon: 'fa-question', badge: '', label: tipe };
            html += `
                <div style="margin-bottom: var(--space-lg);">
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
                const nama = row.nama_sasaran || '-';
                const ket  = row.keterangan  || '-';
                html += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${nama}</td>
                        <td style="color:var(--text-muted); font-size:13px;">${ket}</td>
                    </tr>
                `;
            });

            html += `</tbody></table></div></div>`;
        });

        $('#sasaranDetailArea').html(html);
    }
</script>
<?= $this->endSection() ?>