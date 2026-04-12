<?php
// includes/footer.php
?>
  <!-- Hidden Invoice Template for Image Generation -->
  <div id="invoice-template" style="position: absolute; left: -9999px; top: 0; width: 800px; background: white; font-family: 'Inter', sans-serif; padding: 40px; color: #333; line-height: 1.6;">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 4px solid #3f66f1; padding-bottom: 20px; margin-bottom: 30px;">
        <div>
            <h1 style="margin: 0; color: #3f66f1; font-size: 32px; font-weight: 800; letter-spacing: -1px;">FASTRIDE</h1>
            <p style="margin: 0; font-size: 10px; color: #666; text-transform: uppercase; font-weight: 600;">Premium Car Rental Services</p>
        </div>
        <div style="text-align: right;">
            <h2 style="margin: 0; font-size: 24px; font-weight: 700;">INVOICE</h2>
            <p style="margin: 0; font-size: 14px; color: #888;">#<span id="tmpl-id">000</span></p>
        </div>
    </div>
    
    <div style="display: flex; justify-content: space-between; margin-bottom: 40px;">
        <div>
            <p style="margin: 0 0 5px; font-size: 12px; font-weight: 800; color: #999; text-transform: uppercase;">Bill To:</p>
            <p id="tmpl-email" style="margin: 0; font-weight: 700; font-size: 16px;">customer@example.com</p>
            <p style="margin: 0; font-size: 14px; color: #666;">Verified FastRide Customer</p>
        </div>
        <div style="text-align: right;">
            <p style="margin: 0 0 5px; font-size: 12px; font-weight: 800; color: #999; text-transform: uppercase;">Date Issued:</p>
            <p id="tmpl-date" style="margin: 0; font-weight: 700; font-size: 16px;">Apr 12, 2026</p>
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
        <thead>
            <tr style="background: #f8f9fa;">
                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #eee; font-size: 12px; color: #666;">DESCRIPTION</th>
                <th style="padding: 15px; text-align: right; border-bottom: 2px solid #eee; font-size: 12px; color: #666;">AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 20px 15px; border-bottom: 1px solid #eee;">
                    <p style="margin: 0 0 5px; font-weight: 700; font-size: 16px;" id="tmpl-vehicle">Porshe Cayenne</p>
                    <p style="margin: 0; font-size: 13px; color: #888;" id="tmpl-period">2026-04-12 to 2026-04-14</p>
                </td>
                <td style="padding: 20px 15px; border-bottom: 1px solid #eee; text-align: right; font-weight: 700; font-size: 18px;" id="tmpl-amount">$0.00</td>
            </tr>
        </tbody>
    </table>

    <div style="display: flex; justify-content: flex-end;">
        <div style="width: 300px; background: #3f66f1; color: white; padding: 25px; border-radius: 10px; text-align: right;">
            <p style="margin: 0 0 5px; font-size: 12px; font-weight: 600; opacity: 0.8; text-transform: uppercase;">Grand Total</p>
            <p id="tmpl-total" style="margin: 0; font-size: 32px; font-weight: 800;">$0.00</p>
        </div>
    </div>

    <div style="margin-top: 50px; border-top: 1px solid #eee; padding-top: 20px; text-align: center;">
        <p style="margin: 0; font-size: 13px; color: #888;">Thank you for your business! This is an official digital receipt from FastRide.</p>
        <p style="margin: 5px 0 0; font-size: 11px; color: #bbb;">FastRide Rental Inc. | Global Premium Mobility Solutions</p>
    </div>
  </div>

  <!-- Footer -->
  <?php if (basename($_SERVER['PHP_SELF']) !== 'admin.php'): ?>
  <footer class="mt-0 pt-5" style="margin-top: 0;">
    <div class="container">
      <div class="row">
        <div class="col-md-4 mb-4">
          <h4 class="footer-heading"><i class="bi bi-car-front-fill" style="color: var(--accent-color);"></i> FastRide</h4>
          <p class="footer-text">Premium car rental services delivering comfort, style, and reliability for all your transportation needs.</p>
        </div>
        <div class="col-md-4 mb-4">
          <h4 class="footer-heading">Quick Links</h4>
          <ul class="list-unstyled footer-text">
            <li class="mb-2"><a href="vehicles.php" class="text-decoration-none text-white opacity-75 opacity-100-hover transition">Browse Vehicles</a></li>
            <li class="mb-2"><a href="login.php" class="text-decoration-none text-white opacity-75 opacity-100-hover transition">Login</a></li>
            <li class="mb-2"><a href="register.php" class="text-decoration-none text-white opacity-75 opacity-100-hover transition">Register</a></li>
          </ul>
        </div>
        <div class="col-md-4 mb-4">
          <h4 class="footer-heading">Contact Us</h4>
          <div class="footer-text">
            <p><i class="bi bi-envelope fill me-2"></i> support@fastride.com</p>
            <p><i class="bi bi-telephone-fill me-2"></i> +1 234 567 8900</p>
          </div>
          <div class="social-icons mt-3">
            <a href="#"><i class="bi bi-facebook"></i></a>
            <a href="#"><i class="bi bi-twitter"></i></a>
            <a href="#"><i class="bi bi-instagram"></i></a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2026 FastRide Car Rental. All rights reserved.</p>
      </div>
    </div>
  </footer>
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="frontend/invoice.js"></script>
  <script src="frontend/script.js"></script>
</body>
</html>
