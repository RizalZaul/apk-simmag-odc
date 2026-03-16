<?= $this->extend('layouts/dashboard_layout') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/modules/data-modul.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="modul-container">

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- TAB NAVIGATION -->
    <div class="modul-tab">
        <button class="modul-tab-btn active" data-target="kategori">
            <i class="fas fa-tags"></i>
            Kategori Modul
        </button>
        <button class="modul-tab-btn" data-target="modul">
            <i class="fas fa-book"></i>
            Data Modul
        </button>
    </div>

    <!-- TAB CONTENT AREA — diisi via AJAX -->
    <div id="tabContentArea"></div>

</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="<?= base_url('assets/js/modules/modul.js') ?>"></script>
<script>
    $(document).ready(function () {
        modulMainInit('<?= base_url('dashboard/data-modul') ?>');
    });
</script>
<?= $this->endSection() ?>
