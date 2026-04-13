<?php
$pageTitle = "Vehicles - FastRide";
include 'includes/header.php';
?>

  <div class="container responsive-mt mb-5">
    <div class="text-center mb-5">
      <h2 class="fw-bold display-5">Available Vehicles</h2>
      <p class="text-muted fs-5">Find the perfect ride for your next journey.</p>
    </div>

    <div class="filter-wrapper glass-panel p-4 mb-5 border-0 shadow-lg" style="border-radius: 24px;">
      <div class="row g-4 align-items-end">
        <!-- Search & Type Row -->
        <div class="col-lg-4">
          <div class="form-group mb-0">
             <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="letter-spacing: 1px;"><i class="bi bi-search me-1"></i> Quick Search</label>
             <div class="input-group">
                <input type="text" id="searchInput" class="form-control form-control-lg border-0 bg-light shadow-none" placeholder="Brand or model..." style="border-radius: 12px;">
             </div>
          </div>
        </div>
        
        <div class="col-6 col-md-4 col-lg-2">
          <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="letter-spacing: 1px;">Vehicle Type</label>
          <select id="typeFilter" class="form-select border-0 bg-light py-3 shadow-none" style="border-radius: 12px; height: 52px; font-size: 0.9rem;">
            <option value="">🚗 All Types</option>
            <option value="Sedan">Sedan</option>
            <option value="Luxury">Luxury</option>
            <option value="SUV">SUV</option>
            <option value="Sport">Sport</option>
            <option value="Hatchback">Hatchback</option>
          </select>
        </div>
        
        <div class="col-6 col-md-4 col-lg-2">
          <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="letter-spacing: 1px;">Brand</label>
          <select id="brandFilter" class="form-select border-0 bg-light py-3 shadow-none" style="border-radius: 12px; height: 52px; font-size: 0.9rem;">
            <option value="">🏷️ All Brands</option>
            <option value="Tesla">Tesla</option>
            <option value="Porsche">Porsche</option>
            <option value="BMW">BMW</option>
            <option value="Audi">Audi</option>
            <option value="Mercedes-Benz">Mercedes-Benz</option>
          </select>
        </div>

        <div class="col-md-6 col-lg-2">
          <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="letter-spacing: 1px;">Transmission</label>
          <select id="transmissionFilter" class="form-select border-0 bg-light py-3 shadow-none" style="border-radius: 12px; height: 52px;">
            <option value="">⚙️ All</option>
            <option value="Automatic">Automatic</option>
            <option value="Manual">Manual</option>
          </select>
        </div>

        <div class="col-md-6 col-lg-2">
          <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="letter-spacing: 1px;">Sort By</label>
          <select id="sortFilter" class="form-select border-0 bg-light py-3 shadow-none" style="border-radius: 12px; height: 52px;">
            <option value="">Latest Arrival</option>
            <option value="price_asc">Price: Low-High</option>
            <option value="price_desc">Price: High-Low</option>
            <option value="rating_desc">Best Rated</option>
          </select>
        </div>

        <!-- Second Row: Secondary Filters -->
        <div class="col-md-4 col-lg-2">
          <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="letter-spacing: 1px;">Availability</label>
          <select id="availabilityFilter" class="form-select border-0 bg-light py-3 shadow-none" style="border-radius: 12px; height: 52px;">
            <option value="">✅ All Status</option>
            <option value="Available">Available</option>
            <option value="Booked">Booked</option>
          </select>
        </div>

        <div class="col-md-4 col-lg-2">
          <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="letter-spacing: 1px;">Fuel Type</label>
          <select id="fuelFilter" class="form-select border-0 bg-light py-3 shadow-none" style="border-radius: 12px; height: 52px;">
            <option value="">⛽ Any</option>
            <option value="Petrol">Petrol</option>
            <option value="Electric">Electric</option>
            <option value="Diesel">Diesel</option>
            <option value="Hybrid">Hybrid</option>
          </select>
        </div>

        <div class="col-md-4 col-lg-2">
          <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="letter-spacing: 1px;">Min Seats</label>
          <select id="seatsFilter" class="form-select border-0 bg-light py-3 shadow-none" style="border-radius: 12px; height: 52px;">
            <option value="">💺 Any</option>
            <option value="2">2+ Seats</option>
            <option value="4">4+ Seats</option>
            <option value="5">5+ Seats</option>
          </select>
        </div>

        <div class="col-lg-3">
          <div class="px-3 py-2 bg-light" style="border-radius: 12px;">
            <label class="small fw-bold text-muted text-uppercase mb-2 d-block" style="letter-spacing: 1px;">Budget: <span id="priceDisplay" class="text-primary">$1000</span></label>
            <input type="range" class="form-range custom-range" id="maxPriceFilter" min="50" max="1000" value="1000" step="10" oninput="document.getElementById('priceDisplay').textContent = '$' + this.value">
          </div>
        </div>

        <div class="col-lg-3">
          <div class="d-flex gap-2">
            <button id="filterBtn" class="btn btn-primary flex-grow-1 fw-bold py-3 shadow-sm border-0" style="border-radius: 12px;">
              <i class="bi bi-funnel-fill me-2"></i>Apply
            </button>
            <button onclick="window.location.reload()" class="btn btn-outline-secondary px-3" style="border-radius: 12px;" title="Reset Filters">
              <i class="bi bi-arrow-counterclockwise"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div id="message" class="alert-container"></div>

    <div class="row g-4" id="vehiclesList"></div>

    <div class="d-flex justify-content-between align-items-center mt-5 mb-5 glass-panel p-3 shadow-sm mx-auto"
      style="max-width: 400px;">
      <button id="prevPage" class="btn btn-outline-primary rounded-pill px-4"><i class="bi bi-chevron-left"></i>
        Prev</button>
      <span id="pageInfo" class="fw-bold" style="color: var(--text-primary);">Page 1</span>
      <button id="nextPage" class="btn btn-outline-primary rounded-pill px-4">Next <i
          class="bi bi-chevron-right"></i></button>
    </div>
  </div>

<?php 
// Initialize vehicles script from JS
echo "<script>document.addEventListener('DOMContentLoaded', vehiclesInit);</script>";
include 'includes/footer.php'; 
?>
