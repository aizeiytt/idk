<?php
// the code starts a session and includes the database connection file. It checks if the user is logged in, and if not, it redirects them to the login page.
// It retrieves the order ID from the URL and fetches the order details, including the items in the order and the delivery tracking history. 
// It also maps the order status to display information and calculates the estimated delivery time. Finally, it prepares mock driver information for display on the page.

session_start();
require_once __DIR__ . '/includes/db.php';


// Check if user is logged in, if not redirect to login page
if (empty($_SESSION['user_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Get order ID from URL
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$orderId) {
    header("Location: dashboard.php");
    exit();
}

// pdo queries to fetch order details, items, and tracking history from the database.
$stmt = getPdo()->prepare("
    SELECT o.id, o.order_date, o.total_amount, o.status, o.delivery_date, 
           o.delivery_method, o.shipping_address, u.full_name, u.phone
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ?
");

// the code executes the prepared statement with the order ID and fetches the order details. If the order is not found, it terminates the script with an error message.
$stmt->execute([$orderId]);
$order = $stmt->fetch();
if (!$order) {
    die("Order not found");
}

// Fetch order items with product details
$stmt = getPdo()->prepare("
    SELECT oi.quantity, p.name, p.price
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll();
// Fetch delivery tracking history for the order, ordered by timestamp in ascending order. This will be used to display the delivery timeline on the page.
$stmt = getPdo()->prepare("
    SELECT status, timestamp FROM delivery_tracking
    WHERE order_id = ?
    ORDER BY timestamp ASC
");
$stmt->execute([$orderId]);
$tracking = $stmt->fetchAll();
// the code defines an associative array that maps order statuses to their corresponding display labels and icons. 
// It uses the current order status to determine which label and icon to display on the delivery tracking page.
$statusInfo = [
    'pending' => ['label' => 'Order Confirmed', 'icon' => 'fas fa-check-circle'],
    'processing' => ['label' => 'Order Processing', 'icon' => 'fas fa-cogs'],
    'shipped' => ['label' => 'Out for Delivery', 'icon' => 'fas fa-truck'],
    'delivered' => ['label' => 'Delivered', 'icon' => 'fas fa-box-open'],
    'cancelled' => ['label' => 'Cancelled', 'icon' => 'fas fa-times-circle']
];

$currentStatus = $order['status'];
$statusInfo = $statusInfo[$currentStatus] ?? $statusInfo['pending'];

// the code calculates the estimated delivery time based on the delivery date of the order. 
// It checks if the delivery date is today and formats the estimated delivery time accordingly. If the delivery date is not today, it formats it as a standard date string.
$deliveryDate = new DateTime($order['delivery_date']);
$today = new DateTime();
$isToday = $deliveryDate->format('Y-m-d') === $today->format('Y-m-d');
$estDelivery = $isToday ? 'Today by 6:00 PM' : $deliveryDate->format('F d, Y');

// the code prepares mock driver information for display on the delivery tracking page. In a real application, this information would likely be fetched from the database based on the assigned driver for the order.
$driver = [
    'name' => 'John Smith',
    'phone' => '+1 (555) 123-4567',
    'vehicle' => 'Green Van #GH-2024'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Track Delivery - Greenfield Local Hub</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="delivery.css">
  
</head>
<body>


  <?php
// the code determines the current page by extracting the filename from the server's PHP_SELF variable. 
// It also calculates the total number of items in the user's cart by summing the quantities of items stored in the session variable 'cart'. 
// This information is used to display the active state of navigation links and to show the cart item count in the header.
$__currentPage = basename($_SERVER['PHP_SELF']);
$__cartCount = 0;

// the code checks if the 'cart' session variable is not empty and iterates through its contents to calculate the total quantity of items in the cart.
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

  <main id="main" role="main">
    
    <section class="delivery-header">
      <div class="delivery-container">
        <h1>Track Your Delivery</h1>
        <p>Order #GH-<?php echo str_pad($order['id'], 7, '0', STR_PAD_LEFT); ?></p>
      </div>
    </section>

    
    <section class="delivery-tracking">
      <div class="delivery-container">
        <div class="tracking-card">
          <!-- Order Info -->
          <div class="order-info">
            <div class="order-detail">
              <span class="label">Order ID</span>
              <span class="value">#GH-<?php echo str_pad($order['id'], 7, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="order-detail">
              <span class="label">Status</span>
              <span class="value status-<?php echo $currentStatus; ?>">
                <i class="<?php echo $statusInfo['icon']; ?>"></i> <?php echo htmlspecialchars($statusInfo['label']); ?>
              </span>
            </div>
            <div class="order-detail">
              <span class="label">Estimated Arrival</span>
              <span class="value"><?php echo $estDelivery; ?></span>
            </div>
          </div>

          
          <div class="timeline-track">
            <?php
            // the code defines an array of timeline steps representing the different stages of the delivery process. 
            // It then iterates through these steps and determines whether each step is completed, active, or pending based on the current order status.
            $timelineSteps = ['pending', 'processing', 'shipped', 'delivered'];
            $currentStepIndex = array_search($currentStatus, $timelineSteps);

            // the code generates the HTML for each step in the delivery timeline. 
            // It assigns classes to indicate whether a step is completed or active, and it displays the corresponding label and timestamp for each step based on the tracking history.
            foreach ($timelineSteps as $index => $step):
              $isCompleted = $index < $currentStepIndex || ($index === $currentStepIndex && $currentStatus !== 'pending');
              $isActive = $index === $currentStepIndex;
              $stepLabel = ucfirst(str_replace('_', ' ', $step));
              $stepTime = '';

              // the code iterates through the tracking history to find the timestamp for the current step and formats it for display. 
              // If the step has not been reached yet, it will show "Pending".
              foreach ($tracking as $t) {
                if ($t['status'] === $step) {
                  $stepTime = date('M d, g:i A', strtotime($t['timestamp']));
                  break;
                }
              }

              // the code maps the step identifiers to user-friendly labels for display on the timeline.
              if ($step === 'pending') $stepLabel = 'Order Confirmed';
              if ($step === 'processing') $stepLabel = 'Order Processing';
              if ($step === 'shipped') $stepLabel = 'Out for Delivery';
              if ($step === 'delivered') $stepLabel = 'Delivered';
            ?>
            <div class="timeline-step <?php echo $isCompleted ? 'completed' : ''; ?> <?php echo $isActive ? 'active' : ''; ?>">
              <div class="timeline-dot"></div>
              <div class="timeline-text">
                <h4><?php echo $stepLabel; ?></h4>
                <p><?php echo $stepTime ?: 'Pending'; ?></p>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

          
          <div class="delivery-map">
            <i class="fas fa-map" aria-hidden="true"></i>
            <p>Live tracking map</p>
          </div>

          
          <div class="delivery-details">
            <div class="detail-box">
              <h4>Delivery Address</h4>
              <p><?php echo htmlspecialchars($order['shipping_address']); ?></p>
            </div>

            <?php if ($currentStatus === 'shipped'): ?>
            <div class="detail-box">
              <h4>Driver Information</h4>
              <p><?php echo htmlspecialchars($driver['name']); ?><br>
              <i class="fas fa-phone" aria-hidden="true"></i> <?php echo htmlspecialchars($driver['phone']); ?><br>
              <small><?php echo htmlspecialchars($driver['vehicle']); ?></small></p>
            </div>

            <div class="detail-box">
              <h4>Contact Driver</h4>
              <button class="btn-contact" onclick="callDriver()">
                <i class="fas fa-phone" aria-hidden="true"></i> Call Driver
              </button>
              <button class="btn-contact message" onclick="messageDriver()">
                <i class="fas fa-comment" aria-hidden="true"></i> Message
              </button>
            </div>
            <?php endif; ?>
          </div>

         
          <div class="items-summary">
            <h3>Items in This Order</h3>
            <div class="items-list">
              <?php foreach ($items as $item): ?>
              <div class="item-row">
                <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                <span class="item-qty">Qty: <?php echo $item['quantity']; ?></span>
                <span class="item-price">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
              </div>
              <?php endforeach; ?>
            </div>
            <div class="items-total">
              <strong>Total: $<?php echo number_format($order['total_amount'], 2); ?></strong>
            </div>
          </div>
        </div>
      </div>
    </section>

  
    
    <section class="delivery-faq">
      <div class="delivery-container">
        <h2>Delivery FAQs</h2>
        <div class="faq-grid">
          <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
              <h4>How long does delivery take?</h4>
              <i class="fas fa-chevron-down" aria-hidden="true"></i>
            </div>
            <div class="faq-answer">
              <p>Standard delivery takes 24-48 hours. Express delivery is available within 24 hours.</p>
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
              <h4>Can I reschedule delivery?</h4>
              <i class="fas fa-chevron-down" aria-hidden="true"></i>
            </div>
            <div class="faq-answer">
              <p>Yes, reschedule up to 2 hours before the scheduled time through your dashboard.</p>
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
              <h4>What if I'm not home?</h4>
              <i class="fas fa-chevron-down" aria-hidden="true"></i>
            </div>
            <div class="faq-answer">
              <p>Drivers attempt redelivery. Provide delivery instructions in your order preferences.</p>
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
              <h4>Is delivery available weekends?</h4>
              <i class="fas fa-chevron-down" aria-hidden="true"></i>
            </div>
            <div class="faq-answer">
              <p>Yes, we deliver 7 days a week including weekends and holidays.</p>
            </div>
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

  <script src="delivery.js"></script>
</body>
</html>