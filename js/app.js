/**
 * CHRONOS - Luxury Watch E-commerce
 * Main JavaScript File
 * Vanilla JavaScript (No Frameworks)
 */

// ============================================
// GLOBAL STATE & DATA
// ============================================

// Watch Products Data (frontend fallback)
// NOTE: In full-stack mode, this is replaced at runtime by API-driven data.
let watchesData = [
  {
    id: '1',
    brand: 'Chronos',
    model: 'Prestige Chronograph',
    price: 4899,
    image: 'assets/watch-1.jpg',
    strapType: 'Stainless Steel',
    dialColor: 'Black',
    waterResistant: '100m',
    gender: 'men',
    stock: 12,
    isNew: true,
  },
  {
    id: '2',
    brand: 'Chronos',
    model: 'Heritage Rose Gold',
    price: 3299,
    originalPrice: 3999,
    image: 'assets/watch-2.jpg',
    strapType: 'Leather',
    dialColor: 'Champagne',
    waterResistant: '50m',
    gender: 'unisex',
    stock: 5,
  },
  {
    id: '3',
    brand: 'Chronos',
    model: 'Midnight Eclipse',
    price: 2499,
    image: 'assets/watch-3.jpg',
    strapType: 'Mesh',
    dialColor: 'Black',
    waterResistant: '30m',
    gender: 'men',
    stock: 18,
    isNew: true,
  },
  {
    id: '4',
    brand: 'Chronos',
    model: 'Diamond Elegance',
    price: 5999,
    image: 'assets/watch-4.jpg',
    strapType: 'Gold',
    dialColor: 'White',
    waterResistant: '50m',
    gender: 'women',
    stock: 3,
  },
];

// Cart State (Replaces React useCart hook)
let cartItems = JSON.parse(localStorage.getItem('chronos_cart')) || [];

// Liked Items State
let likedItems = JSON.parse(localStorage.getItem('chronos_liked')) || [];

// ============================================
// UTILITY FUNCTIONS
// ============================================

// Format price with commas
function formatPrice(price) {
  return price.toLocaleString();
}

// Save cart to localStorage
function saveCart() {
  localStorage.setItem('chronos_cart', JSON.stringify(cartItems));
  updateCartBadge();
}

// Save liked items to localStorage
function saveLiked() {
  localStorage.setItem('chronos_liked', JSON.stringify(likedItems));
}

// Update cart badge count
function updateCartBadge() {
  const badge = document.querySelector('.cart-badge');
  if (badge) {
    const count = cartItems.reduce((n, i) => n + Number(i.quantity || 1), 0);
    if (count > 0) {
      badge.textContent = count;
      badge.style.display = 'flex';
    } else {
      badge.style.display = 'none';
    }
  }
}

// Add to cart function (supports quantity + product_id)
function addToCart(watch) {
  if (watch.stock < 1) {
    showNotification('This item is out of stock', 'error');
    return;
  }

  const cartItem = {
    product_id: Number(watch.product_id || watch.id),
    id: watch.id,
    name: watch.name || `${watch.brand} ${watch.model}`,
    brand: watch.brand,
    price: watch.price,
    image: watch.image,
    quantity: 1,
  };

  const existing = cartItems.find(i => Number(i.product_id || i.id) === Number(cartItem.product_id));
  if (existing) {
    existing.quantity = Number(existing.quantity || 1) + 1;
  } else {
    cartItems.push(cartItem);
  }

  saveCart();
  showNotification('Added to cart', 'success');
}

// Toggle like function
function toggleLike(watchId) {
  const index = likedItems.indexOf(watchId);
  if (index > -1) {
    likedItems.splice(index, 1);
  } else {
    likedItems.push(watchId);
  }
  saveLiked();
  
  // Update UI
  const likeBtn = document.querySelector(`[data-like-id="${watchId}"]`);
  if (likeBtn) {
    if (likedItems.includes(watchId)) {
      likeBtn.classList.add('liked');
    } else {
      likeBtn.classList.remove('liked');
    }
  }
}

// Show notification (Replaces React toast)
function showNotification(message, type = 'success') {
  const notification = document.createElement('div');
  notification.className = 'success-notification active';
  notification.innerHTML = `
    <svg class="success-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <span class="success-text">${message}</span>
  `;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.classList.remove('active');
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

// ============================================
// NAVBAR FUNCTIONALITY
// ============================================

function initNavbar() {
  const navbar = document.querySelector('.navbar');
  const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
  const mobileMenuSidebar = document.querySelector('.mobile-menu-sidebar');
  const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
  const mobileMenuClose = document.querySelector('.mobile-menu-close');
  const searchIcon = document.querySelector('.search-icon');
  const searchDropdown = document.querySelector('.search-dropdown');
  
  // Navbar scroll effect
  let lastScroll = 0;
  window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll > 20) {
      navbar.classList.add('scrolled');
      navbar.classList.remove('transparent');
    } else {
      navbar.classList.remove('scrolled');
      if (window.location.pathname === '/' || window.location.pathname === '/index.html') {
        navbar.classList.add('transparent');
      }
    }
    
    lastScroll = currentScroll;
  });
  
  // Set initial navbar state
  if (window.pageYOffset <= 20 && (window.location.pathname === '/' || window.location.pathname === '/index.html')) {
    navbar.classList.add('transparent');
  }
  
  // Mobile menu toggle
  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener('click', () => {
      mobileMenuSidebar.classList.add('active');
      mobileMenuOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    });
  }
  
  // Mobile menu close
  function closeMobileMenu() {
    mobileMenuSidebar.classList.remove('active');
    mobileMenuOverlay.classList.remove('active');
    document.body.style.overflow = '';
  }
  
  if (mobileMenuClose) {
    mobileMenuClose.addEventListener('click', closeMobileMenu);
  }
  
  if (mobileMenuOverlay) {
    mobileMenuOverlay.addEventListener('click', closeMobileMenu);
  }
  
  // Close mobile menu on link click
  const mobileNavLinks = document.querySelectorAll('.mobile-nav-links a');
  mobileNavLinks.forEach(link => {
    link.addEventListener('click', closeMobileMenu);
  });
  
  // Search dropdown toggle
  if (searchIcon && searchDropdown) {
    searchIcon.addEventListener('click', (e) => {
      e.stopPropagation();
      searchDropdown.classList.toggle('active');
    });
    
    document.addEventListener('click', (e) => {
      if (!searchDropdown.contains(e.target) && e.target !== searchIcon) {
        searchDropdown.classList.remove('active');
      }
    });
  }
  
  // Set active nav link
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.nav-link').forEach(link => {
    const href = link.getAttribute('href');
    if (href === currentPage || (currentPage === '' && href === 'index.html')) {
      link.classList.add('active');
    }
  });
  
  // Update cart badge on load
  updateCartBadge();
}

// ============================================
// WATCH CARD RENDERING
// ============================================

function createWatchCard(watch, index) {
  const isLiked = likedItems.includes(watch.id);
  const stockLabel = watch.stock < 5 && watch.stock > 0 
    ? `Only ${watch.stock} left` 
    : watch.stock === 0 
    ? 'Out of Stock' 
    : null;
  
  const card = document.createElement('div');
  card.className = 'watch-card fade-in-up';
  card.style.animationDelay = `${index * 0.1}s`;
  
  card.innerHTML = `
    <a href="product.html?id=${watch.id}" class="watch-card-link">
      <div class="watch-image-container">
        <img src="${watch.image}" alt="${watch.brand} ${watch.model}" loading="lazy">
        
        <div class="watch-badges">
          ${watch.isNew ? '<span class="badge-new">NEW</span>' : ''}
          ${watch.originalPrice ? '<span class="badge-sale">SALE</span>' : ''}
        </div>
        
        <button class="like-button ${isLiked ? 'liked' : ''}" data-like-id="${watch.id}" onclick="event.preventDefault(); event.stopPropagation(); toggleLike('${watch.id}')">
          <svg fill="${isLiked ? 'currentColor' : 'none'}" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
          </svg>
        </button>
        
        <div class="add-to-cart-overlay">
          <button class="btn-add-to-cart" ${watch.stock === 0 ? 'disabled' : ''} onclick="event.preventDefault(); event.stopPropagation(); handleAddToCart('${watch.id}')">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <span>Add to Cart</span>
          </button>
        </div>
      </div>
      
      <div class="watch-details">
        <p class="watch-brand">${watch.brand}</p>
        <h3 class="watch-model">${watch.model}</h3>
        
        <div class="watch-pricing">
          <div class="price-wrapper">
            <span class="watch-price">Rs. ${formatPrice(watch.price)}</span>
            ${watch.originalPrice ? `<span class="watch-price-original">Rs. ${formatPrice(watch.originalPrice)}</span>` : ''}
          </div>
          
          ${stockLabel ? `
            <span class="stock-label ${watch.stock === 0 ? 'stock-out' : 'stock-low'}">
              ${stockLabel}
            </span>
          ` : ''}
        </div>
      </div>
    </a>
  `;
  
  return card;
}

// Handle add to cart from card
function handleAddToCart(watchId) {
  const watch = watchesData.find(w => w.id === watchId);
  if (watch) {
    addToCart(watch);
  }
}

// Render watches in grid
function renderWatches(watches, containerId = 'watchGrid') {
  const container = document.getElementById(containerId);
  if (!container) return;
  
  container.innerHTML = '';
  
  if (watches.length === 0) {
    container.innerHTML = `
      <div class="no-results">
        <p class="no-results-text">No results found.</p>
      </div>
    `;
    return;
  }
  
  watches.forEach((watch, index) => {
    const card = createWatchCard(watch, index);
    container.appendChild(card);
  });
}

// ============================================
// COLLECTION PAGE FILTERING & SORTING
// ============================================

function initCollectionFilters() {
  let currentFilter = 'all';
  let currentSort = 'featured';
  
  // Filter buttons
  const filterButtons = document.querySelectorAll('.filter-btn');
  filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      currentFilter = btn.dataset.filter;
      
      // Update active state
      filterButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      
      // Apply filters
      applyFiltersAndSort();
    });
  });
  
  // Sort select
  const sortSelect = document.getElementById('sortSelect');
  if (sortSelect) {
    sortSelect.addEventListener('change', (e) => {
      currentSort = e.target.value;
      applyFiltersAndSort();
    });
  }
  
  function applyFiltersAndSort() {
    let filtered = watchesData;
    
    // Apply filter
    if (currentFilter !== 'all') {
      filtered = filtered.filter(watch => watch.gender === currentFilter);
    }
    
    // Apply sort
    filtered = [...filtered].sort((a, b) => {
      if (currentSort === 'price-low') return a.price - b.price;
      if (currentSort === 'price-high') return b.price - a.price;
      return 0; // featured (original order)
    });
    
    // Render
    renderWatches(filtered, 'watchGrid');
  }
  
  // Initial render
  applyFiltersAndSort();
}

// ============================================
// TESTIMONIALS & REVIEW MODAL
// ============================================

function initTestimonials() {
  const reviewBtn = document.querySelector('.btn-write-review');
  const modalOverlay = document.querySelector('.review-modal-overlay');
  const modalClose = document.querySelector('.modal-close');
  const reviewForm = document.getElementById('reviewForm');
  const imageUploadCircle = document.querySelector('.image-upload-circle');
  const imageInput = document.getElementById('imageInput');
  const ratingStars = document.querySelectorAll('.rating-star');
  
  let selectedRating = 1;
  let uploadedImage = null;
  
  // Open modal
  if (reviewBtn) {
    reviewBtn.addEventListener('click', () => {
      modalOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    });
  }
  
  // Close modal
  function closeModal() {
    modalOverlay.classList.remove('active');
    document.body.style.overflow = '';
  }
  
  if (modalClose) {
    modalClose.addEventListener('click', closeModal);
  }
  
  if (modalOverlay) {
    modalOverlay.addEventListener('click', (e) => {
      if (e.target === modalOverlay) {
        closeModal();
      }
    });
  }
  
  // Image upload
  if (imageUploadCircle && imageInput) {
    imageUploadCircle.addEventListener('click', () => {
      imageInput.click();
    });
    
    imageInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          uploadedImage = e.target.result;
          imageUploadCircle.innerHTML = `<img src="${uploadedImage}" alt="Uploaded" class="uploaded-image">`;
        };
        reader.readAsDataURL(file);
      }
    });
  }
  
  // Rating stars
  ratingStars.forEach((star, index) => {
    star.addEventListener('click', () => {
      selectedRating = index + 1;
      updateRatingStars();
    });
    
    star.addEventListener('mouseenter', () => {
      updateRatingStars(index + 1);
    });
  });
  
  const ratingWrapper = document.querySelector('.rating-stars');
  if (ratingWrapper) {
    ratingWrapper.addEventListener('mouseleave', () => {
      updateRatingStars();
    });
  }
  
  function updateRatingStars(hoverRating = null) {
    const rating = hoverRating || selectedRating;
    ratingStars.forEach((star, index) => {
      if (index < rating) {
        star.classList.add('active');
      } else {
        star.classList.remove('active');
      }
    });
  }
  
  // Form submit
  if (reviewForm) {
    reviewForm.addEventListener('submit', (e) => {
      e.preventDefault();
      
      closeModal();
      
      // Show success notification
      const notification = document.createElement('div');
      notification.className = 'success-notification active';
      notification.innerHTML = `
        <svg class="success-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="success-text">Review Submitted for Verification!</span>
      `;
      document.body.appendChild(notification);
      
      // Reset form
      reviewForm.reset();
      selectedRating = 1;
      uploadedImage = null;
      imageUploadCircle.innerHTML = `
        <div class="upload-icon-wrapper">
          <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          <span class="upload-text">Upload Photo</span>
        </div>
      `;
      updateRatingStars();
      
      setTimeout(() => {
        notification.classList.remove('active');
        setTimeout(() => notification.remove(), 300);
      }, 4000);
    });
  }
}

// ============================================
// HERO SECTION ANIMATIONS
// ============================================

function initHeroAnimations() {
  const heroContent = document.querySelector('.hero-content');
  if (heroContent) {
    // Ken Burns effect is handled by CSS animation
    // Additional JS animations can be added here if needed
  }
}

// ============================================
// SMOOTH SCROLL
// ============================================

function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href === '#') return;
      
      e.preventDefault();
      const target = document.querySelector(href);
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });
}

// ============================================
// INTERSECTION OBSERVER FOR ANIMATIONS
// ============================================

function initScrollAnimations() {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, observerOptions);
  
  document.querySelectorAll('.fade-in-up').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
    observer.observe(el);
  });
}

// ============================================
// PAGE INITIALIZATION
// ============================================

document.addEventListener('DOMContentLoaded', () => {
  // Initialize common components
  initNavbar();
  initSmoothScroll();
  initScrollAnimations();
  
  // Page-specific initialization
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';
  
  if (currentPage === 'index.html' || currentPage === '') {
    // Home page
    initHeroAnimations();
    initTestimonials();
    // Product rendering is handled by home.api.js (API first, fallback to local dataset)
    renderWatches(watchesData, 'watchGrid');
  } else if (currentPage === 'collection.html') {
    // Collection page
    initCollectionFilters();
  }
});

// ============================================
// EXPOSE FUNCTIONS TO GLOBAL SCOPE
// ============================================

// Make functions available globally for onclick handlers
window.toggleLike = toggleLike;
window.handleAddToCart = handleAddToCart;
window.addToCart = addToCart;

// ============================================
// API INTEGRATION (Full-stack)
// ============================================

async function loadProductsFromAPI(options = {}) {
  try {
    const url = options.url || ('api/products.php' + (options.queryString || ''));
    const res = await fetch(url);
    const json = await res.json();
    if (json && json.success && Array.isArray(json.data) && json.data.length) {
      watchesData = json.data;
      return watchesData;
    }
  } catch (e) {}
  return watchesData;
}

window.loadProductsFromAPI = loadProductsFromAPI;
