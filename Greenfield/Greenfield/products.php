<?php
// products.php - Main product listing page for Greenfield Local Hub
// This page displays all available products with options to filter, sort, and add to cart. It also shows a toast notification when a product is added to the cart.


// Requires session_start() and database connection from db.php. The page also includes a canonical header and footer for consistent navigation and branding across the site.
session_start();
require_once __DIR__ . '/includes/db.php';

// Calculate total items in cart for display in header
$cartCount = 0;
if (!empty($_SESSION['cart'])) foreach ($_SESSION['cart'] as $q) $cartCount += (int)$q;

// pdo query to get all products for display
$products = getPdo()->query("SELECT id, name, farm, price, description, icon FROM products ORDER BY id ASC")->fetchAll();
$totalProducts = count($products);

// Check if a product was just added to the cart to show the toast notification
$addedId = isset($_GET['added']) ? (int)$_GET['added'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products - Greenfield Local Hub</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="products.css">
  <style>
    .cart-badge{display:inline-block;min-width:18px;height:18px;padding:0 5px;margin-left:4px;background:#f4b400;color:#0a3b2c;border-radius:9px;font-size:11px;font-weight:700;text-align:center;line-height:18px}
    .toast-added{position:fixed;top:90px;right:24px;background:#1b5e20;color:#fff;padding:12px 18px;border-radius:8px;box-shadow:0 6px 20px rgba(0,0,0,.18);z-index:999;font-family:'Open Sans',sans-serif;animation:slideIn .3s ease}
    @keyframes slideIn{from{opacity:0;transform:translateX(40px)}to{opacity:1;transform:translateX(0)}}
    .add-cart-form{display:inline}
    .btn-add-cart{cursor:pointer;border:0}
  </style>
  
</head>
<body>

<?php
// Determine the current page for active link highlighting in the header and calculate the total items in the cart for display in the header. 
// The header includes navigation links to all main sections of the site, and shows the user's name and logout option if they are logged in
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

<!-- Toast Notification for Added Products -->
<?php if ($addedId > 0): ?>
  <div class="toast-added" data-testid="added-toast" id="addedToast"><i class="fas fa-check-circle" aria-hidden="true"></i> Added to basket!</div>
  <script>setTimeout(()=>{const t=document.getElementById('addedToast');if(t)t.style.display='none';},2500);</script>
<?php endif; ?>

<main id="main" role="main">
  <section class="products-hero">
    <div class="products-hero-container">
      <h1>Fresh Organic Products</h1>
      <p>Browse our selection of fresh, locally-grown organic produce from our partner farms</p>
    </div>
  </section>

  <section class="products-main">
    <div class="products-container">
      <aside class="products-sidebar">
        <div class="sidebar-section">
          <h3 class="sidebar-title"><i class="fas fa-filter" aria-hidden="true"></i> Categories</h3>
          <div class="filter-option"><input type="checkbox" id="vegetables" checked><label for="vegetables">Vegetables</label></div>
          <div class="filter-option"><input type="checkbox" id="fruits"><label for="fruits">Fruits</label></div>
        </div>
        <div class="sidebar-section">
          <h3 class="sidebar-title"><i class="fas fa-dollar-sign" aria-hidden="true"></i> Price Range</h3>
          <div class="price-range"><input type="number" placeholder="Min" min="0" value="0"><span>-</span><input type="number" placeholder="Max" min="0" value="100"></div>
        </div>
        <div class="sidebar-section">
          <h3 class="sidebar-title"><i class="fas fa-check-circle" aria-hidden="true"></i> Availability</h3>
          <div class="filter-option"><input type="checkbox" id="instock" checked><label for="instock">In Stock</label></div>
        </div>
        <button class="btn-reset-filters"><i class="fas fa-redo" aria-hidden="true"></i> Reset Filters</button>
      </aside>

      <div class="products-content">
        <div class="products-header">
          <div class="header-left">
            <h2>All Products</h2>
            <p class="product-count">Showing <?php echo $totalProducts; ?> products</p>
          </div>
          <div class="sort-controls">
            <label for="sort">Sort by:</label>
            <select id="sort" class="sort-select">
              <option value="featured">Featured</option>
              <option value="price-low">Price: Low to High</option>
              <option value="price-high">Price: High to Low</option>
            </select>
          </div>
        </div>

        <div class="products-grid" data-testid="products-grid">
          <!-- Product cards will be dynamically inserted here -->
          <?php foreach ($products as $p): ?>
          <div class="product-card" data-testid="product-card-<?php echo (int)$p['id']; ?>">
            <div class="product-image"><?php echo htmlspecialchars($p['icon'] ?: '🥗'); ?></div>
            <div class="product-info">
              <p class="product-farm"><?php echo htmlspecialchars($p['farm']); ?></p>
              <h3 class="product-name"><?php echo htmlspecialchars($p['name']); ?></h3>
              <p class="product-desc"><?php echo htmlspecialchars($p['description']); ?></p>
              <div class="product-rating">
                <i class="fas fa-star" aria-hidden="true"></i><i class="fas fa-star" aria-hidden="true"></i><i class="fas fa-star" aria-hidden="true"></i><i class="fas fa-star" aria-hidden="true"></i><i class="fas fa-star-half-alt" aria-hidden="true"></i>
              </div>
              <div class="product-footer">
                <div class="product-price"><span class="price-new">$<?php echo number_format($p['price'], 2); ?></span></div>
                <form class="add-cart-form" action="cart.php" method="POST">
                  <input type="hidden" name="action" value="add">
                  <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                  <input type="hidden" name="redirect" value="products.php?added=<?php echo (int)$p['id']; ?>">
                  <button type="submit" class="btn-add-cart" data-testid="add-to-cart-<?php echo (int)$p['id']; ?>">
                    <i class="fas fa-shopping-cart" aria-hidden="true"></i> Add
                  </button>
                </form>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
</main>

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

<script src="products.js"></script>
</body>
</html>