// Cart page rendering (keeps existing UI language; fixes messy cart)

function getCart(){
  return JSON.parse(localStorage.getItem('chronos_cart')) || [];
}

function setCart(items){
  localStorage.setItem('chronos_cart', JSON.stringify(items));
}

function cartTotal(items){
  return items.reduce((sum,it)=> sum + (Number(it.price)||0) * (Number(it.quantity)||1), 0);
}

function renderCart(){
  const cartContent = document.getElementById('cartContent');
  if(!cartContent) return;

  const items = getCart();
  if(items.length===0){
    cartContent.innerHTML = `
      <div class="text-center">
        <p class="mb-3">Your cart is empty</p>
        <a href="collection.html" class="btn-luxury px-4 py-3">Shop Now</a>
      </div>
    `;
    return;
  }

  cartContent.innerHTML = `
    <div class="row g-4">
      <div class="col-12 col-lg-8">
        <div class="d-flex flex-column gap-3">
          ${items.map((item,idx)=>`
            <div class="card" style="background:#111826;border:1px solid rgba(255,255,255,.08)">
              <div class="row g-0 align-items-center">
                <div class="col-4 col-md-3">
                  <img src="${item.image}" class="img-fluid" style="border-radius:12px 0 0 12px; object-fit:cover; height:110px; width:100%" alt="${item.name}">
                </div>
                <div class="col-8 col-md-9">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                      <div>
                        <h5 class="card-title mb-1" style="color:white">${item.name}</h5>
                        <div class="text-white-50 small">Rs. ${(Number(item.price)||0).toLocaleString()}</div>
                      </div>
                      <button class="btn btn-sm btn-outline-danger" onclick="removeItem(${idx})">Remove</button>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-3">
                      <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-sm btn-outline-light" onclick="decQty(${idx})">-</button>
                        <div style="min-width:36px;text-align:center">${Number(item.quantity||1)}</div>
                        <button class="btn btn-sm btn-outline-light" onclick="incQty(${idx})">+</button>
                      </div>
                      <div class="fw-semibold">Rs. ${(Number(item.price||0)*Number(item.quantity||1)).toLocaleString()}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          `).join('')}
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="card p-4" style="background:#111826;border:1px solid rgba(255,255,255,.08)">
          <h3 class="h5 text-gold mb-3">Summary</h3>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-white-50">Items</span>
            <span>${items.reduce((n,i)=>n+Number(i.quantity||1),0)}</span>
          </div>
          <div class="d-flex justify-content-between mb-3">
            <span class="text-white-50">Total</span>
            <span class="h5 mb-0">Rs. ${cartTotal(items).toLocaleString()}</span>
          </div>
          <a href="checkout.html" class="btn-luxury px-4 py-3 w-100 text-center">Proceed to Checkout</a>
          <button class="btn btn-outline-danger w-100 mt-2" onclick="clearCart()">Clear Cart</button>
        </div>
      </div>
    </div>
  `;
}

function incQty(idx){
  const items = getCart();
  items[idx].quantity = Number(items[idx].quantity||1) + 1;
  setCart(items);
  renderCart();
}

function decQty(idx){
  const items = getCart();
  const q = Number(items[idx].quantity||1);
  items[idx].quantity = Math.max(1, q-1);
  setCart(items);
  renderCart();
}

function removeItem(idx){
  const items = getCart();
  items.splice(idx,1);
  setCart(items);
  renderCart();
  if(window.updateCartBadge) updateCartBadge();
}

function clearCart(){
  localStorage.removeItem('chronos_cart');
  renderCart();
  if(window.updateCartBadge) updateCartBadge();
}

window.incQty = incQty;
window.decQty = decQty;
window.removeItem = removeItem;
window.clearCart = clearCart;

document.addEventListener('DOMContentLoaded', renderCart);
