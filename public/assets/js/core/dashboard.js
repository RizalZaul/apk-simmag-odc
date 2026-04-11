$(document).ready(function () {

    function updateLogo() {
        var $logo = $('.logo-image');
        var $sidebar = $('.dashboard-sidebar');
        if (!$logo.length) return;

        var isCollapsed = $sidebar.hasClass('collapsed') && !$sidebar.hasClass('hover-open');

        if (isCollapsed) {
            $logo.attr('src', $logo.data('logo-small'));
            $logo.removeClass('logo-large').addClass('logo-small');
        } else {
            $logo.attr('src', $logo.data('logo-large'));
            $logo.removeClass('logo-small').addClass('logo-large');
        }
    }

    var isDesktop = function () { return $(window).width() >= 992; };

    try {
        if (isDesktop() && localStorage.getItem('sidebarCollapsed') === '1') {
            $('.dashboard-sidebar').addClass('collapsed');
            $('#dashboardMain').addClass('sidebar-collapsed');
        }
    } catch (e) { }

    updateLogo();

    $('#menuToggle').on('click', function () {
        if (isDesktop()) {
            var $sidebar = $('.dashboard-sidebar');
            $sidebar.toggleClass('collapsed');
            $('#dashboardMain').toggleClass('sidebar-collapsed', $sidebar.hasClass('collapsed'));
            updateLogo();
            if ($sidebar.hasClass('collapsed')) {
                $('#profileDropdown').removeClass('show');
                $('#profileToggle').removeClass('active');
            }
            try {
                localStorage.setItem('sidebarCollapsed', $sidebar.hasClass('collapsed') ? '1' : '0');
            } catch (e) { }
        } else {
            // Mobile: open sidebar overlay
            $('.dashboard-sidebar').addClass('mobile-open');
            $('#sidebarOverlay').addClass('visible');
            $('body').css('overflow', 'hidden');
        }
    });

    $('.dashboard-sidebar').on('mouseenter', function () {
        if (isDesktop() && $(this).hasClass('collapsed')) {
            $(this).addClass('hover-open');
            updateLogo();
        }
    });

    $('.dashboard-sidebar').on('mouseleave', function () {
        if (isDesktop() && $(this).hasClass('collapsed')) {
            $(this).removeClass('hover-open');
            // Tutup submenu agar tidak tertinggal saat sidebar collapse
            if (!$(this).hasClass('hover-open')) {
                // biarkan open class submenu, hanya sembunyikan via CSS
            }
            updateLogo();
        }
    });

    $('#sidebarOverlay').on('click', function () {
        $('.dashboard-sidebar').removeClass('mobile-open');
        $(this).removeClass('visible');
        $('body').css('overflow', '');
    });

    $(window).on('resize', function () {
        if (isDesktop()) {
            $('.dashboard-sidebar').removeClass('mobile-open');
            $('#sidebarOverlay').removeClass('visible');
            $('body').css('overflow', '');
        }
    });


    $('.menu-item.has-submenu > a').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $sidebar = $('.dashboard-sidebar');
        if (isDesktop() && $sidebar.hasClass('collapsed') && !$sidebar.hasClass('hover-open')) return;

        var $parent = $(this).closest('.menu-item.has-submenu');
        $parent.toggleClass('open');

        $('.menu-item.has-submenu').not($parent).removeClass('open');
    });


    $('#profileToggle').on('click', function (e) {
        e.stopPropagation();
        var $dropdown = $('#profileDropdown');
        var isVisible = $dropdown.hasClass('show');

        if (isVisible) {
            $dropdown.removeClass('show');
            $(this).removeClass('active');
        } else {
            $dropdown.addClass('show');
            $(this).addClass('active');
        }
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.sidebar-profile').length) {
            $('#profileDropdown').removeClass('show');
            $('#profileToggle').removeClass('active');
        }
    });

    $('#profileDropdown').on('click', function (e) {
        e.stopPropagation();
    });

    $('[data-logout-link]').on('click', function (e) {
        var href = $(this).attr('href');
        if (!href) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        if (!window.Swal) {
            window.location.href = href;
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Logout dari akun?',
            text: 'Sesi Anda akan diakhiri dan Anda perlu login kembali.',
            showCancelButton: true,
            confirmButtonText: 'Ya, logout',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#334155',
            reverseButtons: true,
            focusCancel: true
        }).then(function (result) {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });


    $('.flash-message[data-timeout]').each(function () {
        var $msg = $(this);
        var timeout = parseInt($msg.data('timeout'), 10) || 4000;
        setTimeout(function () {
            $msg.css({ transition: 'opacity 0.4s ease', opacity: 0 });
            setTimeout(function () { $msg.remove(); }, 400);
        }, timeout);
    });

    $(document).on('click', '.flash-close', function () {
        var $msg = $(this).closest('.flash-message');
        $msg.css({ transition: 'opacity 0.3s ease', opacity: 0 });
        setTimeout(function () { $msg.remove(); }, 300);
    });

});
