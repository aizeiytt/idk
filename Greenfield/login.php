<?php
// the login script starts by initiating a session to manage user authentication state across pages.
// It then includes a shared database helper file that provides a getPdo() function for database interactions.
// The script defines two variables, $errorMessage and $successMessage, to hold any messages that need to be displayed to the user based on the login process outcome.



// The script checks if the request method is POST, which indicates that the login form has been submitted.
// It then reads and trims the email input, and reads the password input without trimming (as passwords may contain leading/trailing spaces).
session_start();

// Pull in the shared DB helper that exposes getPdo().
require_once __DIR__ . '/includes/db.php';

// Error / success strings rendered inline by the template further below.
$errorMessage   = '';
$successMessage = '';

// Only run the auth flow if the form was actually submitted.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Read + trim the email field (defaults to '' if the key is missing).
    $email    = isset($_POST['login-email']) ? trim($_POST['login-email']) : '';
    // Read the password field as-is (never trim passwords).
    $password = isset($_POST['login-password']) ? $_POST['login-password'] : '';

    // Basic client-independent validation — never trust the browser.
    if ($email === '') {
        // Empty email.
        $errorMessage = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Malformed email.
        $errorMessage = 'Please enter a valid email address.';
    } elseif ($password === '') {
        // Empty password.
        $errorMessage = 'Password is required.';
    } elseif (strlen($password) < 6) {
        // Too short (matches the minlength on the input).
        $errorMessage = 'Password must be at least 6 characters long.';
    } else {
        // Look the user up by email — prepared statement = SQL-injection safe.
        $stmt = getPdo()->prepare(
            'SELECT id, full_name, email, password FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Confirm the row exists AND the bcrypt hash matches the plaintext.
        if ($user && password_verify($password, $user['password'])) {
            // Rotate the session id to block session-fixation attacks.
            session_regenerate_id(true);

            // Populate the session with the authenticated user's data.
            $_SESSION['user_id']        = (int)$user['id'];
            $_SESSION['user_email']     = $user['email'];
            $_SESSION['user_name']      = $user['full_name'];
            $_SESSION['user_logged_in'] = true;
            $_SESSION['login_time']     = date('Y-m-d H:i:s');

            // Success → redirect to the dashboard and stop execution.
            header('Location: dashboard.php');
            exit();
        }

        // Either no row was found or the hash didn't verify.
        $errorMessage = 'Invalid email or password. Please try again.';
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log In — Greenfield Local Hub</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="login-gf.css">
</head>
<body>


<header class="gf-header" role="banner">
  <a href="index.html" class="gf-home-link">
    <div class="gf-header-left">
      <img src="images.jpg" alt="Greenfield Local Hub leaf logo" class="gf-logo">
      <span class="gf-brand-name">Greenfield Local Hub</span>
    </div>
  </a>
  <nav class="gf-nav" aria-label="Primary">
    <ul class="gf-nav-links">
      <li><a href="index.html">Home</a></li>
      <li><a href="about.html">About us</a></li>
      <li><a href="products.php">Products</a></li>
      <li><a href="loyalty.html">Loyalty</a></li>
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="checkout.php" data-testid="nav-basket" aria-label="Basket"><i class="fas fa-shopping-basket" aria-hidden="true"></i> Basket</a></li>
    </ul>
  </nav>
  <div class="gf-auth-box">
    <a class="gf-active" href="login.php" aria-current="page">Log in</a>
    <a href="register.php">Sign up</a>
  </div>
</header>

<main id="main-content" class="gf-login-bg" role="main">
  <div class="gf-login-center-box">
    <h1 class="gf-login-welcome">Welcome</h1>
    <p class="gf-login-title">Log In</p>

    <?php if ($errorMessage !== ''): ?>
      <!-- Inline error message (rendered only when validation fails) -->
      <p class="gf-error-message show" data-testid="login-error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <!-- Login form — posts back to this same file -->
    <form method="POST" action="login.php" data-testid="login-form" novalidate>

      <label for="login-email">Email</label>
      <input id="login-email" name="login-email" type="email" autocomplete="email"
             placeholder="you@example.com" required data-testid="login-email-input"
             aria-describedby="email-help">
      <small id="email-help" class="gf-field-help">We'll never share your email.</small>

      <label for="login-password">Password</label>
      <input id="login-password" name="login-password" type="password" autocomplete="current-password"
             placeholder="Your password" required minlength="6" data-testid="login-password-input">


      <button type="submit" class="gf-login-btn" data-testid="login-submit-btn">Log in</button>
    </form>

    <p class="gf-login-hint">Demo: <strong>demo@greenfield.com</strong> / <strong>Password123</strong></p>
    <p class="gf-login-register">Don't have an account? <a href="register.php" class="gf-signuplink">Sign Up</a></p>
  </div>
</main>

<footer class="gf-footer" role="contentinfo">
  <div class="gf-footer-main">

    <div class="gf-footer-logo-col">
      <img src="images.jpg" alt="Greenfield Local Hub leaf logo" class="gf-footer-logo">
      <div class="gf-footer-info">
        <span class="gf-footer-brand">Greenfield Local Hub</span>
        <p class="gf-footer-desc">Empowering sustainable farming communities<br>through innovative and customer-focused digital solutions.</p>

        <div class="gf-footer-social">
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
          <a href="#" aria-label="Twitter"><i class="fab fa-twitter" aria-hidden="true"></i></a>
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
        </div>
      </div>
    </div>


    <div class="gf-footer-col gf-footer-mid-col">
      <div class="gf-contact-title">Get In Touch</div>
      <div class="gf-contact-row"><i class="fas fa-envelope" aria-hidden="true"></i><a href="mailto:info@greenfieldhub.com" class="gf-footer-link">info@greenfieldhub.com</a></div>
      <div class="gf-contact-row"><i class="fas fa-phone" aria-hidden="true"></i><span class="gf-footer-link">+123 45 789 000</span></div>
      <div class="gf-contact-row"><i class="fas fa-location-dot" aria-hidden="true"></i><span class="gf-footer-link">Greenfield, Springfield</span></div>
    </div>


    <div class="gf-footer-links">
      <div class="gf-footer-col">
        <h4>Shop</h4>
        <ul>
          <li><a href="index.html">Home</a></li>
          <li><a href="about.html">About us</a></li>
          <li><a href="products.php">Products</a></li>
          <li><a href="loyalty.html">Loyalty</a></li>
        </ul>
      </div>
      <div class="gf-footer-col">
        <h4>Account</h4>
        <ul>
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="checkout.php">Basket</a></li>
          <li><a href="checkout.php">Checkout</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>
      </div>
      <div class="gf-footer-col">
        <h4>Operations</h4>
        <ul>
          <li><a href="Delivery.php">Delivery</a></li>
          <li><a href="stock.php">Stock</a></li>
          <li><a href="manage.html">Management</a></li>
          <li><a href="Psdashboard.php">Producers Dashboard</a></li>
        </ul>
      </div>
      <div class="gf-footer-col">
        <h4>Legal Auth</h4>
        <ul>
          <li><a href="login.php">Log in</a></li>
          <li><a href="register.php">Sign up</a></li>
          <li><a href="legal.html">Legal</a></li>
          <li><a href="legal.html#privacy">Privacy Policy</a></li>
          <li><a href="legal.html#terms">Terms of Service</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="gf-footer-bar">© 2026 Greenfield Local Hub, All Rights Reserved.</div>
</footer>

</body>
</html>