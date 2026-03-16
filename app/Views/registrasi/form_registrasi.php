<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi PKL — SIMMAG ODC</title>

    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token-hash" content="<?= csrf_hash() ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

    <!-- ==================== FLATPICKR ==================== -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_green.css">

    <!-- ==================== CORE CSS ==================== -->
    <link rel="stylesheet" href="<?= base_url('assets/css/core/variables.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/core/reset.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/core/utilities.css') ?>">

    <link rel="stylesheet" href="<?= base_url('assets/css/modules/registrasi.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/modules/manajemen-pkl.css') ?>">
</head>

<body>

    <div class="reg-page">
        <div class="reg-container">

            <!-- ── Brand ── -->
            <div class="reg-brand">
                <div class="reg-brand-title">SIMMAG ODC</div>
                <div class="reg-brand-sub">Form Pendaftaran PKL</div>
            </div>

            <!-- ══════════════════════════════════════
         STEP INDICATOR
         ══════════════════════════════════════ -->
            <div class="step-indicator" id="stepIndicator">

                <div class="step-item active" id="si-1">
                    <div class="step-dot active" id="sd-1">
                        <span class="step-num">1</span>
                        <i class="fas fa-check step-check"></i>
                    </div>
                    <span class="step-label">Data PKL</span>
                </div>

                <div class="step-connector" id="sc-1"></div>

                <div class="step-item" id="si-2">
                    <div class="step-dot" id="sd-2">
                        <span class="step-num">2</span>
                        <i class="fas fa-check step-check"></i>
                    </div>
                    <span class="step-label">Biodata</span>
                </div>

                <div class="step-connector" id="sc-2"></div>

                <div class="step-item" id="si-3">
                    <div class="step-dot" id="sd-3">
                        <span class="step-num">3</span>
                        <i class="fas fa-check step-check"></i>
                    </div>
                    <span class="step-label">Verifikasi</span>
                </div>

            </div><!-- /step-indicator -->


            <!-- ══════════════════════════════════════
         CARD
         ══════════════════════════════════════ -->
            <div class="reg-card">

                <!-- Header (judul berubah tiap step) -->
                <div class="reg-card-header">
                    <p class="reg-card-title" id="cardTitle">Pilih Tipe PKL</p>
                    <p class="reg-card-subtitle" id="cardSubtitle">Tentukan jenis PKL yang akan dijalani</p>
                </div>


                <!-- ══════════════════════════════════
             STEP 1 — Data PKL & Kelompok
             ══════════════════════════════════ -->
                <div class="step-panel active" id="panel-1">
                    <div class="reg-card-body">

                        <!-- Alert error global -->
                        <div class="alert-reg alert-reg-error" id="alertStep1">
                            <i class="fas fa-circle-exclamation"></i>
                            <span id="alertStep1Msg"></span>
                        </div>

                        <!-- Pilih Tipe PKL -->
                        <div class="tipe-pkl-group" id="tipePklGroup">
                            <label class="tipe-pkl-option selected" id="optMandiri">
                                <input type="radio" name="tipe_pkl" value="mandiri" checked>
                                <div class="tipe-pkl-check"><i class="fas fa-check"></i></div>
                                <div class="tipe-pkl-icon"><i class="fas fa-user"></i></div>
                                <p class="tipe-pkl-name">Mandiri</p>
                                <p class="tipe-pkl-desc">PKL sendiri, tanpa instansi</p>
                            </label>
                            <label class="tipe-pkl-option" id="optInstansi">
                                <input type="radio" name="tipe_pkl" value="instansi">
                                <div class="tipe-pkl-check"><i class="fas fa-check"></i></div>
                                <div class="tipe-pkl-icon"><i class="fas fa-building"></i></div>
                                <p class="tipe-pkl-name">Instansi</p>
                                <p class="tipe-pkl-desc">PKL di kampus atau sekolah</p>
                            </label>
                        </div>

                        <!-- Tanggal PKL (selalu tampil) -->
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Tanggal Mulai <span class="required">*</span></label>
                                <input type="text" id="tgl_mulai" name="tgl_mulai" class="form-control flatpickr-input-custom" placeholder="Pilih tanggal mulai" readonly>
                                <div class="invalid-feedback"><i class="fas fa-circle-xmark"></i> <span></span></div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tanggal Selesai <span class="required">*</span></label>
                                <input type="text" id="tgl_akhir" name="tgl_akhir" class="form-control flatpickr-input-custom" placeholder="Pilih tanggal selesai" readonly>
                                <div class="invalid-feedback"><i class="fas fa-circle-xmark"></i> <span></span></div>
                            </div>
                        </div>

                        <!-- ── Section Instansi (hanya jika tipe=instansi) ── -->
                        <div class="instansi-section" id="instansiSection">

                            <div class="divider-text">Data Instansi & Kelompok</div>

                            <!-- Kategori Instansi -->
                            <div class="form-group">
                                <label class="form-label">Kategori Instansi <span class="required">*</span></label>
                                <select id="kategori_instansi" name="kategori_instansi" class="form-control">
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="kampus">Kampus</option>
                                    <option value="sekolah">Sekolah</option>
                                </select>
                                <div class="invalid-feedback"><i class="fas fa-circle-xmark"></i> <span></span></div>
                            </div>

                            <!-- Select2 Instansi -->
                            <div class="form-group" id="instansiSelectGroup">
                                <label class="form-label">Nama Instansi <span class="required">*</span></label>
                                <select id="instansiSelect" name="id_instansi" class="form-control" style="width:100%">
                                    <option value="">-- Pilih atau Cari Instansi --</option>
                                </select>
                                <div class="invalid-feedback"><i class="fas fa-circle-xmark"></i> <span></span></div>
                                <p class="form-hint">Ketik untuk mencari. Jika belum ada, pilih "+ Tambah Instansi Baru".</p>
                            </div>

                            <!-- Field tambah instansi baru -->
                            <div class="instansi-baru-fields" id="instansiBaruFields">
                                <div class="instansi-baru-label">
                                    <i class="fas fa-plus-circle"></i> Data Instansi Baru
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Nama Instansi <span class="required">*</span></label>
                                    <input type="text" id="nama_instansi" name="nama_instansi"
                                        class="form-control" placeholder="Nama instansi baru">
                                    <div class="invalid-feedback"><i class="fas fa-circle-xmark"></i> <span></span></div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Kota <span class="required">*</span></label>
                                        <input type="text" id="kota_instansi" name="kota_instansi"
                                            class="form-control" placeholder="Kota instansi">
                                        <div class="invalid-feedback"><i class="fas fa-circle-xmark"></i> <span></span></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Alamat</label>
                                        <input type="text" id="alamat_instansi" name="alamat_instansi"
                                            class="form-control" placeholder="Alamat lengkap">
                                    </div>
                                </div>
                            </div><!-- /instansi-baru-fields -->

                            <!-- Pembimbing -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Nama Pembimbing</label>
                                    <input type="text" id="pembimbing" name="pembimbing"
                                        class="form-control" placeholder="Nama pembimbing instansi">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">No. HP Pembimbing</label>
                                    <input type="text" id="no_pembimbing" name="no_pembimbing"
                                        class="form-control" placeholder="Nomor WhatsApp">
                                </div>
                            </div>

                            <!-- Nama Kelompok & Jumlah Anggota -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Nama Kelompok <span class="required">*</span></label>
                                    <input type="text" id="nama_kelompok" name="nama_kelompok"
                                        class="form-control" placeholder="Nama kelompok PKL Anda">
                                    <div class="invalid-feedback"><i class="fas fa-circle-xmark"></i> <span></span></div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Jumlah Anggota <span class="required">*</span></label>
                                    <select id="jumlah_anggota" name="jumlah_anggota" class="form-control">
                                        <option value="">-- Pilih --</option>
                                        <?php for ($i = 1; $i <= 10; $i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?> orang</option>
                                        <?php endfor; ?>
                                    </select>
                                    <p class="form-hint">Termasuk ketua kelompok.</p>
                                    <div class="invalid-feedback"><i class="fas fa-circle-xmark"></i> <span></span></div>
                                </div>
                            </div>

                        </div><!-- /instansiSection -->

                    </div><!-- /reg-card-body -->

                    <div class="reg-nav justify-end">
                        <button type="button" class="btn-reg btn-reg-primary" id="btnStep1Next">
                            <span class="btn-text">Lanjut <i class="fas fa-arrow-right"></i></span>
                            <span class="btn-spinner"></span>
                        </button>
                    </div>
                </div><!-- /panel-1 -->


                <!-- ══════════════════════════════════
             STEP 2 — Biodata Anggota
             ══════════════════════════════════ -->
                <div class="step-panel" id="panel-2">
                    <div class="reg-card-body">

                        <div class="alert-reg alert-reg-error" id="alertStep2">
                            <i class="fas fa-circle-exclamation"></i>
                            <span id="alertStep2Msg"></span>
                        </div>

                        <!-- Wrapper anggota — diisi dinamis oleh JS berdasarkan jumlah_anggota -->
                        <div class="anggota-wrapper" id="anggotaWrapper">
                            <!-- Diisi oleh buildAnggotaForms() -->
                        </div>

                    </div>

                    <div class="reg-nav">
                        <button type="button" class="btn-reg btn-reg-secondary" id="btnStep2Back">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </button>
                        <button type="button" class="btn-reg btn-reg-primary" id="btnStep2Next">
                            <span class="btn-text">Lanjut <i class="fas fa-arrow-right"></i></span>
                            <span class="btn-spinner"></span>
                        </button>
                    </div>
                </div><!-- /panel-2 -->


                <!-- ══════════════════════════════════
             STEP 3 — Verifikasi OTP
             ══════════════════════════════════ -->
                <div class="step-panel" id="panel-3">
                    <div class="reg-card-body">

                        <!-- Box info email ketua -->
                        <div class="otp-info-box">
                            <div class="otp-info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <p class="otp-info-title">Kode OTP Dikirim</p>
                                <p class="otp-info-email" id="otpTargetEmail">-</p>
                                <p class="otp-info-sub">Berlaku 30 menit. Masukkan kode yang dikirim ke email ketua kelompok.</p>
                            </div>
                        </div>

                        <!-- Alert step 3 -->
                        <div class="alert-reg alert-reg-error" id="alertStep3">
                            <i class="fas fa-circle-exclamation"></i>
                            <span id="alertStep3Msg"></span>
                        </div>

                        <!-- Form OTP (sembunyi setelah sukses) -->
                        <div id="otpFormSection">

                            <!-- 6 kotak OTP -->
                            <div class="otp-input-group" id="otpInputGroup">
                                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                                <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                            </div>

                            <!-- Timer & resend -->
                            <div class="otp-resend-area">
                                <p class="otp-timer" id="otpTimerText">
                                    Kirim ulang dalam <strong id="otpCountdown">30:00</strong>
                                </p>
                                <button type="button" class="btn-resend" id="btnResendOtp" disabled>
                                    Kirim Ulang Kode
                                </button>
                            </div>

                            <!-- Ganti email -->
                            <div class="ganti-email-toggle">
                                <button type="button" class="btn-ganti-email" id="btnToggleGantiEmail">
                                    <i class="fas fa-pen"></i> Tidak menerima email? Ganti alamat email
                                </button>
                            </div>

                            <div class="ganti-email-form" id="gantiEmailForm">
                                <div class="form-group" style="margin-bottom:10px;">
                                    <label class="form-label" style="font-size:12px;">Email baru untuk ketua kelompok</label>
                                    <input type="email" id="emailBaru" name="email_baru"
                                        class="form-control" placeholder="Alamat email aktif">
                                    <div class="invalid-feedback"><i class="fas fa-circle-xmark"></i> <span></span></div>
                                </div>
                                <button type="button" class="btn-reg btn-reg-primary" id="btnKirimEmailBaru"
                                    style="width:100%;justify-content:center;">
                                    <span class="btn-text"><i class="fas fa-paper-plane"></i> Kirim OTP ke Email Baru</span>
                                    <span class="btn-spinner"></span>
                                </button>
                            </div>

                        </div><!-- /otpFormSection -->

                        <!-- Sukses (tampil setelah OTP valid) -->
                        <div class="otp-success-box" id="otpSuccessBox">
                            <div class="otp-success-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <h3 class="otp-success-title">Registrasi Berhasil!</h3>
                            <p class="otp-success-sub" id="otpSuccessSub"></p>
                            <a href="<?= base_url('auth/login') ?>" class="btn-reg btn-reg-primary"
                                style="display:inline-flex;text-decoration:none;">
                                <i class="fas fa-sign-in-alt"></i> Masuk ke Aplikasi
                            </a>
                        </div>

                    </div>

                    <div class="reg-nav" id="navStep3">
                        <button type="button" class="btn-reg btn-reg-secondary" id="btnStep3Back">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </button>
                        <button type="button" class="btn-reg btn-reg-success" id="btnVerifyOtp" disabled>
                            <span class="btn-text"><i class="fas fa-check-circle"></i> Verifikasi & Daftar</span>
                            <span class="btn-spinner"></span>
                        </button>
                    </div>
                </div><!-- /panel-3 -->

            </div><!-- /reg-card -->

        </div>
    </div><!-- /reg-page -->


    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Config untuk JS -->
    <script>
        window.REG_CONFIG = {
            urlInstansiList: '<?= base_url('registrasi/instansi-list') ?>',
            urlSendOtp: '<?= base_url('registrasi/send-otp') ?>',
            urlVerifyOtp: '<?= base_url('registrasi/verify-otp') ?>',
            urlResendOtp: '<?= base_url('registrasi/resend-otp') ?>',
            csrfName: '<?= csrf_token() ?>',
            csrfHash: '<?= csrf_hash() ?>',
        };
    </script>

    <script src="<?= base_url('assets/js/modules/registrasi.js') ?>"></script>

</body>

</html>