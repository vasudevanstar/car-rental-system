<?php
require_once 'config/db.php';
// Redirect if already logged in
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reset Password - FastRide</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* --- ANIMATIONS --- */
    @keyframes slideInLeft { 0% { transform: translateX(-100%); opacity: 0; } 100% { transform: translateX(0); opacity: 1; } }
    @keyframes slideUpCard { 0% { transform: translateY(60px); opacity: 0; } 100% { transform: translateY(0); opacity: 1; } }
    @keyframes carEnter { 0% { transform: translateX(-80px) scale(0.9); opacity: 0; } 100% { transform: translateX(0) scale(1); opacity: 1; } }
    @keyframes carFloat { 0% { transform: translateY(0); } 50% { transform: translateY(-12px); } 100% { transform: translateY(0); } }
    @keyframes pulseShadow { 0% { box-shadow: 0 0 0 0 rgba(255, 188, 56, 0.5); } 70% { box-shadow: 0 0 0 12px rgba(255, 188, 56, 0); } 100% { box-shadow: 0 0 0 0 rgba(255, 188, 56, 0); } }
    @keyframes fadeInText { 0% { opacity: 0; transform: translateY(10px); } 100% { opacity: 1; transform: translateY(0); } }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; display: flex; align-items: center; justify-content: center; min-height: 100vh; overflow: hidden; }

    .custom-login-container { position: relative; width: 100%; height: 100vh; display: flex; align-items: center; justify-content: center; }
    .green-bg-shape { position: absolute; top: 0; left: 0; width: 35%; height: 100%; background: linear-gradient(180deg, #95d4a1 0%, #17a085 100%); z-index: 1; opacity: 0; transform: translateX(-100%); animation: slideInLeft 0.9s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

    .login-card-wrapper { position: relative; z-index: 2; display: flex; align-items: center; width: 850px; max-width: 95%; }

    .car-image-container { position: absolute; left: -120px; z-index: 3; width: 500px; pointer-events: none; opacity: 0; animation: carEnter 1s cubic-bezier(0.2, 0.8, 0.2, 1) 0.4s forwards; }
    .car-image-container img { width: 100%; height: auto; filter: drop-shadow(0 20px 25px rgba(0, 0, 0, 0.2)); animation: carFloat 4s ease-in-out infinite; animation-delay: 1.4s; }

    .login-box { width: 100%; background: #ffffff; border-radius: 12px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08); display: flex; justify-content: flex-end; padding: 50px 40px; position: relative; min-height: 450px; opacity: 0; animation: slideUpCard 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) 0.2s forwards; }

    .close-btn { position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 24px; font-weight: bold; color: #000; cursor: pointer; transition: transform 0.3s ease, color 0.3s; }
    .close-btn:hover { transform: rotate(90deg) scale(1.2); color: #fca311; }

    .form-container { width: 50%; padding-left: 20px; display: flex; flex-direction: column; justify-content: center; }

    .form-title { font-family: 'Poppins', sans-serif; font-size: 24px; font-weight: 700; color: #111; margin-bottom: 8px; opacity: 0; animation: fadeInText 0.6s ease 0.6s forwards; }
    .form-subtitle { font-size: 11px; color: #888; margin-bottom: 25px; line-height: 1.5; opacity: 0; animation: fadeInText 0.6s ease 0.7s forwards; }

    #resetPasswordForm { opacity: 0; animation: fadeInText 0.6s ease 0.8s forwards; }

    .input-group { margin-bottom: 15px; width: 100%; }
    .input-field { width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 4px; font-size: 13px; color: #333; font-family: inherit; outline: none; transition: all 0.3s ease; background: #fafafa; }
    .input-field:focus { border-color: #ffbc38; background: #fff; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); }

    .submit-btn { width: 100%; background: #ffc13b; color: white; border: none; border-radius: 4px; padding: 12px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 5px; transition: all 0.3s ease; box-shadow: 0 4px 10px rgba(255, 188, 56, 0.2); }
    .submit-btn:hover { background: #f0ae27; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(255, 188, 56, 0.4); animation: pulseShadow 1.5s infinite; }

    .form-footer { text-align: center; margin-top: 35px; font-size: 12px; color: #666; }
    .form-footer a { color: #19977b; text-decoration: none; font-weight: 600; margin-left: 5px; }

    @media (max-width: 768px) {
      .green-bg-shape, .car-image-container { display: none; }
      .login-box { justify-content: center; padding: 40px 20px; }
      .form-container { width: 100%; padding-left: 0; }
    }
  </style>
</head>

<body>
  <div class="custom-login-container">
    <div class="green-bg-shape"></div>
    <div class="login-card-wrapper">
      <div class="car-image-container">
        <img src="frontend/car-model.png" alt="FastRide SUV">
      </div>
      <div class="login-box">
        <button class="close-btn" onclick="window.location.href='login.php'">&times;</button>
        <div class="form-container">
          <h2 class="form-title">Reset Password</h2>
          <p class="form-subtitle">Enter your new password below.</p>

          <form id="resetPasswordForm">
            <div id="message" style="width: 100%; text-align: center; color: red;"></div>
            <div class="input-group">
              <input type="password" id="newPassword" class="input-field" placeholder="New Password (min 6 chars)" required minlength="6">
            </div>
            <div class="input-group">
              <input type="password" id="confirmPassword" class="input-field" placeholder="Confirm Password" required minlength="6">
            </div>
            <button type="submit" class="submit-btn" id="resetBtn">Reset Password</button>

            <div class="form-footer">
              Remembered your password? <a href="login.php">Login</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="frontend/script.js"></script>
  <script>document.addEventListener('DOMContentLoaded', resetPasswordInit);</script>
</body>

</html>
