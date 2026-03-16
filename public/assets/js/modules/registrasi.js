/**
 * registrasi.js — Form Pendaftaran PKL
 *
 * State yang disimpan di memori:
 *   step        → step aktif (1/2/3)
 *   step1Data   → data form step 1
 *   step2Data   → data form step 2 (array anggota)
 *   otpTimer    → interval ID untuk countdown
 */

$(function () {

    'use strict';

    const CFG = window.REG_CONFIG;

    /* ════════════════════════════════════════════
     * STATE
     * ════════════════════════════════════════════ */

    let currentStep = 1;
    let step1Data = {};
    let step2Data = {};
    let otpTimerRef = null;


    /* ════════════════════════════════════════════
     * CSRF HELPER
     * ════════════════════════════════════════════ */

    function csrfHeaders() {
        return {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': CFG.csrfHash,
        };
    }

    function refreshCsrf(res) {
        if (res && res.csrf) {
            CFG.csrfName = res.csrf.name;
            CFG.csrfHash = res.csrf.hash;
            $('[name="' + res.csrf.name + '"]').val(res.csrf.hash);
        }
    }


    /* ════════════════════════════════════════════
     * STEP NAVIGATION HELPERS
     * ════════════════════════════════════════════ */

    const STEP_META = {
        1: { title: 'Pilih Tipe PKL', subtitle: 'Tentukan jenis PKL yang akan dijalani' },
        2: { title: 'Biodata Anggota', subtitle: 'Isi data diri setiap anggota kelompok' },
        3: { title: 'Verifikasi Email', subtitle: 'Masukkan kode OTP yang dikirim ke email ketua' },
    };

    function goToStep(n) {
        currentStep = n;

        // Update panels
        $('.step-panel').removeClass('active');
        $('#panel-' + n).addClass('active');

        // Update header teks
        $('#cardTitle').text(STEP_META[n].title);
        $('#cardSubtitle').text(STEP_META[n].subtitle);

        // Update step indicator
        for (let i = 1; i <= 3; i++) {
            const $dot = $('#sd-' + i);
            const $item = $('#si-' + i);
            const $conn = $('#sc-' + i); // konektor setelah step i

            $dot.removeClass('active done');
            $item.removeClass('active done');

            if (i < n) {
                $dot.addClass('done');
                $item.addClass('done');
                if ($conn.length) $conn.addClass('done');
            } else if (i === n) {
                $dot.addClass('active');
                $item.addClass('active');
            } else {
                if ($conn.length) $conn.removeClass('done');
            }
        }

        // Scroll ke atas card
        $('html, body').animate({ scrollTop: $('.reg-card').offset().top - 24 }, 250);
    }


    /* ════════════════════════════════════════════
     * VALIDATION HELPERS
     * ════════════════════════════════════════════ */

    function setError($group, msg) {
        $group.addClass('has-error');
        $group.find('.invalid-feedback span').text(msg);
        $group.find('.form-control, .select2').first()
            .addClass('is-invalid');
    }

    function clearError($group) {
        $group.removeClass('has-error');
        $group.find('.form-control').removeClass('is-invalid');
    }

    function clearAllErrors(parent) {
        $(parent).find('.form-group').each(function () { clearError($(this)); });
    }

    function showAlert($alert, $msg, text) {
        $msg.text(text);
        $alert.addClass('visible');
    }

    function hideAlert($alert) {
        $alert.removeClass('visible');
    }

    function setLoading($btn, loading) {
        $btn.toggleClass('loading', loading).prop('disabled', loading);
    }


    /* ════════════════════════════════════════════
     * SELECT2 — Instansi
     * ════════════════════════════════════════════ */

    const BARU_ID = '__baru__';
    const BARU_TEKS = '+ Tambah Instansi Baru';
    let instansiBaru = false; // apakah user pilih tambah baru

    function initSelect2Instansi() {
        $('#instansiSelect').select2({
            placeholder: '-- Pilih atau Cari Instansi --',
            allowClear: true,
            minimumInputLength: 0,
            ajax: {
                url: CFG.urlInstansiList,
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term || '',
                        kategori: $('#kategori_instansi').val() || '',
                    };
                },
                processResults: function (data) {
                    // Tambahkan opsi "+ Tambah Instansi Baru" di atas
                    const results = [
                        { id: BARU_ID, text: BARU_TEKS }
                    ].concat(data.results || []);
                    return { results };
                },
                cache: false,
            },
            templateResult: function (item) {
                if (item.id === BARU_ID) {
                    return $('<span style="color:#0f766e;font-weight:700;">' + item.text + '</span>');
                }
                if (item.kota) {
                    return $('<span>' + item.text
                        + ' <span class="instansi-option-kota">— ' + item.kota + '</span></span>');
                }
                return item.text;
            },
        });

        $('#instansiSelect').on('select2:select', function (e) {
            const id = e.params.data.id;
            if (id === BARU_ID) {
                instansiBaru = true;
                $('#instansiBaruFields').addClass('visible');
            } else {
                instansiBaru = false;
                $('#instansiBaruFields').removeClass('visible');
            }
        });

        $('#instansiSelect').on('select2:clear', function () {
            instansiBaru = false;
            $('#instansiBaruFields').removeClass('visible');
        });
    }

    // Re-trigger Select2 saat kategori berubah (reset pilihan lama)
    $('#kategori_instansi').on('change', function () {
        $('#instansiSelect').val(null).trigger('change');
        instansiBaru = false;
        $('#instansiBaruFields').removeClass('visible');
    });

    flatpickr('#tgl_mulai', {
        locale: 'id',
        dateFormat: 'Y-m-d',        // format yang dikirim ke server
        altInput: true,
        altFormat: 'd F Y',         // format tampilan ke user
        minDate: new Date().fp_incr(-7),
        onChange: (dates) => { /* validasi tgl_akhir > tgl_mulai */ }
    });

    flatpickr('#tgl_akhir', {
        locale: 'id',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd F Y',
        minDate: 'today',
    });

    /* ════════════════════════════════════════════
     * STEP 1 — Tipe PKL toggle
     * ════════════════════════════════════════════ */

    $('.tipe-pkl-option').on('click', function () {
        $('.tipe-pkl-option').removeClass('selected');
        $(this).addClass('selected');
        $(this).find('input[type=radio]').prop('checked', true);

        const tipe = $(this).find('input').val();
        if (tipe === 'instansi') {
            $('#instansiSection').addClass('visible');
            initSelect2Instansi();
        } else {
            $('#instansiSection').removeClass('visible');
        }
    });


    /* ════════════════════════════════════════════
     * STEP 1 — Tombol Lanjut
     * ════════════════════════════════════════════ */

    $('#btnStep1Next').on('click', function () {
        hideAlert($('#alertStep1'));
        clearAllErrors('#panel-1');

        const tipe = $('input[name=tipe_pkl]:checked').val();
        const tglMulai = $('#tgl_mulai').val();
        const tglAkhir = $('#tgl_akhir').val();
        let valid = true;

        // Validasi tanggal
        if (!tglMulai) {
            setError($('#tgl_mulai').closest('.form-group'), 'Tanggal mulai wajib diisi.');
            valid = false;
        }
        if (!tglAkhir) {
            setError($('#tgl_akhir').closest('.form-group'), 'Tanggal selesai wajib diisi.');
            valid = false;
        }
        if (tglMulai && tglAkhir && tglAkhir <= tglMulai) {
            setError($('#tgl_akhir').closest('.form-group'), 'Tanggal selesai harus setelah tanggal mulai.');
            valid = false;
        }

        if (tipe === 'instansi') {
            // Validasi kategori instansi
            if (!$('#kategori_instansi').val()) {
                setError($('#kategori_instansi').closest('.form-group'), 'Kategori instansi wajib dipilih.');
                valid = false;
            }

            // Validasi pilihan instansi
            const idInstansi = $('#instansiSelect').val();
            if (!idInstansi) {
                setError($('#instansiSelect').closest('.form-group'), 'Pilih instansi atau tambah yang baru.');
                valid = false;
            }

            // Validasi field instansi baru
            if (instansiBaru) {
                if (!$('#nama_instansi').val().trim()) {
                    setError($('#nama_instansi').closest('.form-group'), 'Nama instansi baru wajib diisi.');
                    valid = false;
                }
                if (!$('#kota_instansi').val().trim()) {
                    setError($('#kota_instansi').closest('.form-group'), 'Kota instansi wajib diisi.');
                    valid = false;
                }
            }

            // Validasi nama kelompok & jumlah anggota
            if (!$('#nama_kelompok').val().trim()) {
                setError($('#nama_kelompok').closest('.form-group'), 'Nama kelompok wajib diisi.');
                valid = false;
            }
            if (!$('#jumlah_anggota').val()) {
                setError($('#jumlah_anggota').closest('.form-group'), 'Jumlah anggota wajib dipilih.');
                valid = false;
            }
        }

        if (!valid) return;

        // Simpan step1Data
        step1Data = {
            tipe_pkl: tipe,
            tgl_mulai: tglMulai,
            tgl_akhir: tglAkhir,
            kategori_instansi: $('#kategori_instansi').val() || '',
            id_instansi: (!instansiBaru && $('#instansiSelect').val() !== BARU_ID)
                ? ($('#instansiSelect').val() || '') : '',
            nama_instansi: instansiBaru ? $('#nama_instansi').val().trim() : '',
            alamat_instansi: instansiBaru ? $('#alamat_instansi').val().trim() : '',
            kota_instansi: instansiBaru ? $('#kota_instansi').val().trim() : '',
            nama_pembimbing: $('#pembimbing').val().trim(),
            no_wa_pembimbing: $('#no_pembimbing').val().trim(),
            nama_kelompok: $('#nama_kelompok').val().trim(),
            jumlah_anggota: tipe === 'instansi'
                ? parseInt($('#jumlah_anggota').val(), 10) : 1,
        };

        buildAnggotaForms(step1Data.jumlah_anggota, step1Data.tipe_pkl);
        goToStep(2);
    });


    /* ════════════════════════════════════════════
     * STEP 2 — Build biodata forms dinamis
     * ════════════════════════════════════════════ */

    function buildAnggotaForms(jumlah, tipe) {
        const $wrapper = $('#anggotaWrapper');
        const isMandiri = tipe === 'mandiri';
        $wrapper.empty();

        for (let i = 0; i < jumlah; i++) {
            const isKetua = i === 0;
            // Mandiri: hanya 1 orang, tidak ada badge/role
            const badgeHtml = isMandiri
                ? ''
                : `<span class="anggota-badge">${isKetua ? 'Ketua' : 'Anggota'}</span>`;

            // Mandiri: field jurusan disembunyikan
            const jurusanHtml = isMandiri
                ? ''
                : `<div class="form-group">
                        <label class="form-label">Jurusan</label>
                        <input type="text" name="jurusan" class="form-control"
                            placeholder="Jurusan / program studi">
                    </div>`;

            $wrapper.append(`
            <div class="anggota-card" data-index="${i}">
                <div class="anggota-card-header">
                    <i class="fas fa-user-circle"></i>
                    <h4 class="anggota-card-title">Anggota ${i + 1}</h4>
                    ${badgeHtml}
                </div>
                <div class="anggota-card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control"
                                placeholder="Nama lengkap" autocomplete="off">
                            <div class="invalid-feedback"><i class="fas fa-circle-xmark"></i> <span></span></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nama Panggilan</label>
                            <input type="text" name="nama_panggilan" class="form-control"
                                placeholder="Nama panggilan">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control"
                            placeholder="Email aktif ${isKetua ? '(untuk menerima OTP)' : ''}">
                        <div class="invalid-feedback"><i class="fas fa-circle-xmark"></i> <span></span></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control"
                                placeholder="Kota kelahiran">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="text" name="tgl_lahir" class="form-control flatpickr-tgl-lahir">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control">
                                <option value="">-- Pilih --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">No. WhatsApp</label>
                            <input type="text" name="no_wa_pkl" class="form-control"
                                placeholder="08xxxxxxxxxx">
                        </div>
                    </div>
                    ${jurusanHtml}
                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2"
                            placeholder="Alamat lengkap"></textarea>
                    </div>
                </div>
            </div>
        `);
        }

        // ✅ Di luar loop — inisialisasi SEKALI setelah semua card selesai dibuat
        flatpickr('.flatpickr-tgl-lahir', {
            locale: 'id',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd F Y',
            maxDate: 'today',
        });
    }

    /* ════════════════════════════════════════════
     * STEP 2 — Tombol Kembali & Lanjut
     * ════════════════════════════════════════════ */

    $('#btnStep2Back').on('click', function () {
        goToStep(1);
    });

    $('#btnStep2Next').on('click', function () {
        hideAlert($('#alertStep2'));
        clearAllErrors('#panel-2');

        let valid = true;
        const emails = [];
        const anggota = [];
        const isMandiri = step1Data.tipe_pkl === 'mandiri';

        $('#anggotaWrapper .anggota-card').each(function () {
            const $card = $(this);
            const idx = parseInt($card.data('index'), 10);
            const no = idx + 1;

            const nama = $card.find('[name=nama_lengkap]').val().trim();
            const email = $card.find('[name=email]').val().trim();

            if (!nama) {
                setError($card.find('[name=nama_lengkap]').closest('.form-group'),
                    'Nama lengkap anggota ke-' + no + ' wajib diisi.');
                valid = false;
            }

            if (!email) {
                setError($card.find('[name=email]').closest('.form-group'),
                    'Email anggota ke-' + no + ' wajib diisi.');
                valid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                setError($card.find('[name=email]').closest('.form-group'),
                    'Format email anggota ke-' + no + ' tidak valid.');
                valid = false;
            } else if (emails.includes(email)) {
                setError($card.find('[name=email]').closest('.form-group'),
                    'Email anggota ke-' + no + ' sama dengan anggota lain.');
                valid = false;
            } else {
                emails.push(email);
            }

            anggota.push({
                nama_lengkap: nama,
                nama_panggilan: $card.find('[name=nama_panggilan]').val().trim(),
                email: email,
                tempat_lahir: $card.find('[name=tempat_lahir]').val().trim(),
                tgl_lahir: $card.find('[name=tgl_lahir]').val(),
                jenis_kelamin: $card.find('[name=jenis_kelamin]').val(),
                no_wa_pkl: $card.find('[name=no_wa_pkl]').val().trim(),
                // mandiri: jurusan & role_in_group tidak ada → null ke DB
                jurusan: isMandiri ? null : ($card.find('[name=jurusan]').val().trim() || null),
                role_in_group: isMandiri ? null : (idx === 0 ? 'ketua' : 'anggota'),
                alamat: $card.find('[name=alamat]').val().trim(),
            });
        });

        if (!valid) {
            showAlert($('#alertStep2'), $('#alertStep2Msg'),
                'Perbaiki error di atas sebelum melanjutkan.');
            $('html, body').animate({ scrollTop: $('.has-error').first().offset().top - 80 }, 300);
            return;
        }

        step2Data = { anggota };

        // Kirim ke server → generate OTP
        setLoading($('#btnStep2Next'), true);
        sendOtp();
    });


    /* ════════════════════════════════════════════
     * SEND OTP
     * ════════════════════════════════════════════ */

    function sendOtp() {
        $.ajax({
            url: CFG.urlSendOtp,
            method: 'POST',
            headers: { ...csrfHeaders(), 'Content-Type': 'application/json' },
            contentType: 'application/json',
            data: JSON.stringify({
                step1: step1Data,
                step2: step2Data,
            }),
            success: function (res) {
                refreshCsrf(res);
                setLoading($('#btnStep2Next'), false);

                // Tampilkan email ketua di step 3
                $('#otpTargetEmail').text(step2Data.anggota[0].email);
                goToStep(3);
                startOtpTimer(30 * 60);
                $('#otpInputGroup .otp-digit').first().focus();
            },
            error: function (xhr) {
                refreshCsrf(xhr.responseJSON);
                setLoading($('#btnStep2Next'), false);

                const msg = xhr.responseJSON?.message ?? 'Gagal mengirim OTP. Coba lagi.';
                showAlert($('#alertStep2'), $('#alertStep2Msg'), msg);
            },
        });
    }


    /* ════════════════════════════════════════════
     * STEP 3 — OTP Input (6 kotak)
     * ════════════════════════════════════════════ */

    // Auto advance & backspace
    $('#otpInputGroup').on('input', '.otp-digit', function () {
        const $this = $(this);
        const val = $this.val().replace(/[^0-9]/g, '');
        $this.val(val.charAt(0)); // hanya 1 digit

        if (val) {
            $this.addClass('filled');
            $this.next('.otp-digit').focus();
        } else {
            $this.removeClass('filled');
        }
        checkOtpComplete();
    });

    $('#otpInputGroup').on('keydown', '.otp-digit', function (e) {
        if (e.key === 'Backspace' && !$(this).val()) {
            $(this).prev('.otp-digit').focus().val('').removeClass('filled');
            checkOtpComplete();
        }
        // Allow paste
        if (e.key === 'v' && (e.ctrlKey || e.metaKey)) return;
    });

    // Handle paste ke kotak pertama
    $('#otpInputGroup .otp-digit').first().on('paste', function (e) {
        e.preventDefault();
        const paste = (e.originalEvent.clipboardData || window.clipboardData)
            .getData('text').replace(/\D/g, '').slice(0, 6);
        $('#otpInputGroup .otp-digit').each(function (i) {
            $(this).val(paste.charAt(i) || '');
            if (paste.charAt(i)) $(this).addClass('filled'); else $(this).removeClass('filled');
        });
        checkOtpComplete();
    });

    function getOtpValue() {
        let otp = '';
        $('#otpInputGroup .otp-digit').each(function () { otp += $(this).val(); });
        return otp;
    }

    function checkOtpComplete() {
        const otp = getOtpValue();
        $('#btnVerifyOtp').prop('disabled', otp.length < 6);
    }

    function resetOtpInputs(shake = false) {
        $('#otpInputGroup .otp-digit').val('').removeClass('filled');
        if (shake) {
            $('#otpInputGroup .otp-digit').addClass('error');
            setTimeout(() => $('#otpInputGroup .otp-digit').removeClass('error'), 600);
        }
        checkOtpComplete();
    }


    /* ════════════════════════════════════════════
     * STEP 3 — OTP Countdown Timer
     * ════════════════════════════════════════════ */

    function startOtpTimer(seconds) {
        clearInterval(otpTimerRef);
        $('#btnResendOtp').prop('disabled', true);
        $('#otpTimerText').show();

        let remaining = seconds;
        otpTimerRef = setInterval(function () {
            remaining--;
            const m = String(Math.floor(remaining / 60)).padStart(2, '0');
            const s = String(remaining % 60).padStart(2, '0');
            $('#otpCountdown').text(m + ':' + s);

            if (remaining <= 0) {
                clearInterval(otpTimerRef);
                $('#otpTimerText').hide();
                $('#btnResendOtp').prop('disabled', false);
            }
        }, 1000);
    }


    /* ════════════════════════════════════════════
     * STEP 3 — Resend OTP
     * ════════════════════════════════════════════ */

    $('#btnResendOtp').on('click', function () {
        setLoading($(this), true);
        resendOtp('');
    });


    /* ════════════════════════════════════════════
     * STEP 3 — Ganti Email
     * ════════════════════════════════════════════ */

    $('#btnToggleGantiEmail').on('click', function () {
        $('#gantiEmailForm').toggleClass('visible');
    });

    $('#btnKirimEmailBaru').on('click', function () {
        const emailBaru = $('#emailBaru').val().trim();
        clearError($('#emailBaru').closest('.form-group'));

        if (!emailBaru) {
            setError($('#emailBaru').closest('.form-group'), 'Email baru wajib diisi.');
            return;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailBaru)) {
            setError($('#emailBaru').closest('.form-group'), 'Format email tidak valid.');
            return;
        }

        setLoading($(this), true);
        resendOtp(emailBaru);
    });

    function resendOtp(emailBaru) {
        $.ajax({
            url: CFG.urlResendOtp,
            method: 'POST',
            headers: csrfHeaders(),
            data: {
                email: emailBaru,
            },
            success: function (res) {
                refreshCsrf(res);
                setLoading($('#btnResendOtp'), false);
                setLoading($('#btnKirimEmailBaru'), false);

                // Update email di info box
                const targetEmail = step2Data.anggota[0].email;
                if (emailBaru) {
                    step2Data.anggota[0].email = emailBaru;
                    $('#otpTargetEmail').text(emailBaru);
                }

                $('#gantiEmailForm').removeClass('visible');
                $('#emailBaru').val('');
                resetOtpInputs();
                startOtpTimer(30 * 60);

                Swal.fire({
                    icon: 'success', title: 'OTP Terkirim',
                    text: res.message, timer: 2000, showConfirmButton: false,
                });
            },
            error: function (xhr) {
                refreshCsrf(xhr.responseJSON);
                setLoading($('#btnResendOtp'), false);
                setLoading($('#btnKirimEmailBaru'), false);

                const msg = xhr.responseJSON?.message ?? 'Gagal mengirim OTP.';
                if (emailBaru) {
                    setError($('#emailBaru').closest('.form-group'), msg);
                } else {
                    showAlert($('#alertStep3'), $('#alertStep3Msg'), msg);
                }
            },
        });
    }


    /* ════════════════════════════════════════════
     * STEP 3 — Verifikasi OTP
     * ════════════════════════════════════════════ */

    $('#btnVerifyOtp').on('click', function () {
        const otp = getOtpValue();
        if (otp.length < 6) return;

        hideAlert($('#alertStep3'));
        setLoading($(this), true);

        $.ajax({
            url: CFG.urlVerifyOtp,
            method: 'POST',
            headers: csrfHeaders(),
            data: {
                otp: otp,
            },
            success: function (res) {
                refreshCsrf(res);
                setLoading($('#btnVerifyOtp'), false);
                clearInterval(otpTimerRef);

                // Sesuaikan pesan sukses berdasarkan tipe PKL
                if (step1Data.tipe_pkl === 'mandiri') {
                    $('#otpSuccessSub').html(
                        'Akun PKL berhasil dibuat. Informasi username dan password telah dikirim ke email kamu.'
                    );
                } else {
                    const namaKelompok = step1Data.nama_kelompok || '-';
                    $('#otpSuccessSub').html(
                        'Akun PKL kelompok <strong>' + namaKelompok + '</strong> berhasil dibuat. Informasi username dan password' +
                        ' telah dikirim ke email masing-masing anggota.' +
                        '<br><br>' +
                        'Ketua kelompok juga menerima rekapitulasi login semua anggota.'
                    );
                }

                // Sembunyikan form OTP, tampilkan sukses
                $('#otpFormSection').fadeOut(200, function () {
                    $('#otpSuccessBox').addClass('visible');
                });
                // Sembunyikan nav buttons
                $('#navStep3').hide();

                // Update step indicator ke selesai semua
                for (let i = 1; i <= 3; i++) {
                    $('#sd-' + i).removeClass('active').addClass('done');
                    $('#si-' + i).removeClass('active').addClass('done');
                    $('#sc-' + i).addClass('done');
                }
            },
            error: function (xhr) {
                refreshCsrf(xhr.responseJSON);
                setLoading($('#btnVerifyOtp'), false);

                const data = xhr.responseJSON ?? {};
                const msg = data.message ?? 'Kode OTP tidak valid.';
                const field = data.field ?? '';

                if (field === 'otp_expired' || field === 'otp_max_attempts' || field === 'otp_used') {
                    // OTP tidak bisa dipakai lagi — aktifkan tombol kirim ulang
                    showAlert($('#alertStep3'), $('#alertStep3Msg'), msg);
                    resetOtpInputs(false);
                    $('#btnResendOtp').prop('disabled', false);
                    clearInterval(otpTimerRef);
                    $('#otpTimerText').hide();
                    $('#btnVerifyOtp').prop('disabled', true);
                } else {
                    showAlert($('#alertStep3'), $('#alertStep3Msg'), msg);
                    resetOtpInputs(true);
                    $('#otpInputGroup .otp-digit').first().focus();
                }
            },
        });
    });


    /* ════════════════════════════════════════════
     * STEP 3 — Kembali ke Step 2
     * ════════════════════════════════════════════ */

    $('#btnStep3Back').on('click', function () {
        clearInterval(otpTimerRef);
        goToStep(2);
    });

});