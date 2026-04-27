<?php
// register.php - User registration page with input validation, password hashing, and PDO database interaction.
// This page allows new users to create an account by providing their full name, email address, password, phone number, and gender. 
// It includes comprehensive input validation to ensure data integrity and security.


// start the session and include the database connection
session_start();
require_once __DIR__ . '/includes/db.php';

// Initialize variables for error and success messages
$errorMessage   = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName        = isset($_POST['full-name'])        ? trim(htmlspecialchars($_POST['full-name'], ENT_QUOTES, 'UTF-8')) : '';
    $email           = isset($_POST['email'])            ? trim(htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'))      : '';
    $password        = isset($_POST['password'])         ? $_POST['password']                                                 : '';
    $passwordConfirm = isset($_POST['password-confirm']) ? $_POST['password-confirm']                                         : '';
    $gender          = isset($_POST['gender'])           ? htmlspecialchars($_POST['gender'], ENT_QUOTES, 'UTF-8')            : '';
    $phone           = isset($_POST['phone-number'])     ? trim(htmlspecialchars($_POST['phone-number'], ENT_QUOTES, 'UTF-8')): '';
    $newsletter      = isset($_POST['newsletter']) ? 1 : 0;

    // validation checks with detailed error messages for each case
    if (empty($fullName)) {
        $errorMessage = 'Full name is required.';
    } elseif (strlen($fullName) < 5) {
        $errorMessage = 'Full name must be at least 5 characters long (e.g., John Doe).';
    } elseif (!preg_match('/^[a-zA-Z\s\'-]+$/', $fullName)) {
        $errorMessage = 'Full name can only contain letters, spaces, hyphens, and apostrophes.';
    } elseif (substr_count($fullName, ' ') < 1) {
        $errorMessage = 'Please enter your full name (first and last name).';
    } elseif (empty($email)) {
        $errorMessage = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } elseif (empty($password)) {
        $errorMessage = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errorMessage = 'Password must be at least 8 characters long.';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errorMessage = 'Password must contain at least one uppercase letter.';
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errorMessage = 'Password must contain at least one lowercase letter.';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errorMessage = 'Password must contain at least one number.';
    } elseif ($password !== $passwordConfirm) {
        $errorMessage = 'Passwords do not match.';
    } elseif (empty($phone)) {
        $errorMessage = 'Phone number is required.';
    } elseif (!preg_match('/^[\d\s\-\+\(\)]{10,}$/', $phone)) {
        $errorMessage = 'Phone number must contain at least 10 digits.';
    } elseif (preg_match('/[a-zA-Z]/', $phone)) {
        $errorMessage = 'Phone number cannot contain letters.';
    } elseif (empty($gender)) {
        $errorMessage = 'Please select a gender.';
    } else {
        // a pdo check to see if the email is already registered
        $pdo  = getPdo();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);

        // If a record is found, the email is already registered. Otherwise, create the account.
        if ($stmt->fetch()) {
            $errorMessage = 'This email is already registered. Please log in or use a different email.';
        } else {
            // create the account with hashed password and prepared statement to prevent SQL injection
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                'INSERT INTO users (full_name, email, password, phone, gender, newsletter)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            // execute the statement and handle any potential database errors gracefully
            try {
                $stmt->execute([$fullName, $email, $hashedPassword, $phone, $gender, $newsletter]);
                $successMessage = 'Account created successfully! Redirecting to login in 2 seconds...';
                header('refresh:2;url=login.php');
                // Note: In a production environment, you would typically redirect immediately after setting a success message in the session, rather than using a meta refresh. 
                // This is done here for demonstration purposes to allow the user to see the success message before being redirected.
            } catch (PDOException $e) {
                $errorMessage = 'Error creating account. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="register.css">
</head>
<body>

  <?php
// Determine the current page for active link highlighting and calculate cart item count for the basket icon in the navigation bar.
// This code is included in the header section of the page to ensure that the correct navigation link is highlighted based on the current page, and to display the number of items in the cart if there are any.
$__currentPage = basename($_SERVER['PHP_SELF']);
$__cartCount = 0;
if (!empty($_SESSION['cart'])) foreach ($_SESSION['cart'] as $__q) $__cartCount += (int)$__q;
?>
<header class="header-navbar" role="banner">
  <a href="index.html" class="header-home-link">
    <div class="header-left">
      <img src="images.jpg" alt="Greenfield Local Hub Logo" class="header-logo">
      <span class="header-brand">Greenfield Local Hub</span>
    </div>
  </a>
  <nav aria-label="Primary">
    <ul class="header-links">
      <li><a href="index.html">Home</a></li>
      <li><a href="about.html">About us</a></li>
      <li><a href="products.php"<?php if ($__currentPage==='products.php') echo ' class="active"'; ?>>Products</a></li>
      <li><a href="loyalty.html">Loyalty</a></li>
      <li><a href="dashboard.php"<?php if ($__currentPage==='dashboard.php') echo ' class="active"'; ?>>Dashboard</a></li>
      <li><a href="contact.php"<?php if ($__currentPage==='contact.php') echo ' class="active"'; ?>>Contact</a></li>
      <li>
        <a href="checkout.php"<?php if ($__currentPage==='checkout.php') echo ' class="active"'; ?> data-testid="nav-basket">
          <i class="fas fa-shopping-basket" aria-hidden="true"></i> Basket<?php if ($__cartCount > 0): ?><span style="display:inline-block;min-width:18px;height:18px;padding:0 5px;margin-left:4px;background:#f4b400;color:#0a3b2c;border-radius:9px;font-size:11px;font-weight:700;text-align:center;line-height:18px;" data-testid="cart-count"><?php echo $__cartCount; ?></span><?php endif; ?>
        </a>
      </li>
    </ul>
  </nav>
  <div class="header-auth">
    <?php if (!empty($_SESSION['user_logged_in'])): ?>
      <span class="user-greeting-nav" data-testid="nav-user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
      <a href="dashboard.php?action=logout" data-testid="nav-logout">Log out</a>
    <?php else: ?>
      <a href="login.php"<?php if ($__currentPage==='login.php') echo ' class="active"'; ?>>Log in</a>
      <a href="register.php"<?php if ($__currentPage==='register.php') echo ' class="active"'; ?>>Sign up</a>
    <?php endif; ?>
  </div>
</header>

  <div class="container">
    <h1>Sign Up</h1>
    <p>Please enter your details to create an account.</p>
    <?php if (!empty($errorMessage)): ?>
      <div style="background:#fdecea;color:#b71c1c;padding:10px 14px;border-radius:6px;margin-bottom:14px;" data-testid="register-error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>
    <?php if (!empty($successMessage)): ?>
      <div style="background:#e8f5e9;color:#1b5e20;padding:10px 14px;border-radius:6px;margin-bottom:14px;" data-testid="register-success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>
    <form action="register.php" method="POST" data-testid="register-form">
      <div class="form-group">
        <label for="full-name">Full Name</label>
        <input type="text" id="full-name" name="full-name" placeholder="Enter your full name" required>
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email address" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Min 8 chars, 1 upper, 1 lower, 1 number" required>
      </div>
      <div class="form-group">
        <label for="password-confirm">Confirm Password</label>
        <input type="password" id="password-confirm" name="password-confirm" placeholder="Re-enter your password" required>
      </div>
      <div class="form-group">
        <label for="gender">Gender</label>
        <select id="gender" name="gender" required>
          <option value="" disabled selected>Select a gender</option>
          <option value="male">Male</option>
          <option value="female">Female</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="form-group">
        <label for="phone-number">Phone Number</label>
        <input type="tel" id="phone-number" name="phone-number" placeholder="Enter your phone number" required>
      </div>
      <div class="form-group checkbox-container">
        <input type="checkbox" id="newsletter" name="newsletter">
        <label for="newsletter">Please keep me updated by email with the latest news, research findings, reward programs, event updates.</label>
      </div>
      <button type="submit" class="btn" data-testid="register-submit-btn">Create an account</button>
    </form>
    <p>Already have an account? <a href="login.php">Sign in</a></p>
  </div>

  <footer class="footer" role="contentinfo">
  <div class="footer-main">
    <div class="logo-col">
      <img src="images.jpg" alt="Greenfield Local Hub leaf logo" class="footer-logo">
      <div class="footer-info">
        <span class="footer-brand">Greenfield Local Hub</span>
        <p class="footer-desc">Empowering sustainable farming communities<br>through innovative and customer-focused digital solutions.</p>
        <div class="footer-social">
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
          <a href="#" aria-label="Twitter"><i class="fab fa-twitter" aria-hidden="true"></i></a>
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
        </div>
      </div>
    </div>
    <div class="footer-col mid-col">
      <div class="footer-mid-title">Get In Touch</div>
      <div class="footer-contact-row"><i class="fas fa-envelope" aria-hidden="true"></i><a href="mailto:info@greenfieldhub.com" class="footer-link">info@greenfieldhub.com</a></div>
      <div class="footer-contact-row"><i class="fas fa-phone" aria-hidden="true"></i><span class="footer-link">+123 45 789 000</span></div>
      <div class="footer-contact-row"><i class="fas fa-location-dot" aria-hidden="true"></i><span class="footer-link">Greenfield, Springfield</span></div>
    </div>
    <div class="footer-links-group">
      <div class="footer-col">
        <h4>Shop</h4>
        <ul>
          <li><a href="index.html">Home</a></li>
          <li><a href="about.html">About us</a></li>
          <li><a href="products.php">Products</a></li>
          <li><a href="loyalty.html">Loyalty</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Account</h4>
        <ul>
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="checkout.php">Basket</a></li>
          <li><a href="checkout.php">Checkout</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Operations</h4>
        <ul>
          <li><a href="Delivery.php">Delivery</a></li>
          <li><a href="stock.php">Stock</a></li>
          <li><a href="manage.html">Management</a></li>
          <li><a href="Psdashboard.php">Producers Dashboard</a></li>
        </ul>
      </div>
      <div class="footer-col">
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
  <div class="footer-bar">© 2026 Greenfield Local Hub, All Rights Reserved.</div>
</footer>
</body>
</html>