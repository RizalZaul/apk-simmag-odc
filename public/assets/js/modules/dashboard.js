/**
 * Dashboard JavaScript
 * Handle sidebar navigation, collapse, profile dropdown, submenu
 */

$(document).ready(function () {

    function updateLogo() {
        const $logo = $('.logo-image');
        const $sidebar = $('.dashboard-sidebar');

        if (!$logo.length) return;

        const isCollapsed = $sidebar.hasClass('collapsed') && !$sidebar.hasClass('hover-open');

        if (isCollapsed) {
            $logo.attr('src', $logo.data('logo-small'));
            $logo.removeClass('logo-large').addClass('logo-small');
        } else {
            $logo.attr('src', $logo.data('logo-large'));
            $logo.removeClass('logo-small').addClass('logo-large');
        }
    }

    updateLogo();

    $('.menu-toggle').on('click', function () {
        $('.dashboard-sidebar').toggleClass('collapsed');
        updateLogo();
    });

    $('.dashboard-sidebar').on('mouseenter', function () {
        if ($(this).hasClass('collapsed')) {
            $(this).addClass('hover-open');
            updateLogo();
        }
    });

    $('.dashboard-sidebar').on('mouseleave', function () {
        if ($(this).hasClass('collapsed')) {
            $(this).removeClass('hover-open');
            updateLogo();
        }
    });

    $('.menu-item.has-submenu > a').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const parentLi = $(this).closest('.menu-item.has-submenu');

        parentLi.toggleClass('open');
        $('.menu-item.has-submenu').not(parentLi).removeClass('open');
    });

    $('#profileToggle').on('click', function (e) {
        e.stopPropagation();

        const $dropdown = $('#profileDropdown');
        const isVisible = $dropdown.hasClass('show');

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

    if ($(window).width() <= 768) {
        $('.menu-toggle').on('click', function () {
            $('.dashboard-sidebar').toggleClass('mobile-open');
        });
    }
});