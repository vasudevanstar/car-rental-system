<?php
$pageTitle = "FastRide - Premium Car Rental";
include 'includes/header.php';
?>

  <!-- Hero Section -->
  <section class="hero-section text-center position-relative d-flex align-items-center justify-content-center"
    style="min-height: 90vh; padding-top: 5vh; padding-bottom: 5vh;">
    <div class="container hero-content z-3">
      <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8 fade-in-scroll">
          <span
            class="badge bg-primary bg-opacity-25 text-white mb-3 py-2 px-3 rounded-pill border border-white border-opacity-25"
            style="backdrop-filter: blur(5px); font-weight: 500; letter-spacing: 1px;"><i
              class="bi bi-star-fill text-warning me-1"></i> #1 Rated Car Rental Service</span>
          <h1 class="display-3 fw-bold mb-4 text-white" style="text-shadow: 0 4px 15px rgba(0,0,0,0.3); line-height: 1.1;">Drive Your
            Dream Car Today</h1>
          <p class="lead mb-5 text-white-50 px-md-5">Experience the thrill of the road with our premium fleet.
            Affordable rates, 24/7 support, and zero hidden fees. Your journey begins here.</p>

          <div class="hero-actions d-flex flex-wrap justify-content-center align-items-center gap-3 mb-5">
            <a href="vehicles.php"
              class="btn btn-primary pulse-btn btn-lg px-5 py-3 rounded-pill fw-bold text-uppercase shadow-lg"
              style="letter-spacing: 1px;">Browse Fleet <i class="bi bi-arrow-right ms-2"></i></a>

            <div
              class="currency-widget p-1 ps-3 pe-2 bg-dark bg-opacity-50 border border-white border-opacity-10 rounded-pill d-flex align-items-center shadow-sm"
              style="backdrop-filter: blur(10px);">
              <span class="small opacity-75 text-white me-2"><i class="bi bi-globe me-1"></i> Currency:</span>
              <select id="currencySelector"
                class="form-select form-select-sm bg-transparent border-0 text-white fw-bold shadow-none cursor-pointer"
                style="width: auto;">
                <option value="USD" class="text-dark">USD ($)</option>
                <option value="INR" class="text-dark">INR (₹)</option>
                <option value="EUR" class="text-dark">EUR (€)</option>
                <option value="GBP" class="text-dark">GBP (£)</option>
              </select>
              <div id="currencyRate"
                class="ms-2 small fw-bold text-accent py-1 px-2 rounded-pill bg-white bg-opacity-10"></div>
            </div>
          </div>

          <!-- Glassmorphism Search Bar -->
          <div class="glass-panel p-2 p-md-3 mx-auto rounded-pill border border-white border-opacity-25 shadow-lg"
            style="max-width: 850px; background: rgba(15, 23, 42, 0.4);">
            <form action="vehicles.php" method="GET" class="row g-2 align-items-center m-0">
              <div class="col-8 col-md-5">
                <div class="input-group">
                  <span class="input-group-text bg-transparent border-0 text-white-50 fs-5 ps-3"><i
                      class="bi bi-search"></i></span>
                  <input type="text" name="search"
                    class="form-control border-0 shadow-none bg-transparent text-white fs-5 px-0 custom-search"
                    placeholder="Search fleet..." style="color:white!important;">
                </div>
              </div>
              <div class="col-4 col-md-3 border-start border-white border-opacity-25 px-2">
                <select name="type"
                   class="form-select border-0 shadow-none bg-transparent text-white fs-5 cursor-pointer px-1">
                  <option value="" class="text-dark">All Types</option>
                  <option value="SUV" class="text-dark">SUV</option>
                  <option value="Sedan" class="text-dark">Sedan</option>
                  <option value="Luxury" class="text-dark">Luxury</option>
                </select>
              </div>
              <div class="col-12 col-md-4 ps-md-3">
                <button type="submit"
                  class="btn btn-primary w-100 py-3 rounded-pill fw-bold text-uppercase shadow-sm">Find Vehicle <i
                    class="bi bi-car-front ms-1"></i></button>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>

    <!-- Decorative geometric background elements -->
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden"
      style="z-index: 1; opacity: 0.15; pointer-events: none;">
      <div class="position-absolute rounded-circle bg-primary blur-glow"
        style="width: 50vw; height: 50vw; top: -10vw; left: -10vw; filter: blur(100px);"></div>
      <div class="position-absolute rounded-circle bg-info blur-glow"
        style="width: 40vw; height: 40vw; bottom: -10vw; right: -5vw; filter: blur(80px);"></div>
    </div>
  </section>

  <!-- How It Works Section -->
  <section class="py-5" style="background-color: var(--bg-main);">
    <div class="container py-5">
      <div class="text-center mb-5 fade-in-scroll">
        <span class="badge bg-primary bg-opacity-10 text-primary mb-2 px-3 py-2 rounded-pill">Process</span>
        <h2 class="fw-bold display-5 mb-3">How It Works</h2>
        <p class="text-muted mx-auto" style="max-width: 600px;">Getting behind the wheel has never been easier. Follow
          these three simple steps to start your journey.</p>
      </div>

      <div class="row g-4 text-center justify-content-center position-relative">
        <div class="col-md-4 fade-in-scroll" style="transition-delay: 0.1s;">
          <div class="glass-card h-100 p-5 position-relative overflow-hidden border-0">
            <div class="feature-icon-wrapper mb-4 mx-auto shadow-sm" style="width: 80px; height: 80px;">
              <i class="bi bi-geo-alt fs-1 text-primary m-0"></i>
            </div>
            <h4 class="fw-bold mb-3">1. Choose Location</h4>
            <p class="text-muted mb-0">Select your preferred pickup and drop-off branch from our widespread network.</p>
            <div class="position-absolute"
              style="font-size: 15rem; color: rgba(0,0,0,0.02); top: -40px; right: -20px; font-weight: 900; line-height: 1;">
              1</div>
          </div>
        </div>

        <div class="col-md-4 fade-in-scroll" style="transition-delay: 0.2s;">
          <div class="glass-card h-100 p-5 position-relative overflow-hidden border-0">
            <div class="feature-icon-wrapper mb-4 mx-auto shadow-sm"
              style="width: 80px; height: 80px; background: rgba(56, 189, 248, 0.1);">
              <i class="bi bi-car-front fs-1 text-info m-0"></i>
            </div>
            <h4 class="fw-bold mb-3">2. Pick Vehicle</h4>
            <p class="text-muted mb-0">Browse our extensive fleet of premium cars, SUVs, and luxury sedans.</p>
            <div class="position-absolute"
              style="font-size: 15rem; color: rgba(0,0,0,0.02); top: -40px; right: -20px; font-weight: 900; line-height: 1;">
              2</div>
          </div>
        </div>

        <div class="col-md-4 fade-in-scroll" style="transition-delay: 0.3s;">
          <div class="glass-card h-100 p-5 position-relative overflow-hidden border-0">
            <div class="feature-icon-wrapper mb-4 mx-auto shadow-sm"
              style="width: 80px; height: 80px; background: rgba(16, 185, 129, 0.1);">
              <i class="bi bi-calendar-check fs-1 text-success m-0"></i>
            </div>
            <h4 class="fw-bold mb-3">3. Book & Pay</h4>
            <p class="text-muted mb-0">Confirm your dates and pay securely online to instantly finalize your
              reservation.</p>
            <div class="position-absolute"
              style="font-size: 15rem; color: rgba(0,0,0,0.02); top: -40px; right: -20px; font-weight: 900; line-height: 1;">
              3</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- About Our Company Section -->
  <section class="py-5 bg-white position-relative" style="border-top: 1px solid rgba(0,0,0,0.05);">
    <div class="container py-5">
      <div class="row align-items-center g-5">
        <div class="col-lg-6 fade-in-scroll">
          <span
            class="badge bg-accent bg-opacity-10 text-accent mb-3 px-3 py-2 rounded-pill border border-accent border-opacity-25"
            style="color: var(--accent)!important;">About FastRide</span>
          <h2 class="display-5 fw-bold mb-4">Redefining the Premium Car Rental Experience</h2>
          <p class="lead text-muted mb-4">Founded in 2026, FastRide sets a new benchmark in mobility. We believe that
            renting a vehicle shouldn't be a tedious chore—it should be the seamless start to your next great adventure.
          </p>
          <p class="text-muted mb-4">Whether you need an ultra-luxury sedan for a corporate event or a rugged SUV for
            a family getaway, our meticulously maintained, state-of-the-art fleet is entirely at your disposal. With a
            fully digitized remote booking system and a strict no-hidden-fee policy, we put you directly in the driver's
            seat.</p>
          <div class="d-flex align-items-center mt-4">
            <div class="d-flex align-items-center me-4">
              <i class="bi bi-check-circle-fill fs-4 me-2" style="color: var(--accent);"></i>
              <span class="fw-bold">Certified Fleet</span>
            </div>
            <div class="d-flex align-items-center">
              <i class="bi bi-headset fs-4 me-2" style="color: var(--accent);"></i>
              <span class="fw-bold">24/7 Roadside</span>
            </div>
          </div>
        </div>
        <div class="col-lg-6 fade-in-scroll" style="transition-delay: 0.2s;">
          <div class="position-relative hover-shadow transition"
            style="border-radius: var(--radius-lg); overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.4);">
            <div class="position-absolute top-0 start-0 w-100 h-100"
              style="background: radial-gradient(circle at center, transparent 0%, rgba(15,23,42,0.4) 100%); z-index: 1;">
            </div>
            <img src="https://images.unsplash.com/photo-1560958089-b8a1929cea89?auto=format&fit=crop&q=80"
              alt="About FastRide Fleet" class="img-fluid w-100"
              style="object-fit: cover; height: 400px; transform: scale(1.05); transition: transform 0.5s;"
              onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1.05)'">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Stats Section -->
  <section class="py-5 position-relative overflow-hidden"
    style="background-color: rgba(0,0,0,0.02); border-top: 1px solid rgba(0,0,0,0.05); border-bottom: 1px solid rgba(0,0,0,0.05);">
    <div class="container py-4">
      <div class="row g-4 text-center">
        <div class="col-md-3 col-6 fade-in-scroll">
          <div class="p-3">
            <h2 class="display-4 fw-bold text-primary mb-0">10k+</h2>
            <p class="text-muted fw-bold text-uppercase mt-2" style="letter-spacing: 1px;">Customers</p>
          </div>
        </div>
        <div class="col-md-3 col-6 fade-in-scroll" style="transition-delay: 0.1s;">
          <div class="p-3">
            <h2 class="display-4 fw-bold text-accent mb-0">500+</h2>
            <p class="text-muted fw-bold text-uppercase mt-2" style="letter-spacing: 1px;">Premium Cars</p>
          </div>
        </div>
        <div class="col-md-3 col-6 fade-in-scroll" style="transition-delay: 0.2s;">
          <div class="p-3">
            <h2 class="display-4 fw-bold text-success mb-0">30+</h2>
            <p class="text-muted fw-bold text-uppercase mt-2" style="letter-spacing: 1px;">Locations</p>
          </div>
        </div>
        <div class="col-md-3 col-6 fade-in-scroll" style="transition-delay: 0.3s;">
          <div class="p-3">
            <h2 class="display-4 fw-bold text-info mb-0">4.9</h2>
            <p class="text-muted fw-bold text-uppercase mt-2" style="letter-spacing: 1px;">Average Rating</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="py-5 bg-white">
    <div class="container py-5">
      <div class="text-center mb-5 fade-in-scroll">
        <span class="badge bg-warning text-dark mb-2 px-3 py-2 rounded-pill"><i class="bi bi-star-fill"></i>
          Reviews</span>
        <h2 class="fw-bold display-5 mb-3">Trusted by Thousands</h2>
      </div>

      <div class="row g-4 fade-in-scroll">
        <div class="col-md-4">
          <div class="glass-card p-4 h-100 border-0 shadow-sm">
            <div class="text-warning mb-3 fs-5">
              <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
            </div>
            <p class="fst-italic text-muted mb-4">"The vehicle was in pristine condition. FastRide's customer service
              was incredibly helpful when I had to extend my rental by a day."</p>
            <div class="d-flex align-items-center">
              <div
                class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 fs-5"
                style="width: 50px; height: 50px; font-weight: bold;">JD</div>
              <div>
                <h6 class="fw-bold mb-0">John Davies</h6>
                <small class="text-muted">Rented a Luxury Sedan</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="glass-card p-4 h-100 border-0 shadow-sm">
            <div class="text-warning mb-3 fs-5">
              <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
            </div>
            <p class="fst-italic text-muted mb-4">"Transparent pricing, no hidden fees, and the pickup process took
              less than 5 minutes. Best car rental experience I've had so far."</p>
            <div class="d-flex align-items-center">
              <div
                class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3 fs-5"
                style="width: 50px; height: 50px; font-weight: bold;">SM</div>
              <div>
                <h6 class="fw-bold mb-0">Sarah Miller</h6>
                <small class="text-muted">Rented an SUV</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="glass-card p-4 h-100 border-0 shadow-sm">
            <div class="text-warning mb-3 fs-5">
              <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
            </div>
            <p class="fst-italic text-muted mb-4">"Great selection of vehicles! The interactive dashboard made it
              super easy to track my bookings and download invoices."</p>
            <div class="d-flex align-items-center">
              <div class="bg-info text-dark rounded-circle d-flex align-items-center justify-content-center me-3 fs-5"
                style="width: 50px; height: 50px; font-weight: bold;">AL</div>
              <div>
                <h6 class="fw-bold mb-0">Alex Lee</h6>
                <small class="text-muted">Rented a Sedan</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Company Promotion Banner -->
  <div id="globalPromoBanner" class="py-4 px-4 w-100 shadow-lg position-relative z-3"
    style="background: linear-gradient(90deg, rgba(15,23,42,1) 0%, rgba(99,102,241,1) 100%); border-top: 1px solid rgba(255,255,255,0.05); display: none;">
    <div class="container">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
        <div class="d-flex align-items-center mb-2 mb-md-0">
          <div class="bg-warning text-dark fw-bold px-3 py-1 rounded-pill me-3 shadow-sm text-uppercase"
            style="letter-spacing: 1px; font-size: 0.85rem;"><i class="bi bi-stars"></i> Special Offer</div>
          <h5 class="text-white mb-0 fw-bold" id="promoBannerTitle">Get <span class="text-warning">20% OFF</span> your first premium rental!
          </h5>
        </div>
        <div class="d-flex align-items-center gap-3 mt-3 mt-md-0">
          <span class="text-white-50 small fw-bold d-none d-sm-inline-block">Use Code: <span id="promoBannerCode"
              class="text-white border border-white border-opacity-25 px-2 py-1 rounded ms-1 bg-dark bg-opacity-50"
              style="letter-spacing: 2px;">FASTRIDE20</span></span>
          <a href="vehicles.php" class="btn btn-light fw-bold rounded-pill px-4"
            style="color: var(--accent)!important;">Claim Now</a>
        </div>
      </div>
      <div class="text-white-50 small mt-2 w-100 text-center text-md-start" id="promoBannerDesc" style="opacity: 0.8;"></div>
    </div>
  </div>


  <!-- Featured Fleet Section -->
  <section class="py-5 bg-light">
    <div class="container py-5">
      <div class="text-center mb-5 fade-in-scroll">
        <span class="badge bg-primary bg-opacity-10 text-primary mb-2 px-3 py-2 rounded-pill">Exclusive</span>
        <h2 class="fw-bold display-5 mb-3">Featured Fleet</h2>
        <p class="text-muted mx-auto" style="max-width: 600px;">Explore our most popular rides this week, curated for performance and luxury.</p>
      </div>
      <div class="row g-4 justify-content-center">
        <?php
        // PHP array for images as requested
        $featuredVehicles = [
          [
            'id' => 52,
            'brand' => 'Tesla',
            'name' => 'Model S',
            'price' => 150,
            'images' => [
              'https://images.unsplash.com/photo-1560958089-b8a1929cea89?auto=format&fit=crop&w=800&q=80',
              'https://images.unsplash.com/photo-1554744480-cd197503e232?auto=format&fit=crop&w=800&q=80',
              'https://images.unsplash.com/photo-1536700503339-1e4b06520771?auto=format&fit=crop&w=800&q=80'
            ]
          ],
          [
            'id' => 53,
            'brand' => 'Porsche',
            'name' => 'Cayenne',
            'price' => 200,
            'images' => [
              'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=800&q=80',
              'https://images.unsplash.com/photo-1580273916550-e323be2ae537?auto=format&fit=crop&w=800&q=80'
            ]
          ],
          [
            'id' => 78,
            'brand' => 'Mahindra',
            'name' => 'XUV700',
            'price' => 95,
            'images' => [
              'https://images.unsplash.com/photo-1619682817481-e994891cd1f5?auto=format&fit=crop&w=800&q=80',
              'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=800&q=80'
            ]
          ]
        ];

        foreach ($featuredVehicles as $v): ?>
          <div class="col-md-4 fade-in-scroll">
            <div class="premium-slider-card" data-vehicle-id="<?php echo $v['id']; ?>">
              <div class="card-slider-container">
                <button class="slider-btn btn-prev">❮</button>
                <button class="slider-btn btn-next">❯</button>
                <div class="card-slider-wrapper">
                  <?php foreach ($v['images'] as $img): ?>
                    <div class="card-slide">
                      <img src="<?php echo $img; ?>" alt="<?php echo $v['name']; ?>" loading="lazy">
                    </div>
                  <?php endforeach; ?>
                </div>
                <div class="slider-indicators">
                  <?php foreach ($v['images'] as $i => $img): ?>
                    <div class="indicator-dot <?php echo $i === 0 ? 'active' : ''; ?>"></div>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="p-4">
                <div class="small fw-bold text-accent text-uppercase mb-1"><?php echo htmlspecialchars($v['brand']); ?></div>
                <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($v['name']); ?></h4>
                <div class="d-flex justify-content-between align-items-center">
                  <span class="fs-4 fw-bold text-success">$<?php echo $v['price']; ?><small class="fs-6 text-muted">/day</small></span>
                  <a href="vehicle-details.php?id=<?php echo $v['id']; ?>" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">View Details</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <script>document.addEventListener('DOMContentLoaded', initCardSliders);</script>

<?php include 'includes/footer.php'; ?>
