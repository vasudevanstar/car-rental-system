<?php
$pageTitle = "Booking - FastRide";
include 'includes/header.php';
// Protected page
if (!isset($_SESSION['user'])) {
    header('Location: login.php?redirect=' . urlencode('booking.php?' . $_SERVER['QUERY_STRING']));
    exit;
}
?>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

  <div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    <div class="form-section position-relative fade-in-scroll" style="max-width: 650px; margin: auto;">
      <div class="text-center mb-5">
        <div class="feature-icon-wrapper mb-3 mx-auto shadow-sm" style="width: 70px; height: 70px;">
          <i class="bi bi-calendar-check fs-2 m-0" style="color: var(--accent);"></i>
        </div>
        <h3 class="fw-bold mb-1">Complete Your Booking</h3>
        <p class="text-muted">Reserve your car quickly and securely.</p>
      </div>

      <div id="message" class="alert-container position-relative top-0 mb-3" style="max-width: 100%;"></div>

      <form id="bookingForm" class="needs-validation">
        <div class="glass-panel p-4 mb-4" style="background: rgba(255,255,255,0.4);">
          <h5 class="fw-bold mb-3 d-flex align-items-center"><i class="bi bi-info-circle me-2 text-primary"></i> Step 1: Vehicle & Dates</h5>

          <div class="form-floating mb-3">
            <input type="number" id="vehicleId" class="form-control" required readonly>
            <label><i class="bi bi-car-front me-1"></i> Vehicle ID</label>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating">
                <input type="date" id="startDate" class="form-control" required>
                <label for="startDate"><i class="bi bi-calendar-event me-1 text-primary"></i> Pickup Date</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="date" id="endDate" class="form-control" required>
                <label for="endDate"><i class="bi bi-calendar-x me-1 text-danger"></i> Return Date</label>
              </div>
            </div>
          </div>

          <div class="mt-3 p-3 rounded-md" style="background: rgba(148, 163, 184, 0.1); border: 1px solid rgba(148, 163, 184, 0.2);">
            <div class="d-flex align-items-center justify-content-between">
              <span class="fw-medium">Vehicle Availability:</span>
              <span id="availabilityBadge" class="badge bg-secondary" style="font-size: 0.9rem;">Select dates to check</span>
            </div>
          </div>
        </div>

        <div class="glass-panel p-4 mb-4" style="background: rgba(255,255,255,0.4);">
          <h5 class="fw-bold mb-3 d-flex align-items-center"><i class="bi bi-geo-alt me-2 text-success"></i> Step 2: Locations</h5>
            
          <div id="map" class="mb-4 w-100 shadow-sm border border-2 border-white" style="height: 250px; border-radius: 12px; z-index: 1;"></div>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating">
                <select id="pickupLocation" class="form-select" required>
                  <option value="Downtown">Downtown Branch</option>
                  <option value="Airport">Airport Terminal</option>
                  <option value="Suburbs">Suburbs Center</option>
                </select>
                <label><i class="bi bi-geo-alt text-primary me-1"></i> Pickup Location</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <select id="dropoffLocation" class="form-select" required>
                  <option value="Downtown">Downtown Branch</option>
                  <option value="Airport">Airport Terminal</option>
                  <option value="Suburbs">Suburbs Center</option>
                </select>
                <label><i class="bi bi-geo-alt-fill text-danger me-1"></i> Drop-off Location</label>
              </div>
            </div>
          </div>
        </div>

        <div class="glass-panel p-4 mb-4" style="background: rgba(255,255,255,0.4);">
          <h5 class="fw-bold mb-3 d-flex align-items-center"><i class="bi bi-tag me-2 text-warning"></i> Step 3: Discounts (Optional)</h5>
          <div class="row g-3">
            <div class="col-md-12">
              <div class="glass-panel p-3 mb-3 d-flex justify-content-between align-items-center" id="loyaltySection" style="display: none !important;">
                 <div>
                   <i class="bi bi-star-fill text-warning me-2"></i>
                   <span id="loyaltyText" class="fw-bold">Loading points...</span>
                 </div>
                 <div class="form-check form-switch cursor-pointer m-0">
                   <input class="form-check-input shadow-none cursor-pointer" type="checkbox" id="applyPointsToggle">
                   <label class="form-check-label fw-semibold ms-1" for="applyPointsToggle">Apply Points</label>
                 </div>
              </div>
              <div class="form-floating">
                <input type="text" id="promoCode" class="form-control text-uppercase" placeholder="e.g. SUMMER20">
                <label><i class="bi bi-tag me-1"></i> Promo Code</label>
              </div>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary pulse-btn w-100 py-3 rounded-pill fw-bold text-uppercase"
          style="letter-spacing: 1px;">
          Proceed to Payment <i class="bi bi-arrow-right-circle ms-1"></i>
        </button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<?php 
echo "<script>document.addEventListener('DOMContentLoaded', bookingInit);</script>";
include 'includes/footer.php'; 
?>
