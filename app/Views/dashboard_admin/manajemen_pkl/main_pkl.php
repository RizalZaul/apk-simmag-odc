<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<!-- Manajemen PKL Module CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/modules/manajemen-pkl.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Tab Navigation -->
<div class="tab-navigation">
    <button class="tab-btn active" data-tab="instansi" id="tab-instansi">
        <i class="fas fa-building"></i>
        <span>Data Instansi</span>
    </button>
    <button class="tab-btn" data-tab="pkl" id="tab-pkl">
        <i class="fas fa-users"></i>
        <span>Data PKL</span>
    </button>
</div>

<!-- Tab Content Area -->
<div class="tab-content-wrapper">

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <!-- Dynamic Content Area -->
    <div id="tabContentArea">
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="<?= base_url('assets/js/modules/pkl.js') ?>"></script>
<script>
    $(document).ready(function() {
        pklMainInit('<?= base_url('dashboard/manajemen-pkl') ?>');
    });
</script>
<?= $this->endSection() ?>