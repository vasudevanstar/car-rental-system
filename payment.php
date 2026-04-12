<?php
$pageTitle = "Payment - FastRide";
include 'includes/header.php';
// Protected page
if (!isset($_SESSION['user'])) {
    header('Location: login.php?redirect=' . urlencode('payment.php?' . $_SERVER['QUERY_STRING']));
    exit;
}
?>

  <div class="container fade-in-scroll visible" style="margin-top: 100px; margin-bottom: 50px;">
    <div class="form-section shadow-lg p-5 position-relative" style="max-width: 600px; margin: auto;">

      <div class="text-center mb-5">
        <div class="feature-icon-wrapper mb-3 mx-auto shadow-sm" style="width: 70px; height: 70px;">
          <i class="bi bi-shield-lock fs-2 m-0" style="color: var(--success);"></i>
        </div>
        <h3 class="fw-bold mb-1">Secure Checkout</h3>
        <p class="text-muted">Complete your transaction to finalize booking.</p>
      </div>

      <div id="message" class="alert-container position-relative top-0 w-100 mb-3"></div>

      <!-- Stripe-like Credit Card Mockup CSS-only -->
      <div class="mx-auto mb-5 glass-card position-relative overflow-hidden"
        style="max-width: 360px; height: 200px; padding: 25px; border-radius: 15px; background: linear-gradient(135deg, rgba(15,23,42,0.95), rgba(30,41,59,0.9)); color: white; box-shadow: 0 15px 35px rgba(0,0,0,0.2);">
        <div class="position-absolute"
          style="top: -50px; right: -50px; width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.05);">
        </div>
        <div class="position-absolute"
          style="bottom: -50px; left: -50px; width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,0.05);">
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
          <i class="bi bi-sim" style="font-size: 2rem; color: #fbbf24; transform: rotate(90deg);"></i>
          <i class="bi bi-wallet2 text-white-50 fs-3"></i>
        </div>
        <div class="mb-3">
          <div class="fs-4 fw-bold" style="letter-spacing: 2px;">•••• •••• •••• 4242</div>
        </div>
        <div class="d-flex justify-content-between">
          <div>
            <div style="font-size: 0.6rem; text-transform: uppercase; color: #94a3b8;">Card Holder</div>
            <div class="fw-bold text-uppercase" style="font-size: 0.85rem;">PREMIUM RENTER</div>
          </div>
          <div>
            <div style="font-size: 0.6rem; text-transform: uppercase; color: #94a3b8;">Expires</div>
            <div class="fw-bold" style="font-size: 0.85rem;">12/28</div>
          </div>
        </div>
      </div>

      <form id="paymentForm">
        <div class="glass-panel p-4 mb-4" style="background: rgba(255,255,255,0.4);">
          <div class="form-floating mb-3">
            <input type="number" id="rentalId" class="form-control" style="background-color: var(--bg-main);" required
              readonly>
            <label><i class="bi bi-receipt me-1 text-muted"></i> Rental ID</label>
          </div>

          <div class="form-floating mb-3">
            <input type="number" id="amount" class="form-control fw-bold text-success fs-4"
              style="background-color: var(--bg-main);" required readonly step="0.01">
            <label><i class="bi bi-currency-dollar me-1 text-success"></i> Total Amount Due</label>
          </div>

          <div class="form-floating">
            <select id="method" class="form-select" required>
              <option value="Credit Card" selected>💳 Credit Card</option>
              <option value="Debit Card">💳 Debit Card</option>
              <option value="Cash">💵 Cash</option>
            </select>
            <label><i class="bi bi-credit-card me-1 text-primary"></i> Payment Method</label>
          </div>
        </div>

        <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-bold text-uppercase pulse-btn"
          style="letter-spacing: 1px;">
          <i class="bi bi-lock-fill me-1"></i> Pay Securely
        </button>
      </form>
    </div>
  </div>

<?php 
echo "<script>document.addEventListener('DOMContentLoaded', paymentInit);</script>";
include 'includes/footer.php'; 
?>
