$(document).ready(function () {

    var $searchKategori = $('#pklModulSearchKategori');

    if ($searchKategori.length) {
        var $cardList = $('#pklModulCardList');
        var $cards = $cardList.find('.pkl-kat-card');
        var $emptySearch = $('#pklModulEmptySearch');
        var $countNum = $('#pklModulKategoriCountNum');
        var totalKategori = $cards.length;

        $searchKategori.on('input keyup', function () {
            var keyword = $(this).val().toLowerCase().trim();
            var visible = 0;

            $cards.each(function () {
                var nama = $(this).data('nama') || '';
                var match = !keyword || nama.indexOf(keyword) !== -1;
                $(this).toggle(match);
                if (match) visible++;
            });

            $countNum.text(visible);

            var isEmpty = visible === 0 && keyword !== '';
            $emptySearch.toggle(isEmpty);
            $cardList.toggle(!isEmpty || keyword === '');

            if (!keyword) {
                $cards.show();
                $countNum.text(totalKategori);
                $emptySearch.hide();
                $cardList.show();
            }
        });

        $('#pklModulResetKategori').on('click', function () {
            $('#pklModulSearchKategori').val('').trigger('input');

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'Pencarian direset',
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true,
            });
        });
    }

    if ($('#tabelModulPkl').length) {
        var tableModul = $('#tabelModulPkl').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            order: [[1, 'asc']],
            dom: 'lrtip',

            columnDefs: [
                { targets: 0, orderable: false, searchable: false, className: 'text-center' },
                { targets: 1 },
                { targets: 2, className: 'min-tablet-p' },
                { targets: 3, className: 'min-tablet-p' },
                { targets: 4, className: 'min-tablet-p' },
            ],

            language: {
                lengthMenu: 'Tampilkan _MENU_ data',
                zeroRecords: 'Tidak ada modul yang cocok',
                emptyTable: 'Belum ada modul dalam kategori ini',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ modul',
                infoEmpty: 'Menampilkan 0 sampai 0 dari 0 modul',
                infoFiltered: '(difilter dari _MAX_ total modul)',
                paginate: { previous: 'Sebelumnya', next: 'Selanjutnya' },
            },

            drawCallback: function () {
                var api = this.api();
                var start = api.page.info().start;

                api.rows({ page: 'current' }).every(function (rowIdx, tl, rowLoop) {
                    $(this.node()).find('.dt-no-col').text(start + rowLoop + 1);
                });
            },
        });

        $('#pklModulSearchModul').on('keyup input', function () {
            tableModul.column(1).search($(this).val()).draw();
        });

        $('#pklModulResetModul').on('click', function () {
            $('#pklModulSearchModul').val('');
            tableModul.column(1).search('').draw();

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'Pencarian direset',
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true,
            });
        });
    }

});