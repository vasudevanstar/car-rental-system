const API_BASE = 'api';


const getToken = () => localStorage.getItem('token');
const getUser = () => JSON.parse(localStorage.getItem('user') || 'null');

const applyTheme = () => {
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-theme');
    document.documentElement.setAttribute('data-theme', 'dark');
    document.documentElement.setAttribute('data-bs-theme', 'dark');
  } else {
    document.body.classList.remove('dark-theme');
    document.documentElement.setAttribute('data-theme', 'light');
    document.documentElement.setAttribute('data-bs-theme', 'light');
  }
};
applyTheme();

window.toggleTheme = () => {
  if (document.body.classList.contains('dark-theme')) {
    document.body.classList.remove('dark-theme');
    document.documentElement.setAttribute('data-theme', 'light');
    document.documentElement.setAttribute('data-bs-theme', 'light');
    localStorage.setItem('theme', 'light');
  } else {
    document.body.classList.add('dark-theme');
    document.documentElement.setAttribute('data-theme', 'dark');
    document.documentElement.setAttribute('data-bs-theme', 'dark');
    localStorage.setItem('theme', 'dark');
  }
  updateNavbar();
};

window.toggleFavorite = async (vehicleId, e) => {
  if(e) e.preventDefault();
  if (!getUser()) return showMessage('message', 'Please login to save favorites', 'warning');
  try {
    const res = await requestWithAuth('/api/favorites/toggle', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({vehicleId}) });
    const user = getUser();
    user.favorites = res.favorites;
    localStorage.setItem('user', JSON.stringify(user));
    
    // Update icon everywhere needed
    document.querySelectorAll('#heart-' + vehicleId).forEach(icon => {
      if(user.favorites.includes(vehicleId)) {
        icon.classList.remove('bi-heart');
        icon.classList.add('bi-heart-fill');
      } else {
        icon.classList.remove('bi-heart-fill');
        icon.classList.add('bi-heart');
      }
    });
  } catch(err) { showMessage('message', err.message, 'danger'); }
};

window.comparisonList = [];
window.toggleCompare = (vehicleId, name, brand, image, price, type, rating, fuel, transmission, seating, year) => {
  const index = window.comparisonList.findIndex(v => v.id === vehicleId);
  if (index > -1) {
    window.comparisonList.splice(index, 1);
  } else {
    if (window.comparisonList.length >= 3) {
      document.getElementById('compare-'+vehicleId).checked = false;
      return showMessage('message', 'You can compare maximum 3 cars', 'warning');
    }
    window.comparisonList.push({ id: vehicleId, name, brand, image, price, type, rating, fuel, transmission, seating, year });
  }
  updateCompareToolbar();
};

window.updateCompareToolbar = () => {
  let toolbar = document.getElementById('compareToolbar');
  if (!toolbar) {
    toolbar = document.createElement('div');
    toolbar.id = 'compareToolbar';
    toolbar.className = 'compare-toolbar';
    document.body.appendChild(toolbar);
  }
  if (window.comparisonList.length > 0) {
    toolbar.classList.add('visible');
    toolbar.innerHTML = `<span class="fw-bold">Comparing ${window.comparisonList.length} cars</span>
      <button class="btn btn-primary btn-sm rounded-pill px-4 fw-bold" onclick="showComparison()">View Comparison</button>
      <button class="btn btn-outline-danger btn-sm rounded-circle p-1 d-flex align-items-center justify-content-center" style="width:28px; height:28px;" onclick="clearComparison()"><i class="bi bi-x"></i></button>`;
  } else {
    toolbar.classList.remove('visible');
  }
};

window.clearComparison = () => {
  window.comparisonList = [];
  document.querySelectorAll('.compare-cb').forEach(cb => cb.checked = false);
  updateCompareToolbar();
};

window.showComparison = () => {
  let modal = document.getElementById('compareModal');
  if (!modal) {
    document.body.insertAdjacentHTML('beforeend', `
    <div class="modal fade" id="compareModal" tabindex="-1">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content glass-panel shadow-lg" style="background: var(--bg-main); border-radius: 28px;">
          <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
             <h5 class="modal-title fw-bold h3"><i class="bi bi-layout-split me-2 text-accent"></i> Compare Vehicles</h5>
             <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4" id="compareModalBody"></div>
        </div>
      </div>
    </div>`);
    modal = document.getElementById('compareModal');
  }
  
  const body = document.getElementById('compareModalBody');
  const specs = [
    { label: 'Type', icon: 'bi-car-front', key: 'type' },
    { label: 'Daily Rent', icon: 'bi-tag', key: 'price', prefix: '$' },
    { label: 'Rating', icon: 'bi-star-fill', key: 'rating', suffix: ' ★' },
    { label: 'Fuel', icon: 'bi-fuel-pump', key: 'fuel' },
    { label: 'Transmission', icon: 'bi-gear-wide-connected', key: 'transmission' },
    { label: 'Seats', icon: 'bi-people', key: 'seating', suffix: ' Seats' },
    { label: 'Year', icon: 'bi-calendar3', key: 'year' }
  ];

  body.innerHTML = `
    <div class="table-responsive">
      <table class="table table-borderless align-middle mb-0">
        <thead>
          <tr>
            <th style="width: 200px;" class="p-3"></th>
            ${window.comparisonList.map(v => `
              <th class="text-center p-3" style="min-width: 250px;">
                <div class=" glass-card p-3 rounded-4 h-100">
                  <img src="${v.image}" class="img-fluid rounded-3 mb-3 shadow-sm" style="height:140px; width:100%; object-fit:cover;">
                  <h5 class="fw-bold mb-0">${v.brand} ${v.name}</h5>
                </div>
              </th>
            `).join('')}
          </tr>
        </thead>
        <tbody>
          ${specs.map(spec => `
            <tr class="border-bottom" style="border-color: rgba(0,0,0,0.05)!important;">
              <td class="p-4 fw-bold text-secondary">
                <i class="bi ${spec.icon} me-2 text-accent"></i> ${spec.label}
              </td>
              ${window.comparisonList.map(v => `
                <td class="text-center p-4">
                  <span class="fw-semibold ${spec.key === 'price' ? 'text-success fs-5' : ''}">
                    ${spec.prefix || ''}${v[spec.key] || 'N/A'}${spec.suffix || ''}
                  </span>
                </td>
              `).join('')}
            </tr>
          `).join('')}
          <tr>
            <td></td>
            ${window.comparisonList.map(v => `
              <td class="text-center p-4">
                <a href="vehicle-details.php?id=${v.id}" class="btn btn-primary rounded-pill px-4 shadow-sm w-100">Book Now</a>
              </td>
            `).join('')}
          </tr>
        </tbody>
      </table>
    </div>`;
  
  new bootstrap.Modal(modal).show();
};

window.logout = () => {
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  window.location.href = 'logout.php';
};

window.forgotPassword = async (e) => {
  e?.preventDefault();
  const email = prompt("Enter your registered email address to reset your password:");
  if (email && email.trim()) {
    try {
      await fetch(API_BASE + '/forgot-password', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email.trim() })
      });
      showMessage('message', `If an account with ${email} exists, a password reset link has been sent.`, 'info');
    } catch (err) {
      showMessage('message', err.message, 'danger');
    }
  }
};

const updateNavbar = () => {
  // Check for PHP success messages
  const urlParams = new URLSearchParams(window.location.search);
  const msg = urlParams.get('msg');
  if (msg) showMessage('message', decodeURIComponent(msg), 'success');

  // Show welcome notification ONCE on destination page after login
  if (sessionStorage.getItem('justLoggedIn') && !window._welcomeShown) {
    window._welcomeShown = true;
    const name = sessionStorage.getItem('welcomeName') || 'there';
    const role = sessionStorage.getItem('welcomeRole') || 'customer';
    const roleEmoji = { admin: '👑', delivery: '🚚', maintenance: '🔧', customer: '🚗' };
    const roleLabel = { admin: 'Admin dashboard is ready.', delivery: 'Your delivery assignments are waiting.', maintenance: 'Fleet care portal is ready.', customer: 'Ready for your next ride?' };
    const emoji = roleEmoji[role] || '🚗';
    const label = roleLabel[role] || roleLabel.customer;
    setTimeout(() => showBottomNotification(`${emoji} Welcome back, ${name}! ${label}`), 600);
    sessionStorage.removeItem('justLoggedIn');
    sessionStorage.removeItem('welcomeName');
    sessionStorage.removeItem('welcomeRole');
  }

  const navContainer = document.querySelector('#navbarNav ul');
  if (!navContainer) return;
  const user = getUser();

  const themeIcon = document.body.classList.contains('dark-theme') ? 'bi-sun-fill' : 'bi-moon-stars-fill';
  const themeBtn = `<button class="btn btn-outline-secondary btn-sm rounded-circle ms-2 p-1 d-flex align-items-center justify-content-center border-0 shadow-none" style="width: 32px; height: 32px; color: var(--text-primary);" onclick="toggleTheme()" title="Toggle Theme"><i class="bi ${themeIcon}"></i></button>`;

  if (user) {
    let dashLabel = 'My Bookings';
    let dashHref = 'history.php';
    let dashIcon = 'bi-clock-history';

    if (user.role === 'admin') {
      dashLabel = 'Dashboard';
      dashHref = 'admin.php';
      dashIcon = 'bi-speedometer2';
    } else if (user.role === 'delivery') {
      dashLabel = 'Delivery Portal';
      dashHref = 'delivery.php';
      dashIcon = 'bi-truck';
    } else if (user.role === 'maintenance') {
      dashLabel = 'Fleet Care';
      dashHref = 'maintenance.php';
      dashIcon = 'bi-tools';
    }

    const dashboardLink = `<li class="nav-item"><a class="nav-link fw-semibold" href="${dashHref}"><i class="bi ${dashIcon}"></i> ${dashLabel}</a></li>`;

    navContainer.innerHTML = `
      <li class="nav-item"><a class="nav-link fw-semibold" href="index.php">Home</a></li>
      <li class="nav-item"><a class="nav-link fw-semibold" href="vehicles.php">Vehicles</a></li>
      ${dashboardLink}
      <li class="nav-item"><a class="nav-link fw-semibold" href="favorites.php"><i class="bi bi-heart-fill text-danger me-1"></i>Favorites</a></li>
      <li class="nav-item ms-lg-3 d-flex align-items-center">
        <a href="profile.php" class="nav-link text-primary fw-bold d-flex align-items-center" title="My Profile (Points: ${user.loyaltyPoints || 0})">
          ${user.profilePicture ? `<img src="${user.profilePicture}" class="rounded-circle me-2" style="width: 26px; height: 26px; object-fit: cover; border: 2px solid var(--accent);">` : '<i class="bi bi-person-circle fs-5 me-1"></i>'}
          ${user.name}
        </a>
      </li>
      <li class="nav-item ms-lg-2 mt-2 mt-lg-0 d-flex align-items-center">
        <button class="btn btn-outline-danger btn-sm rounded-pill py-2 px-3 fw-bold shadow-sm" onclick="logout()">Logout <i class="bi bi-box-arrow-right ms-1"></i></button>
        ${themeBtn}
      </li>
    `;
  } else {
    navContainer.innerHTML = `
      <li class="nav-item"><a class="nav-link fw-semibold" href="index.php">Home</a></li>
      <li class="nav-item"><a class="nav-link fw-semibold" href="vehicles.php">Vehicles</a></li>
      <li class="nav-item ms-lg-3 mt-2 mt-lg-0"><a class="btn btn-outline-primary btn-sm rounded-pill py-2 px-4 shadow-sm fw-bold w-100" href="login.php">Login</a></li>
      <li class="nav-item ms-lg-2 mt-2 mt-lg-0 d-flex align-items-center"><a class="btn btn-primary btn-sm rounded-pill py-2 px-4 shadow-sm fw-bold me-2" href="register.php">Register</a> ${themeBtn}</li>
    `;
  }
};

const showMessage = (containerId, message, type = 'success') => {
  let toastContainer = document.getElementById('toastWrapper');
  if (!toastContainer) {
    toastContainer = document.createElement('div');
    toastContainer.id = 'toastWrapper';
    toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3 mt-5';
    toastContainer.style.zIndex = '1055';
    document.body.appendChild(toastContainer);
  }

  const toastId = 'toast' + Date.now();
  const bg = type === 'danger' ? 'danger' : type === 'warning' ? 'warning' : type === 'info' ? 'info' : 'success';
  const icon = type === 'danger' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : type === 'info' ? 'info-circle' : 'check-circle';

  const toastHTML = `
    <div id="${toastId}" class="toast align-items-center text-white bg-${bg} border-0 show shadow-lg mt-2" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body fw-semibold"><i class="bi bi-${icon} me-2 fs-5 align-middle"></i> ${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close" onclick="this.parentElement.parentElement.remove()"></button>
      </div>
    </div>
  `;

  toastContainer.insertAdjacentHTML('beforeend', toastHTML);
  const toastEl = document.getElementById(toastId);
  setTimeout(() => {
    if (toastEl) {
      toastEl.classList.remove('show');
      setTimeout(() => toastEl.remove(), 300);
    }
  }, 4000);
};

// Bottom-panel notification for login/signup welcome
const showBottomNotification = (message) => {
  const existing = document.getElementById('bottomNotifBar');
  if (existing) existing.remove();

  const bar = document.createElement('div');
  bar.id = 'bottomNotifBar';
  bar.style.cssText = `
    position: fixed; bottom: -80px; left: 50%; transform: translateX(-50%);
    background: linear-gradient(135deg, #1e293b, #334155);
    color: white; padding: 14px 28px; border-radius: 50px;
    font-size: 0.9rem; font-weight: 600; letter-spacing: 0.3px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.3);
    z-index: 9999; white-space: nowrap;
    transition: bottom 0.5s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.4s ease;
    opacity: 0; border: 1px solid rgba(255,255,255,0.1);
  `;
  bar.textContent = message;
  document.body.appendChild(bar);

  // Slide up
  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      bar.style.bottom = '30px';
      bar.style.opacity = '1';
    });
  });

  // Slide down and remove after 4s
  setTimeout(() => {
    bar.style.bottom = '-80px';
    bar.style.opacity = '0';
    setTimeout(() => bar.remove(), 500);
  }, 4000);
};

const requestWithAuth = async (url, options = {}) => {
  const token = getToken();
  const headers = { ...(options.headers || {}) };
  if (token) headers.Authorization = `Bearer ${token}`;

  const res = await fetch(API_BASE + url, { ...options, headers });
  
  // Handle empty or non-json responses gracefully
  const contentType = res.headers.get("content-type");
  let body = {};
  if (contentType && contentType.indexOf("application/json") !== -1) {
      body = await res.json();
  }

  if (!res.ok) {
    if (res.status === 401) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      window.location.href = 'login.php?message=Session expired. Please login again.';
    }
    throw new Error(body.message || 'Error');
  }
  return body;
};

const postData = async (url, data, auth = false) => {
  const options = {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  };
  return auth ? requestWithAuth(url, options) : requestWithAuth(url, options);
};

const putData = async (url, data) => {
  return requestWithAuth(url, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  });
};

const deleteData = async (url) => {
  return requestWithAuth(url, { method: 'DELETE' });
};

const getData = async (url) => {
  return requestWithAuth(url, { method: 'GET' });
};

const registerInit = () => {
  const form = document.getElementById('registerForm');
  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = form.querySelector('button[type="submit"]');
    const originalBtnText = btn.innerHTML;
    try {
      const payload = {
        name: document.getElementById('name').value.trim(),
        email: document.getElementById('email').value.trim(),
        password: document.getElementById('password').value.trim(),
        phone: document.getElementById('phone').value.trim(),
      };
      if (!payload.name || !payload.email || !payload.password || !payload.phone) {
        throw new Error('Please fill all fields');
      }
      btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating Account...';
      btn.disabled = true;

      await postData('/register', payload);

      // Premium success notification
      showMessage('message', `🎉 Welcome aboard, ${payload.name.split(' ')[0]}! Your FastRide account is ready. Please login to continue.`, 'success');
      form.reset();
      btn.innerHTML = '✅ Account Created!';
      setTimeout(() => {
        window.location.href = 'login.php';
      }, 2500);
    } catch (err) {
      showMessage('message', err.message, 'danger');
      btn.innerHTML = originalBtnText;
      btn.disabled = false;
    }
  });
};

const loginInit = () => {
  const urlParams = new URLSearchParams(window.location.search);
  const redirect = urlParams.get('redirect');

  // Redirect if already logged in
  if (getToken()) {
    const user = getUser();
    if (redirect) {
      window.location.href = redirect;
    } else if (user && user.role === 'admin') {
      window.location.href = 'admin.php';
    } else if (user && user.role === 'delivery') {
      window.location.href = 'delivery.php';
    } else if (user && user.role === 'maintenance') {
      window.location.href = 'maintenance.php';
    } else {
      window.location.href = 'vehicles.php';
    }
    return;
  }

  const togglePasswordBtn = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('password');
  togglePasswordBtn?.addEventListener('click', () => {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    togglePasswordBtn.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
  });

  const form = document.getElementById('loginForm');
  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = form.querySelector('button[type="submit"]');
    const originalBtnText = btn.innerHTML;
    const emailInput = document.getElementById('email');

    try {
      // Loading state
      btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Logging in...';
      btn.disabled = true;
      emailInput.disabled = true;
      passwordInput.disabled = true;

      const payload = {
        email: emailInput.value.trim(),
        password: passwordInput.value.trim(),
      };
      const resp = await requestWithAuth('/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });

      localStorage.setItem('token', resp.token);
      localStorage.setItem('user', JSON.stringify(resp.user));

      // Store welcome flag so destination page shows the notification
      sessionStorage.setItem('justLoggedIn', '1');
      sessionStorage.setItem('welcomeName', resp.user.name.split(' ')[0]);
      sessionStorage.setItem('welcomeRole', resp.user.role);

      updateNavbar();

      setTimeout(() => {
        if (redirect) {
          window.location.href = redirect;
        } else if (resp.user.role === 'admin') {
          window.location.href = 'admin.php';
        } else if (resp.user.role === 'delivery') {
          window.location.href = 'delivery.php';
        } else if (resp.user.role === 'maintenance') {
          window.location.href = 'maintenance.php';
        } else {
          window.location.href = 'vehicles.php';
        }
      }, 800);
    } catch (err) {
      showMessage('message', err.message || 'Invalid credentials. Please try again.', 'danger');
      btn.innerHTML = originalBtnText;
      btn.disabled = false;
      emailInput.disabled = false;
      passwordInput.disabled = false;
      passwordInput.value = '';
      passwordInput.focus();
    }
  });
};

window.favoritesInit = async () => {
  if (!getUser()) return window.location.href = 'login.php';
  const loadFavorites = async () => {
    try {
      const vehicles = await getData('/api/favorites');
      const container = document.getElementById('vehiclesList');
      if (!container) return;
      if (!vehicles || !vehicles.length) {
        container.innerHTML = '<div class="text-center p-5 w-100"><i class="bi bi-heart-break fs-1 text-muted mb-3 d-block"></i><p class="text-muted fs-5">You haven\'t added any vehicles to your favorites yet.</p><a href="vehicles.php" class="btn btn-primary rounded-pill mt-3 px-4 shadow-sm fw-bold">Browse Vehicles</a></div>';
        return;
      }
      
      const userFavorites = getUser()?.favorites || [];
      container.innerHTML = vehicles.map((v) => {
        const isFav = userFavorites.includes(v.id);
        const checkState = window.comparisonList.find(c => c.id === v.id) ? 'checked' : '';
        return `
        <div class="col-sm-6 col-lg-4 mb-4 fade-in-scroll visible">
          <div class="card h-100 border-0 rounded-4 position-relative">
            <button class="heart-btn position-absolute top-0 start-0 m-2" onclick="toggleFavorite(${v.id}, event); setTimeout(()=>window.location.reload(), 300)" title="Toggle Favorite">
              <i id="heart-${v.id}" class="bi ${isFav ? 'bi-heart-fill' : 'bi-heart'}"></i>
            </button>
            <div class="position-relative">
              <img src="${v.image}" class="card-img-top" alt="${v.name}" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;" />
              <div class="position-absolute top-0 end-0 m-2 d-flex flex-column gap-2 align-items-end">
                <span class="badge ${v.status === 'Available' ? 'bg-success' : v.status === 'Booked' ? 'bg-danger' : 'bg-warning text-dark'} shadow-sm px-3 py-2 rounded-pill">${v.status}</span>
              </div>
            </div>
            <div class="card-body d-flex flex-column p-4">
               <h5 class="card-title d-flex justify-content-between align-items-center mb-1">
                <span class="fw-bold text-truncate">${v.brand || ''} ${v.name}</span>
                <span class="badge bg-light text-dark shadow-sm ms-2">⭐ ${v.rating_average || 'New'}</span>
              </h5>
              <div class="mt-auto d-flex justify-content-between align-items-center mb-3">
                <div><span class="price-tag fs-4 fw-bold text-success">$${v.rent_per_day}</span><span class="text-muted small">/day</span></div>
                <div class="form-check form-switch cursor-pointer">
                  <input class="form-check-input compare-cb shadow-none cursor-pointer" type="checkbox" id="compare-${v.id}" ${checkState} onchange="toggleCompare(${v.id}, '${v.name.replace(/'/g, "\\'")}', '${v.brand || ''}', '${v.image}', ${v.rent_per_day}, '${v.type}', '${v.rating_average || 'N/A'}', '${v.fuel_type || 'Petrol'}', '${v.transmission || 'Auto'}', ${v.seating_capacity || 5}, ${v.model_year || 2024})">
                  <label class="form-check-label fw-semibold text-muted small" for="compare-${v.id}">Compare</label>
                </div>
              </div>
              <a href="vehicle-details.php?id=${v.id}" class="btn btn-outline-primary w-100 py-2 fw-bold text-uppercase">View Details</a>
            </div>
          </div>
        </div>
      `;}).join('');
    } catch(err) { showMessage('message', err.message, 'danger'); }
  };
  loadFavorites();
};

const vehiclesInit = async () => {
  let currentPage = 1;

  // Pre-fill filters from URL parameters if they exist
  const urlParams = new URLSearchParams(window.location.search);
  const searchInput = document.getElementById('searchInput');
  const typeFilter = document.getElementById('typeFilter');

  if (urlParams.has('search') && searchInput) searchInput.value = urlParams.get('search');
  if (urlParams.has('type') && typeFilter) typeFilter.value = urlParams.get('type');

  const fetchVehicles = async () => {
    try {
      const search = searchInput?.value.trim() || '';
      const type = typeFilter?.value || '';
      const status = document.getElementById('availabilityFilter')?.value || '';
      const maxPrice = document.getElementById('maxPriceFilter')?.value || '';
      const brand = document.getElementById('brandFilter')?.value || '';
      const transmission = document.getElementById('transmissionFilter')?.value || '';
      const fuel_type = document.getElementById('fuelFilter')?.value || '';
      const seats = document.getElementById('seatsFilter')?.value || '';
      const sort = document.getElementById('sortFilter')?.value || '';

      const qs = new URLSearchParams();
      qs.append('page', currentPage);
      qs.append('perPage', 9);
      if (search) qs.append('search', search);
      if (type) qs.append('type', type);
      if (status) qs.append('status', status);
      if (maxPrice) qs.append('maxPrice', maxPrice);
      if (brand) qs.append('brand', brand);
      if (transmission) qs.append('transmission', transmission);
      if (fuel_type) qs.append('fuel_type', fuel_type);
      if (seats) qs.append('seats', seats);
      if (sort) qs.append('sort', sort);

      const response = await getData('/vehicles?' + qs.toString());
      const vehicles = response.vehicles || [];
      const container = document.getElementById('vehiclesList');
      if (!container) return;

      const pageInfo = document.getElementById('pageInfo');
      if (pageInfo) pageInfo.textContent = `Page ${response.page}`;

      if (!vehicles.length) {
        container.innerHTML = '<p class="text-muted">No vehicles found matching your criteria.</p>';
        return;
      }
      const userFavorites = getUser()?.favorites || [];
      container.innerHTML = vehicles.map((v) => {
        const isFav = userFavorites.includes(v.id);
        const checkState = window.comparisonList.find(c => c.id === v.id) ? 'checked' : '';
        return `
        <div class="col-sm-6 col-lg-4 mb-4 fade-in-scroll visible">
          <div class="card h-100 border-0 rounded-4 position-relative">
            <button class="heart-btn position-absolute top-0 start-0 m-2" onclick="toggleFavorite(${v.id}, event)" title="Toggle Favorite">
              <i id="heart-${v.id}" class="bi ${isFav ? 'bi-heart-fill' : 'bi-heart'}"></i>
            </button>
            <div class="position-relative">
              <img src="${v.image}" class="card-img-top" alt="${v.name}" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;" />
              <div class="position-absolute top-0 end-0 m-2 d-flex flex-column gap-2 align-items-end">
                <span class="badge ${v.status === 'Available' ? 'bg-success' : v.status === 'Booked' ? 'bg-danger' : 'bg-warning text-dark'} shadow-sm px-3 py-2 rounded-pill">${v.status}</span>
              </div>
            </div>
            <div class="card-body d-flex flex-column p-4">
              <h5 class="card-title d-flex justify-content-between align-items-center mb-1">
                <span class="fw-bold text-truncate">${v.brand || ''} ${v.name}</span>
                <span class="badge bg-light text-dark shadow-sm ms-2">⭐ ${v.rating_average || 'New'}</span>
              </h5>
              <div class="text-muted small mb-3"><i class="bi bi-calendar3 me-1"></i>${v.model_year || 'N/A'} model</div>
              <p class="card-text text-muted small mb-3 flex-grow-1" style="line-height: 1.5;">${v.description || 'No description available for this vehicle. Experience comfort and reliability.'}</p>
              
              <div class="vehicle-specs border-top pt-3 border-bottom pb-3 mb-3">
                <div title="Type"><i class="bi bi-car-front"></i> ${v.type}</div>
                <div title="Fuel"><i class="bi bi-fuel-pump"></i> ${v.fuel_type || 'N/A'}</div>
                <div title="Transmission"><i class="bi bi-gear-wide-connected"></i> ${v.transmission || 'N/A'}</div>
                <div title="Seats"><i class="bi bi-people"></i> ${v.seating_capacity || 'N/A'} Seats</div>
              </div>
              
              <div class="mt-auto d-flex justify-content-between align-items-center mb-3">
                <div>
                  <span class="price-tag fs-4 fw-bold text-success">$${v.rent_per_day}</span><span class="text-muted small">/day</span>
                </div>
                <div class="form-check form-switch cursor-pointer">
                  <input class="form-check-input compare-cb shadow-none cursor-pointer" type="checkbox" id="compare-${v.id}" ${checkState} onchange="toggleCompare(${v.id}, '${v.name.replace(/'/g, "\\'")}', '${v.brand || ''}', '${v.image}', ${v.rent_per_day}, '${v.type}', '${v.rating_average || 'N/A'}', '${v.fuel_type || 'Petrol'}', '${v.transmission || 'Auto'}', ${v.seating_capacity || 5}, ${v.model_year || 2024})">
                  <label class="form-check-label fw-semibold text-muted small" for="compare-${v.id}">Compare</label>
                </div>
              </div>
              <a href="vehicle-details.php?id=${v.id}" class="btn btn-outline-primary w-100 py-2 fw-bold text-uppercase" style="letter-spacing: 1px;">
                View Details <i class="bi bi-arrow-right-short ms-1"></i>
              </a>
            </div>
          </div>
        </div>
      `;}).join('');
    } catch (err) {
      showMessage('message', err.message, 'danger');
    }
  };

  document.getElementById('filterBtn')?.addEventListener('click', () => {
    currentPage = 1;
    fetchVehicles();
  });

  document.getElementById('prevPage')?.addEventListener('click', () => {
    if (currentPage > 1) {
      currentPage--;
      fetchVehicles();
    }
  });

  document.getElementById('nextPage')?.addEventListener('click', () => {
    currentPage++;
    fetchVehicles();
  });

  fetchVehicles();
};

  const bookingInit = async () => {
    const params = new URLSearchParams(window.location.search);
    const vehicleId = params.get('vehicleId');
    if (vehicleId) document.getElementById('vehicleId').value = Number(vehicleId);
    const form = document.getElementById('bookingForm');
  
    const vehicleIdInput = document.getElementById('vehicleId');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const availabilityBadge = document.getElementById('availabilityBadge');
  
    const user = getUser();
    if(user && user.loyaltyPoints > 0) {
      const loyaltySection = document.getElementById('loyaltySection');
      const loyaltyText = document.getElementById('loyaltyText');
      if(loyaltySection && loyaltyText) {
         loyaltySection.style.setProperty('display', 'flex', 'important');
         loyaltyText.textContent = `You have ${user.loyaltyPoints} points ($${(user.loyaltyPoints * 0.1).toFixed(2)} value)`;
      }
    }

    if(user && !user.is_verified) {
        showMessage('message', 'Please Note: You must be a verified member to complete a booking. Update your profile if needed.', 'warning');
        const submitBtn = form?.querySelector('button[type="submit"]');
        if(submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-shield-lock me-2"></i> Verification Required';
            submitBtn.classList.replace('btn-primary', 'btn-secondary');
        }
    }

    const mapContainer = document.getElementById('map');
    if (mapContainer && window.L) {
      const pickupMap = L.map('map').setView([51.505, -0.09], 12);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(pickupMap);

      const locations = {
        Downtown: [51.505, -0.09],
        Airport: [51.53, -0.12],
        Suburbs: [51.48, -0.06]
      };

      Object.entries(locations).forEach(([name, coords]) => {
        const marker = L.marker(coords).addTo(pickupMap).bindPopup(`<b>${name} Branch</b><br>Click marker to select this location.`);
        marker.on('click', () => {
           const pLoc = document.getElementById('pickupLocation');
           const dLoc = document.getElementById('dropoffLocation');
           if(pLoc) pLoc.value = name;
           if(dLoc) dLoc.value = name;
           showMessage('message', `Location selected: ${name} Branch`, 'info');
        });
      });
    }

  const checkAvailability = async () => {
    if (!startDateInput?.value || !endDateInput?.value || !vehicleIdInput?.value) return;
    try {
      const response = await fetch(API_BASE + '/check-availability', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          vehicleId: Number(vehicleIdInput.value),
          startDate: startDateInput.value,
          endDate: endDateInput.value
        })
      });
      const data = await response.json();
      if (availabilityBadge) {
        if (data.available) {
          availabilityBadge.className = 'badge bg-success';
          availabilityBadge.textContent = '🟢 Available';
        } else {
          availabilityBadge.className = 'badge bg-danger';
          availabilityBadge.textContent = '🔴 Not Available (Dates overlap)';
        }
      }
    } catch (e) {
      console.error(e);
    }
  };

  const initCalendar = async () => {
    try {
      if(!vehicleId) return;
      const resp = await fetch(API_BASE + `/api/vehicles/${vehicleId}/blocked-dates`);
      const blockedDates = await resp.json();
      const disableArr = blockedDates.map(b => ({ from: b.from, to: b.to }));
      
      const endPicker = flatpickr(endDateInput, {
        minDate: 'today',
        disable: disableArr,
        onChange: function() { checkAvailability(); }
      });
      
      flatpickr(startDateInput, {
        minDate: 'today',
        disable: disableArr,
        onChange: function(sel, dstr) { 
           endPicker.set('minDate', dstr); 
           checkAvailability(); 
        }
      });
    } catch(e) { console.error('Calendar error:', e); }
  };
  
  if (window.flatpickr) initCalendar();
  else {
    startDateInput?.addEventListener('change', checkAvailability);
    endDateInput?.addEventListener('change', checkAvailability);
  }

    form?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const user = getUser();
      const payload = {
        vehicleId: Number(document.getElementById('vehicleId').value),
        startDate: document.getElementById('startDate').value,
        endDate: document.getElementById('endDate').value,
        pickupLocation: document.getElementById('pickupLocation')?.value,
        dropoffLocation: document.getElementById('dropoffLocation')?.value,
        promoCode: document.getElementById('promoCode')?.value.trim().toUpperCase(),
        applyPoints: document.getElementById('applyPointsToggle')?.checked || false
      };
      const paymentMethod = document.getElementById('method')?.value;
      if (!user || !payload.vehicleId || !payload.startDate || !payload.endDate) {
        showMessage('message', 'Please login and complete all fields to book', 'danger');
        return;
      }
    try {
      showMessage('message', 'Processing booking...', 'info');

      const bookResp = await requestWithAuth('/book', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });

      showMessage('message', `Booking successful! Redirecting to secure payment...`, 'success');
      form.reset();
      setTimeout(() => {
        window.location.href = `payment.php?rentalId=${bookResp.rental.id}&amount=${bookResp.rental.total_amount}`;
      }, 1500);
    } catch (err) {
      showMessage('message', err.message, 'danger');
    }
  });
};

const paymentInit = () => {
  const params = new URLSearchParams(window.location.search);
  const rentalIdMatch = params.get('rentalId');
  const amountMatch = params.get('amount');
  if (rentalIdMatch) document.getElementById('rentalId').value = rentalIdMatch;
  if (amountMatch) document.getElementById('amount').value = amountMatch;

  const form = document.getElementById('paymentForm');
  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const payload = {
      rentalId: Number(document.getElementById('rentalId').value),
      amount: Number(document.getElementById('amount').value),
      method: document.getElementById('method').value,
    };
    if (!payload.rentalId || !payload.amount || !payload.method) {
      showMessage('message', 'All fields are required', 'danger');
      return;
    }
    try {
      const response = await requestWithAuth('/payment', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });



      showMessage('message', 'Payment successful and rental completed! Redirecting...', 'success');
      if (response.invoiceUrl) {
        setTimeout(() => {
          showMessage('message', `Your receipt is ready. <a href="${response.invoiceUrl}" target="_blank" class="text-white text-decoration-underline fw-bold px-2"><i class="bi bi-download"></i> Download PDF</a>`, 'info');
        }, 500);
      }
      form.reset();
      setTimeout(() => {
        window.location.href = './history.php';
      }, 800);
    } catch (err) {
      showMessage('message', err.message, 'danger');
    }
  });
};

const historyInit = () => {
  const loadHistory = async () => {
    try {
      if (!getToken()) {
        showMessage('message', 'Please login to view your history.', 'danger');
        document.getElementById('historyList').innerHTML = '<div class="text-center p-5"><i class="bi bi-person-x fs-1 text-muted mb-3 d-block"></i><p class="text-muted fs-5">You are not authenticated.</p><a href="login.php" class="btn btn-primary rounded-pill mt-3 px-4 shadow-sm fw-bold">Login Now</a></div>';
        return;
      }

      const bookings = await getData('/bookings');
      const container = document.getElementById('historyList');
      if (!container) return;
      if (!bookings.length) {
        container.innerHTML = '<div class="text-center p-5"><i class="bi bi-journal-x fs-1 text-muted mb-3 d-block"></i><p class="text-muted fs-5">You have no rental history yet.</p><a href="vehicles.php" class="btn btn-primary rounded-pill mt-3 px-4 shadow-sm fw-bold">Browse Vehicles</a></div>';
        return;
      }
      container.innerHTML = `<div class="table-responsive"><table class="table table-hover align-middle border"><thead><tr class="table-light"><th>ID</th><th>Vehicle</th><th>Dates</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead><tbody class="border-top-0">${bookings.map((b) => {
        let actions = '';
        if (b.status === 'Completed' && !b.is_rated) {
          actions += `<button class="btn btn-sm btn-outline-warning rounded-pill me-1 mb-1 shadow-sm" onclick="openRateModal(${b.id}, ${b.vehicle_id})"><i class="bi bi-star"></i> Rate</button>`;
        }
        if (b.status !== 'Cancelled' && b.status !== 'Pending') {
          actions += `<button class="btn btn-sm btn-outline-info rounded-pill me-1 mb-1 shadow-sm" onclick="downloadInvoice(${b.id})"><i class="bi bi-file-earmark-pdf"></i> Invoice</button>`;
        }

        const isFuture = new Date(b.start_date) > new Date();
        if ((b.status === 'Pending' || b.status === 'Confirmed') && isFuture) {
          actions += `<button class="btn btn-sm btn-outline-danger rounded-pill mb-1 shadow-sm" onclick="cancelRental(${b.id})"><i class="bi bi-x-circle"></i> Cancel</button>`;
        } else if (b.status === 'Pending') {
          actions += `<a class="btn btn-sm btn-outline-success rounded-pill mb-1 shadow-sm" href="payment.php?rentalId=${b.id}&amount=${b.total_amount}"><i class="bi bi-credit-card"></i> Pay</a>`;
        }

        if ((b.status === 'Confirmed' || b.status === 'Ongoing') && (!b.return_status || b.return_status === 'None')) {
          actions += `<button class="btn btn-sm btn-outline-primary rounded-pill mb-1 shadow-sm" onclick="requestReturn(${b.id})"><i class="bi bi-arrow-return-left"></i> Return Car</button>`;
        } else if (b.return_status && b.return_status !== 'None') {
          actions += `<span class="badge bg-dark rounded-pill me-1 mb-1 p-2">Return: ${b.return_status}</span>`;
        }
        const vName = b.vehicle?.name || b.vehicle_id;
        return `<tr><td class="fw-bold text-muted">#${b.id}</td><td><i class="bi bi-car-front text-accent me-1"></i> ${vName}</td><td><span class="small text-muted"><i class="bi bi-calendar-event me-1"></i> ${b.start_date} <br> <i class="bi bi-calendar-check me-1"></i> ${b.end_date}</span></td><td class="fw-bold text-success">$${b.total_amount}</td><td><span class="badge rounded-pill ${b.status === 'Completed' ? 'bg-success' : b.status === 'Ongoing' ? 'bg-primary' : b.status === 'Confirmed' ? 'bg-info' : b.status === 'Cancelled' ? 'bg-danger' : 'bg-secondary'} px-3 py-2 shadow-sm">${b.status}</span></td><td><div class="d-flex flex-wrap">${actions}</div></td></tr>`;
      }).join('')}</tbody></table></div>`;
    } catch (err) {
      showMessage('message', err.message, 'danger');
    }
  };

  loadHistory();

  const ratingForm = document.getElementById('ratingForm');
  ratingForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const payload = {
      rentalId: Number(document.getElementById('rateRentalId').value),
      vehicleId: Number(document.getElementById('rateVehicleId').value),
      rating: Number(document.getElementById('rateStars').value),
      review: document.getElementById('rateReview').value
    };
    try {
      await requestWithAuth('/rate-vehicle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      showMessage('message', 'Review submitted successfully!', 'success');
      const modal = bootstrap.Modal.getInstance(document.getElementById('ratingModal'));
      modal?.hide();
      loadHistory();
    } catch (err) {
      showMessage('message', err.message, 'danger');
    }
  });

  window.cancelRental = async (rentalId) => {
    if (!confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) return;
    try {
      await requestWithAuth('/cancel-booking', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ rentalId })
      });
      showMessage('message', 'Booking cancelled successfully.', 'success');
      loadHistory();
    } catch (err) {
      showMessage('message', err.message, 'danger');
    }
  };

  window.openRateModal = (rentalId, vehicleId) => {
    document.getElementById('rateRentalId').value = rentalId;
    document.getElementById('rateVehicleId').value = vehicleId;
    document.getElementById('rateStars').value = '5';
    document.getElementById('rateReview').value = '';
    const modal = new bootstrap.Modal(document.getElementById('ratingModal'));
    modal.show();
  };

  window.downloadInvoice = (rentalId) => {
    // Simply redirect to our PDF generator with the id
    window.open(`invoice.php?id=${rentalId}`, '_blank');
  };

  window.requestReturn = async (id) => {
    if(!confirm('Are you sure you want to request a return for this vehicle?')) return;
    try {
      await requestWithAuth('/bookings', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'request_return', id })
      });
      showMessage('message', 'Return request sent to Fleet Ops!', 'success');
      loadHistory();
    } catch(err) { showMessage('message', err.message, 'danger'); }
  };
};

const adminInit = async () => {
    const tableContainers = {
      vehicles: document.getElementById('vehicleTable'),
      bookings: document.getElementById('bookingTable'),
      users: document.getElementById('usersTable'),
      verifications: document.getElementById('verificationsTable')
    };

    window.filterTable = (containerId, query) => {
      const rows = document.querySelectorAll(`#${containerId} tbody tr`);
      const q = query.toLowerCase();
      rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
      });
    };

    const loadAnalytics = async () => {
      try {
        const analytics = await getData('/admin/analytics');
        
        // Update Global Stats
        const statRev = document.getElementById('statRevenue');
        if (statRev) statRev.textContent = `$${Number(analytics.total_revenue || 0).toLocaleString()}`;
 
        // 1. Render Utilization Chart
        const utilCtx = document.getElementById('utilizationChart')?.getContext('2d');
        if (utilCtx) {
            if (window.utilChart) window.utilChart.destroy();
            window.utilChart = new Chart(utilCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Rented', 'Available'],
                    datasets: [{
                        data: [analytics.utilization_rate, 100 - analytics.utilization_rate],
                        backgroundColor: ['#6366f1', '#e2e8f0'],
                        borderWidth: 0
                    }]
                },
                options: { 
                    cutout: '80%', 
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } } 
                }
            });
        }

        // 2. Render Revenue by Category
        const catCtx = document.getElementById('categoryRevChart')?.getContext('2d');
        if (catCtx) {
            if (window.catChart) window.catChart.destroy();
            window.catChart = new Chart(catCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(analytics.revenue_by_type || {}),
                    datasets: [{
                        data: Object.values(analytics.revenue_by_type || {}),
                        backgroundColor: '#10b981',
                        borderRadius: 5
                    }]
                },
                options: { 
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }, 
                    scales: { y: { display: false }, x: { grid: { display: false } } } 
                }
            });
        }

        // 3. Render Activity Feed
        const feed = document.getElementById('activityFeed');
        if (feed && analytics.recent_activity) {
            feed.innerHTML = analytics.recent_activity.map(a => {
                const icon = a.action.includes('Payment') ? 'cash-coin' : a.action.includes('Vehicle') ? 'car-front' : 'shield-check';
                const color = a.action.includes('Deleted') || a.action.includes('Rejected') ? 'danger' : 'primary';
                return `
                    <div class="activity-item">
                        <div class="activity-icon bg-${color} bg-opacity-10 text-${color}"><i class="bi bi-${icon}"></i></div>
                        <div>
                            <div class="small fw-bold">${a.action}</div>
                            <div class="small text-muted mb-1">${a.details}</div>
                            <div class="small opacity-50" style="font-size: 0.75rem;">${new Date(a.created_at).toLocaleString()} • ${a.user_name || 'System'}</div>
                        </div>
                    </div>
                `;
            }).join('') || '<div class="p-4 text-center text-muted small">No recent activity</div>';
        }

        const cardsContainer = document.getElementById('analyticsCards');
        if (cardsContainer) {
          const mrv = analytics.most_rented_vehicle;
          const topVehicle = mrv ? `${mrv.brand} ${mrv.name}` : 'No Data';
          cardsContainer.innerHTML = `
            <div class="col-md-4">
              <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-wallet2"></i></div>
                <div class="text-muted small fw-bold text-uppercase">Total Revenue</div>
                <div class="h2 fw-bold m-0">$${Number(analytics.total_revenue || 0).toLocaleString()}</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-journal-check"></i></div>
                <div class="text-muted small fw-bold text-uppercase">Total Bookings</div>
                <div class="h2 fw-bold m-0">${analytics.total_bookings || 0}</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-trophy"></i></div>
                <div class="text-muted small fw-bold text-uppercase">Top Performer</div>
                <div class="h3 fw-bold m-0 text-truncate" title="${topVehicle}">${topVehicle}</div>
              </div>
            </div>
          `;
        }
 
        const revCtx = document.getElementById('revenueChart')?.getContext('2d');
        if (revCtx) {
          if (window.revChart) window.revChart.destroy();
            window.revChart = new Chart(revCtx, {
              type: 'line',
              data: {
                labels: Object.keys(analytics.monthly_revenue || {}),
                datasets: [{
                  label: 'Monthly Revenue ($)',
                  data: Object.values(analytics.monthly_revenue || {}),
                  borderColor: '#6366f1',
                  backgroundColor: 'rgba(99, 102, 241, 0.1)',
                  fill: true,
                  tension: 0.4,
                  pointRadius: 5,
                  pointHoverRadius: 8
                }]
              },
              options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } } 
              }
            });
        }
      } catch (err) { console.error('Analytics error:', err); }
    };

    const loadVehicles = async () => {
      try {
        const vehicles = await getData('/admin/vehicles');
        if (!tableContainers.vehicles) return;
        
        // Update Global Stats
        const statFleet = document.getElementById('statFleet');
        if (statFleet) statFleet.textContent = vehicles.length;

        const available = vehicles.filter(v => v.status === 'Available').length;
        const booked = vehicles.filter(v => v.status === 'Booked').length;

        const statAvailable = document.getElementById('statAvailable');
        if (statAvailable) statAvailable.textContent = available;

        const statBooked = document.getElementById('statBooked');
        if (statBooked) statBooked.textContent = booked;

        updateTypeChart(vehicles);

        tableContainers.vehicles.innerHTML = `
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
              <tr><th class="ps-4">Vehicle</th><th>Type</th><th>Daily Rent</th><th>Status</th><th class="text-end pe-4">Actions</th></tr>
            </thead>
            <tbody>
              ${vehicles.map(v => `
                <tr>
                  <td class="ps-4">
                    <div class="d-flex align-items-center">
                      <img src="${v.image}" class="rounded-3 me-3" style="width: 45px; height: 32px; object-fit: cover; border: 1px solid rgba(0,0,0,0.05);">
                      <div><div class="fw-bold">${v.brand || ''} ${v.name}</div><small class="text-muted">ID: #${v.id}</small></div>
                    </div>
                  </td>
                  <td><span class="badge bg-light text-dark border">${v.type}</span></td>
                  <td><div class="fw-bold text-success">$${v.rent_per_day}</div></td>
                  <td><span class="badge rounded-pill ${v.status === 'Available' ? 'bg-success' : 'bg-danger'}">${v.status}</span></td>
                  <td class="text-end pe-4">
                    <div class="d-flex justify-content-end gap-2">
                        <button class="quick-action-btn" onclick="toggleVehicleStatus(${v.id}, '${v.status}')" title="Quick Toggle Status"><i class="bi bi-power"></i></button>
                        <button class="quick-action-btn" onclick="editVehicle(${v.id})" data-bs-toggle="modal" data-bs-target="#vehicleModal"><i class="bi bi-pencil"></i></button>
                        <button class="quick-action-btn text-danger" onclick="deleteVehicle(${v.id})"><i class="bi bi-trash"></i></button>
                    </div>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        `;
      } catch (err) { showMessage('message', err.message, 'danger'); }
    };

    const loadUsers = async () => {
      try {
        const users = await requestWithAuth('/admin/users');
        
        // Update Global Stats
        const statUsers = document.getElementById('statUsers');
        if (statUsers) statUsers.textContent = users.length;

        if (!tableContainers.users) return;
        tableContainers.users.innerHTML = `
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
              <tr><th class="ps-4">User</th><th>Role</th><th>Verified</th><th>Status</th><th class="text-end pe-4">Action</th></tr>
            </thead>
            <tbody>
              ${users.map(u => `
                <tr>
                  <td class="ps-4">
                    <div class="fw-bold">${u.name}</div><small class="text-muted">${u.email}</small>
                  </td>
                  <td><span class="badge border ${u.role === 'admin' ? 'text-danger border-danger' : 'text-primary border-primary'}">${u.role}</span></td>
                  <td><i class="bi ${u.is_verified ? 'bi-patch-check-fill text-primary' : 'bi-x-circle text-muted'}"></i></td>
                  <td><span class="badge ${u.is_active ? 'bg-success' : 'bg-secondary'}">${u.is_active ? 'Active' : 'Disabled'}</span></td>
                  <td class="text-end pe-4">
                    <button class="btn btn-sm ${u.is_active ? 'btn-outline-danger' : 'btn-outline-success'} rounded-pill px-3" onclick="toggleUserStatus(${u.id})">
                      ${u.is_active ? 'Disable' : 'Enable'}
                    </button>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        `;
      } catch (err) { console.error('User load error:', err); }
    };

    const loadVerifications = async () => {
      try {
        const users = await requestWithAuth('/api/admin/verifications');
        if (!tableContainers.verifications) return;
        const badge = document.getElementById('verificationBadge');
        if (badge) {
          badge.textContent = users.length;
          badge.style.display = users.length > 0 ? 'inline-block' : 'none';
        }
        if (!users.length) {
          tableContainers.verifications.innerHTML = '<div class="p-5 text-center text-muted">All verified! No pending requests.</div>';
          return;
        }
        tableContainers.verifications.innerHTML = `
          <table class="table align-middle">
            <thead><tr><th class="ps-4">Customer</th><th>Reason</th><th class="text-end pe-4">Action</th></tr></thead>
            <tbody>
              ${users.map(u => `
                <tr>
                  <td class="ps-4"><div class="fw-bold">${u.name}</div><small class="text-muted">${u.email}</small></td>
                  <td>License Verification</td>
                  <td class="text-end pe-4">
                    <button class="btn btn-primary btn-sm rounded-pill px-3" onclick="showLicenseModal(${u.id}, '${u.driver_license_url}')">Review DOC</button>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        `;
      } catch (err) { console.error('Verifications load error:', err); }
    };

    window.showLicenseModal = (userId, licenseUrl) => {
        const modal = new bootstrap.Modal(document.getElementById('licenseModal'));
        document.getElementById('licensePreviewImg').src = licenseUrl;
        document.getElementById('verifyActions').innerHTML = `
            <button class="btn btn-danger px-4 rounded-pill fw-bold" onclick="processVerification(${userId}, false)">Reject</button>
            <button class="btn btn-success px-4 rounded-pill fw-bold" onclick="processVerification(${userId}, true)">Approve</button>
        `;
        modal.show();
    };

    window.processVerification = async (userId, isApproved) => {
        if (!confirm(`Are you sure you want to ${isApproved ? 'approve' : 'reject'} this verification?`)) return;
        try {
            await requestWithAuth('/api/admin/verify', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: userId, verified: isApproved })
            });
            showMessage('message', `User ${isApproved ? 'verified' : 'verification rejected'} successfully`, 'success');
            bootstrap.Modal.getInstance(document.getElementById('licenseModal')).hide();
            loadVerifications();
        } catch (err) {
            showMessage('message', err.message, 'danger');
        }
    };

    const updateTypeChart = (vehicles) => {
      const typeCounts = vehicles.reduce((acc, v) => {
        acc[v.type] = (acc[v.type] || 0) + 1;
        return acc;
      }, {});
      const ctx = document.getElementById('typeChart')?.getContext('2d');
      if (ctx) {
        if (window.tChart) window.tChart.destroy();
        window.tChart = new Chart(ctx, {
          type: 'doughnut',
          data: {
            labels: Object.keys(typeCounts),
            datasets: [{
              data: Object.values(typeCounts),
              backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
            }]
          },
          options: { cutout: '70%', plugins: { legend: { position: 'bottom' } } }
        });
      }
    };

    const loadPromo = async () => {
      try {
        const p = await getData('/api/promotions');
        if (p) {
          document.getElementById('promoTitle').value = p.title || '';
          document.getElementById('promoDesc').value = p.description || '';
          document.getElementById('promoCodeInput').value = p.promo_code || '';
          document.getElementById('promoDiscount').value = p.discount_percentage || '';
        }
      } catch (e) { console.error('Promo load error:', e); }
    };

    // --- Bookings ---
    const loadBookings = async () => {
      try {
        if (!tableContainers.bookings) return;
        tableContainers.bookings.innerHTML = '<div class="p-4 text-center text-muted"><span class="spinner-border spinner-border-sm me-2"></span> Loading bookings...</div>';
        
        const bookings = await getData('/admin/bookings');
        console.log('Admin Bookings Data:', bookings);
        
        if (!bookings.length) {
          tableContainers.bookings.innerHTML = '<div class="p-5 text-center text-muted"><i class="bi bi-journal-x fs-1 d-block mb-3"></i>No booking records found in the database.</div>';
          return;
        }

        tableContainers.bookings.innerHTML = `
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
              <tr><th class="ps-4">ID</th><th>Customer</th><th>Vehicle</th><th>Dates</th><th>Profit</th><th>Status</th></tr>
            </thead>
            <tbody>
              ${bookings.map(b => `
                <tr>
                  <td class="ps-4 text-muted small">#${b.id}</td>
                  <td>
                    <div class="fw-bold text-primary">${b.customer_name || 'Guest'}</div>
                    <div class="small"><i class="bi bi-telephone text-muted me-1"></i> ${b.customer_phone || 'N/A'}</div>
                    <div class="small text-muted"><i class="bi bi-envelope text-muted me-1"></i> ${b.customer_email}</div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <img src="${b.vehicle_image || 'https://via.placeholder.com/100'}" class="rounded-2 me-2" style="width: 50px; height: 35px; object-fit: cover; border: 1px solid rgba(0,0,0,0.1);">
                      <div>
                        <div class="fw-bold">${b.vehicle_brand || ''} ${b.vehicle_name || 'Unknown Car'}</div>
                        <div class="small text-muted"><span class="badge bg-light text-dark border-0 p-0">${b.vehicle_type || ''}</span></div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="small fw-bold">${b.start_date}</div>
                    <div class="small text-muted">to ${b.end_date}</div>
                    <div class="small opacity-50">Duration: ${b.days} days</div>
                  </td>
                  <td class="fw-bold text-success">$${Number(b.total_amount).toLocaleString()}</td>
                  <td>
                     <div class="mt-2 text-wrap" style="width: 150px;">
                        <span class="badge rounded-pill bg-light text-dark border">${b.status}</span>
                        ${b.delivery_status && b.delivery_status !== 'Pending' ? `
                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border-0 small ms-1" style="font-size:0.65rem;">
                                <i class="bi bi-truck"></i> ${b.delivery_status}
                            </span>
                        ` : ''}
                        ${b.return_status && b.return_status !== 'None' ? `
                            <span class="badge rounded-pill bg-dark bg-opacity-10 text-dark border-0 small ms-1" style="font-size:0.65rem;">
                                <i class="bi bi-arrow-return-left"></i> ${b.return_status}
                            </span>
                        ` : ''}
                     </div>
                     ${b.status === 'Confirmed' ? `
                        <div class="mt-2 text-primary small fw-bold" style="cursor:pointer;" onclick="openAssignModal(${b.id}, '${b.customer_email}')">
                            <i class="bi bi-truck me-1"></i> ${b.delivery_status !== 'Pending' ? b.delivery_status : 'Schedule Delivery'}
                        </div>
                        <div class="mt-1 small fw-bold" style="cursor:pointer; color: #6366f1;" onclick="window.open('invoice.php?id=${b.id}', '_blank')">
                            <i class="bi bi-file-earmark-pdf me-1"></i> View Invoice
                        </div>
                     ` : ''}
                   </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        `;
      } catch(e) { 
        console.error('Bookings load error:', e);
        if(tableContainers.bookings) tableContainers.bookings.innerHTML = `<div class="p-4 text-center text-danger">Error loading bookings: ${e.message}</div>`;
      }
    };

    window.exportBookings = async () => {
       try {
         const bookings = await getData('/admin/bookings');
         let csv = 'ID,Customer,Vehicle,Start,End,Amount,Status\n';
         bookings.forEach(b => {
           csv += `${b.id},${b.customer_name || b.customer_email},${b.vehicle_name || b.vehicle_id},${b.start_date},${b.end_date},${b.total_amount},${b.status}\n`;
         });
         const blob = new Blob([csv], { type: 'text/csv' });
         const url = window.URL.createObjectURL(blob);
         const a = document.createElement('a');
         a.href = url;
         a.download = `bookings_export_${Date.now()}.csv`;
         a.click();
         showMessage('message', 'Export successful!', 'success');
       } catch(e) { showMessage('message', 'Export failed: ' + e.message, 'danger'); }
    };

    window.editVehicle = async (id) => {
      try {
        const vehicles = await getData('/admin/vehicles');
        const v = vehicles.find(x => x.id === id);
        if (!v) return;
        
        document.getElementById('vehicleId').value = v.id;
        document.getElementById('vehicleBrand').value = v.brand || '';
        document.getElementById('vehicleName').value = v.name || '';
        document.getElementById('vehicleType').value = v.type || 'Sedan';
        document.getElementById('vehicleRent').value = v.rent_per_day || 0;
        document.getElementById('vehicleStatus').value = v.status || 'Available';
        document.getElementById('vehicleImage').value = v.image || '';
        document.getElementById('vehicleTransmission').value = v.transmission || 'Automatic';
        document.getElementById('vehicleFuel').value = v.fuel_type || 'Petrol';
        document.getElementById('vehicleSeats').value = v.seating_capacity || 5;
        document.getElementById('vehicleYear').value = v.model_year || new Date().getFullYear();
        document.getElementById('vehicleDescription').value = v.description || '';
        
        document.getElementById('vehicleModalTitle').textContent = 'Edit Vehicle';
      } catch(e) { console.error(e); }
    };

    window.deleteVehicle = async (id) => {
      if (!confirm('Are you sure you want to delete this vehicle?')) return;
      try {
        await requestWithAuth(`/api/admin/delete-vehicle?id=${id}`, { method: 'DELETE' });
        showMessage('message', 'Vehicle deleted successfully', 'success');
        loadVehicles();
      } catch(e) { showMessage('message', e.message, 'danger'); }
    };

    const vForm = document.getElementById('vehicleForm');
    vForm?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const id = document.getElementById('vehicleId').value;
      const payload = {
        brand: document.getElementById('vehicleBrand').value,
        name: document.getElementById('vehicleName').value,
        type: document.getElementById('vehicleType').value,
        rent_per_day: document.getElementById('vehicleRent').value,
        status: document.getElementById('vehicleStatus').value,
        image: document.getElementById('vehicleImage').value,
        transmission: document.getElementById('vehicleTransmission').value,
        fuel_type: document.getElementById('vehicleFuel').value,
        seating_capacity: document.getElementById('vehicleSeats').value,
        model_year: document.getElementById('vehicleYear').value,
        description: document.getElementById('vehicleDescription').value
      };

      try {
        const endpoint = id ? '/api/admin/update-vehicle' : '/api/admin/add-vehicle';
        if (id) payload.id = id;

        await requestWithAuth(endpoint, {
          method: id ? 'PUT' : 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });

        showMessage('message', `Vehicle ${id ? 'updated' : 'added'} successfully!`, 'success');
        bootstrap.Modal.getInstance(document.getElementById('vehicleModal')).hide();
        loadVehicles();
        vForm.reset();
        document.getElementById('vehicleId').value = '';
      } catch(err) { showMessage('message', err.message, 'danger'); }
    });

    const promoForm = document.getElementById('promoForm');
    promoForm?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const payload = {
        title: document.getElementById('promoTitle').value,
        description: document.getElementById('promoDesc').value,
        promo_code: document.getElementById('promoCodeInput').value,
        discount_percentage: Number(document.getElementById('promoDiscount').value)
      };

      try {
        const btn = promoForm.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Broadcasting...';
        btn.disabled = true;

        await requestWithAuth('/promotions', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });

        showMessage('message', 'Promotion broadcasted successfully!', 'success');
        btn.innerHTML = originalText;
        btn.disabled = false;
        loadPromo();
      } catch(err) { 
        showMessage('message', err.message, 'danger'); 
      }
    });

    // Initial Data Load
    loadPromo();
    loadAnalytics();
    loadVehicles();
    loadBookings();
    loadUsers();
    loadVerifications();

    // --- Command Palette Logic (Ctrl + K) ---
    const palette = new bootstrap.Modal(document.getElementById('commandPalette'));
    const pSearch = document.getElementById('paletteSearch');
    const pResults = document.getElementById('paletteResults');

    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            palette.show();
            setTimeout(() => pSearch.focus(), 500);
        }
    });

    pSearch?.addEventListener('input', async () => {
        const query = pSearch.value.trim().toLowerCase();
        if (query.length < 2) {
            pResults.innerHTML = '<div class="p-4 text-center text-muted small">Start typing to see results...</div>';
            return;
        }

        try {
            // Simulated deep search across local/cached data
            const vehicles = await getData('/admin/vehicles');
            const users = await requestWithAuth('/admin/users');
            
            const vMatches = vehicles.filter(v => v.name.toLowerCase().includes(query) || v.brand.toLowerCase().includes(query));
            const uMatches = users.filter(u => u.name.toLowerCase().includes(query) || u.email.toLowerCase().includes(query));

            let html = '';
            if (vMatches.length) {
                html += '<div class="p-2 px-3 small fw-bold text-muted text-uppercase bg-light">Vehicles</div>';
                html += vMatches.map(v => `<div class="palette-item" onclick="showSection('vehicles'); editVehicle(${v.id}); bootstrap.Modal.getInstance(document.getElementById('commandPalette')).hide();"><i class="bi bi-car-front"></i> ${v.brand} ${v.name}</div>`).join('');
            }
            if (uMatches.length) {
                html += '<div class="p-2 px-3 small fw-bold text-muted text-uppercase bg-light">Users</div>';
                html += uMatches.map(u => `<div class="palette-item" onclick="showSection('users'); bootstrap.Modal.getInstance(document.getElementById('commandPalette')).hide();"><i class="bi bi-person"></i> ${u.name} (${u.email})</div>`).join('');
            }

            pResults.innerHTML = html || '<div class="p-4 text-center text-muted small">No results found matching your query.</div>';
        } catch (e) { }
    });
};

window.toggleVehicleStatus = async (id, currentStatus) => {
    const newStatus = currentStatus === 'Available' ? 'Maintenance' : 'Available';
    try {
        await requestWithAuth('/api/admin/update-vehicle', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, status: newStatus })
        });
        showMessage('message', `Vehicle #${id} marked as ${newStatus}`, 'info');
        // Reload all data
        const page = window.location.pathname.split('/').pop();
        if(page === 'admin.php') adminInit();
    } catch(e) { showMessage('message', e.message, 'danger'); }
};

window.openAssignModal = async (rentalId, customerEmail) => {
    document.getElementById('assignRentalId').value = rentalId;
    const modal = new bootstrap.Modal(document.getElementById('assignDeliveryModal'));
    
    try {
        const users = await requestWithAuth('/admin/users');
        const employees = users.filter(u => u.role === 'delivery');
        
        const select = document.getElementById('deliveryEmpSelect');
        select.innerHTML = employees.map(e => `<option value="${e.id}">${e.name}</option>`).join('');
        
        if (!employees.length) {
            select.innerHTML = '<option disabled>No delivery employees found</option>';
        }
        
        modal.show();
    } catch (e) { showMessage('message', 'Failed to load employees', 'danger'); }
};

const profileInit = async () => {
  const user = getUser();
  if (!user) return window.location.href = 'login.php';

  try {
    const profile = await getData('/api/profile');
    document.getElementById('profileName').value = profile.name || '';
    document.getElementById('profilePhone').value = profile.phone || '';
    document.getElementById('profileEmail').value = profile.email || '';
    if (profile.profilePicture) {
      const hiddenInput = document.getElementById('profileImageUrl');
      if (hiddenInput) hiddenInput.value = profile.profilePicture;
      document.getElementById('profilePicturePreview').src = profile.profilePicture;
    }
    document.getElementById('profileGreeting').textContent = `Hello, ${profile.name.split(' ')[0]}`;
    
    if (profile.loyaltyPoints > 0) {
       const badge = document.getElementById('profileLoyaltyBadge');
       const pointsText = document.getElementById('profileLoyaltyPoints');
       if(badge && pointsText) {
          badge.style.display = 'inline-block';
          pointsText.textContent = profile.loyaltyPoints;
       }
    }
    
    if (profile.is_verified) {
       const badge = document.getElementById('verificationStatusBadge');
       if(badge) { badge.textContent = 'Verified ✔️'; badge.className = 'position-absolute top-0 end-0 m-3 badge bg-success shadow-sm'; }
    } else if (profile.driver_license_url) {
       const badge = document.getElementById('verificationStatusBadge');
       if(badge) { badge.textContent = 'Pending ⏳'; badge.className = 'position-absolute top-0 end-0 m-3 badge bg-warning text-dark shadow-sm'; }
    }
  } catch (err) {
    showMessage('message', err.message, 'danger');
  }

  document.getElementById('profileImageFile')?.addEventListener('change', (e) => {
    if (e.target.files && e.target.files[0]) {
      document.getElementById('profilePicturePreview').src = URL.createObjectURL(e.target.files[0]);
    }
  });

  document.getElementById('uploadLicenseBtn')?.addEventListener('click', async () => {
    const fileInput = document.getElementById('licenseFile');
    if (!fileInput || !fileInput.files.length) return showMessage('message', 'Please select a document first', 'danger');
    showMessage('message', 'Uploading your document securely...', 'info');
    
    const formData = new FormData();
    formData.append('licenseImage', fileInput.files[0]);
    try {
      const resp = await fetch(API_BASE + '/api/upload-license', {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${getToken()}` },
        body: formData
      });
      const data = await resp.json();
      if (!resp.ok) throw new Error(data.message || 'Error uploading document');
      showMessage('message', data.message, 'success');
      
      const badge = document.getElementById('verificationStatusBadge');
      if(badge) { badge.textContent = 'Pending ⏳'; badge.className = 'position-absolute top-0 end-0 m-3 badge bg-warning text-dark shadow-sm'; }
      fileInput.value = '';
    } catch(err) {
      showMessage('message', err.message, 'danger');
    }
  });

  const toggleProfilePassword = document.getElementById('toggleProfilePassword');
  const profilePassword = document.getElementById('profilePassword');
  toggleProfilePassword?.addEventListener('click', () => {
    const type = profilePassword.getAttribute('type') === 'password' ? 'text' : 'password';
    profilePassword.setAttribute('type', type);
    toggleProfilePassword.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
  });

  document.getElementById('profileForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    let uploadedImageUrl = document.getElementById('profileImageUrl')?.value || '';

    const fileInput = document.getElementById('profileImageFile');
    if (fileInput && fileInput.files.length > 0) {
      showMessage('message', 'Uploading image...', 'info');
      const formData = new FormData();
      formData.append('image', fileInput.files[0]);
      try {
        const uploadResp = await fetch(API_BASE + '/api/upload-profile-picture', {
          method: 'POST',
          headers: { 'Authorization': `Bearer ${getToken()}` },
          body: formData
        });
        const uploadData = await uploadResp.json();
        if (!uploadResp.ok) throw new Error(uploadData.message || 'Image upload failed');
        uploadedImageUrl = uploadData.url;
      } catch (err) {
        return showMessage('message', err.message, 'danger');
      }
    }

    const payload = {
      name: document.getElementById('profileName').value.trim(),
      phone: document.getElementById('profilePhone').value.trim(),
      profilePicture: uploadedImageUrl
    };
    const newPassword = document.getElementById('profilePassword').value;
    if (newPassword) payload.password = newPassword;

    try {
      const resp = await requestWithAuth('/api/update-profile', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      localStorage.setItem('user', JSON.stringify(resp.user));
      updateNavbar();
      document.getElementById('profileGreeting').textContent = `Hello, ${resp.user.name.split(' ')[0]}`;
      document.getElementById('profilePassword').value = '';
      showMessage('message', 'Profile updated successfully!', 'success');
    } catch (err) {
      showMessage('message', err.message, 'danger');
    }
  });
};

window.handleHomeBookNow = (e) => {
  e.preventDefault();
  if (!getUser()) {
    window.location.href = 'login.php?redirect=vehicles.php';
  } else {
    window.location.href = 'vehicles.php';
  }
};

const currencyInit = () => {
  const selector = document.getElementById('currencySelector');
  const rateDisplay = document.getElementById('currencyRate');
  if (!selector) return;

  const updateRate = async () => {
    const currency = selector.value;
    if (currency === 'USD') {
      rateDisplay.textContent = '';
      localStorage.setItem('preferred_currency', 'USD');
      return;
    }

    rateDisplay.textContent = '...';
    try {
      // Free API: ExchangeRate-API (no key required for this specific daily endpoint)
      const res = await fetch(`https://open.er-api.com/v6/latest/USD`);
      const data = await res.json();
      if (data.result === 'success') {
        const rate = data.rates[currency];
        rateDisplay.textContent = `1$ ≈ ${rate.toFixed(2)} ${currency}`;
        localStorage.setItem('preferred_currency', currency);
        localStorage.setItem('currency_rate', rate);
      }
    } catch (e) {
      rateDisplay.textContent = 'Rate N/A';
      console.error('Currency API Error:', e);
    }
  };

  selector.addEventListener('change', updateRate);

  // Set initial state from storage
  const saved = localStorage.getItem('preferred_currency') || 'USD';
  selector.value = saved;
  updateRate();
};

const resetPasswordInit = () => {
  const urlParams = new URLSearchParams(window.location.search);
  const token = urlParams.get('token');
  if (!token) {
    showMessage('message', 'Invalid or missing reset token. Please request a new link.', 'danger');
    const form = document.getElementById('resetPasswordForm');
    if (form) {
      form.innerHTML = '<div class="alert alert-danger mt-3 mb-0" style="font-size:12px;">Invalid or expired token. <a href="login.php" class="alert-link">Return to login</a></div>';
    }
    return;
  }

  const form = document.getElementById('resetPasswordForm');
  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (newPassword !== confirmPassword) {
      return showMessage('message', 'Passwords do not match', 'danger');
    }

    try {
      const res = await fetch(API_BASE + '/reset-password', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ token, newPassword })
      });
      const body = await res.json();
      if (!res.ok) throw new Error(body.message || 'Error processing reset');
      
      showMessage('message', 'Password successfully reset! Redirecting to login...', 'success');
      setTimeout(() => {
        window.location.href = 'login.php';
      }, 2000);
    } catch (err) {
      showMessage('message', err.message, 'danger');
    }
  });
};

const vehicleDetailsInit = async () => {
  const urlParams = new URLSearchParams(window.location.search);
  const vehicleId = urlParams.get('id');
  if (!vehicleId) return window.location.href = 'vehicles.php';

  try {
    const v = await getData(`/api/vehicles/${vehicleId}`);
    const detailsContainer = document.getElementById('vehicleDetailsContainer');
    if (detailsContainer) {
      detailsContainer.innerHTML = `
        <div class="col-lg-7">
          <div id="vCarousel" class="carousel slide shadow-lg rounded-4 overflow-hidden" data-bs-ride="carousel">
            <div class="carousel-indicators">
              <button type="button" data-bs-target="#vCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Front"></button>
              <button type="button" data-bs-target="#vCarousel" data-bs-slide-to="1" aria-label="Side"></button>
              <button type="button" data-bs-target="#vCarousel" data-bs-slide-to="2" aria-label="Rear"></button>
              <button type="button" data-bs-target="#vCarousel" data-bs-slide-to="3" aria-label="Interior"></button>
            </div>
            <div class="carousel-inner">
               <div class="carousel-item active"><img src="${v.image}" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Front Angle"></div>
               <!-- Realistic High-Quality Unsplash Car Alternate Angles -->
               <div class="carousel-item"><img src="https://images.unsplash.com/photo-1606152421802-db97b9c7a11b?auto=format&fit=crop&w=800&q=80" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Side Profile"></div>
               <div class="carousel-item"><img src="https://images.unsplash.com/photo-1494976388531-d1058494cdd8?auto=format&fit=crop&w=800&q=80" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Rear Angle"></div>
               <div class="carousel-item"><img src="https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=800&q=80" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Interior View"></div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#vCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
            <button class="carousel-control-next" type="button" data-bs-target="#vCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="glass-panel p-4 p-lg-5 h-100 d-flex flex-column border-0 shadow-sm" style="background: rgba(255,255,255,0.6);">
             <h2 class="fw-bold mb-2">${v.brand || ''} ${v.name}</h2>
             <div class="mb-4">
               <span class="badge bg-warning text-dark shadow-sm fs-6 me-2"><i class="bi bi-star-fill"></i> ${v.rating_average || 'New'}</span>
               <span class="badge ${v.status === 'Available' ? 'bg-success' : 'bg-danger'} shadow-sm fs-6">${v.status}</span>
             </div>
             <h3 class="text-success fw-bold mb-4">$${v.rent_per_day} <span class="fs-6 text-muted fw-normal">/ day</span></h3>
             
             <div class="d-flex flex-column gap-3 mb-4 flex-grow-1">
                <div class="d-flex justify-content-between border-bottom border-secondary border-opacity-10 pb-2">
                  <span class="text-muted"><i class="bi bi-car-front me-2"></i> Body Type</span> <span class="fw-bold">${v.type}</span>
                </div>
                <div class="d-flex justify-content-between border-bottom border-secondary border-opacity-10 pb-2">
                  <span class="text-muted"><i class="bi bi-gear me-2"></i> Transmission</span> <span class="fw-bold">${v.transmission || 'Automatic'}</span>
                </div>
                <div class="d-flex justify-content-between border-bottom border-secondary border-opacity-10 pb-2">
                  <span class="text-muted"><i class="bi bi-fuel-pump me-2"></i> Fuel Type</span> <span class="fw-bold">${v.fuel_type || 'Gas'}</span>
                </div>
                <div class="d-flex justify-content-between border-bottom border-secondary border-opacity-10 pb-2">
                  <span class="text-muted"><i class="bi bi-people me-2"></i> Seating</span> <span class="fw-bold">${v.seating_capacity || '5'} Seats</span>
                </div>
             <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded-4">
                <div class="form-check form-switch cursor-pointer mb-0">
                  <input class="form-check-input compare-cb shadow-none cursor-pointer" type="checkbox" id="compare-${v.id}" ${window.comparisonList.find(c => c.id === v.id) ? 'checked' : ''} onchange="toggleCompare(${v.id}, '${v.name.replace(/'/g, "\\'")}', '${v.brand || ''}', '${v.image}', ${v.rent_per_day}, '${v.type}', '${v.rating_average || 'N/A'}', '${v.fuel_type || 'Petrol'}', '${v.transmission || 'Auto'}', ${v.seating_capacity || 5}, ${v.model_year || 2024})">
                  <label class="form-check-label fw-bold small text-primary" for="compare-${v.id}">Add to Comparison</label>
                </div>
                <span class="text-muted small">Max 3 cars</span>
             </div>
             
             <a href="booking.php?vehicleId=${v.id}" class="btn btn-primary btn-lg rounded-pill fw-bold w-100 text-uppercase pulse-btn ${v.status !== 'Available' ? 'disabled' : ''}"><i class="bi bi-calendar-check me-2"></i> Reserve Now</a>
          </div>
        </div>
      `;
    }

    const reviews = v.reviews || [];
    const reviewsContainer = document.getElementById('vehicleReviewsContainer');
    if (reviewsContainer) {
      if (reviews.length === 0) {
        reviewsContainer.innerHTML = '<div class="col-12"><div class="glass-panel p-4 text-center text-muted border-0 shadow-sm">No reviews yet for this vehicle. Be the first to try it out!</div></div>';
      } else {
        reviewsContainer.innerHTML = reviews.map(r => `
          <div class="col-md-6 mb-3">
            <div class="glass-panel p-4 border-0 shadow-sm h-100 d-flex flex-column">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                   <img src="${r.customer_image || 'https://via.placeholder.com/40'}" class="rounded-circle me-3 border border-2 border-white shadow-sm" style="width: 45px; height: 45px; object-fit: cover;">
                   <div>
                     <h6 class="mb-0 fw-bold">${r.customer_name}</h6>
                     <small class="text-muted">${new Date(r.created_at).toLocaleDateString()}</small>
                   </div>
                </div>
                <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill"><i class="bi bi-star-fill"></i> ${r.rating}.0</span>
              </div>
              <p class="text-muted small mb-0 flex-grow-1" style="font-style: italic;">"${r.comment || 'No comment provided.'}"</p>
            </div>
          </div>
        `).join('');
      }
    }
  } catch (err) {
    showMessage('message', 'Error loading vehicle details: ' + err.message, 'danger');
  }
};

const indexInit = async () => {
  try {
    const promo = await getData('/api/promotions');
    const banner = document.getElementById('globalPromoBanner');
    if (promo && promo.is_active && banner) {
      banner.style.display = 'block';
      const titleEl = document.getElementById('promoBannerTitle');
      if (titleEl) titleEl.innerHTML = `${promo.title} <span class="text-warning">${promo.discount_percentage}% OFF</span>`;
      const codeEl = document.getElementById('promoBannerCode');
      if (codeEl) codeEl.textContent = promo.promo_code;
      const descEl = document.getElementById('promoBannerDesc');
      if (descEl) descEl.textContent = promo.description;
    }
  } catch(e) { }
};

const deliveryInit = () => {
  console.log('Delivery Portal Initialized');
};

const maintenanceInit = () => {
  console.log('Maintenance Portal Initialized');
};

const init = () => {
  updateNavbar();
  currencyInit();
  const path = window.location.pathname;
  const page = path.split('/').pop();

  if (page === 'index.php' || page === '') indexInit();
  if (page === 'register.php') registerInit();
  if (page === 'login.php') loginInit();
  if (page === 'reset-password.php') resetPasswordInit();
  if (page === 'vehicles.php') vehiclesInit();
  if (page === 'favorites.php') favoritesInit();
  if (page === 'booking.php') bookingInit();
  if (page === 'payment.php') paymentInit();
  if (page === 'history.php') historyInit();
  if (page === 'admin.php') adminInit();
  if (page === 'profile.php') profileInit();
  if (page === 'vehicle-details.php') vehicleDetailsInit();
  if (page === 'delivery.php') deliveryInit();
  if (page === 'maintenance.php') maintenanceInit();
};

init();

document.addEventListener('DOMContentLoaded', () => {
  const scrollObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        scrollObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.fade-in-scroll').forEach(el => {
    scrollObserver.observe(el);
  });
});

/* --- Global Slider Logic --- */
window.initCardSliders = () => {
    const cards = document.querySelectorAll('.premium-slider-card');
    cards.forEach(card => {
        const id = card.dataset.vehicleId;
        const wrapper = card.querySelector('.card-slider-wrapper');
        const slides = card.querySelectorAll('.card-slide');
        const dots = card.querySelectorAll('.indicator-dot');
        let current = 0;

        const update = (index) => {
            current = (index + slides.length) % slides.length;
            if(wrapper) wrapper.style.transform = `translateX(-${current * 100}%)`;
            dots.forEach((dot, i) => dot.classList.toggle('active', i === current));
        };

        // Auto-slide every 3.5 seconds
        let timer = setInterval(() => update(current + 1), 3500);

        // Navigation listeners
        const prev = card.querySelector('.btn-prev');
        const next = card.querySelector('.btn-next');
        if(prev) prev.onclick = (e) => { e.preventDefault(); clearInterval(timer); update(current - 1); };
        if(next) next.onclick = (e) => { e.preventDefault(); clearInterval(timer); update(current + 1); };
    });
};
