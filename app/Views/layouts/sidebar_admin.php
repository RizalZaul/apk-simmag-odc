<aside class="dashboard-sidebar">

  <!-- ==================== TOP ==================== -->
  <div class="sidebar-top">

    <!-- LOGO -->
    <div class="sidebar-logo">
      <img src="<?= base_url('assets/images/logo.png') ?>"
        alt="OurWeb.id"
        class="logo-image logo-large"
        data-logo-large="<?= base_url('assets/images/logo.png') ?>"
        data-logo-small="<?= base_url('assets/images/logo3.png') ?>">
    </div>

    <!-- MENU NAVIGATION -->
    <ul class="sidebar-menu">

      <!-- Dashboard -->
      <li class="menu-item <?= active_class('', true) ?>">
        <a href="<?= base_url('/') ?>">
          <i class="fas fa-chart-line icon"></i>
          <span class="text">Dashboard</span>
        </a>
      </li>

      <!-- Manajemen PKL -->
      <li class="menu-item <?= active_class('dashboard/manajemen-pkl') ?>">
        <a href="<?= base_url('dashboard/manajemen-pkl') ?>">
          <i class="fas fa-users-gear icon"></i>
          <span class="text">Manajemen PKL</span>
        </a>
      </li>

      <!-- Data Modul -->
      <li class="menu-item <?= active_class('dashboard/data-modul') ?>">
        <a href="<?= base_url('dashboard/data-modul') ?>">
          <i class="fas fa-book-open icon"></i>
          <span class="text">Data Modul</span>
        </a>
      </li>

      <!-- Manajemen Tugas (dengan Submenu) -->
      <?php
      $tugasSubmenus = [
        'dashboard/manajemen-tugas/penugasan',
        'dashboard/manajemen-tugas/pengumpulan',
      ];
      ?>
      <li class="menu-item has-submenu <?= active_open_class('dashboard/manajemen-tugas', $tugasSubmenus) ?>">

        <?php
        /**
         * <a> harus langsung jadi child <li> (tanpa wrapper <div>)
         * agar selector JS ".menu-item.has-submenu > a" bisa menemukannya.
         */
        ?>
        <a href="javascript:void(0)">
          <i class="fas fa-clipboard-list icon"></i>
          <span class="text">Manajemen Tugas</span>
          <i class="fas fa-chevron-down submenu-arrow"></i>
        </a>

        <ul class="submenu">
          <li class="submenu-item <?= active_class('dashboard/manajemen-tugas/penugasan') ?>">
            <a href="<?= base_url('dashboard/manajemen-tugas/penugasan') ?>">
              <i class="fas fa-file-circle-plus icon"></i>
              <span class="text">Penugasan</span>
            </a>
          </li>
          <li class="submenu-item <?= active_class('dashboard/manajemen-tugas/pengumpulan') ?>">
            <a href="<?= base_url('dashboard/manajemen-tugas/pengumpulan') ?>">
              <i class="fas fa-box-archive icon"></i>
              <span class="text">Pengumpulan</span>
            </a>
          </li>
        </ul>

      </li>

    </ul>

  </div><!-- /sidebar-top -->

  <!-- ==================== BOTTOM / PROFILE ==================== -->
  <div class="sidebar-bottom">

    <div class="sidebar-profile" id="profileToggle">
      <div class="avatar">
        <i class="fas fa-user-circle"></i>
      </div>
      <div class="profile-info">
        <span class="name"><?= session()->get('panggilan') ?></span>
        <span class="role">Administrator</span>
      </div>
      <i class="fas fa-chevron-down chevron"></i>
    </div>

    <!-- DROPDOWN -->
    <div class="profile-dropdown" id="profileDropdown">
      <a href="<?= base_url('dashboard/profil') ?>" class="dropdown-item">
        <i class="fas fa-user"></i>
        <span>Profile</span>
      </a>
      <a href="<?= base_url('auth/logout') ?>" class="dropdown-item">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>

  </div><!-- /sidebar-bottom -->

</aside>