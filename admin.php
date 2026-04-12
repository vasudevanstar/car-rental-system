<?php
$pageTitle = "Admin Dashboard - FastRide";
include 'includes/header.php';
// Protected page
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php?redirect=admin.php');
    exit;
}
?>

<style>
  :root {
    --sidebar-width: 280px;
    --glass-bg: rgba(255, 255, 255, 0.7);
    --glass-border: rgba(255, 255, 255, 0.3);
  }

  [data-theme="dark"] {
    --glass-bg: rgba(15, 23, 42, 0.7);
    --glass-border: rgba(255, 255, 255, 0.1);
  }

  body {
    background-color: var(--bg-main);
    overflow-x: hidden;
  }

  .admin-wrapper {
    display: flex;
    min-height: 100vh;
    padding-top: 0;
  }

  /* Sidebar Styling */
  .admin-sidebar {
    width: var(--sidebar-width);
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    background: var(--glass-bg);
    backdrop-filter: blur(15px);
    border-right: 1px solid var(--glass-border);
    padding: 2rem 1.5rem;
    z-index: 1000;
    transition: all 0.3s ease;
  }

  .admin-nav-link {
    display: flex;
    align-items: center;
    padding: 0.8rem 1.2rem;
    color: var(--text-muted);
    text-decoration: none;
    border-radius: 12px;
    margin-bottom: 0.5rem;
    transition: all 0.2s ease;
    font-weight: 500;
  }

  .admin-nav-link i {
    font-size: 1.25rem;
    margin-right: 1rem;
    transition: transform 0.2s ease;
  }

  .admin-nav-link:hover {
    background: linear-gradient(90deg, rgba(99, 102, 241, 0.12) 0%, rgba(99, 102, 241, 0.04) 100%);
    color: var(--accent-color);
    transform: translateX(8px);
    box-shadow: inset 3px 0 0 0 var(--accent-color);
  }

  .admin-nav-link:hover i {
    color: var(--accent-color);
    transform: scale(1.15);
  }

  .admin-nav-link.active {
    background: var(--accent-color);
    color: white !important;
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
  }

  /* Main Content Layout */
  .admin-main {
    flex-grow: 1;
    margin-left: var(--sidebar-width);
    padding: 2.5rem;
    transition: all 0.3s ease;
  }

  @media (max-width: 991px) {
    .admin-sidebar {
      left: calc(-1 * var(--sidebar-width));
    }
    .admin-sidebar.show {
      left: 0;
    }
    .admin-main {
      margin-left: 0;
      padding: 1.5rem;
    }
  }

  /* Card and Stat Improvements */
  .stat-card {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 1.5rem;
    height: 100%;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  }

  .stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
  }

  .table-container {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 0;
    overflow: hidden;
    margin-bottom: 2rem;
  }

  /* Modal Styling */
  .modal-content {
    border-radius: 20px;
    border: none;
    background: var(--bg-main);
  }

  .modal-header {
    border-bottom: 1px solid var(--glass-border);
    padding: 1.5rem 2rem;
  }

  .modal-body {
    padding: 2rem;
  }
  .stats-card {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 1.5rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
  }

  .stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border-color: var(--accent-color);
  }

  .stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
  }

  .admin-section {
    display: none;
    animation: fadeIn 0.4s ease-out forwards;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .welcome-banner {
    background: linear-gradient(135deg, var(--accent-color), #4f46e5);
    border-radius: 24px;
    padding: 2.5rem;
    color: white;
    margin-bottom: 2.5rem;
    position: relative;
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
  }
  .backdrop-blur-sm {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
  }
</style>

<div class="admin-wrapper">
  <!-- Sidebar -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="mb-5 px-2">
      <h6 class="text-uppercase small fw-bold text-muted mb-3" style="letter-spacing: 2px;">Core Menu</h6>
      <nav>
        <a href="index.php" class="admin-nav-link text-primary mb-3 pb-3 border-bottom border-white border-opacity-10">
          <i class="bi bi-house-door-fill"></i> Go to Site Home
        </a>
        <a href="#overview" class="admin-nav-link active" onclick="showSection('overview')">
          <i class="bi bi-grid-1x2-fill"></i> Overview
        </a>
        <a href="#vehicles" class="admin-nav-link" onclick="showSection('vehicles')">
          <i class="bi bi-car-front-fill"></i> Fleet Portfolio
        </a>
        <a href="#bookings" class="admin-nav-link" onclick="showSection('bookings')">
          <i class="bi bi-journal-text"></i> Bookings
        </a>
        <a href="#verifications" class="admin-nav-link" onclick="showSection('verifications')">
          <i class="bi bi-shield-check"></i> Verifications
          <span class="badge bg-danger ms-auto rounded-pill" id="verificationBadge" style="display:none;">0</span>
        </a>
        <a href="#users" class="admin-nav-link" onclick="showSection('users')">
          <i class="bi bi-people-fill"></i> User Accounts
        </a>
      </nav>
    </div>

    <div class="mb-5 px-2">
      <h6 class="text-uppercase small fw-bold text-muted mb-3" style="letter-spacing: 2px;">Marketing</h6>
      <nav>
        <a href="#promotions" class="admin-nav-link" onclick="showSection('promotions')">
          <i class="bi bi-megaphone-fill"></i> Promotions
        </a>
      </nav>
    </div>

    <div class="mt-auto pt-5">
      <a href="javascript:void(0)" onclick="logout()" class="admin-nav-link text-danger">
        <i class="bi bi-box-arrow-right"></i> Sign Out
      </a>
    </div>
  </aside>

  <!-- Main Content Area -->
  <main class="admin-main">
    <div id="message"></div>

    <!-- Header Banner -->
    <div class="welcome-banner d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="display-5 fw-bold mb-1">Welcome back, Admin!</h1>
        <p class="opacity-75 mb-0 fs-5" id="currentDateDisplay"></p>
      </div>
      <div class="backdrop-blur-sm bg-white bg-opacity-20 rounded-pill px-4 py-2 border border-dark border-opacity-25 text-dark fw-bold">
        <i class="bi bi-shield-check me-2"></i> System Secured
      </div>
    </div>

    <!-- Quick Stats Overview -->
    <div class="row g-4 mb-5">
      <div class="col-md-3">
        <div class="stats-card">
          <div class="stats-icon bg-primary bg-opacity-10 text-primary">
            <i class="bi bi-cash-stack"></i>
          </div>
          <h6 class="text-muted small fw-bold text-uppercase">Total Revenue</h6>
          <h2 class="fw-bold mb-0" id="statRevenue">$0</h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card">
          <div class="stats-icon bg-success bg-opacity-10 text-success">
            <i class="bi bi-people"></i>
          </div>
          <h6 class="text-muted small fw-bold text-uppercase">Active Users</h6>
          <h2 class="fw-bold mb-0" id="statUsers">0</h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card">
          <div class="stats-icon bg-warning bg-opacity-10 text-warning">
            <i class="bi bi-car-front-fill"></i>
          </div>
          <h6 class="text-muted small fw-bold text-uppercase">Total Fleet</h6>
          <h2 class="fw-bold mb-0" id="statFleet">0</h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card">
          <div class="stats-icon bg-success bg-opacity-10 text-success">
            <i class="bi bi-check-circle-fill"></i>
          </div>
          <h6 class="text-muted small fw-bold text-uppercase">Available Today</h6>
          <h2 class="fw-bold mb-0" id="statAvailable">0</h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stats-card">
          <div class="stats-icon bg-danger bg-opacity-10 text-danger">
            <i class="bi bi-calendar-event-fill"></i>
          </div>
          <h6 class="text-muted small fw-bold text-uppercase">Booked Cars</h6>
          <h2 class="fw-bold mb-0" id="statBooked">0</h2>
        </div>
      </div>
    </div>

    <!-- Section: Overview -->
    <section id="overviewSection" class="admin-section active" style="display:block;">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0 h3">Revenue Analytics</h2>
        <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 1px;">
           <i class="bi bi-calendar3 me-2"></i> Real-time Analytics
        </div>
      </div>

      <div class="row g-4 mb-5" id="analyticsCards">
         <!-- Populated via JS -->
      </div>

      <div class="row g-4">
        <div class="col-lg-8">
          <div class="stat-card">
            <h5 class="fw-bold mb-4 d-flex align-items-center">
              <i class="bi bi-graph-up text-primary me-2"></i> Monthly Revenue Detail
            </h5>
            <canvas id="revenueChart" style="max-height: 400px;"></canvas>
          </div>
        </div>
        <div class="col-lg-4">
           <div class="stat-card">
            <h5 class="fw-bold mb-4 d-flex align-items-center">
              <i class="bi bi-pie-chart text-accent me-2"></i> Fleet Distribution
            </h5>
            <canvas id="typeChart" style="max-height: 400px;"></canvas>
          </div>
        </div>
      </div>
    </section>

    <!-- Section: Vehicles -->
    <section id="vehiclesSection" style="display:none;">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0 h3">Fleet Management</h2>
        <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#vehicleModal" onclick="resetVehicleForm()">
          <i class="bi bi-plus-lg me-2"></i> New Vehicle
        </button>
      </div>

      <div class="glass-panel p-3 mb-4 rounded-4 border">
        <div class="row g-3">
          <div class="col-md-6">
             <div class="input-group">
               <span class="input-group-text bg-transparent border-0"><i class="bi bi-search"></i></span>
               <input type="text" id="vehicleSearch" class="form-control border-0 bg-transparent shadow-none" placeholder="Search by name, brand or type..." onkeyup="filterTable('vehicleTable', this.value)">
             </div>
          </div>
        </div>
      </div>

      <div class="table-container">
        <div id="vehicleTable"></div>
      </div>
    </section>

    <!-- Section: Bookings -->
    <section id="bookingsSection" style="display:none;">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0 h3">Booking Management</h2>
        <button class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" onclick="exportBookings()">
          <i class="bi bi-download me-2"></i> Export CSV
        </button>
      </div>

      <div class="glass-panel p-3 mb-4 rounded-4 border">
        <div class="row g-3 align-items-center">
          <div class="col-md-6">
             <div class="input-group">
               <span class="input-group-text bg-transparent border-0"><i class="bi bi-search"></i></span>
               <input type="text" id="bookingSearch" class="form-control border-0 bg-transparent shadow-none" placeholder="Search by customer or car..." onkeyup="filterTable('bookingTable', this.value)">
             </div>
          </div>
        </div>
      </div>

      <div class="table-container">
        <div id="bookingTable"></div>
      </div>
    </section>

    <!-- Section: Users -->
    <section id="usersSection" style="display:none;" class="admin-section">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0 h3">User Accounts Directory</h2>
        <div class="glass-panel px-3 py-1 rounded-pill border d-flex align-items-center bg-white bg-opacity-50">
           <i class="bi bi-search text-muted me-2"></i>
           <input type="text" class="form-control border-0 bg-transparent shadow-none p-0" placeholder="Search users by name or email..." onkeyup="filterTable('usersTable', this.value)" style="width: 250px;">
        </div>
      </div>
      <div class="table-container">
        <div id="usersTable"></div>
      </div>
    </section>

    <!-- Section: Verifications -->
    <section id="verificationsSection" style="display:none;" class="admin-section">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0 h3">Identity Verifications</h2>
        <div class="d-flex gap-2">
           <button class="btn btn-outline-secondary rounded-circle" onclick="loadVerifications()" title="Refresh List"><i class="bi bi-arrow-clockwise"></i></button>
           <div class="glass-panel px-3 py-1 rounded-pill border d-flex align-items-center bg-white bg-opacity-50">
              <i class="bi bi-search text-muted me-2"></i>
              <input type="text" class="form-control border-0 bg-transparent shadow-none p-0" placeholder="Filter verifications..." onkeyup="filterTable('verificationsTable', this.value)" style="width: 200px;">
           </div>
        </div>
      </div>
      <div class="table-container">
        <div id="verificationsTable"></div>
      </div>
    </section>

    <!-- Section: Promotions -->
    <section id="promotionsSection" style="display:none;">
      <h2 class="fw-bold mb-4 h3">Global Promotion Control</h2>
      <div class="stat-card" style="max-width: 600px;">
        <form id="promoForm" class="d-flex flex-column gap-4">
          <div class="form-floating">
            <input type="text" id="promoTitle" class="form-control bg-transparent" placeholder="Headline" required>
            <label>Headline Title</label>
          </div>
          <div class="form-floating">
            <textarea id="promoDesc" class="form-control bg-transparent" placeholder="Description" style="height: 100px" required></textarea>
            <label>Detailed Description</label>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" id="promoCodeInput" class="form-control bg-transparent fw-bold" placeholder="Code" required style="letter-spacing: 2px;">
                <label>Promo Code</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="number" id="promoDiscount" class="form-control bg-transparent" placeholder="Discount" required>
                <label>Discount %</label>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary py-3 rounded-pill fw-bold text-uppercase shadow-sm">
            <i class="bi bi-broadcast me-2"></i> Update Global Promotion
          </button>
        </form>
      </div>
    </section>

  </main>
</div>

<!-- Modal: Vehicle Entry/Edit -->
<div class="modal fade" id="vehicleModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="vehicleModalTitle">Add New Vehicle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="vehicleForm" class="d-flex flex-column gap-3">
          <input type="hidden" id="vehicleId">
          <div class="form-floating">
            <input type="text" id="vehicleBrand" class="form-control" placeholder="Brand" required>
            <label>Brand (e.g. Toyota)</label>
          </div>
          <div class="form-floating">
            <input type="text" id="vehicleName" class="form-control" placeholder="Model" required>
            <label>Model Name</label>
          </div>
          <div class="form-floating">
             <select id="vehicleType" class="form-select" required>
               <option value="Sedan">Sedan</option>
               <option value="SUV">SUV</option>
               <option value="Luxury">Luxury</option>
               <option value="Hatchback">Hatchback</option>
             </select>
            <label>Vehicle Type</label>
          </div>
          <div class="form-floating">
            <input type="number" id="vehicleRent" class="form-control" placeholder="Rent" required>
            <label>Rent Per Day ($)</label>
          </div>
          <div class="form-floating">
            <select id="vehicleStatus" class="form-select" required>
              <option value="Available">Available</option>
              <option value="Maintenance">Maintenance</option>
              <option value="Booked">Booked</option>
            </select>
            <label>Starting Status</label>
          </div>
          <div class="form-floating">
            <input type="url" id="vehicleImage" class="form-control" placeholder="Image URL">
            <label>Image URL</label>
          </div>
          <div class="row g-2">
            <div class="col-md-6">
              <div class="form-floating">
                <select id="vehicleTransmission" class="form-select">
                  <option value="Automatic">Automatic</option>
                  <option value="Manual">Manual</option>
                </select>
                <label>Transmission</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <select id="vehicleFuel" class="form-select">
                  <option value="Petrol">Petrol</option>
                  <option value="Diesel">Diesel</option>
                  <option value="Electric">Electric</option>
                  <option value="Hybrid">Hybrid</option>
                </select>
                <label>Fuel Type</label>
              </div>
            </div>
          </div>
          <div class="row g-2">
            <div class="col-md-6">
              <div class="form-floating">
                <input type="number" id="vehicleSeats" class="form-control" value="5">
                <label>Seats</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="number" id="vehicleYear" class="form-control" value="2023">
                <label>Model Year</label>
              </div>
            </div>
          </div>
          <div class="form-floating">
            <textarea id="vehicleDescription" class="form-control" style="height: 100px" placeholder="Description"></textarea>
            <label>Description</label>
          </div>
          <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold mt-2 shadow-sm">
            Save Vehicle Details
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal: License Verification -->
<div class="modal fade" id="licenseModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Review License Document</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img id="licensePreviewImg" src="" class="img-fluid rounded-4 mb-4 border shadow-sm" style="max-height: 500px;">
        <div id="verifyActions" class="d-flex justify-content-center gap-3">
          <!-- Buttons generated by JS -->
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php 
echo "<script>document.addEventListener('DOMContentLoaded', () => { if(typeof adminInit === 'function') adminInit(); });</script>";
?>

<script>
  function showSection(sectionId) {
    const sections = document.querySelectorAll('main section');
    const links = document.querySelectorAll('.admin-nav-link');
    
    // Smooth transition
    sections.forEach(sec => {
      sec.style.display = 'none';
      sec.classList.remove('active');
    });
    
    const target = document.getElementById(sectionId + 'Section');
    if (target) {
      target.style.display = 'block';
      setTimeout(() => target.classList.add('active'), 10);
    }
    
    // Update active class in sidebar
    links.forEach(link => link.classList.remove('active'));
    if (event && event.currentTarget) {
      event.currentTarget.classList.add('active');
    }
  }

  // Initialize Date
  document.addEventListener('DOMContentLoaded', () => {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const date = new Date().toLocaleDateString('en-US', options);
    const dateEl = document.getElementById('currentDateDisplay');
    if (dateEl) dateEl.textContent = date;
  });
</script>

<?php include 'includes/footer.php'; ?>
