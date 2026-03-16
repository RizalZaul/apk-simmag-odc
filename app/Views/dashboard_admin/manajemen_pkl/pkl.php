<!-- Statistics Cards -->
    <div class="pkl-stats-container">
        <!-- PKL Aktif Card -->
        <div class="stat-card aktif" data-tab="aktif" id="cardPklAktif">
            <div class="stat-icon">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-label">PKL Aktif</div>
            <div class="stat-value"><?= $stats['aktif'] ?></div>
        </div>

        <!-- PKL Selesai Card -->
        <div class="stat-card selesai" data-tab="selesai" id="cardPklSelesai">
            <div class="stat-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-label">PKL Selesai</div>
            <div class="stat-value"><?= $stats['selesai'] ?></div>
        </div>

        <!-- PKL Non-Aktif Card -->
        <div class="stat-card nonaktif" data-tab="nonaktif" id="cardPklNonaktif">
            <div class="stat-icon">
                <i class="fas fa-user-slash"></i>
            </div>
            <div class="stat-label">PKL Non-Aktif</div>
            <div class="stat-value"><?= $stats['nonaktif'] ?></div>
        </div>
    </div>

    <!-- Sub-Tab Navigation -->
    <div class="pkl-tab-navigation">
        <button class="pkl-tab-btn active" data-subtab="aktif" id="subTabAktif">
            <i class="fas fa-user-check"></i>
            <span>PKL Aktif</span>
        </button>
        <button class="pkl-tab-btn" data-subtab="selesai" id="subTabSelesai">
            <i class="fas fa-user-graduate"></i>
            <span>PKL Selesai</span>
        </button>
        <button class="pkl-tab-btn" data-subtab="nonaktif" id="subTabNonaktif">
            <i class="fas fa-user-slash"></i>
            <span>PKL Non-Aktif</span>
        </button>
    </div>

    <!-- Content Area -->
    <div class="pkl-content-area">
        <!-- Loading Overlay -->
        <div class="pkl-loading" id="pklLoadingOverlay">
            <div class="pkl-spinner"></div>
        </div>

        <!-- Dynamic Content -->
        <div id="pklSubTabContent">
            <!-- Content will be loaded here via AJAX -->
        </div>
    </div>

    <script>
        // ==========================================
        // PKL SUB-TAB STATE
        // ==========================================
        // ==========================================
        // PKL SUB-TAB STATE
        // window.currentPklSubTab dipakai oleh hapusPkl/nonaktifkanPkl di pkl.js
        // ==========================================
        window.currentPklSubTab = window.currentPklSubTab || 'aktif';

        // ==========================================
        // INIT PKL SUB-TABS
        // ==========================================
        function initPklSubTabs() {
            // Baca flag restore dari _reloadPklSection() di pkl.js
            // Flag ini di-set sebelum loadTabContent('pkl') dipanggil ulang,
            // agar setelah inject pkl.php kita langsung load sub-tab yang benar.
            const restoreSubTab = window.__pklRestoreSubTab || 'aktif';
            window.__pklRestoreSubTab = null; // consume — hanya pakai sekali

            // Update visual tombol sub-tab sesuai target
            $('.pkl-tab-btn').removeClass('active');
            $('.pkl-tab-btn[data-subtab="' + restoreSubTab + '"]').addClass('active');

            // Load sub-tab target
            loadPklSubTab(restoreSubTab);

            // Sub-Tab button clicks
            $('.pkl-tab-btn').off('click.pklsubtab').on('click.pklsubtab', function() {
                const subTab = $(this).data('subtab');
                $('.pkl-tab-btn').removeClass('active');
                $(this).addClass('active');
                loadPklSubTab(subTab);
            });

            // Statistics card clicks
            $('.stat-card').off('click.pklsubtab').on('click.pklsubtab', function() {
                const tab = $(this).data('tab');
                $('.pkl-tab-btn').removeClass('active');
                $('.pkl-tab-btn[data-subtab="' + tab + '"]').addClass('active');
                loadPklSubTab(tab);
            });
        }

        // ==========================================
        // LOAD PKL SUB-TAB CONTENT
        // Dibuat window.loadPklSubTab agar bisa diakses dari scope luar
        // (sebelumnya local function → tidak bisa dipanggil dari callback AJAX di view files)
        // ==========================================
        window.loadPklSubTab = function loadPklSubTab(subTab) {
            currentPklSubTab = subTab;

            const overlay = $('#pklLoadingOverlay');
            const contentArea = $('#pklSubTabContent');

            const url = '<?= base_url('dashboard/manajemen-pkl') ?>' + '/load-pkl-' + subTab;

            overlay.addClass('active');
            contentArea.empty();

            $.ajax({
                url: url,
                method: 'GET',
                timeout: 10000,
                success: function(response) {
                    contentArea.html(response);
                    overlay.removeClass('active');

                    // Delay to let injected scripts register their functions
                    setTimeout(function() {
                        if (subTab === 'aktif' && typeof initPklAktifTable === 'function') {
                            initPklAktifTable();
                            initPklAktifEvents();
                        } else if (subTab === 'selesai' && typeof initPklSelesaiTable === 'function') {
                            initPklSelesaiTable();
                            initPklSelesaiEvents();
                        } else if (subTab === 'nonaktif' && typeof initPklNonaktifTable === 'function') {
                            initPklNonaktifTable();
                            initPklNonaktifEvents();
                        }
                    }, 100);
                },
                error: function(xhr, status) {
                    overlay.removeClass('active');

                    let errorMsg = 'Gagal memuat data PKL';
                    if (status === 'timeout') errorMsg = 'Request timeout. Coba lagi.';
                    else if (xhr.status === 404) errorMsg = 'Endpoint tidak ditemukan (404)';
                    else if (xhr.status === 500) errorMsg = 'Server error (500)';

                    contentArea.html(`
                <div style="text-align:center;padding:60px 20px;color:#64748b;">
                    <i class="fas fa-exclamation-triangle" style="font-size:48px;margin-bottom:20px;"></i>
                    <h3 style="margin-bottom:10px;">Error Memuat Data</h3>
                    <p>${errorMsg}</p>
                </div>
            `);

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                    }
                }
            });
        }
    </script>