// API helper for CHRONOS frontend

async function apiGet(url){
  const r = await fetch(url, {method:'GET'});
  return r.json();
}

async function apiPost(url, payload){
  const r = await fetch(url, {
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify(payload)
  });
  return r.json();
}

window.apiGet = apiGet;
window.apiPost = apiPost;
