(function ($) {
    'use strict';

    var cfg = window.BiodataPKL || {};
    var v = window.SimmagValidation || {};

    if (window.SimmagValidation && typeof window.SimmagValidation.applyInputRules === 'function') {
        window.SimmagValidation.applyInputRules([
            { selector: '#bNamaPembimbing', rule: 'person_name', label: 'Nama Pembimbing' },
            { selector: '#bWaPembimbing', rule: 'phone', label: 'No WA Pembimbing' },
            { selector: '#bJumlahAnggota', rule: 'numeric', label: 'Jumlah Anggota PKL' },
            { selector: '#bNamaKelompok', rule: 'group_name', label: 'Nama Kelompok' },
            { selector: '#bAlamatInstansi', rule: 'multiline_address', label: 'Alamat Instansi Baru' }
        ]);
    }

    var state = {
        step: 1,
        kategori: 'mandiri',
        jumlahAnggota: 1,
        tglMulai: null,
        tglAkhir: null,
        fpMulai: null,
        fpAkhir: null,
        instansiMode: 'existing',
        instansiData: {},
        anggotaData: [],
        otpVerified: false,
        otpCountdownTimer: null,
    };

    var fpLocale = {
        months: {
            longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        },
        weekdays: {
            longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
        },
    };

    function getCsrfBody(extra) {
        var data = {};
        data[cfg.csrfName] = cfg.csrfHash;
        return $.extend(data, extra || {});
    }

    function refreshCsrf(res) {
        if (res && res.csrf_hash) {
            cfg.csrfHash = res.csrf_hash;
            $('meta[name="csrf-token-hash"]').attr('content', res.csrf_hash);
        }
    }

    function toast(icon, title, timer) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: title,
            showConfirmButton: false,
            timer: timer || 2200,
            timerProgressBar: true,
        });
    }

    function goToStep(n) {
        state.step = n;
        $('.biodata-panel').hide();
        $('#panel' + n).show();

        $('.biodata-step').removeClass('active done');
        for (var i = 1; i < n; i++) {
            $('#stepInd' + i).addClass('done');
        }
        $('#stepInd' + n).addClass('active');

        $('html, body').animate({ scrollTop: 0 }, 250);
    }

    $('input[name="b_kategori"]').on('change', function () {
        state.kategori = $(this).val();
        var isInstansi = state.kategori === 'instansi';
        $('#bFieldInstansiGroup').toggle(isInstansi);

        if (!isInstansi) {
            state.jumlahAnggota = 1;
        } else {
            state.jumlahAnggota = parseInt($('#bJumlahAnggota').val(), 10) || 1;
        }
    });

    $('#bJumlahAnggota').on('change', function () {
        var val = parseInt($(this).val(), 10);
        if (isNaN(val) || val < 1) { $(this).val(1); val = 1; }
        if (val > 10) { $(this).val(10); val = 10; }
        state.jumlahAnggota = val;
    });

    function initDatepickers() {
        var minMulai = $('#bTglMulai').data('min');
        var maxMulai = $('#bTglMulai').data('max');

        state.fpMulai = flatpickr('#bTglMulai', {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd M Y',
            minDate: minMulai,
            maxDate: maxMulai,
            allowInput: false,
            locale: fpLocale,
            onChange: function (dates, dateStr) {
                state.tglMulai = dateStr;

                if (state.fpAkhir) {
                    var minAkhirDate = null;
                    if (dateStr) {
                        minAkhirDate = new Date(dateStr + 'T00:00:00');
                        minAkhirDate.setMonth(minAkhirDate.getMonth() + 2);
                    }

                    state.fpAkhir.set('minDate', minAkhirDate);

                    if (state.tglAkhir && v.validatePklEndDate && v.validatePklEndDate(dateStr, state.tglAkhir)) {
                        state.fpAkhir.clear();
                        state.tglAkhir = null;
                    }
                }
            },
        });

        state.fpAkhir = flatpickr('#bTglAkhir', {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd M Y',
            minDate: null,
            allowInput: false,
            locale: fpLocale,
            onChange: function (dates, dateStr) {
                state.tglAkhir = dateStr;
            },
        });
    }

    function initSelect2() {
        $('#bNamaInstansi').select2({
            placeholder: 'Ketik atau pilih instansi...',
            allowClear: true,
            width: '100%',
            tags: true,
            createTag: function (params) {
                var term = $.trim(params.term);
                if (!term) return null;
                return { id: 'new:' + term, text: term + ' (Tambah Baru)', newTag: true, nama: term };
            },
        }).on('select2:select', function (e) {
            var val = e.params.data.id || '';

            if (String(val).startsWith('new:')) {
                state.instansiMode = 'new';
                state.instansiData.nama = e.params.data.nama || String(val).replace('new:', '');
                $('#bFieldAlamatBaru, #bFieldKotaBaru').show();
            } else if (String(val).startsWith('existing:')) {
                state.instansiMode = 'existing';
                state.instansiData.id = parseInt(String(val).replace('existing:', ''), 10);
                state.instansiData.nama = e.params.data.text;
                $('#bFieldAlamatBaru, #bFieldKotaBaru').hide();
            }
        }).on('select2:clear', function () {
            state.instansiMode = 'existing';
            state.instansiData = {};
            $('#bFieldAlamatBaru, #bFieldKotaBaru').hide();
        });
    }

    function init() {
        initDatepickers();
        initSelect2();
        goToStep(1);
    }

    $(document).ready(init);

}(jQuery));