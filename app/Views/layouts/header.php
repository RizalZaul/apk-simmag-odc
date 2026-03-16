<header class="dashboard-header">

    <div class="header-left">
        <button class="menu-toggle" id="toggleSidebar" aria-label="Toggle Sidebar">
            &#9776;
        </button>
        <?php
        /**
         * $title dikirim dari Controller — sama dengan yang dipakai <title> di layout.
         * Tidak perlu mapping URI manual di sini.
         * Contoh di Controller: $data['title'] = 'Manajemen PKL';
         */
        ?>
        <h1 class="page-title"><?= esc($title ?? 'Dashboard') ?></h1>
    </div>

    <div class="header-right">
        <img src="<?= base_url('assets/images/logo2.png') ?>"
             alt="Logo SIMMAG ODC"
             class="header-logo">
    </div>

</header>
