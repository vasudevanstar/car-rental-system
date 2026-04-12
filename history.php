<?php
$pageTitle = "Rental History - FastRide";
include 'includes/header.php';
// Protected page
if (!isset($_SESSION['user'])) {
    header('Location: login.php?redirect=history.php');
    exit;
}
?>

  <div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    <div class="text-center mb-5 fade-in-scroll">
      <div class="feature-icon-wrapper mb-3 mx-auto shadow-sm" style="width: 70px; height: 70px;">
        <i class="bi bi-clock-history fs-2 m-0" style="color: var(--accent);"></i>
      </div>
      <h3 class="fw-bold mb-1">My Rental History</h3>
      <p class="text-muted">View and manage all your past and current rentals here.</p>
    </div>

    <div id="message" class="alert-container position-relative top-0 w-100 z-3 mb-3"></div>

    <div class="glass-panel p-4 fade-in-scroll">
      <div id="historyList">
        <div class="text-center text-muted p-5">
          <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p>Loading your rental history...</p>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content glass-card border-0">
        <div class="modal-header border-bottom border-opacity-10">
          <h5 class="modal-title fw-bold" id="ratingModalLabel"><i class="bi bi-star-fill text-warning me-2"></i> Rate Your Experience</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <form id="ratingForm">
            <input type="hidden" id="rateRentalId">
            <input type="hidden" id="rateVehicleId">

            <div class="form-floating mb-3">
              <select id="rateStars" class="form-select">
                <option value="5">⭐⭐⭐⭐⭐ 5 - Excellent</option>
                <option value="4">⭐⭐⭐⭐ 4 - Very Good</option>
                <option value="3">⭐⭐⭐ 3 - Average</option>
                <option value="2">⭐⭐ 2 - Poor</option>
                <option value="1">⭐ 1 - Terrible</option>
              </select>
              <label><i class="bi bi-star"></i> Rating (Stars)</label>
            </div>

            <div class="form-floating mb-4">
              <textarea id="rateReview" class="form-control" style="height: 100px" placeholder="Tell us about the vehicle condition, service, etc."></textarea>
              <label><i class="bi bi-pencil me-1"></i> Review / Feedback</label>
            </div>

            <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold text-uppercase pulse-btn">Submit Review <i class="bi bi-arrow-right-circle ms-1"></i></button>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php 
echo "<script>document.addEventListener('DOMContentLoaded', historyInit);</script>";
include 'includes/footer.php'; 
?>
