const TARGET_URL = 'list-of-cars.html';

const slug = s => (s || '').toString().toLowerCase().replace(/\s+/g,'-');

(function () {
  const grid   = document.getElementById('categoriesGrid');
  const search = document.getElementById('catSearch');
  const chips  = Array.from(document.querySelectorAll('.chip'));
  if (!grid) return;

  const cards = Array.from(grid.querySelectorAll('.cat-card'));
  let activeFilter = 'all';
  let query = '';

  function applyFilters() {
    const q = query.trim().toLowerCase();

    cards.forEach(card => {
      const name = (card.dataset.name || '').toLowerCase();
      const tag  = (card.dataset.tag  || '').toLowerCase();

      const byChip = (activeFilter === 'all') || tag === activeFilter.toLowerCase();
      const byText = !q || name.includes(q) || tag.includes(q);

      card.style.display = (byChip && byText) ? '' : 'none';
    });
  }

  /* Search */
  if (search) {
    search.addEventListener('input', (e) => {
      query = e.target.value || '';
      applyFilters();
    });
  }

  /* Chips */
  chips.forEach(chip => {
    chip.addEventListener('click', () => {
      chips.forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      activeFilter = chip.dataset.filter || 'all';
      applyFilters();
    });
  });

  /* Click => Go to non-SPA page with ?cat=slug */
  cards.forEach(card => {
    card.addEventListener('click', (e) => {
      if (e.target.closest('.view-btn') || e.currentTarget === card) {
        const cat = card.dataset.name || '';
        try { localStorage.setItem('selectedCategory', cat); } catch (_) {}
        window.location.href = `${TARGET_URL}?cat=${encodeURIComponent(slug(cat))}`;
      }
    });
  });
})();
