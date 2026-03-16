<aside class="dashboard-sidebar">

    <!-- TOP -->
    <div class="sidebar-top">

        <!-- LOGO SECTION -->
        <div class="sidebar-logo">
            <img src="<?= base_url('assets/images/logo.png') ?>"
                alt="OurWeb.id"
                class="logo-image logo-large"
                data-logo-small="<?= base_url('assets/images/logo3.png') ?>"
                data-logo-large="<?= base_url('assets/images/logo.png') ?>">
        </div>

        <!-- MENU NAVIGATION -->
        <ul class="sidebar-menu">

            <!-- Dashboard -->
            <li class="menu-item <?= active_class('pkl/dashboard', true) ?>">
                <a href="<?= base_url('pkl/dashboard') ?>">
                    <i class="fas fa-chart-line icon"></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <!-- Data Modul -->
            <li class="menu-item <?= active_class('pkl/data-modul') ?>">
                <a href="<?= base_url('pkl/data-modul') ?>">
                    <i class="fas fa-book-open icon"></i>
                    <span class="text">Data Modul</span>
                </a>
            </li>

            <!-- Manajemen Tugas -->
            <li class="menu-item <?= active_class('pkl/manajemen-tugas') ?>">
                <a href="<?= base_url('pkl/manajemen-tugas') ?>">
                    <i class="fas fa-clipboard-list icon"></i>
                    <span class="text">Manajemen Tugas</span>
                </a>
            </li>

        </ul>

    </div>

    <!-- BOTTOM / PROFILE -->
    <div class="sidebar-bottom">

        <div class="sidebar-profile" id="profileToggle">
            <div class="avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="profile-info">
                <span class="name"><?= session()->get('panggilan') ?></span>
                <span class="role">PKL</span>
            </div>
            <i class="fas fa-chevron-down chevron"></i>
        </div>

        <!-- DROPDOWN -->
        <div class="profile-dropdown" id="profileDropdown">
            <a href="<?= base_url('pkl/profil/data-diri') ?>" class="dropdown-item">
                <i class="fas fa-user icon"></i>
                <span>Data Diri</span>
            </a>
            <a href="<?= base_url('auth/logout') ?>" class="dropdown-item">
                <i class="fas fa-sign-out-alt icon"></i>
                <span>Logout</span>
            </a>
        </div>

    </div>

</aside>