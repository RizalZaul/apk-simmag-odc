// ===== DATA MODUL — SEARCH & RESET =====

const searchInput = document.getElementById('searchModul');
const resetBtn    = document.getElementById('resetSearch');
const grid        = document.getElementById('kategoriGrid');
const cards       = document.querySelectorAll('#kategoriGrid .kategori-card');

function filterKategori(keyword) {
  const kw = keyword.trim().toLowerCase();
  let ada = false;

  cards.forEach(card => {
    const nama = card.querySelector('h3').textContent.toLowerCase();
    const tampil = kw === '' || nama.includes(kw);
    card.style.display = tampil ? 'flex' : 'none';
    if (tampil) ada = true;
  });

  // Empty state
  let emptyEl = document.getElementById('emptySearch');
  if (!ada) {
    if (!emptyEl) {
      emptyEl = document.createElement('div');
      emptyEl.id = 'emptySearch';
      emptyEl.className = 'empty-state';
      emptyEl.innerHTML = '<i class="fas fa-search fa-2x"></i><p>Kategori "<strong>' + keyword + '</strong>" tidak ditemukan</p>';
      grid.appendChild(emptyEl);
    }
  } else {
    if (emptyEl) emptyEl.remove();
  }
}

searchInput.addEventListener('input', function() {
  filterKategori(this.value);
});

resetBtn.addEventListener('click', function() {
  searchInput.value = '';
  filterKategori('');
  searchInput.focus();
});