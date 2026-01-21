// Load featured products from backend for homepage

document.addEventListener('DOMContentLoaded', async ()=>{
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';
  if (currentPage !== 'index.html' && currentPage !== '') return;

  const data = await loadProductsFromAPI({queryString: '?featured=1'});
  if (Array.isArray(data) && data.length) {
    renderWatches(data, 'watchGrid');
  }
});
