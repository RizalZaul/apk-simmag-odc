<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/modules/data-diri.css') ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1>Profil Saya</h1>
    <p class="page-subtitle">Data diri dan informasi PKL</p>
</div>

<!-- ================= PROFILE CARD ================= -->
<div class="profile-section">

    <!-- Profile Header -->
    <div class="profile-header-card">

        <!-- Avatar: icon FA, tidak ada kolom foto di DB -->
        <div class="profile-avatar-section">
            <div class="profile-avatar">
                <?php
                // Tampilkan ikon berbeda berdasarkan jenis kelamin
                $ikonfaUser = ($siswa['jenis_kelamin'] ?? '') === 'P'
                    ? 'fa-user'
                    : 'fa-user';
                ?>
                <i class="fas <?= $ikonfaUser ?>"></i>
            </div>
        </div>

        <div class="profile-header-info">
            <h2><?= esc($siswa['nama'] ?? 'Nama Siswa') ?></h2>
            <p class="profile-username">@<?= esc($siswa['username'] ?? 'username') ?></p>
            <div class="profile-badges">
                <?php if (($siswa['jenis_pkl'] ?? 'mandiri') === 'instansi'): ?>
                    <span class="badge badge-primary">
                        <i class="fas fa-building"></i> PKL Instansi
                    </span>
                <?php else: ?>
                    <span class="badge badge-primary">
                        <i class="fas fa-graduation-cap"></i> PKL Mandiri
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Edit Mode Toggle -->
    <div class="edit-mode-bar" id="editModeBar">
        <button type="button" class="btn btn-edit" id="btnEdit" onclick="enableEditMode()">
            <i class="fas fa-edit"></i> Edit Profil
        </button>
    </div>

    <!-- Profile Form -->
    <form id="formDataDiri" class="profile-form">

        <?= csrf_field() ?><!-- [FIX] CSRF token wajib untuk POST AJAX dengan FormData -->

        <!-- 1. INFORMASI PRIBADI -->
        <div class="form-section">
            <h3 class="section-title">
                <i class="fas fa-user-circle"></i>
                Informasi Pribadi
            </h3>

            <div class="form-grid">

                <div class="form-group">
                    <label for="nama">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" id="nama" name="nama" class="form-control editable"
                        value="<?= esc($siswa['nama'] ?? '') ?>" required readonly>
                </div>

                <div class="form-group">
                    <label for="nama_panggilan">Nama Panggilan <span class="required">*</span></label>
                    <input type="text" id="nama_panggilan" name="nama_panggilan" class="form-control editable"
                        value="<?= esc($siswa['nama_panggilan'] ?? '') ?>" required readonly>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                        value="<?= esc($siswa['email'] ?? '') ?>" readonly>
                    <small class="form-hint readonly-hint">
                        <i class="fas fa-lock"></i> Tidak dapat diedit
                    </small>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control"
                        value="<?= esc($siswa['username'] ?? '') ?>" readonly>
                    <small class="form-hint readonly-hint">
                        <i class="fas fa-lock"></i> Tidak dapat diedit
                    </small>
                </div>

                <div class="form-group">
                    <label for="tempat_lahir">Tempat Lahir</label>
                    <input type="text" id="tempat_lahir" name="tempat_lahir" class="form-control editable"
                        value="<?= esc($siswa['tempat_lahir'] ?? '') ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="tanggal_lahir">Tanggal Lahir</label>
                    <input type="hidden" id="tanggal_lahir" name="tanggal_lahir"
                        value="<?= esc($siswa['tanggal_lahir'] ?? '') ?>">
                    <input type="text" id="tanggal_lahir_display"
                        class="form-control editable flatpickr-input-custom"
                        placeholder="Pilih tanggal lahir" readonly>
                </div>

                <div class="form-group">
                    <label for="no_wa">No. WhatsApp</label>
                    <input type="tel" id="no_wa" name="no_wa" class="form-control editable"
                        value="<?= esc($siswa['no_wa'] ?? '') ?>"
                        placeholder="08xxxxxxxxxx" readonly>
                </div>

                <div class="form-group">
                    <label for="jenis_kelamin">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-control editable" disabled>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L" <?= ($siswa['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= ($siswa['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label for="alamat">Alamat Lengkap</label>
                    <textarea id="alamat" name="alamat" class="form-control editable"
                        rows="3" readonly><?= esc($siswa['alamat'] ?? '') ?></textarea>
                </div>

            </div>
        </div>

        <!-- 2. PERIODE PKL -->
        <div class="form-section">
            <h3 class="section-title">
                <i class="fas fa-calendar-alt"></i>
                Periode PKL
            </h3>

            <div class="form-grid">

                <div class="form-group">
                    <label>Tanggal Mulai</label>
                    <input type="text" class="form-control"
                        value="<?= ! empty($siswa['tgl_mulai'])
                                    ? date('d M Y', strtotime($siswa['tgl_mulai']))
                                    : '-' ?>"
                        readonly>
                    <small class="form-hint readonly-hint">
                        <i class="fas fa-lock"></i> Dikelola admin
                    </small>
                </div>

                <div class="form-group">
                    <label>Tanggal Akhir</label>
                    <input type="text" class="form-control"
                        value="<?= ! empty($siswa['tgl_akhir'])
                                    ? date('d M Y', strtotime($siswa['tgl_akhir']))
                                    : '-' ?>"
                        readonly>
                    <small class="form-hint readonly-hint">
                        <i class="fas fa-lock"></i> Dikelola admin
                    </small>
                </div>

                <div class="form-group full-width">
                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Durasi PKL:</strong>
                            <span id="durasiPKL">-</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- 3. INFO INSTANSI + PEMBIMBING + KELOMPOK (hanya jika PKL Instansi) -->
        <?php if (($siswa['jenis_pkl'] ?? 'mandiri') === 'instansi'): ?>

            <!-- Instansi -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-building"></i>
                    Informasi Instansi
                </h3>

                <div class="form-grid">

                    <div class="form-group full-width">
                        <label>Nama Instansi</label>
                        <input type="text" class="form-control"
                            value="<?= esc($siswa['nama_instansi'] ?? '-') ?>" readonly>
                        <small class="form-hint readonly-hint">
                            <i class="fas fa-lock"></i> Dikelola admin
                        </small>
                    </div>

                    <div class="form-group full-width">
                        <label>Alamat Instansi</label>
                        <textarea class="form-control" rows="3" readonly><?= esc($siswa['alamat_instansi'] ?? '-') ?></textarea>
                        <small class="form-hint readonly-hint">
                            <i class="fas fa-lock"></i> Dikelola admin
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="jurusan_instansi">Jurusan / Divisi</label>
                        <input type="text" id="jurusan_instansi" name="jurusan" class="form-control editable"
                            value="<?= esc($siswa['jurusan_instansi'] ?? '') ?>"
                            placeholder="Contoh: IT Support, Marketing, dll" readonly>
                        <small class="form-hint edit-hint">
                            <i class="fas fa-edit"></i> Dapat diedit saat mode edit
                        </small>
                    </div>

                </div>
            </div>

            <!-- Pembimbing -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-user-tie"></i>
                    Pembimbing PKL
                </h3>

                <div class="form-grid">

                    <div class="form-group">
                        <label>Nama Pembimbing</label>
                        <input type="text" class="form-control"
                            value="<?= esc($siswa['nama_pembimbing'] ?? '-') ?>" readonly>
                        <small class="form-hint readonly-hint">
                            <i class="fas fa-lock"></i> Dikelola admin
                        </small>
                    </div>

                    <div class="form-group">
                        <label>No. WA Pembimbing</label>
                        <input type="text" class="form-control"
                            value="<?= esc($siswa['no_wa_pembimbing'] ?? '-') ?>" readonly>
                        <small class="form-hint readonly-hint">
                            <i class="fas fa-lock"></i> Dikelola admin
                        </small>
                    </div>

                </div>
            </div>

            <!-- Kelompok -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-users"></i>
                    Informasi Kelompok
                </h3>

                <div class="kelompok-info">

                    <div class="kelompok-item">
                        <div class="kelompok-label">
                            <i class="fas fa-crown"></i> Ketua Kelompok
                        </div>
                        <div class="kelompok-value">
                            <?php if (! empty($siswa['ketua_kelompok'])): ?>
                                <strong><?= esc($siswa['ketua_kelompok']) ?></strong>
                            <?php else: ?>
                                <span class="text-muted">Belum ada ketua</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="kelompok-item">
                        <div class="kelompok-label">
                            <i class="fas fa-user-friends"></i> Anggota Kelompok
                        </div>
                        <div class="kelompok-list">
                            <?php
                            $anggota = ! empty($siswa['anggota_kelompok'])
                                ? json_decode($siswa['anggota_kelompok'], true)
                                : [];
                            ?>
                            <?php if (! empty($anggota)): ?>
                                <?php foreach ($anggota as $i => $nama): ?>
                                    <div class="anggota-item">
                                        <span class="anggota-number"><?= $i + 1 ?>.</span>
                                        <span class="anggota-name"><?= esc($nama) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted">Belum ada anggota</span>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

        <?php endif; ?>

        <!-- Form Actions (hidden by default, muncul saat mode edit) -->
        <div class="form-actions" id="formActions" style="display: none;">
            <button type="button" class="btn btn-secondary" onclick="cancelEdit()">
                <i class="fas fa-times"></i> Batal
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </div>

    </form>


<!-- ── Card: Ubah Password ── -->
<div class="profile-section" style="margin-top: 24px;">

    <div class="profile-form" style="padding-top: 0;">
        <div class="form-section">

            <div class="profil-form-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 22px; padding-bottom: 14px; border-bottom: 2px solid var(--slate-300);">
                <h3 class="section-title" style="margin:0; border:none; padding:0;">
                    <i class="fas fa-lock"></i>
                    Ubah Password
                </h3>
                <button type="button" class="btn btn-edit" id="btnEditPassword" style="margin:0; box-shadow:none;">
                    <i class="fas fa-pen"></i> Edit
                </button>
            </div>

            <form id="formPassword">
                <?= csrf_field() ?>

                <div class="form-grid">

                    <!-- Password Baru -->
                    <div class="form-group">
                        <label for="password_baru">
                            <i class="fas fa-key"></i>
                            Password Baru
                        </label>
                        <div class="password-input-wrapper">
                            <input type="password"
                                id="password_baru"
                                name="password_baru"
                                class="form-control password-field"
                                placeholder="Min. 8 karakter, huruf besar/kecil, angka & simbol"
                                autocomplete="new-password"
                                disabled>
                            <button type="button"
                                class="btn-toggle-password"
                                data-target="#password_baru"
                                tabindex="-1"
                                title="Tampilkan / sembunyikan password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="form-group">
                        <label for="konfirmasi_password">
                            <i class="fas fa-key"></i>
                            Konfirmasi Password
                        </label>
                        <div class="password-input-wrapper">
                            <input type="password"
                                id="konfirmasi_password"
                                name="konfirmasi_password"
                                class="form-control password-field"
                                placeholder="Ulangi password baru"
                                autocomplete="new-password"
                                disabled>
                            <button type="button"
                                class="btn-toggle-password"
                                data-target="#konfirmasi_password"
                                tabindex="-1"
                                title="Tampilkan / sembunyikan password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                </div><!-- /.form-grid -->

                <!-- Indikator kekuatan password -->
                <div class="password-strength-wrapper" id="passwordStrengthWrapper" style="display:none;">
                    <div class="password-strength-bar">
                        <div class="password-strength-fill" id="passwordStrengthFill"></div>
                    </div>
                    <span class="password-strength-label" id="passwordStrengthLabel">Masukkan password</span>
                </div>

                <!-- Form Actions -->
                <div class="form-actions" id="passwordFormActions" style="display:none;">
                    <button type="button" class="btn btn-secondary" id="btnCancelPassword">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSavePassword">
                        <i class="fas fa-save"></i> Simpan Password
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<?php
/*
 * [FIX BUG 1 & 2]
 * Sebelumnya menggunakan `const DATA_DIRI_CONFIG = { ... }`.
 * `const` di top-level script TIDAK membuat property di `window` (perilaku ES6).
 * Akibatnya `window.DATA_DIRI_CONFIG` selalu undefined → fetch ke URL kosong
 * → update tidak terkirim, durasi selalu menampilkan '-'.
 *
 * Solusi: pakai assignment langsung ke `window.DATA_DIRI_CONFIG`.
 */
?>
<script>
    window.DATA_DIRI_CONFIG = {
        updateUrl: '<?= base_url('pkl/profil/update') ?>',
        updatePasswordUrl: '<?= base_url('pkl/profil/update-password') ?>',
        tanggalLahir: '<?= esc($siswa['tanggal_lahir'] ?? '') ?>',
        tglMulai: '<?= esc($siswa['tgl_mulai']     ?? '') ?>',
        tglAkhir: '<?= esc($siswa['tgl_akhir']     ?? '') ?>',
    };
</script>

<script src="<?= base_url('assets/js/modules/data-diri.js') ?>"></script>
<?= $this->endSection() ?>