<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard') ?> — SIMMAG ODC</title>

    <!--
        [BARU] Meta tag untuk JavaScript:
          - base-url   : dipakai sebagai fallback URL jika base URL belum diinisialisasi
          - csrf-token : dipakai jQuery untuk menyertakan token CSRF di setiap POST request
          - csrf-name  : nama field yang diharapkan CI4 (default: csrf_token)
    -->
    <meta name="base-url" content="<?= base_url() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">

    <!-- ==================== GOOGLE FONTS ==================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- ==================== FONT AWESOME ==================== -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- ==================== CORE CSS ==================== -->
    <link rel="stylesheet" href="<?= base_url('assets/css/core/variables.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/core/reset.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/core/utilities.css') ?>">

    <!-- ==================== LAYOUT CSS ==================== -->
    <link rel="stylesheet" href="<?= base_url('assets/css/layout/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/layout/components.css') ?>">

    <!-- ==================== DATATABLES ==================== -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" />

    <!-- ==================== SELECT2 ==================== -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- ==================== FLATPICKR ==================== -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_green.css">

    <?= $this->renderSection('styles') ?>

</head>

<body>

    <div class="dashboard-wrapper">

        <!-- SIDEBAR -->
        <?php
        $role = session()->get('role') ?? 'admin';

        if ($role === 'pkl') {
            echo $this->include('layouts/sidebar_pkl');
        } else {
            echo $this->include('layouts/sidebar_admin');
        }
        ?>

        <!-- MAIN AREA -->
        <div class="dashboard-main">

            <!-- HEADER -->
            <?= $this->include('layouts/header') ?>

            <!-- PAGE CONTENT -->
            <main class="dashboard-content">
                <?= $this->renderSection('content') ?>
            </main>

        </div>
    </div>

    <!-- ==================== JQUERY ==================== -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- ==================== DATATABLES ==================== -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <!-- ==================== SELECT2 ==================== -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- ==================== FLATPICKR ==================== -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <!-- ==================== SWEETALERT2 ==================== -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ==================== DASHBOARD JS ==================== -->
    <script src="<?= base_url('assets/js/modules/dashboard.js') ?>"></script>

    <!--
        [BARU] Setup global jQuery AJAX:
          Setiap POST request otomatis menyertakan CSRF token di header.
          Ini mengatasi 404/403 yang disebabkan CSRF check gagal di CI4.
    -->
    <script>
        (function() {
            var csrfName = document.querySelector('meta[name="csrf-name"]').content;
            var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            $.ajaxSetup({
                beforeSend: function(xhr, settings) {
                    if (settings.type && settings.type.toUpperCase() === 'POST') {
                        xhr.setRequestHeader('X-' + csrfName, csrfToken);
                    }
                }
            });
        })();
    </script>

    <!-- ==================== PAGE SPECIFIC JS ==================== -->
    <?= $this->renderSection('javascript') ?>

</body>

</html>