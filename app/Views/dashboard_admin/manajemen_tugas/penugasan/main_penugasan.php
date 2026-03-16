<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/modules/tugas.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Tab Navigation -->
<div class="tab-navigation">
    <button class="tab-btn active" data-tab="kategori" id="tab-kategori">
        <i class="fas fa-tags"></i>
        <span>Kategori Tugas</span>
    </button>
    <button class="tab-btn" data-tab="tugas" id="tab-tugas">
        <i class="fas fa-tasks"></i>
        <span>Tugas</span>
    </button>
</div>

<!-- Tab Content Area -->
<div class="tab-content-wrapper">
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>
    <div id="tabContentArea">
        <!-- Konten dimuat via AJAX -->
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="<?= base_url('assets/js/modules/tugas.js') ?>"></script>
<script>
    // -------------------------------------------------------
    // BASE_URL dibutuhkan oleh loadTabContent() di tugas.js
    // -------------------------------------------------------
    window.BASE_URL = '<?= base_url('dashboard/manajemen-tugas/penugasan') ?>';

    $(document).ready(function() {
        if (typeof $.fn.DataTable === 'undefined') {
            console.error('DataTables library not loaded!');
            showAlert('error', 'Error', 'DataTables library tidak ditemukan');
            return;
        }
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 not loaded!');
        }

        loadTabContent('kategori');

        $('.tab-btn').on('click', function() {
            $('.tab-btn').removeClass('active');
            $(this).addClass('active');
            loadTabContent($(this).data('tab'));
        });
    });
</script>
<?= $this->endSection() ?>