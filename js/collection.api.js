// API-driven collection filtering (brand/category/price) while preserving existing UI

document.addEventListener('DOMContentLoaded', async ()=>{
  const extra = document.getElementById('extraFilters');
  if(!extra) return;

  extra.innerHTML = `
    <select id="brandFilter" class="sort-select" style="min-width:160px">
      <option value="">All Brands</option>
    </select>
    <select id="categoryFilter" class="sort-select" style="min-width:160px">
      <option value="">All Categories</option>
    </select>
    <input id="minPrice" class="sort-select" style="width:120px" placeholder="Min Rs." />
    <input id="maxPrice" class="sort-select" style="width:120px" placeholder="Max Rs." />
    <button id="applyFilters" class="filter-btn" type="button">Apply</button>
  `;

  const [brands, cats] = await Promise.all([
    fetch('api/brands.php').then(r=>r.json()).catch(()=>({success:false})),
    fetch('api/categories.php').then(r=>r.json()).catch(()=>({success:false})),
  ]);

  const brandSel = document.getElementById('brandFilter');
  const catSel = document.getElementById('categoryFilter');

  if(brands.success){
    brands.data.forEach(b=>{
      const opt = document.createElement('option');
      opt.value = b.slug;
      opt.textContent = b.name;
      brandSel.appendChild(opt);
    });
  }

  if(cats.success){
    cats.data.forEach(c=>{
      const opt = document.createElement('option');
      opt.value = c.slug;
      opt.textContent = c.name;
      catSel.appendChild(opt);
    });
  }

  async function fetchAndRender(){
    const brand = brandSel.value;
    const category = catSel.value;
    const min = document.getElementById('minPrice').value.trim();
    const max = document.getElementById('maxPrice').value.trim();

    const qs = new URLSearchParams();
    if(brand) qs.set('brand', brand);
    if(category) qs.set('category', category);
    if(min) qs.set('min', min.replace(/[^0-9.]/g,''));
    if(max) qs.set('max', max.replace(/[^0-9.]/g,''));

    await loadProductsFromAPI({queryString: '?' + qs.toString()});

    // Keep existing gender filter behavior by mapping it to category slug when possible
    // Existing initCollectionFilters uses watch.gender; backend uses category_slug.
    // We set gender field to category_slug so the existing filter buttons keep working.
    watchesData = (watchesData || []).map(w=>({
      ...w,
      gender: w.category_slug || 'all'
    }));

    // Trigger built-in initCollectionFilters logic if present
    if(typeof initCollectionFilters === 'function'){
      // re-init by forcing render
      renderWatches(watchesData, 'watchGrid');
    }
  }

  document.getElementById('applyFilters').addEventListener('click', fetchAndRender);

  // initial load
  await fetchAndRender();
});
