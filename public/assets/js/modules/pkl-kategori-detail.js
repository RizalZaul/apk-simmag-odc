// ===== KATEGORI DETAIL — DATATABLES + SEARCH + RESET =====

$(document).ready(function () {

  // Init DataTables
  const table = $('#modulTable').DataTable({
    language: {
      lengthMenu: 'Tampilkan _MENU_ data',
      info:       'Menampilkan _START_–_END_ dari _TOTAL_ modul',
      infoEmpty:  'Tidak ada data',
      paginate: {
        previous: '<i class="fas fa-chevron-left"></i>',
        next:     '<i class="fas fa-chevron-right"></i>',
      },
      emptyTable: 'Belum ada modul dalam kategori ini',
      zeroRecords: 'Modul tidak ditemukan',
    },
    pageLength: 10,
    order: [[0, 'asc']],
  });

  // Search custom
  $('#searchTable').on('input', function () {
    table.search(this.value).draw();
  });

  // Reset
  $('#resetSearch').on('click', function () {
    $('#searchTable').val('');
    table.search('').draw();
    $('#searchTable').focus();
  });

});