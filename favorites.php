<?php
$pageTitle = "Favorites - FastRide";
include 'includes/header.php';
// Protected page
if (!isset($_SESSION['user'])) {
    header('Location: login.php?redirect=favorites.php');
    exit;
}
?>

  <div class="container" style="margin-top: 100px;">
    <div class="mb-2">
      <button class="btn btn-outline-secondary rounded-pill px-4 shadow-sm fw-bold" onclick="history.back()"><i class="bi bi-arrow-left me-2"></i> Back</button>
    </div>
    <div class="text-center mb-5">
      <h2 class="fw-bold display-5">Your Favorites</h2>
      <p class="text-muted fs-5">All the vehicles you've loved in one place.</p>
    </div>

    <div id="message" class="alert-container"></div>

    <div class="row g-4" id="vehiclesList"></div>

  </div>

<?php 
echo "<script>document.addEventListener('DOMContentLoaded', favoritesInit);</script>";
include 'includes/footer.php'; 
?>
