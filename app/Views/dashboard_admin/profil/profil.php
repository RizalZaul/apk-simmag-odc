<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/modules/profil.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="profil-page">

    <!-- Page Header -->
    <div class="profil-page-header">
        <h2 class="profil-page-title">Profil Saya</h2>
        <p class="profil-page-sub">Data diri dan informasi akun</p>
    </div>

    <!-- Tab Navigation -->
    <div class="tab-navigation">
        <button class="tab-btn active" data-tab="bio">
            <i class="fas fa-user"></i>
            Bio Pribadi
        </button>
        <button class="tab-btn" data-tab="pengaturan">
            <i class="fas fa-cog"></i>
            Pengaturan Form Biodata PKL
        </button>
    </div>

    <!-- ════════════════════════════════════
         TAB 1: BIO PRIBADI
         ════════════════════════════════════ -->
    <div class="tab-content active" id="tab-bio">

        <!-- ── Card 1: Informasi Pribadi ── -->
        <div class="profil-card">

            <!-- Avatar + Info Utama -->
            <div class="profil-avatar-section">
                <div class="profil-avatar-default">
                    <i class="fas fa-user"></i>
                </div>

                <div class="profil-avatar-info">
                    <h3 class="profil-nama" id="displayNama"><?= esc($profil['nama_lengkap']) ?></h3>
                    <p class="profil-username">@<?= esc($profil['username']) ?></p>
                    <p class="profil-email">
                        <i class="fas fa-envelope"></i>
                        <?= esc($profil['email']) ?>
                    </p>
                </div>
            </div>

            <div class="profil-divider"></div>

            <!-- Form Data Diri -->
            <div class="profil-form-section">

                <div class="profil-form-header">
                    <h4 class="profil-form-title">
                        <i class="fas fa-id-card"></i>
                        Informasi Pribadi
                    </h4>
                    <button type="button" class="btn-profil-edit" id="btnEditProfil">
                        <i class="fas fa-pen"></i>
                        Edit
                    </button>
                </div>

                <form id="formProfil">
                    <?= csrf_field() ?>

                    <div class="profil-field-grid">

                        <!-- Nama Lengkap -->
                        <div class="profil-field-group">
                            <label class="profil-field-label">
                                <i class="fas fa-user"></i>
                                Nama Lengkap
                            </label>
                            <input type="text"
                                id="nama_lengkap"
                                name="nama_lengkap"
                                value="<?= esc($profil['nama_lengkap']) ?>"
                                class="profil-field-input"
                                disabled>
                        </div>

                        <!-- Nama Panggilan -->
                        <div class="profil-field-group">
                            <label class="profil-field-label">
                                <i class="fas fa-smile"></i>
                                Nama Panggilan
                            </label>
                            <input type="text"
                                id="nama_panggilan"
                                name="nama_panggilan"
                                value="<?= esc($profil['nama_panggilan']) ?>"
                                class="profil-field-input"
                                disabled>
                        </div>

                        <!-- Username (tidak bisa diubah) -->
                        <div class="profil-field-group">
                            <label class="profil-field-label">
                                <i class="fas fa-at"></i>
                                Username
                                <span class="profil-field-locked" title="Tidak dapat diubah">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </label>
                            <input type="text"
                                id="username"
                                value="<?= esc($profil['username']) ?>"
                                class="profil-field-input profil-field-readonly"
                                disabled
                                readonly>
                        </div>

                        <!-- Email (tidak bisa diubah) -->
                        <div class="profil-field-group">
                            <label class="profil-field-label">
                                <i class="fas fa-envelope"></i>
                                Email
                                <span class="profil-field-locked" title="Tidak dapat diubah">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </label>
                            <input type="email"
                                id="email"
                                value="<?= esc($profil['email']) ?>"
                                class="profil-field-input profil-field-readonly"
                                disabled
                                readonly>
                        </div>

                        <!-- No. WhatsApp -->
                        <div class="profil-field-group">
                            <label class="profil-field-label">
                                <i class="fab fa-whatsapp"></i>
                                No. WhatsApp
                            </label>
                            <input type="text"
                                id="no_wa_admin"
                                name="no_wa_admin"
                                value="<?= esc($profil['no_wa_admin']) ?>"
                                class="profil-field-input"
                                disabled>
                        </div>

                        <!-- Alamat (full width) -->
                        <div class="profil-field-group full-width">
                            <label class="profil-field-label">
                                <i class="fas fa-map-marker-alt"></i>
                                Alamat
                            </label>
                            <textarea id="alamat"
                                name="alamat"
                                rows="3"
                                class="profil-field-input profil-field-textarea"
                                disabled><?= esc($profil['alamat']) ?></textarea>
                        </div>

                    </div><!-- /.profil-field-grid -->

                    <!-- Form Actions (hanya tampil saat mode edit) -->
                    <div class="profil-form-actions" id="profilFormActions" style="display:none;">
                        <button type="button" class="btn-profil-cancel" id="btnCancelProfil">
                            <i class="fas fa-times"></i>
                            Batal
                        </button>
                        <button type="submit" class="btn-profil-save" id="btnSaveProfil">
                            <i class="fas fa-save"></i>
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div><!-- /.profil-form-section -->
        </div><!-- /.profil-card -->


        <!-- ── Card 2: Ubah Password ── -->
        <!--
            [BARU] Section ubah password diletakkan di bawah card bio pribadi,
            masih dalam tab yang sama agar tidak perlu pindah tab hanya untuk ganti password.
            Tidak meminta password lama — langsung input password baru + konfirmasi.
        -->
        <div class="profil-card profil-card-password">

            <div class="profil-form-section">

                <div class="profil-form-header">
                    <h4 class="profil-form-title">
                        <i class="fas fa-lock"></i>
                        Ubah Password
                    </h4>
                    <button type="button" class="btn-profil-edit" id="btnEditPassword">
                        <i class="fas fa-pen"></i>
                        Edit
                    </button>
                </div>

                <form id="formPassword">
                    <?= csrf_field() ?>

                    <div class="profil-field-grid">

                        <!-- Password Baru -->
                        <div class="profil-field-group">
                            <label class="profil-field-label">
                                <i class="fas fa-key"></i>
                                Password Baru
                            </label>
                            <div class="password-input-wrapper">
                                <input type="password"
                                    id="password_baru"
                                    name="password_baru"
                                    class="profil-field-input"
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
                        <div class="profil-field-group">
                            <label class="profil-field-label">
                                <i class="fas fa-key"></i>
                                Konfirmasi Password
                            </label>
                            <div class="password-input-wrapper">
                                <input type="password"
                                    id="konfirmasi_password"
                                    name="konfirmasi_password"
                                    class="profil-field-input"
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

                    </div><!-- /.profil-field-grid -->

                    <!-- Indikator kekuatan password (tampil saat mode edit) -->
                    <div class="password-strength-wrapper" id="passwordStrengthWrapper" style="display:none;">
                        <div class="password-strength-bar">
                            <div class="password-strength-fill" id="passwordStrengthFill"></div>
                        </div>
                        <span class="password-strength-label" id="passwordStrengthLabel">Masukkan password</span>
                    </div>

                    <!-- Form Actions -->
                    <div class="profil-form-actions" id="passwordFormActions" style="display:none;">
                        <button type="button" class="btn-profil-cancel" id="btnCancelPassword">
                            <i class="fas fa-times"></i>
                            Batal
                        </button>
                        <button type="submit" class="btn-profil-save" id="btnSavePassword">
                            <i class="fas fa-save"></i>
                            Simpan Password
                        </button>
                    </div>

                </form>
            </div><!-- /.profil-form-section -->
        </div><!-- /.profil-card-password -->

    </div><!-- /#tab-bio -->


    <!-- ════════════════════════════════════
         TAB 2: PENGATURAN FORM BIODATA PKL
         ════════════════════════════════════ -->
    <div class="tab-content" id="tab-pengaturan">

        <div class="profil-card">

            <div class="profil-form-section">
                <div class="profil-form-header">
                    <h4 class="profil-form-title">
                        <i class="fas fa-cog"></i>
                        Pengaturan Form Biodata PKL
                    </h4>
                </div>

                <div class="pengaturan-item">

                    <div class="pengaturan-item-info">
                        <div class="pengaturan-item-title">
                            <i class="fas fa-file-alt"></i>
                            Form Biodata PKL
                        </div>
                        <div class="pengaturan-item-desc">
                            Ketika diaktifkan, siswa PKL dapat membuka form biodata.
                            Ketika dinonaktifkan, form biodata tidak dapat diakses.
                        </div>
                    </div>

                    <!-- Toggle Switch -->
                    <div class="toggle-wrapper">
                        <label class="toggle-switch">
                            <input type="checkbox"
                                id="toggleFormBiodata"
                                <?= $formBiodataAktif ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                        <span class="toggle-label" id="toggleLabel">
                            <?= $formBiodataAktif ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </div>
                </div><!-- /.pengaturan-item -->

                <!-- Status Info Box -->
                <div class="pengaturan-status-box <?= $formBiodataAktif ? 'status-aktif' : 'status-nonaktif' ?>"
                    id="pengaturanStatusBox">
                    <?php if ($formBiodataAktif): ?>
                        <i class="fas fa-check-circle"></i>
                        <span>Form biodata PKL sedang <strong>terbuka</strong>. Siswa dapat mengisi dan mengubah data mereka.</span>
                    <?php else: ?>
                        <i class="fas fa-times-circle"></i>
                        <span>Form biodata PKL sedang <strong>ditutup</strong>. Siswa tidak dapat mengakses form biodata saat ini.</span>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div><!-- /#tab-pengaturan -->

</div><!-- /.profil-page -->

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
    $(document).ready(function() {

        // ══════════════════════════════════════════════════════════════
        // CSRF HELPERS
        // Baca dari meta tag agar selalu fresh setelah setiap AJAX
        // ══════════════════════════════════════════════════════════════

        function getCsrfData() {
            return {
                name: $('meta[name="csrf-token-name"]').attr('content'),
                hash: $('meta[name="csrf-token-hash"]').attr('content'),
            };
        }

        function refreshCsrfToken(response) {
            if (response && response.csrf) {
                $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                $('meta[name="csrf-token-name"]').attr('content', response.csrf.name);
                $('meta[name="csrf-token-hash"]').attr('content', response.csrf.hash);
            }
        }


        /* ══════════════════════════════════
         * TAB SWITCHING
         * ══════════════════════════════════ */
        $('.tab-btn').on('click', function() {
            const tab = $(this).data('tab');
            if ($(this).hasClass('active')) return;

            $('.tab-btn').removeClass('active');
            $(this).addClass('active');

            $('.tab-content').removeClass('active');
            $('#tab-' + tab).addClass('active');
        });


        /* ══════════════════════════════════
         * EDIT PROFIL — Informasi Pribadi
         * ══════════════════════════════════ */
        const editableFields = ['#nama_lengkap', '#nama_panggilan', '#no_wa_admin', '#alamat'];

        const originalValues = {};
        editableFields.forEach(sel => {
            originalValues[sel] = $(sel).val();
        });

        function enableEditMode() {
            editableFields.forEach(sel => {
                $(sel).prop('disabled', false).addClass('profil-field-editable');
            });
            $('#profilFormActions').slideDown(200);
            $('#btnEditProfil').hide();
            $('#nama_lengkap').focus();
        }

        function disableEditMode(restoreValues = false) {
            if (restoreValues) {
                editableFields.forEach(sel => $(sel).val(originalValues[sel]));
            }
            editableFields.forEach(sel => {
                $(sel).prop('disabled', true).removeClass('profil-field-editable');
            });
            $('#profilFormActions').slideUp(200);
            $('#btnEditProfil').show();
        }

        $('#btnEditProfil').on('click', enableEditMode);
        $('#btnCancelProfil').on('click', function() {
            disableEditMode(true);
        });

        $('#formProfil').on('submit', function(e) {
            e.preventDefault();

            const namaLengkap = $('#nama_lengkap').val().trim();
            if (!namaLengkap) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Field Wajib Diisi',
                    text: 'Nama Lengkap tidak boleh kosong.',
                    confirmButtonColor: '#0f766e',
                });
                $('#nama_lengkap').focus();
                return;
            }

            $('#btnSaveProfil').prop('disabled', true);
            Swal.fire({
                title: 'Menyimpan...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const csrf = getCsrfData();

            $.ajax({
                url: '<?= base_url('dashboard/profil/update') ?>',
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: {
                    nama_lengkap: namaLengkap,
                    nama_panggilan: $('#nama_panggilan').val().trim(),
                    no_wa_admin: $('#no_wa_admin').val().trim(),
                    alamat: $('#alamat').val().trim(),
                    [csrf.name]: csrf.hash,
                },
                success: function(res) {
                    refreshCsrfToken(res);

                    // Update originalValues agar Batal setelah simpan tidak revert
                    editableFields.forEach(sel => {
                        originalValues[sel] = $(sel).val();
                    });

                    // Update nama di header avatar
                    $('#displayNama').text(res.data.nama_lengkap);

                    disableEditMode(false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Profil Diperbarui!',
                        text: res.message,
                        timer: 1800,
                        showConfirmButton: false,
                    });
                },
                error: function(xhr) {
                    refreshCsrfToken(xhr.responseJSON);

                    if (xhr.status === 419) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Sesi Kedaluwarsa',
                            text: 'Halaman akan dimuat ulang secara otomatis.',
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(() => location.reload());
                        return;
                    }

                    const message = xhr.responseJSON?.message ?? 'Terjadi kesalahan. Silakan coba lagi.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: message,
                        confirmButtonColor: '#0f766e'
                    });
                },
                complete: function() {
                    $('#btnSaveProfil').prop('disabled', false);
                },
            });
        });


        /* ══════════════════════════════════
         * UBAH PASSWORD
         * [BARU] Tanpa verifikasi password lama.
         * Validasi di sisi klien: min 6 karakter, dua field harus cocok.
         * Validasi di sisi server: rules CI4 di Dashboard::updatePassword().
         * ══════════════════════════════════ */

        function enablePasswordMode() {
            $('#password_baru, #konfirmasi_password').prop('disabled', false).addClass('profil-field-editable');
            $('#passwordFormActions').slideDown(200);
            $('#passwordStrengthWrapper').slideDown(200);
            $('#btnEditPassword').hide();
            $('#password_baru').focus();
        }

        function disablePasswordMode() {
            // Kosongkan field dan kembalikan ke state disabled
            $('#password_baru, #konfirmasi_password')
                .val('')
                .prop('disabled', true)
                .prop('type', 'password') // reset show/hide ke default
                .removeClass('profil-field-editable');

            // Reset ikon mata ke state awal
            $('.btn-toggle-password').find('i')
                .removeClass('fa-eye-slash')
                .addClass('fa-eye');

            // Reset indikator kekuatan
            $('#passwordStrengthFill').css('width', '0%').attr('class', 'password-strength-fill');
            $('#passwordStrengthLabel').text('Masukkan password');
            $('#passwordStrengthWrapper').slideUp(200);

            $('#passwordFormActions').slideUp(200);
            $('#btnEditPassword').show();
        }

        $('#btnEditPassword').on('click', enablePasswordMode);
        $('#btnCancelPassword').on('click', disablePasswordMode);

        // ── Toggle show / hide password ──────────────────────────────
        $(document).on('click', '.btn-toggle-password', function() {
            const target = $(this).data('target');
            const $input = $(target);
            const isPass = $input.attr('type') === 'password';
            $input.attr('type', isPass ? 'text' : 'password');
            $(this).find('i')
                .toggleClass('fa-eye', !isPass)
                .toggleClass('fa-eye-slash', isPass);
        });

        // ── Indikator kekuatan password (real-time) ──────────────────
        $('#password_baru').on('input', function() {
            const val = $(this).val();
            const strength = _calcPasswordStrength(val);
            const $fill = $('#passwordStrengthFill');
            const $label = $('#passwordStrengthLabel');

            $fill.css('width', strength.pct + '%')
                .attr('class', 'password-strength-fill strength-' + strength.level);
            $label.text(strength.label);
        });

        function _calcPasswordStrength(password) {
            if (!password) return {
                pct: 0,
                level: '',
                label: 'Masukkan password'
            };

            // BUG-03 FIX: Sesuaikan scoring dengan aturan validasi baru (min 8 char + kompleksitas).
            let score = 0;
            if (password.length >= 8) score++;
            if (password.length >= 12) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[a-z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;

            const map = {
                0: {
                    pct: 10,
                    level: 'weak',
                    label: 'Sangat lemah'
                },
                1: {
                    pct: 20,
                    level: 'weak',
                    label: 'Lemah'
                },
                2: {
                    pct: 40,
                    level: 'weak',
                    label: 'Kurang'
                },
                3: {
                    pct: 60,
                    level: 'medium',
                    label: 'Cukup'
                },
                4: {
                    pct: 75,
                    level: 'medium',
                    label: 'Sedang'
                },
                5: {
                    pct: 90,
                    level: 'strong',
                    label: 'Kuat'
                },
                6: {
                    pct: 100,
                    level: 'strong',
                    label: 'Sangat kuat'
                },
            };
            return map[score] ?? map[0];
        }

        // ── Submit form password ──────────────────────────────────────
        // BUG-03 FIX: Validasi kompleksitas password di sisi klien.
        // Aturan: min 8 char + huruf besar + huruf kecil + angka + simbol.
        const REGEX_PASSWORD = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}$/;

        $('#formPassword').on('submit', function(e) {
            e.preventDefault();

            const passwordBaru = $('#password_baru').val().trim();
            const konfirmasiPassword = $('#konfirmasi_password').val().trim();

            // Validasi sisi klien
            if (!passwordBaru) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Field Wajib Diisi',
                    text: 'Password baru tidak boleh kosong.',
                    confirmButtonColor: '#0f766e'
                });
                $('#password_baru').focus();
                return;
            }

            if (passwordBaru.length < 8) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Password Terlalu Pendek',
                    text: 'Password minimal 8 karakter.',
                    confirmButtonColor: '#0f766e'
                });
                $('#password_baru').focus();
                return;
            }

            if (!REGEX_PASSWORD.test(passwordBaru)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Password Kurang Kuat',
                    html: 'Password harus mengandung:<br>' +
                        '<ul style="text-align:left;margin:8px 0 0;padding-left:20px;">' +
                        '<li>Minimal 8 karakter</li>' +
                        '<li>Huruf besar (A–Z)</li>' +
                        '<li>Huruf kecil (a–z)</li>' +
                        '<li>Angka (0–9)</li>' +
                        '<li>Simbol (!@#$% dll.)</li>' +
                        '</ul>',
                    confirmButtonColor: '#0f766e',
                });
                $('#password_baru').focus();
                return;
            }

            if (passwordBaru !== konfirmasiPassword) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Password Tidak Cocok',
                    text: 'Konfirmasi password tidak sesuai dengan password baru.',
                    confirmButtonColor: '#0f766e'
                });
                $('#konfirmasi_password').focus();
                return;
            }

            $('#btnSavePassword').prop('disabled', true);
            Swal.fire({
                title: 'Menyimpan password...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const csrf = getCsrfData();

            $.ajax({
                url: '<?= base_url('dashboard/profil/update-password') ?>',
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: {
                    password_baru: passwordBaru,
                    konfirmasi_password: konfirmasiPassword,
                    [csrf.name]: csrf.hash,
                },
                success: function(res) {
                    refreshCsrfToken(res);
                    disablePasswordMode();
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Diperbarui!',
                        text: res.message,
                        timer: 1800,
                        showConfirmButton: false,
                    });
                },
                error: function(xhr) {
                    refreshCsrfToken(xhr.responseJSON);

                    if (xhr.status === 419) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Sesi Kedaluwarsa',
                            text: 'Halaman akan dimuat ulang secara otomatis.',
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(() => location.reload());
                        return;
                    }

                    const message = xhr.responseJSON?.message ?? 'Terjadi kesalahan. Silakan coba lagi.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: message,
                        confirmButtonColor: '#0f766e'
                    });
                },
                complete: function() {
                    $('#btnSavePassword').prop('disabled', false);
                },
            });
        });


        /* ══════════════════════════════════
         * TOGGLE FORM BIODATA PKL
         * Simpan ke DB via AJAX saat toggle berubah.
         * Revert ke state sebelumnya jika request gagal.
         * ══════════════════════════════════ */

        let lastToggleState = $('#toggleFormBiodata').is(':checked');

        $('#toggleFormBiodata').on('change', function() {
            const isAktif = $(this).is(':checked');
            const $toggle = $(this);
            const $label = $('#toggleLabel');
            const $statusBox = $('#pengaturanStatusBox');

            $toggle.prop('disabled', true);
            Swal.fire({
                title: 'Menyimpan pengaturan...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const csrf = getCsrfData();

            $.ajax({
                url: '<?= base_url('dashboard/profil/setting/biodata') ?>',
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: {
                    form_biodata_aktif: isAktif ? 1 : 0,
                    [csrf.name]: csrf.hash,
                },
                success: function(res) {
                    refreshCsrfToken(res);

                    lastToggleState = isAktif;
                    $label.text(isAktif ? 'Aktif' : 'Nonaktif');
                    $statusBox
                        .removeClass('status-aktif status-nonaktif')
                        .addClass(isAktif ? 'status-aktif' : 'status-nonaktif')
                        .html(isAktif ?
                            '<i class="fas fa-check-circle"></i> <span>Form biodata PKL sedang <strong>terbuka</strong>. Siswa dapat mengisi dan mengubah data mereka.</span>' :
                            '<i class="fas fa-times-circle"></i> <span>Form biodata PKL sedang <strong>ditutup</strong>. Siswa tidak dapat mengakses form biodata saat ini.</span>'
                        );

                    Swal.fire({
                        icon: 'success',
                        title: isAktif ? 'Form Diaktifkan' : 'Form Dinonaktifkan',
                        text: res.message,
                        timer: 1800,
                        showConfirmButton: false,
                    });
                },
                error: function(xhr) {
                    refreshCsrfToken(xhr.responseJSON);

                    // Revert toggle ke state sebelumnya jika gagal
                    $toggle.prop('checked', lastToggleState);

                    if (xhr.status === 419) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Sesi Kedaluwarsa',
                            text: 'Halaman akan dimuat ulang secara otomatis.',
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(() => location.reload());
                        return;
                    }

                    const message = xhr.responseJSON?.message ?? 'Terjadi kesalahan. Silakan coba lagi.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: message,
                        confirmButtonColor: '#0f766e'
                    });
                },
                complete: function() {
                    $toggle.prop('disabled', false);
                },
            });
        });

    });
</script>
<?= $this->endSection() ?>