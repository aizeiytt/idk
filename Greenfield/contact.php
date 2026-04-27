<?php
// the contact page handles the contact form submission and displays the contact page with the form and contact details.
// This file is meant to be included in a larger application that has session management and database connection already set up.
// The form submits to the same page (contact.php) using POST method. We validate the input, send an email to the site admin, and display success or error messages accordingly.

// Start the session to access session variables for user authentication and cart management.
session_start();

// Initialize variables for success and error messages that will be displayed to the user after form submission.
$successMessage = "";
$errorMessage = "";

// Check if the form was submitted via POST method. If so, we process the form data.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8')) : '';
    $phone = isset($_POST['phone']) ? trim(htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8')) : '';
    $email = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8')) : '';
    $message = isset($_POST['message']) ? trim(htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8')) : '';

    // Validate Full Name - must be at least 5 characters, contain only letters, spaces, hyphens, and apostrophes, and include at least a first and last name.
    if (empty($name)) {
        $errorMessage = "Full name is required.";
    } elseif (strlen($name) < 5) {
        $errorMessage = "Full name must be at least 5 characters long (e.g., John Doe).";
    } elseif (!preg_match('/^[a-zA-Z\s\'-]+$/', $name)) {
        $errorMessage = "Full name can only contain letters, spaces, hyphens, and apostrophes.";
    } elseif (substr_count($name, ' ') < 1) {
        $errorMessage = "Please enter your full name (first and last name).";
    } 
    // Validate Phone - must contain at least 10 digits and can include spaces, hyphens, parentheses, and +, but no letters.
    elseif (empty($phone)) {
        $errorMessage = "Phone number is required.";
    } elseif (!preg_match('/^[\d\s\-\+\(\)]{10,}$/', $phone)) {
        $errorMessage = "Phone number must contain at least 10 digits (numbers only, spaces, hyphens, parentheses, or + are allowed).";
    } elseif (preg_match('/[a-zA-Z]/', $phone)) {
        $errorMessage = "Phone number cannot contain letters.";
    }
    //  Validate Email - must be a valid email format.
    elseif (empty($email)) {
        $errorMessage = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please enter a valid email address.";
    }
    // Validate Message - must be at least 10 characters long.
    elseif (empty($message)) {
        $errorMessage = "Message is required.";
    } elseif (strlen($message) < 10) {
        $errorMessage = "Message must be at least 10 characters long.";
    } 
    //  If all validations pass, we proceed to send the email to the site admin with the form details.
    else {
        $to = 'info@greenfieldhub.com';
        $subject = 'New Contact Form Submission from ' . $name;
        
        $emailBody = "Name: $name\n";
        $emailBody .= "Phone: $phone\n";
        $emailBody .= "Email: $email\n";
        $emailBody .= "Message:\n$message\n";
        
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        if (mail($to, $subject, $emailBody, $headers)) {
            $successMessage = "Thank you! Your message has been sent successfully.";
        } else {
            $errorMessage = "Error sending message. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact</title>
  <link rel="stylesheet" href="contact.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
 
</head>
<body>

  <?php
// Current file's basename, used to mark the active nav item.
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
      <li><a href="contact.html"<?php if ($__currentPage==='contact.php') echo ' class="active"'; ?>>Contact</a></li>
      <li>
        <a href="checkout.php"<?php if ($__currentPage==='checkout.php') echo ' class="active"'; ?> data-testid="nav-basket">
          <i class="fas fa-shopping-basket" aria-hidden="true"></i> Basket<?php if ($__cartCount > 0): ?><span style="display:inline-block;min-width:18px;height:18px;padding:0 5px;margin-left:4px;background:#f4b400;color:#0a3b2c;border-radius:9px;font-size:11px;font-weight:700;text-align:center;line-height:18px;" data-testid="cart-count"><?php echo $__cartCount; ?></span><?php endif; ?>
        </a>
      </li>
    </ul>
  </nav>
  <div class="header-auth">
    <!-- check to see if the user is logged in -->
    <?php if (!empty($_SESSION['user_logged_in'])): ?>
      <span class="user-greeting-nav" data-testid="nav-user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
      <a href="dashboard.php?action=logout" data-testid="nav-logout">Log out</a>
    <?php else: ?>
      <!-- else, show login and register links -->
      <a href="login.php"<?php if ($__currentPage==='login.php') echo ' class="active"'; ?>>Log in</a>
      <a href="register.php"<?php if ($__currentPage==='register.php') echo ' class="active"'; ?>>Sign up</a>
    <?php endif; ?>
  </div>
</header>

  <main id="main" role="main">
    <section class="contact-section">
      <h1>Let's Get In Touch</h1>
      <div class="contact-details">
        <div class="contact-item">
          <i class="fas fa-phone-alt" aria-hidden="true"></i>
          <p>+123 45 789 000</p>
        </div>
        <div class="contact-item">
          <i class="fas fa-envelope" aria-hidden="true"></i>
          <p>info@greenfieldhub.com</p>
        </div>
        <div class="contact-item">
          <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
          <p>Location</p>
        </div>
      </div>

      <hr>

      <h2>Or fill out the form below</h2>
      
     <!-- <?php
      // Display success or error messages after form submission
      if (isset($_GET['status'])) {
        if ($_GET['status'] == 'success') {
          echo '<div class="alert alert-success">Thank you! Your message has been sent successfully.</div>';
        } elseif ($_GET['status'] == 'error') {
          echo '<div class="alert alert-error">Sorry, there was an error sending your message. Please try again.</div>';
        }
      }
      ?>-->

        <form method="POST" class="contact-form">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" placeholder="Enter your full name..." required>
        </div>
        <div class="form-group">
          <label for="phone">Phone</label>
          <input type="tel" id="phone" name="phone" placeholder="Enter your phone number..." required>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="Enter your email..." required>
        </div>
        <div class="form-group">
          <label for="message">Inquiry Purpose</label>
          <textarea id="message" name="message" rows="5" placeholder="Enter your message here..." required></textarea>
        </div>
        <button type="submit" class="btn">Submit</button>
      </form>
    </section>
  </main>
</html>

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