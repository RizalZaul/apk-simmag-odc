document.addEventListener('DOMContentLoaded', function () {
    if (window.SimmagValidation && typeof window.SimmagValidation.applyInputRules === 'function') {
        window.SimmagValidation.applyInputRules([
            { selector: '#inputNamaLengkap', rule: 'person_name', label: 'Nama Lengkap' },
            { selector: '#inputNamaPanggilan', rule: 'nickname', label: 'Nama Panggilan' },
            { selector: '#inputEmail', rule: 'email', label: 'Email' },
            { selector: '#inputNoWa', rule: 'phone', label: 'No WA' },
            { selector: '#inputAlamat', rule: 'multiline_address', label: 'Alamat' }
        ]);
    }

    const tabBtns = document.querySelectorAll('.profil-tab-btn');
    const tabContents = document.querySelectorAll('.profil-tab-content');
    const elHeading = document.querySelector('.page-heading');
    const elSubheading = document.querySelector('.page-subheading');
    const elHeaderH1 = document.querySelector('.page-title');

    const tabMeta = {
        'biodata': { heading: 'Profil Saya', subheading: 'Data diri dan informasi akun' },
        'setting': { heading: 'Pengaturan', subheading: 'Pengaturan form biodata siswa PKL' },
    };

    tabBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var target = btn.dataset.tab;

            tabBtns.forEach(function (b) { b.classList.remove('active'); });
            tabContents.forEach(function (c) { c.classList.remove('active'); });
            btn.classList.add('active');

            var targetEl = document.getElementById('tab-' + target);
            if (targetEl) targetEl.classList.add('active');

            var meta = tabMeta[target];
            if (meta) {
                if (elHeading) elHeading.textContent = meta.heading;
                if (elSubheading) elSubheading.textContent = meta.subheading;
                if (elHeaderH1) elHeaderH1.textContent = meta.heading;
                document.title = meta.heading + ' — SIMMAG ODC';
            }

            setUrlParam('tab', target);
            removeUrlParam('mode');
        });
    });

    function setUrlParam(key, value) {
        var url = new URL(window.location.href);
        url.searchParams.set(key, value);
        window.history.replaceState(null, '', url.toString());
    }

    function removeUrlParam(key) {
        var url = new URL(window.location.href);
        url.searchParams.delete(key);
        window.history.replaceState(null, '', url.toString());
    }

    function getUrlParam(key) {
        return new URL(window.location.href).searchParams.get(key);
    }

    function buildMissingFieldsMessage(missingFields, totalRequired) {
        var labels = Array.from(new Set((missingFields || []).filter(Boolean)));
        if (!labels.length) return 'Semua field harus diisi.';
        if (totalRequired && labels.length >= totalRequired) return 'Semua field harus diisi.';
        if (labels.length === 1) return labels[0] + ' wajib diisi.';
        return 'Field berikut wajib diisi: ' + labels.join(', ') + '.';
    }

    var currentEditMode = null;

    function confirmSwitchEdit(targetMode) {
        return new Promise(function (resolve) {
            if (!currentEditMode || currentEditMode === targetMode) {
                resolve(true);
                return;
            }

            var sectionLabel = currentEditMode === 'biodata' ? 'Informasi Pribadi' : 'Ubah Password';

            Swal.fire({
                icon: 'question',
                title: 'Batalkan perubahan?',
                html: 'Form <strong>' + sectionLabel + '</strong> belum disimpan.<br>Batalkan dan pindah?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Tidak',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: 'var(--primary)',
                reverseButtons: true,
            }).then(function (result) {
                resolve(result.isConfirmed);
            });
        });
    }

    var btnEditBiodata = document.getElementById('btnEditBiodata');
    var btnCancelBiodata = document.getElementById('btnCancelBiodata');
    var actionsBiodata = document.getElementById('actionsBiodata');

    var biodataFields = [
        { display: document.getElementById('displayNamaLengkap'), input: document.getElementById('inputNamaLengkap') },
        { display: document.getElementById('displayNamaPanggilan'), input: document.getElementById('inputNamaPanggilan') },
        { display: document.getElementById('displayEmail'), input: document.getElementById('inputEmail') },
        { display: document.getElementById('displayNoWa'), input: document.getElementById('inputNoWa') },
        { display: document.getElementById('displayAlamat'), input: document.getElementById('inputAlamat') },
    ];

    var biodataOriginal = {};

    function enterBiodataEdit() {
        biodataFields.forEach(function (pair) {
            if (pair.input) biodataOriginal[pair.input.name] = pair.input.value;
        });

        biodataFields.forEach(function (pair) {
            if (pair.display) pair.display.style.display = 'none';
            if (pair.input) pair.input.style.display = 'block';
        });

        actionsBiodata.style.display = 'flex';
        btnEditBiodata.style.display = 'none';

        if (biodataFields[0].input) biodataFields[0].input.focus();

        currentEditMode = 'biodata';
        setUrlParam('mode', 'edit_biodata');
    }

    function exitBiodataEdit(restore) {
        if (restore) {
            biodataFields.forEach(function (pair) {
                if (pair.input) pair.input.value = biodataOriginal[pair.input.name] || '';
            });
        }

        biodataFields.forEach(function (pair) {
            if (pair.display) pair.display.style.display = 'flex';
            if (pair.input) pair.input.style.display = 'none';
        });

        actionsBiodata.style.display = 'none';
        btnEditBiodata.style.display = '';

        currentEditMode = null;
        removeUrlParam('mode');
    }

    if (btnEditBiodata) {
        btnEditBiodata.addEventListener('click', function () {
            confirmSwitchEdit('biodata').then(function (confirmed) {
                if (!confirmed) return;
                if (currentEditMode === 'password') exitPasswordEdit();
                enterBiodataEdit();
            });
        });
    }

    if (btnCancelBiodata) {
        btnCancelBiodata.addEventListener('click', function () {
            exitBiodataEdit(true);
        });
    }

    function init() {
        var initMode = getUrlParam('mode');
        if (initMode === 'edit_biodata') {
            enterBiodataEdit();
        }
    }

    init();
});