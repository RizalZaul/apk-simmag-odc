<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMMag ODC PKL System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.5/sweetalert2.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/modules/auth.css') ?>">
</head>
<body>

<div class="login-container">

    <!-- ── LEFT SIDE ── -->
    <div class="login-left">
        <div class="login-overlay">
            <div class="login-branding">
                <img src="<?= base_url('assets/images/logo.png') ?>" alt="SIMMag ODC" class="login-logo">
                <h1>SIMMAG ODC</h1>
                <p>Sistem Informasi Manajemen Magang Our Digital Creative</p>
                <p class="subtitle">Platform digital untuk mengelola dan memantau kegiatan Praktik Kerja Lapangan</p>
            </div>
            <div class="login-features">
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Manajemen Tugas Terintegrasi</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Monitoring Progress Real-time</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Laporan Digital Otomatis</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── RIGHT SIDE ── -->
    <div class="login-right">
        <div class="login-form-wrapper">

            <!-- Logo Mobile -->
            <div class="login-logo-mobile">
                <img src="<?= base_url('assets/images/logo.png') ?>" alt="SIMMag ODC">
                <h3>SIMMag ODC</h3>
            </div>

            <!-- CARD -->
            <div class="login-card">

                <!-- Header -->
                <div class="login-header">
                    <h2>Selamat Datang! 👋</h2>
                    <p>Silakan login untuk melanjutkan ke sistem</p>
                </div>

                <div class="form-divider"></div>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success" id="flashAlert">
                        <i class="fas fa-check-circle"></i>
                        <span><?= session()->getFlashdata('success') ?></span>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form id="loginForm" class="login-form" novalidate>
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-user"></i> Username / Email
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="username" name="username"
                                class="form-control"
                                placeholder="Masukkan username atau email"
                                autocomplete="username" autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="input-wrapper password-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password" name="password"
                                class="form-control"
                                placeholder="Masukkan password"
                                autocomplete="current-password">
                            <button type="button" class="toggle-password" id="togglePassword">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <a href="<?= base_url('auth/forgot-password') ?>" class="forgot-password">
                            Lupa Password?
                        </a>
                    </div>

                    <button type="submit" class="btn-login" id="btnLogin">
                        <span class="btn-text">Masuk</span>
                        <i class="fas fa-arrow-right btn-icon"></i>
                    </button>

                </form>
<!-- 
                <div class="register-link">
                    Belum punya akun? <a href="<?= base_url('registrasi') ?>">Daftar PKL</a>
                </div> -->

                <!-- Footer -->
                <div class="login-footer">
                    <p>&copy; <?= date('Y') ?> <strong>Our Digital Creative</strong> — All rights reserved</p>
                    <!-- <div class="security-badge">
                        <i class="fas fa-shield-halved"></i>
                        <span>Koneksi aman &amp; terenkripsi</span>
                    </div> -->
                </div>

            </div><!-- /login-card -->

        </div>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<script src="<?= base_url('assets/js/modules/auth.js') ?>"></script>

</body>
</html>