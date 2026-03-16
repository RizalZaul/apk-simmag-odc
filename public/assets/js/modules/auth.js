/**
 * auth.js
 * Handles login page interactions:
 *   - Toggle show/hide password
 *   - Client-side validation  → inline alert (cepat, tanpa round-trip)
 *   - Server-side validation  → AJAX fetch + SweetAlert2
 *   - Auto-dismiss flash alert (logout success, dll)
 */

(function () {
    'use strict';


    // ══════════════════════════════════════
    // TOGGLE PASSWORD VISIBILITY
    // ══════════════════════════════════════

    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', function () {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';

            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye', !isHidden);
            icon.classList.toggle('fa-eye-slash', isHidden);
        });
    }


    // ══════════════════════════════════════
    // ELEMEN FORM
    // ══════════════════════════════════════

    const loginForm = document.getElementById('loginForm');
    const btnLogin = document.getElementById('btnLogin');
    const btnText = btnLogin?.querySelector('.btn-text');
    const btnIcon = btnLogin?.querySelector('.btn-icon');
    const usernameInput = document.getElementById('username');


    // ══════════════════════════════════════
    // FORM SUBMIT — AJAX + VALIDASI BERLAPIS
    // ══════════════════════════════════════

    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const username = usernameInput.value.trim();
            const password = passwordInput.value;

            // ── 1. Client-side validation (inline alert, instant) ──────
            if (!username) {
                showInlineAlert('Username atau email tidak boleh kosong.', 'error');
                usernameInput.focus();
                return;
            }

            if (!password) {
                showInlineAlert('Password tidak boleh kosong.', 'error');
                passwordInput.focus();
                return;
            }

            if (password.length < 6) {
                showInlineAlert('Password minimal 6 karakter.', 'error');
                passwordInput.focus();
                return;
            }

            // ── 2. Semua lolos → kirim ke server via AJAX ──────────────
            clearInlineAlert();
            setLoadingState(true);

            try {
                // Ambil CSRF token dari hidden input
                const csrfInput = loginForm.querySelector('input[type="hidden"]');
                const csrfName = csrfInput?.name;
                const csrfValue = csrfInput?.value;

                const body = new URLSearchParams();
                body.append('username', username);
                body.append('password', password);
                if (csrfName) body.append(csrfName, csrfValue);

                const response = await fetch(loginForm.dataset.action || getBaseUrl() + 'auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: body.toString(),
                });

                // Tangani response non-JSON (misal: server error 500)
                const contentType = response.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    throw new Error('Respons server tidak valid. Silakan coba lagi.');
                }

                const data = await response.json();

                if (data.success) {
                    // ── Login berhasil → redirect langsung ──────────────
                    // Tampilkan loading sebentar, lalu redirect
                    if (btnText) btnText.textContent = 'Berhasil! Mengalihkan…';
                    window.location.href = data.redirect;
                } else {
                    // ── Login gagal → SweetAlert2 ────────────────────────
                    setLoadingState(false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Gagal',
                        text: data.message || 'Terjadi kesalahan. Silakan coba lagi.',
                        confirmButtonText: 'Coba Lagi',
                        confirmButtonColor: '#20a8a8',
                        customClass: {
                            popup: 'swal-custom-popup',
                            title: 'swal-custom-title',
                            confirmButton: 'swal-custom-confirm',
                        },
                    }).then(() => {
                        // Fokus balik ke field yang relevan setelah dialog ditutup
                        if (data.field === 'password') {
                            passwordInput.value = '';
                            passwordInput.focus();
                        } else {
                            usernameInput.focus();
                        }
                    });
                }

            } catch (err) {
                setLoadingState(false);
                Swal.fire({
                    icon: 'warning',
                    title: 'Koneksi Bermasalah',
                    text: err.message || 'Tidak dapat terhubung ke server. Periksa koneksi Anda.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#20a8a8',
                });
            }
        });
    }


    // ══════════════════════════════════════
    // LOADING STATE
    // ══════════════════════════════════════

    function setLoadingState(loading) {
        if (!btnLogin) return;

        btnLogin.disabled = loading;
        btnLogin.style.opacity = loading ? '0.75' : '1';

        if (btnText) btnText.textContent = loading ? 'Memproses…' : 'Masuk';

        if (btnIcon) {
            btnIcon.classList.toggle('fa-arrow-right', !loading);
            btnIcon.classList.toggle('fa-circle-notch', loading);
            btnIcon.classList.toggle('fa-spin', loading);
        }
    }


    // ══════════════════════════════════════
    // INLINE ALERT — client-side errors
    // ══════════════════════════════════════

    function showInlineAlert(message, type) {
        clearInlineAlert();

        const alertEl = document.createElement('div');
        alertEl.id = 'inlineAlert';
        alertEl.className = `alert alert-${type}`;
        alertEl.innerHTML = `
            <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i>
            <span>${message}</span>
        `;

        loginForm.insertAdjacentElement('beforebegin', alertEl);

        // Auto-dismiss setelah 5 detik
        alertEl._timer = setTimeout(() => alertEl.remove(), 5000);
    }

    function clearInlineAlert() {
        const old = document.getElementById('inlineAlert');
        if (old) {
            clearTimeout(old._timer);
            old.remove();
        }
    }


    // ══════════════════════════════════════
    // AUTO-DISMISS FLASH ALERT (logout, dll)
    // ══════════════════════════════════════

    const flashAlert = document.getElementById('flashAlert');
    if (flashAlert) {
        setTimeout(() => {
            flashAlert.style.transition = 'opacity 0.5s ease';
            flashAlert.style.opacity = '0';
            setTimeout(() => flashAlert.remove(), 500);
        }, 4000);
    }


    // ══════════════════════════════════════
    // HELPER — ambil base URL dari meta/script
    // ══════════════════════════════════════

    function getBaseUrl() {
        // Coba ambil dari <meta name="base-url"> jika ada,
        // fallback ke origin + '/'
        const meta = document.querySelector('meta[name="base-url"]');
        return meta ? meta.content.replace(/\/?$/, '/') : (window.location.origin + '/');
    }

})();