// Inject extra dynamic home sections from backend (admin-editable)

document.addEventListener('DOMContentLoaded', async ()=>{
  const holder = document.getElementById('dynamicHomeSections');
  if(!holder) return;

  try{
    const r = await fetch('api/homepage_sections.php');
    const res = await r.json();
    if(!res.success) return;

    const sections = res.data || [];
    if(!sections.length) return;

    // Use existing section header and feature-card style to keep UI consistent
    holder.innerHTML = sections.map((s, idx)=>`
      <section class="features-section" style="padding-top: 60px; padding-bottom: 60px;">
        <div class="container">
          <div class="section-header">
            <p class="section-subtitle">${s.subtitle ? s.subtitle : 'CHRONOS'}</p>
            <h2 class="section-title">${s.title}</h2>
          </div>
          <div class="row g-4 align-items-center">
            ${s.image_url ? `
              <div class="col-12 col-lg-5">
                <div class="feature-card" style="padding:0; overflow:hidden;">
                  <img src="${s.image_url}" alt="${s.title}" style="width:100%;height:100%;object-fit:cover;display:block;">
                </div>
              </div>
              <div class="col-12 col-lg-7">
                <div class="feature-card">
                  <div class="feature-description" style="color:rgba(255,255,255,.85)">${s.content_html || ''}</div>
                </div>
              </div>
            ` : `
              <div class="col-12">
                <div class="feature-card">
                  <div class="feature-description" style="color:rgba(255,255,255,.85)">${s.content_html || ''}</div>
                </div>
              </div>
            `}
          </div>
        </div>
      </section>
    `).join('');
  } catch(e) {}
});
