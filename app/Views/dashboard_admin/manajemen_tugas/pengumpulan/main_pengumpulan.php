<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/modules/pengumpulan.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/modules/tugas.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="pengumpulan-container">

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- TAB NAVIGATION -->
    <div class="tab-navigation">
        <button class="tab-btn active" data-tab="mandiri">
            <i class="fas fa-user"></i>
            Tugas Mandiri
        </button>

        <button class="tab-btn" data-tab="kelompok">
            <i class="fas fa-users"></i>
            Tugas Kelompok
        </button>

        <button class="tab-btn" data-tab="tim">
            <i class="fas fa-user-friends"></i>
            Tugas Tim
        </button>
    </div>

    <!-- TAB CONTENT AREA — diisi via AJAX -->
    <div id="tabContentArea"></div>

</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="<?= base_url('assets/js/modules/pengumpulan.js') ?>"></script>
<script>
    window.BASE_URL = '<?= base_url('dashboard/manajemen-tugas/pengumpulan') ?>';

    $(document).ready(function() {
        // Load tab aktif saat halaman pertama dibuka
        loadPengumpulanTab('mandiri');

        // Handle klik tab
        $('.tab-btn').on('click', function() {
            const tab = $(this).data('tab');
            if ($(this).hasClass('active')) return;

            $('.tab-btn').removeClass('active');
            $(this).addClass('active');
            loadPengumpulanTab(tab);
        });
    });
</script>
<?= $this->endSection() ?>