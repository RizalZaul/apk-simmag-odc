/**
 * ===============================================================
 * DATA DIRI - PROFILE PAGE JAVASCRIPT
 * ===============================================================
 */

(function () {
  'use strict';

  // Flatpickr instance (hanya tanggal lahir — tgl_mulai/akhir dikelola admin)
  let fpTanggalLahir = null;

  /**
   * Initialize
   */
  function init() {
    initFlatpickr();
    initFormValidation();
    initFormSubmission();
    initEditableFields();
    calculateDurasi();
    initPassword();
  }

  // ---------------------------------------------------------------
  // FLATPICKR — hanya tanggal lahir
  // ---------------------------------------------------------------

  function initFlatpickr() {
    const displayEl = document.getElementById('tanggal_lahir_display');
    const hiddenEl  = document.getElementById('tanggal_lahir');

    if (!displayEl) return;

    fpTanggalLahir = flatpickr(displayEl, {
      dateFormat   : 'Y-m-d',
      locale       : 'id',
      disableMobile: true,
      allowInput   : false,
      maxDate      : 'today',
      defaultDate  : hiddenEl ? (hiddenEl.value || null) : null,
      onReady(selectedDates, dateStr, instance) {
        if (selectedDates.length) {
          displayEl.value = instance.formatDate(selectedDates[0], 'd F Y');
        }
        lockFlatpickr(instance, displayEl);
      },
      onChange(selectedDates, dateStr, instance) {
        if (hiddenEl) hiddenEl.value = dateStr;
        if (selectedDates.length) {
          displayEl.value = instance.formatDate(selectedDates[0], 'd F Y');
        }
      }
    });
  }

  function lockFlatpickr(instance, displayEl) {
    if (!instance) return;
    instance.set('clickOpens', false);
    if (displayEl) displayEl.setAttribute('readonly', true);
  }

  function unlockFlatpickr(instance, displayEl) {
    if (!instance) return;
    instance.set('clickOpens', true);
    if (displayEl) displayEl.removeAttribute('readonly');
  }

  // ---------------------------------------------------------------
  // DURASI PKL
  // Tgl mulai/akhir bukan input — baca dari DATA_DIRI_CONFIG (PHP inject).
  // ---------------------------------------------------------------

  function calculateDurasi() {
    const durasiEl = document.getElementById('durasiPKL');
    if (!durasiEl) return;

    const config   = window.DATA_DIRI_CONFIG || {};
    const mulaiVal = config.tglMulai || '';
    const akhirVal = config.tglAkhir || '';

    if (!mulaiVal || !akhirVal) {
      durasiEl.textContent = '-';
      return;
    }

    const mulai = new Date(mulaiVal);
    const akhir = new Date(akhirVal);

    if (isNaN(mulai) || isNaN(akhir) || akhir < mulai) {
      durasiEl.textContent = '-';
      return;
    }

    const diffDays = Math.ceil(Math.abs(akhir - mulai) / (1000 * 60 * 60 * 24));
    const months   = Math.floor(diffDays / 30);
    const days     = diffDays % 30;

    let text = '';
    if (months > 0) {
      text = months + ' bulan';
      if (days > 0) text += ' ' + days + ' hari';
    } else {
      text = days + ' hari';
    }

    durasiEl.textContent      = text;
    durasiEl.style.color      = '#20a8a8';
    durasiEl.style.fontWeight = '600';
  }

  // ---------------------------------------------------------------
  // FORM VALIDATION
  // ---------------------------------------------------------------

  function initFormValidation() {
    const form = document.getElementById('formDataDiri');
    if (!form) return;

    form.querySelectorAll('input[required], textarea[required], select[required]').forEach(input => {
      input.addEventListener('blur',  function () { validateField(this); });
      input.addEventListener('input', function () {
        if (this.classList.contains('error')) validateField(this);
      });
    });
  }

  function validateField(field) {
    if (field.type === 'hidden') return true;

    const value = field.value.trim();
    let isValid  = true;
    let errorMsg = '';

    if (field.hasAttribute('required') && !value) {
      isValid  = false;
      errorMsg = 'Field ini wajib diisi';
    } else if (field.type === 'email' && value) {
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
        isValid  = false;
        errorMsg = 'Format email tidak valid';
      }
    } else if (field.type === 'tel' && value) {
      if (!/^08[0-9]{8,11}$/.test(value)) {
        isValid  = false;
        errorMsg = 'Format nomor WA tidak valid (contoh: 08123456789)';
      }
    }

    if (!isValid) showFieldError(field, errorMsg);
    else          clearFieldError(field);

    return isValid;
  }

  function showFieldError(field, message) {
    field.classList.add('error');
    field.style.borderColor = '#e74c3c';

    let errorDiv = field.parentElement.querySelector('.error-message');
    if (!errorDiv) {
      errorDiv = document.createElement('small');
      errorDiv.className = 'error-message';
      errorDiv.style.cssText = 'color:#e74c3c;font-size:12px;margin-top:4px;display:block;';
      field.parentElement.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
  }

  function clearFieldError(field) {
    field.classList.remove('error');
    field.style.borderColor = '';
    const errorDiv = field.parentElement.querySelector('.error-message');
    if (errorDiv) errorDiv.remove();
  }

  // ---------------------------------------------------------------
  // FORM SUBMISSION — AJAX ke DATA_DIRI_CONFIG.updateUrl
  // ---------------------------------------------------------------

  function initFormSubmission() {
    const form = document.getElementById('formDataDiri');
    if (!form) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      const inputs = this.querySelectorAll('input[required]:not([type="hidden"]), textarea[required], select[required]');
      let isValid = true;
      inputs.forEach(input => { if (!validateField(input)) isValid = false; });

      if (!isValid) {
        Swal.fire({
          icon: 'error',
          title: 'Validasi Gagal',
          text: 'Mohon lengkapi semua field yang wajib diisi',
          confirmButtonColor: '#20a8a8'
        });
        return;
      }

      Swal.fire({
        title: 'Menyimpan Data...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
      });

      const formData  = new FormData(this);
      const updateUrl = (window.DATA_DIRI_CONFIG || {}).updateUrl || '';

      fetch(updateUrl, {
        method : 'POST',
        body   : formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              icon             : 'success',
              title            : 'Berhasil!',
              text             : data.message || 'Data diri berhasil diperbarui',
              timer            : 2000,
              showConfirmButton: false
            }).then(() => {
              disableEditMode();
              window.location.reload();
            });
          } else {
            let errMsg = data.message || 'Gagal menyimpan data.';
            if (data.errors) errMsg = Object.values(data.errors).join('\n');
            Swal.fire({ icon: 'error', title: 'Gagal', text: errMsg, confirmButtonColor: '#20a8a8' });
          }
        })
        .catch(() => {
          Swal.fire({
            icon : 'error',
            title: 'Kesalahan Jaringan',
            text : 'Tidak dapat terhubung ke server. Coba lagi.',
            confirmButtonColor: '#20a8a8'
          });
        });
    });
  }

  // ---------------------------------------------------------------
  // EDITABLE FIELDS HIGHLIGHT
  // ---------------------------------------------------------------

  function initEditableFields() {
    document.querySelectorAll('.form-control.editable').forEach(field => {
      field.addEventListener('focus', function () {
        this.style.transform  = 'scale(1.01)';
        this.style.boxShadow  = '0 0 0 4px rgba(32, 168, 168, 0.15)';
      });
      field.addEventListener('blur', function () {
        this.style.transform = '';
        this.style.boxShadow = '';
      });
    });
  }

  // ---------------------------------------------------------------
  // UBAH PASSWORD
  // [FIX] URL dibaca dari window.DATA_DIRI_CONFIG.updatePasswordUrl
  //       yang di-inject PHP di data-diri.php — bukan hardcode di sini,
  //       karena file .js statis tidak diproses PHP.
  // ---------------------------------------------------------------

  function initPassword() {
    // [FIX] Ambil URL dari config yang di-inject PHP, bukan PHP tag langsung
    const updatePasswordUrl = (window.DATA_DIRI_CONFIG || {}).updatePasswordUrl || '';

    const REGEX_PASSWORD = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}$/;

    // ── Tombol Edit & Batal ────────────────────────────────────
    const btnEdit   = document.getElementById('btnEditPassword');
    const btnCancel = document.getElementById('btnCancelPassword');

    if (btnEdit)   btnEdit.addEventListener('click',   enablePasswordMode);
    if (btnCancel) btnCancel.addEventListener('click',  disablePasswordMode);

    // ── Toggle show / hide password ───────────────────────────
    document.querySelectorAll('.btn-toggle-password').forEach(btn => {
      btn.addEventListener('click', function () {
        const target = document.querySelector(this.dataset.target);
        if (!target) return;

        const isPass = target.type === 'password';
        target.type  = isPass ? 'text' : 'password';

        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye',      !isPass);
        icon.classList.toggle('fa-eye-slash',  isPass);
      });
    });

    // ── Indikator kekuatan password (real-time) ────────────────
    const pwInput = document.getElementById('password_baru');
    if (pwInput) {
      pwInput.addEventListener('input', function () {
        const strength = calcPasswordStrength(this.value);
        const fill  = document.getElementById('passwordStrengthFill');
        const label = document.getElementById('passwordStrengthLabel');

        if (fill) {
          fill.style.width = strength.pct + '%';
          fill.className   = 'password-strength-fill strength-' + strength.level;
        }
        if (label) label.textContent = strength.label;
      });
    }

    // ── Submit ─────────────────────────────────────────────────
    const form = document.getElementById('formPassword');
    if (!form) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      const passwordBaru       = (document.getElementById('password_baru')?.value       || '').trim();
      const konfirmasiPassword = (document.getElementById('konfirmasi_password')?.value  || '').trim();

      // Validasi sisi klien
      if (!passwordBaru) {
        return Swal.fire({ icon: 'warning', title: 'Field Wajib Diisi', text: 'Password baru tidak boleh kosong.', confirmButtonColor: '#20a8a8' });
      }
      if (passwordBaru.length < 8) {
        return Swal.fire({ icon: 'warning', title: 'Password Terlalu Pendek', text: 'Password minimal 8 karakter.', confirmButtonColor: '#20a8a8' });
      }
      if (!REGEX_PASSWORD.test(passwordBaru)) {
        return Swal.fire({
          icon : 'warning',
          title: 'Password Kurang Kuat',
          html : 'Password harus mengandung:<br>' +
                 '<ul style="text-align:left;margin:8px 0 0;padding-left:20px;">' +
                 '<li>Minimal 8 karakter</li>' +
                 '<li>Huruf besar (A–Z)</li>' +
                 '<li>Huruf kecil (a–z)</li>' +
                 '<li>Angka (0–9)</li>' +
                 '<li>Simbol (!@#$% dll.)</li>' +
                 '</ul>',
          confirmButtonColor: '#20a8a8',
        });
      }
      if (passwordBaru !== konfirmasiPassword) {
        return Swal.fire({ icon: 'warning', title: 'Password Tidak Cocok', text: 'Konfirmasi password tidak sesuai dengan password baru.', confirmButtonColor: '#20a8a8' });
      }

      const btnSave = document.getElementById('btnSavePassword');
      if (btnSave) btnSave.disabled = true;

      Swal.fire({ title: 'Menyimpan password...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

      const formData = new FormData(form);

      fetch(updatePasswordUrl, {
        method : 'POST',
        body   : formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              icon             : 'success',
              title            : 'Password Diperbarui!',
              text             : data.message || 'Password berhasil diubah.',
              timer            : 1800,
              showConfirmButton: false,
            });
            disablePasswordMode();
          } else {
            Swal.fire({
              icon : 'error',
              title: 'Gagal',
              text : data.message || 'Gagal menyimpan password.',
              confirmButtonColor: '#20a8a8'
            });
          }
        })
        .catch(() => {
          Swal.fire({
            icon : 'error',
            title: 'Kesalahan Jaringan',
            text : 'Tidak dapat terhubung ke server. Coba lagi.',
            confirmButtonColor: '#20a8a8'
          });
        })
        .finally(() => {
          if (btnSave) btnSave.disabled = false;
        });
    });

    // ── Enable password edit mode ──────────────────────────────
    function enablePasswordMode() {
      document.querySelectorAll('.password-field').forEach(f => {
        f.disabled = false;
        f.classList.add('editable');
      });

      const wrapper = document.getElementById('passwordStrengthWrapper');
      const actions = document.getElementById('passwordFormActions');
      if (wrapper) wrapper.style.display = 'flex';
      if (actions) actions.style.display = 'flex';

      const btn = document.getElementById('btnEditPassword');
      if (btn) btn.style.display = 'none';

      const first = document.getElementById('password_baru');
      if (first) setTimeout(() => first.focus(), 200);
    }

    // ── Disable password edit mode & reset semua field ─────────
    function disablePasswordMode() {
      document.querySelectorAll('.password-field').forEach(f => {
        f.value    = '';
        f.disabled = true;
        f.type     = 'password';
        f.classList.remove('editable');
      });

      // Reset ikon mata ke default (fa-eye)
      document.querySelectorAll('.btn-toggle-password').forEach(btn => {
        const icon = btn.querySelector('i');
        if (icon) {
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        }
      });

      // Reset strength bar
      const fill  = document.getElementById('passwordStrengthFill');
      const label = document.getElementById('passwordStrengthLabel');
      if (fill)  { fill.style.width = '0%'; fill.className = 'password-strength-fill'; }
      if (label) label.textContent = 'Masukkan password';

      const wrapper = document.getElementById('passwordStrengthWrapper');
      const actions = document.getElementById('passwordFormActions');
      if (wrapper) wrapper.style.display = 'none';
      if (actions) actions.style.display = 'none';

      const btn = document.getElementById('btnEditPassword');
      if (btn) btn.style.display = '';
    }
  }

  // ── Hitung kekuatan password ─────────────────────────────────
  // Didefinisikan di scope IIFE agar bisa dipakai oleh initPassword()
  function calcPasswordStrength(password) {
    if (!password) return { pct: 0, level: '', label: 'Masukkan password' };

    let score = 0;
    if (password.length >= 8)           score++;
    if (password.length >= 12)          score++;
    if (/[A-Z]/.test(password))         score++;
    if (/[a-z]/.test(password))         score++;
    if (/[0-9]/.test(password))         score++;
    if (/[^A-Za-z0-9]/.test(password))  score++;

    const map = {
      0: { pct: 10,  level: 'weak',   label: 'Sangat lemah' },
      1: { pct: 20,  level: 'weak',   label: 'Lemah'        },
      2: { pct: 40,  level: 'weak',   label: 'Kurang'       },
      3: { pct: 60,  level: 'medium', label: 'Cukup'        },
      4: { pct: 75,  level: 'medium', label: 'Sedang'       },
      5: { pct: 90,  level: 'strong', label: 'Kuat'         },
      6: { pct: 100, level: 'strong', label: 'Sangat kuat'  },
    };
    return map[score] ?? map[0];
  }

  // ---------------------------------------------------------------
  // EDIT MODE — form data diri utama
  // ---------------------------------------------------------------

  window.enableEditMode = function () {
    document.body.classList.add('edit-mode');

    document.querySelectorAll('.form-control.editable').forEach(field => {
      if (field.tagName === 'SELECT') field.disabled  = false;
      else                            field.readOnly  = false;
    });

    unlockFlatpickr(fpTanggalLahir, document.getElementById('tanggal_lahir_display'));

    document.getElementById('formActions').style.display  = 'flex';
    document.getElementById('editModeBar').style.display  = 'none';

    showEditModeBanner();

    const firstField = document.querySelector('.form-control.editable');
    if (firstField) setTimeout(() => firstField.focus(), 300);
  };

  window.cancelEdit = function () {
    Swal.fire({
      title              : 'Batalkan Perubahan?',
      text               : 'Semua perubahan yang belum disimpan akan hilang',
      icon               : 'warning',
      showCancelButton   : true,
      confirmButtonColor : '#e74c3c',
      cancelButtonColor  : '#6c757d',
      confirmButtonText  : 'Ya, Batalkan',
      cancelButtonText   : 'Tidak'
    }).then(result => {
      if (result.isConfirmed) {
        disableEditMode();
        window.location.reload();
      }
    });
  };

  function disableEditMode() {
    document.body.classList.remove('edit-mode');

    document.querySelectorAll('.form-control.editable').forEach(field => {
      if (field.tagName === 'SELECT') field.disabled = true;
      else                            field.readOnly = true;
    });

    lockFlatpickr(fpTanggalLahir, document.getElementById('tanggal_lahir_display'));

    document.getElementById('formActions').style.display = 'none';
    document.getElementById('editModeBar').style.display = 'flex';

    const banner = document.querySelector('.edit-mode-banner');
    if (banner) banner.remove();
  }

  // ---------------------------------------------------------------
  // EDIT MODE BANNER
  // ---------------------------------------------------------------

  function showEditModeBanner() {
    const existing = document.querySelector('.edit-mode-banner');
    if (existing) existing.remove();

    const banner = document.createElement('div');
    banner.className = 'edit-mode-banner';
    banner.innerHTML = '<i class="fas fa-edit"></i><span>Mode Edit Aktif</span>';
    document.body.appendChild(banner);

    setTimeout(() => {
      banner.style.animation = 'slideOutRight 0.3s ease';
      setTimeout(() => banner.remove(), 300);
    }, 3000);
  }

  // ---------------------------------------------------------------
  // BOOT
  // ---------------------------------------------------------------

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();