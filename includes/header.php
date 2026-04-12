<?php
// includes/header.php
require_once __DIR__ . '/../config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo isset($pageTitle) ? $pageTitle . " - FastRide" : "FastRide - Premium Car Rental"; ?></title>
  <link rel="icon" href="frontend/logo for car rental system _ company name is FastRide in  web logo in letter.jpg">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="frontend/style.css" />
</head>
<body class="<?php echo isset($bodyClass) ? $bodyClass : ''; ?>">

  <!-- Navigation -->
  <?php if (basename($_SERVER['PHP_SELF'] ?? '') !== 'admin.php'): ?>
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <i class="bi bi-car-front-fill text-accent" style="color: var(--accent-color);"></i> FastRide
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-center">
            <!-- Navbar will be populated by script.js but we can add fallback or server-side links here if needed -->
        </ul>
      </div>
    </div>
  </nav>
  <?php endif; ?>
