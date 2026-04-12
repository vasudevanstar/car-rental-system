<?php
$pageTitle = "My Profile - FastRide";
include 'includes/header.php';
// Protected page
if (!isset($_SESSION['user'])) {
    header('Location: login.php?redirect=profile.php');
    exit;
}
?>

  <div class="container fade-in-scroll visible" style="margin-top: 100px; margin-bottom: 50px;">
    <div class="form-section shadow-lg position-relative" style="max-width: 650px; margin: auto;">

      <div class="text-center mb-5 mt-3">
        <div class="mb-4 position-relative d-inline-block">
          <label for="profileImageFile"
            class="position-absolute bottom-0 end-0 bg-accent text-white rounded-circle d-flex align-items-center justify-content-center cursor-pointer shadow-sm"
            style="width: 35px; height: 35px; right: 5px!important; bottom: 5px!important; cursor: pointer; transition: transform 0.2s;"
            onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
            <i class="bi bi-camera"></i>
          </label>
          <img id="profilePicturePreview" src="https://via.placeholder.com/120" class="rounded-circle shadow-md"
            style="width: 130px; height: 130px; object-fit: cover; border: 4px solid var(--accent); background: white;">
        </div>
        <h3 id="profileGreeting" class="fw-bold mb-1">Hello, User</h3>
        <p class="text-muted mb-2">Update your contact details and security settings.</p>
        <div class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill shadow-sm" id="profileLoyaltyBadge" style="display:none;"><i class="bi bi-star-fill me-1"></i> <span id="profileLoyaltyPoints"></span> Points</div>
      </div>

      <div id="message" class="alert-container position-relative top-0 mb-3" style="max-width: 100%;"></div>

      <form id="profileForm">
        <div class="mb-4 d-none">
          <input type="file" id="profileImageFile" class="form-control" accept="image/*">
          <input type="hidden" id="profileImageUrl" value="">
        </div>

        <div class="glass-panel p-4 mb-4" style="background: rgba(255,255,255,0.4);">
          <h5 class="fw-bold mb-4 d-flex align-items-center"><i class="bi bi-person-lines-fill text-primary me-2"></i>
            Personal Details</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" id="profileName" class="form-control" required>
                <label><i class="bi bi-person me-1"></i> Full Name</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="tel" id="profilePhone" class="form-control" required>
                <label><i class="bi bi-telephone me-1"></i> Phone Number</label>
              </div>
            </div>
          </div>
        </div>

        <div class="glass-panel p-4 mb-5" style="background: rgba(255,255,255,0.4);">
          <h5 class="fw-bold mb-4 d-flex align-items-center"><i class="bi bi-shield-lock text-success me-2"></i>
            Security Settings</h5>

          <div class="form-floating mb-3">
            <input type="email" id="profileEmail" class="form-control" style="background-color: var(--bg-main);"
              readonly disabled>
            <label><i class="bi bi-envelope me-1"></i> Email Address (Cannot be changed)</label>
          </div>

          <div class="form-floating position-relative">
            <input type="password" id="profilePassword" class="form-control pe-5"
              placeholder="Leave blank to keep current password">
            <label><i class="bi bi-key me-1"></i> New Password</label>
            <button
              class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted text-decoration-none shadow-none"
              type="button" id="toggleProfilePassword" style="z-index: 5;">
              <i class="bi bi-eye"></i>
            </button>
          </div>
          <small class="text-muted ms-2 mt-1 d-block"><i class="bi bi-info-circle me-1"></i> Leave blank to keep current
            password.</small>
        </div>

        <div class="glass-panel p-4 mb-5 position-relative" style="background: rgba(255,255,255,0.4);">
          <div id="verificationStatusBadge" class="position-absolute top-0 end-0 m-3 badge bg-secondary">Unverified</div>
          <h5 class="fw-bold mb-4 d-flex align-items-center"><i class="bi bi-person-badge text-info me-2"></i>
            Identity Verification</h5>
          <p class="text-muted small mb-3">Upload your driver's license to unlock premium bookings. Formats: JPG, PNG, PDF.</p>
          <div class="mb-3">
            <input class="form-control" type="file" id="licenseFile" accept="image/*,.pdf">
          </div>
          <button type="button" class="btn btn-outline-info btn-sm rounded-pill px-4" id="uploadLicenseBtn">Submit Document</button>
        </div>

        <button type="submit" class="btn btn-primary pulse-btn w-100 py-3 rounded-pill fw-bold text-uppercase"
          style="letter-spacing: 0.5px;">
          <i class="bi bi-check-circle me-1"></i> Save Changes
        </button>
      </form>
    </div>
  </div>

<?php 
echo "<script>document.addEventListener('DOMContentLoaded', () => { if(typeof profileInit === 'function') profileInit(); });</script>";
include 'includes/footer.php'; 
?>
