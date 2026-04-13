<?php
$pageTitle = "Admin Dashboard - FastRide";
include 'includes/header.php';
// Protected page (Allow Admin and Maintenance)
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'maintenance')) {
    header('Location: login.php?redirect=admin.php');
    exit;
}

$userRole = $_SESSION['user']['role'];

// Handle Delivery Assignment (Pure PHP POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignDelivery'])) {
    require_once __DIR__ . '/config/db.php';
    $rentalId = (int)$_POST['rentalId'];
    $empId = (int)$_POST['employeeId'];
    $address = $_POST['deliveryAddress'];

    try {
        $stmt = $pdo->prepare("UPDATE rentals SET delivery_employee_id = ?, delivery_address = ?, delivery_status = 'Assigned' WHERE id = ?");
        $stmt->execute([$empId, $address, $rentalId]);
        
        $logStmt = $pdo->prepare("INSERT INTO activity_log (user_id, action, details) VALUES (?, 'Delivery Assigned', ?)");
        $logStmt->execute([$_SESSION['user']['id'], "Assigned Delivery Emp #$empId to Rental #$rentalId"]);
        
        $msg = "Delivery assigned successfully!";
        header("Location: admin.php?msg=" . urlencode($msg));
        exit;
    } catch (PDOException $e) { $error = $e->getMessage(); }
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
      box-shadow: none;
    }
    .admin-sidebar.show {
      left: 0;
      box-shadow: 20px 0 50px rgba(0,0,0,0.15);
    }
    .admin-main {
      margin-left: 0;
      padding: 1.5rem;
    }
    .sidebar-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.4);
      backdrop-filter: blur(4px);
      z-index: 999;
    }
    .sidebar-overlay.show {
      display: block;
    }
  }

  .mobile-toggle {
    display: none;
    background: white;
    border: none;
    width: 45px;
    height: 45px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #000;
  }

  @media (max-width: 991px) {
    .mobile-toggle {
      display: flex;
    }
  }

  /* Card and Stat Improvements */
  .stat-card {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 1.5rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  @media (min-width: 992px) {
    .stat-card { height: 100%; }
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
  <!-- Toggle Backdrop -->
  <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleMobileMenu()"></div>

  <!-- Sidebar -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="d-flex d-lg-none justify-content-end mb-3">
        <button class="btn btn-light rounded-circle p-2 text-dark" onclick="toggleMobileMenu()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="mb-5 px-2">
      <h6 class="text-uppercase small fw-bold text-muted mb-3" style="letter-spacing: 2px;">Core Menu</h6>
      <nav>
        <a href="index.php" class="admin-nav-link text-primary mb-3 pb-3 border-bottom border-white border-opacity-10">
          <i class="bi bi-house-door-fill"></i> Go to Site Home
        </a>
        <?php if ($userRole === 'admin'): ?>
        <a href="#overview" class="admin-nav-link active" onclick="showSection('overview')">
          <i class="bi bi-grid-1x2-fill"></i> Overview
        </a>
        <?php endif; ?>

        <a href="#vehicles" class="admin-nav-link <?php echo $userRole === 'maintenance' ? 'active' : ''; ?>" onclick="showSection('vehicles')">
          <i class="bi bi-car-front-fill"></i> Fleet Portfolio
        </a>

        <?php if ($userRole === 'admin'): ?>
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
        <?php endif; ?>

        <?php if ($userRole === 'maintenance'): ?>
        <a href="maintenance.php" class="admin-nav-link">
          <i class="bi bi-tools"></i> Maintenance Portal
        </a>
        <?php endif; ?>
      </nav>
    </div>

    <?php if ($userRole === 'admin'): ?>
    <div class="mb-5 px-2">
      <h6 class="text-uppercase small fw-bold text-muted mb-3" style="letter-spacing: 2px;">Marketing</h6>
      <nav>
        <a href="#promotions" class="admin-nav-link" onclick="showSection('promotions')">
          <i class="bi bi-megaphone-fill"></i> Promotions
        </a>
      </nav>
    </div>
    <?php endif; ?>

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
    <div class="welcome-banner d-flex justify-content-between align-items-center mb-4 g-3">
      <div class="d-flex align-items-center">
        <button class="mobile-toggle me-3 d-lg-none" onclick="toggleMobileMenu()">
           <i class="bi bi-list"></i>
        </button>
        <div>
          <h1 class="display-5 fw-bold mb-1">Welcome back, Admin!</h1>
          <p class="opacity-75 mb-0 fs-5" id="currentDateDisplay"></p>
        </div>
      </div>
      <div class="backdrop-blur-sm bg-white bg-opacity-20 rounded-pill px-4 py-2 border border-dark border-opacity-25 text-dark fw-bold d-none d-md-block">
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

      <div class="row g-4 gy-5">
        <div class="col-12 col-lg-7">
          <div class="stat-card mb-4">
            <h5 class="fw-bold mb-4 d-flex align-items-center">
              <i class="bi bi-graph-up text-primary me-2"></i> Monthly Revenue Detail
            </h5>
            <canvas id="revenueChart" style="max-height: 350px;"></canvas>
          </div>
          <div class="row g-4">
            <div class="col-md-6">
                <div class="stat-card">
                  <h6 class="fw-bold mb-3 small text-uppercase text-muted">Fleet Utilization</h6>
                  <canvas id="utilizationChart" style="max-height: 200px;"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card">
                  <h6 class="fw-bold mb-3 small text-uppercase text-muted">Revenue by Vehicle Type</h6>
                  <canvas id="categoryRevChart" style="max-height: 200px;"></canvas>
                </div>
            </div>
          </div>
        </div>
        
        <div class="col-12 col-lg-5">
           <div class="stat-card d-flex flex-column">
            <h5 class="fw-bold mb-4 d-flex align-items-center">
              <i class="bi bi-list-stars text-accent me-2"></i> Recent System Activity
            </h5>
            <div id="activityFeed" class="flex-grow-1 overflow-auto" style="max-height: 520px;">
                <!-- Activity logs will go here -->
                <div class="p-4 text-center text-muted small">
                  <span class="spinner-border spinner-border-sm me-2"></span> Initializing feed...
                </div>
            </div>
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

<!-- Modal: Global Command Palette (Ctrl+K) -->
<div class="modal fade" id="commandPalette" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content backdrop-blur-sm bg-white bg-opacity-95 shadow-2xl border-0 overflow-hidden" style="border-radius: 1.5rem;">
      <div class="p-3 border-bottom d-flex align-items-center">
        <i class="bi bi-search fs-4 text-primary me-3"></i>
        <input type="text" id="paletteSearch" class="form-control border-0 shadow-none fs-5 p-2 bg-transparent" placeholder="Type to search users, cars, or bookings..." autofocus>
        <span class="badge bg-light text-muted fw-normal ms-2 border">ESC to close</span>
      </div>
      <div id="paletteResults" class="modal-body p-0 overflow-auto" style="max-height: 400px;">
        <div class="p-4 text-center text-muted small">Start typing to see results...</div>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Assign Delivery -->
<div class="modal fade" id="assignDeliveryModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow rounded-4">
      <div class="modal-header border-0 pb-0">
        <h5 class="fw-bold">Assign Delivery Employee</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <div class="modal-body">
            <input type="hidden" name="rentalId" id="assignRentalId">
            <div class="mb-3">
                <label class="form-label small fw-bold">Select Employee</label>
                <select name="employeeId" id="deliveryEmpSelect" class="form-select border-0 bg-light" required>
                    <!-- Populated via JS -->
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Delivery Address</label>
                <textarea name="deliveryAddress" id="deliveryAddr" class="form-control border-0 bg-light" rows="3" required></textarea>
            </div>
        </div>
        <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="assignDelivery" class="btn btn-primary rounded-pill px-4">Assign Now</button>
        </div>
      </form>
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
      setTimeout(() => {
        target.classList.add('active');
        // Re-init charts if overview is shown
        if (sectionId === 'overview' && typeof loadAnalytics === 'function') {
           loadAnalytics();
        }
      }, 10);
    }
    
    // Update active class in sidebar
    if (event && event.currentTarget) {
      links.forEach(link => link.classList.remove('active'));
      event.currentTarget.classList.add('active');
    }

    // Auto-hide on mobile after selection
    if (window.innerWidth < 992) {
      toggleMobileMenu();
    }
  }

  function toggleMobileMenu() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('show');
    overlay.classList.toggle('show');
    
    // Toggle body scroll
    if (sidebar.classList.contains('show')) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = 'auto';
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
