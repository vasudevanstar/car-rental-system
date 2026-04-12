<?php
$pageTitle = "Vehicle Details - FastRide";
include 'includes/header.php';
?>

  <div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    <div id="message" class="alert-container"></div>
    
    <button class="btn btn-outline-secondary mb-4 rounded-pill px-4" onclick="history.back()"><i class="bi bi-arrow-left me-1"></i> Back to Fleet</button>
    
    <div id="vehicleDetailsContainer" class="row g-5 fade-in-scroll">
      <div class="col-12 text-center text-muted">
        <div class="spinner-border text-primary my-5" role="status"></div>
        <p>Loading premium vehicle details...</p>
      </div>
    </div>

    <!-- Reviews Section -->
    <div class="row mt-5 pt-4 border-top border-opacity-10">
      <div class="col-12">
        <h4 class="fw-bold mb-4"><i class="bi bi-chat-quote-fill me-2 text-primary"></i> Customer Reviews</h4>
        <div id="vehicleReviewsContainer" class="row g-4">
          <div class="col-12 text-muted small px-3">Loading reviews...</div>
        </div>
      </div>
    </div>
  </div>

<?php 
echo "<script>document.addEventListener('DOMContentLoaded', () => { if(typeof vehicleDetailsInit === 'function') vehicleDetailsInit(); });</script>";
include 'includes/footer.php'; 
?>
