<?php
// the dashboard.php file is the main user dashboard page for the Greenfield Local Hub website. It starts by initializing a session and including the database connection file.
// It checks if the user is authenticated, and if not, it redirects them to the login page. If the user is logged in, it retrieves their user ID and name from the session.
// It then performs several database queries to fetch the user's profile information, loyalty points, active/upcoming orders, order history, and ratings.
// The dashboard displays a welcome message, quick stats about the user's orders and loyalty points, and a sidebar for navigating between different sections of the dashboard (Overview, Orders, Profile, Rewards, Settings, Rate Us).



// Initialise session and include database connection
session_start();
require_once __DIR__ . '/includes/db.php';


//  Check if user is authenticated
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}



// Fetch user data from session
$userId = (int)$_SESSION['user_id'];
$userName = htmlspecialchars($_SESSION['user_name']);

// Calculate cart item count for header display
$cartCount = 0;
if (!empty($_SESSION['cart'])) foreach ($_SESSION['cart'] as $q) $cartCount += (int)$q;

// this section retrieves the user's profile information from the database, including their full name, email
$stmt = getPdo()->prepare("SELECT full_name, email, phone, gender, created_at FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userData = $stmt->fetch();
// this section retrieves the user's loyalty points from the database 
$stmt = getPdo()->prepare("SELECT points, redeemed_points FROM loyalty_points WHERE user_id = ?");
$stmt->execute([$userId]);
$loyaltyData = $stmt->fetch();
$loyaltyPoints = $loyaltyData['points'] ?? 0;
$redeemedPoints = $loyaltyData['redeemed_points'] ?? 0;

// this section fetches active/upcoming orders (limit 1)
$stmt = getPdo()->prepare("
    SELECT o.id, o.order_date, o.total_amount, o.status, o.delivery_date
    FROM orders o
    WHERE o.user_id = ? AND o.status IN ('pending', 'processing', 'shipped')
    ORDER BY o.order_date DESC
    LIMIT 1
");
$stmt->execute([$userId]);
$upcomingOrder = $stmt->fetch();
// this section fetches the total order count
$stmt = getPdo()->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ?");
$stmt->execute([$userId]);
$countResult = $stmt->fetch();
$totalOrders = $countResult['total'] ?? 0;
// this section fetches the total amount spent

$stmt = getPdo()->prepare("SELECT COALESCE(SUM(total_amount), 0) as total_spent FROM orders WHERE user_id = ? AND status = 'completed'");
$stmt->execute([$userId]);
$spentResult = $stmt->fetch();
$totalSpent = $spentResult['total_spent'] ?? 0;
// this section determines the user's member status based on their loyalty points
$memberStatus = $loyaltyPoints >= 500 ? 'Gold' : ($loyaltyPoints >= 200 ? 'Silver' : 'Bronze');

// this section fetches the 5 most recent orders for the "Recent Orders" section on the overview tab
$stmt = getPdo()->prepare("SELECT stars, comment, created_at FROM ratings WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$userId]);
$myRating = $stmt->fetch();
$agg = getPdo()->query("SELECT COUNT(*) AS cnt, ROUND(AVG(stars), 2) AS avg_r FROM ratings")->fetch();
$ratingCount = (int)($agg['cnt'] ?? 0);
$ratingAvg   = (float)($agg['avg_r'] ?? 0);

// this section handles user logout. If the "action" parameter in the URL is set to "logout", it destroys the user's session and redirects them to the homepage (index.html).
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.html");
    exit();
}
// 1. Prepare the SQL statement (Secure way)
$stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = :id");

// 2. Execute with the actual ID
$stmt->execute(['id' => 1]);

// 3. Fetch the result
$user = $stmt->fetch();

echo "Welcome back, " . htmlspecialchars($user['full_name']);
?> 

// this formats the user's registration date to display how long they have been a member of the Greenfield Local Hub. If the "created_at" field is available in the user data, 
// it formats it as "Month Day, Year". If not, it defaults to "N/A".
$memberSince = !empty($userData['created_at']) ? date('F d, Y', strtotime($userData['created_at'])) : 'N/A';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Greenfield Local Hub</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="dashboard.css">
  
</head>
<body>

  <?php
// this section calculates the current page name and the number of items in the user's cart. 
// The current page name is used to highlight the active link in the navigation menu, while the cart count is displayed as a badge next to the "Basket" link in the header.
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

  <main id="main" class="dashboard-main" role="main">
    <?php if (isset($_GET['order_placed'])): ?>
    <div style="max-width:1200px;margin:16px auto 0;padding:12px 18px;background:#e8f5e9;color:#1b5e20;border:1px solid #c8e6c9;border-radius:8px;font-family:'Open Sans',sans-serif;" data-testid="order-placed-banner">
      <i class="fas fa-check-circle" aria-hidden="true"></i> Order #ORD-<?php echo str_pad((int)$_GET['order_placed'], 7, '0', STR_PAD_LEFT); ?> placed successfully! You'll see it in your orders shortly.
    </div>
    <?php endif; ?>
    
    <section class="dashboard-header">
      <div class="header-content">
        <div class="user-greeting">
          <h1>Welcome back, <span class="user-name"><?php echo htmlspecialchars($userData['full_name'] ?? 'User'); ?></span></h1>
          <p>Manage your account, orders, and preferences in one place</p>
        </div>
        <div class="header-actions">
          <a href="#profile" class="action-link" onclick="switchTab('profile'); return false;">
            <i class="fas fa-user-circle" aria-hidden="true"></i> Profile
          </a>
          <a href="#settings" class="action-link" onclick="switchTab('settings'); return false;">
            <i class="fas fa-cog" aria-hidden="true"></i> Settings
          </a>
          <a href="?action=logout" class="action-link">
            <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Logout
          </a>
        </div>
      </div>
    </section>

    
    <section class="quick-stats">
      <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-box" aria-hidden="true"></i></div>
        <div class="stat-content">
          <p class="stat-label">Total Orders</p>
          <p class="stat-value"><?php echo $totalOrders; ?></p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-star" aria-hidden="true"></i></div>
        <div class="stat-content">
          <p class="stat-label">Loyalty Points</p>
          <p class="stat-value"><?php echo number_format($loyaltyPoints); ?></p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-dollar-sign" aria-hidden="true"></i></div>
        <div class="stat-content">
          <p class="stat-label">Total Spent</p>
          <p class="stat-value">$<?php echo number_format($totalSpent, 2); ?></p>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-trophy" aria-hidden="true"></i></div>
        <div class="stat-content">
          <p class="stat-label">Member Status</p>
          <p class="stat-value"><?php echo $memberStatus; ?></p>
        </div>
      </div>
    </section>

    
    <div class="dashboard-container">
      <!-- the sidebar that contains the navigation menu -->
      <aside class="dashboard-sidebar">
        <nav class="sidebar-menu">
          <a href="#" class="menu-item active" onclick="switchTab('overview'); return false;">
            <i class="fas fa-th-large" aria-hidden="true"></i> Overview
          </a>
          <a href="#" class="menu-item" onclick="switchTab('orders'); return false;">
            <i class="fas fa-shopping-bag" aria-hidden="true"></i> Orders
          </a>
          <a href="#" class="menu-item" onclick="switchTab('profile'); return false;">
            <i class="fas fa-user" aria-hidden="true"></i> Profile
          </a>
          <a href="#" class="menu-item" onclick="switchTab('rewards'); return false;">
            <i class="fas fa-gift" aria-hidden="true"></i> Rewards
          </a>
          <a href="#" class="menu-item" onclick="switchTab('settings'); return false;">
            <i class="fas fa-sliders-h" aria-hidden="true"></i> Settings
          </a>
          <a href="#rate" class="menu-item" onclick="switchTab('rate'); return false;" data-testid="sidebar-rate">
            <i class="fas fa-star" aria-hidden="true"></i> Rate Us
          </a>
        </nav>
      </aside>

      
      <div class="dashboard-content">
        
        
        <div id="overview" class="tab-content active">
          <div class="content-header">
            <h2>Overview</h2>
          </div>

          <?php if ($upcomingOrder): ?>
          <div class="section-card">
            <h3><i class="fas fa-truck" aria-hidden="true"></i> Your Next Delivery</h3>
            <div class="active-order">
              <div class="order-status">
                <div class="status-badge in-transit">
                  <?php echo htmlspecialchars(ucfirst($upcomingOrder['status'])); ?>
                </div>
                <p class="delivery-date">Estimated Delivery: <strong><?php echo date('F d, Y', strtotime($upcomingOrder['delivery_date'])); ?></strong></p>
              </div>
              <div class="order-items">
                <p><strong>Order #ORD-<?php echo str_pad($upcomingOrder['id'], 7, '0', STR_PAD_LEFT); ?></strong></p>
                <p>📅 Placed: <?php echo date('F d, Y', strtotime($upcomingOrder['order_date'])); ?></p>
                <p>💰 Total: $<?php echo number_format($upcomingOrder['total_amount'], 2); ?></p>
              </div>
              <div class="order-actions">
                <a href="Delivery.php?id=<?php echo $upcomingOrder['id']; ?>" class="btn-secondary" data-testid="track-delivery-btn"><i class="fas fa-map-marker-alt" aria-hidden="true"></i> Track Delivery</a>
              </div>
            </div>
          </div>
          <?php else: ?>
          <div class="section-card">
            <p style="text-align: center; color: var(--text-light);">No active deliveries at the moment. <a href="products.php">Continue shopping</a></p>
          </div>
          <?php endif; ?>

          
          <div class="section-card">
            <h3><i class="fas fa-history" aria-hidden="true"></i> Recent Orders</h3>
            <div class="recent-orders-list">
              <?php
              // this section fetches the 5 most recent orders for the "Recent Orders" section on the overview tab. 
              // It prepares a SQL statement to select the order ID, order date, total amount, and status of the orders for the logged-in user, ordered by date in descending order and limited to 5 results.
              $stmt = getPdo()->prepare("
                SELECT id, order_date, total_amount, status
                FROM orders
                WHERE user_id = ?
                ORDER BY order_date DESC
                LIMIT 5
              ");
              // it executes the prepared statement with the user ID as a parameter. 
              // If there are any recent orders, it loops through them and displays each order's details (order ID, date, total amount, and status) in a styled format.
              // If there are no recent orders, it shows a message indicating that there are no orders yet.
              $stmt->execute([$userId]);

              // this section checks if there are any recent orders for the user. If there are, it loops through each order and displays its details in a styled format.
              if ($recent->rowCount() > 0):
                while ($order = $recent->fetch()):
              ?>
              <div class="order-item">
                <div class="order-header">
                  <span class="order-id">#ORD-<?php echo str_pad($order['id'], 7, '0', STR_PAD_LEFT); ?></span>
                  <span class="order-date"><?php echo date('F d, Y', strtotime($order['order_date'])); ?></span>
                  <span class="order-badge completed">✓ <?php echo htmlspecialchars(ucfirst($order['status'])); ?></span>
                </div>
                <p class="order-summary">Total: $<?php echo number_format($order['total_amount'], 2); ?></p>
              </div>
              <?php 
                endwhile;
              else:
                echo '<p style="text-align: center; color: var(--text-light);">No orders yet.</p>';
              endif;
              ?>
            </div>
            <a href="#" class="btn-primary" onclick="switchTab('orders'); return false;">View All Orders</a>
          </div>
        </div>

       
        <div id="orders" class="tab-content">
          <div class="content-header">
            <h2>Order History</h2>
          </div>

          <div class="orders-table">
            <table>
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Date</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // this section retrieves all orders for the logged-in user to display in the "Order History" tab. 
                // It prepares a SQL statement to select the order ID, order date, total amount, and status of all orders for the user, ordered by date in descending order.
                $stmt = getPdo()->prepare("
                  SELECT id, order_date, total_amount, status
                  FROM orders
                  WHERE user_id = ?
                  ORDER BY order_date DESC
                ");
                // it executes the prepared statement with the user ID as a parameter.
                $stmt->execute([$userId]);

                // this section checks if there are any orders for the user. If there are, it loops through each order and displays its details (order ID, date, total amount, status) in a table format.
                if ($allOrders->rowCount() > 0):
                  while ($order = $allOrders->fetch()):
                ?>
                <tr>
                  <td>#ORD-<?php echo str_pad($order['id'], 7, '0', STR_PAD_LEFT); ?></td>
                  <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                  <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                  <td><span class="badge completed"><?php echo htmlspecialchars(ucfirst($order['status'])); ?></span></td>
                  <td><button class="btn-small" onclick="alert('Order details for #<?php echo $order['id']; ?>')">Details</button></td>
                </tr>
                <?php 
                  endwhile;
                else:
                  echo '<tr><td colspan="5" style="text-align: center; padding: 20px;">No orders found.</td></tr>';
                endif;
                ?>
              </tbody>
            </table>
          </div>
        </div>

        
        <div id="profile" class="tab-content">
          <div class="content-header">
            <h2>Personal Information</h2>
          </div>

          <div class="profile-form">
            <div class="form-group">
              <label>Full Name</label>
              <input type="text" value="<?php echo htmlspecialchars($userData['full_name'] ?? ''); ?>" readonly>
            </div>

            <div class="form-group">
              <label>Email Address</label>
              <input type="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" readonly>
            </div>

            <div class="form-group">
              <label>Phone Number</label>
              <input type="tel" value="<?php echo htmlspecialchars($userData['phone'] ?? 'N/A'); ?>" readonly>
            </div>

            <div class="form-group">
              <label>Gender</label>
              <input type="text" value="<?php echo htmlspecialchars(ucfirst($userData['gender'] ?? 'N/A')); ?>" readonly>
            </div>

            <div class="form-group">
              <label>Member Since</label>
              <input type="text" value="<?php echo $memberSince; ?>" readonly>
            </div>
          </div>
        </div>

        
        <div id="rewards" class="tab-content">
          <div class="content-header">
            <h2>Loyalty & Rewards</h2>
          </div>

          <div class="rewards-section">
            <div class="points-card">
              <h3>Current Points Balance</h3>
              <div class="points-display">
                <p class="points-number"><?php echo number_format($loyaltyPoints); ?></p>
                <p class="points-label">points available</p>
              </div>
              <div class="points-progress">
                <div class="progress-bar" style="width: <?php echo min(($loyaltyPoints / 500) * 100, 100); ?>%;"></div>
              </div>
              <p class="points-info"><?php echo $memberStatus; ?> Member</p>
            </div>

            <div class="rewards-offers">
              <h3>Available Rewards</h3>
              <div class="rewards-grid">
                <div class="reward-option">
                  <p class="reward-cost">100 pts</p>
                  <p class="reward-desc">$5 Discount</p>
                  <button class="btn-small" onclick="redeemReward(100, '$5 Discount')">Redeem</button>
                </div>
                <div class="reward-option">
                  <p class="reward-cost">250 pts</p>
                  <p class="reward-desc">Free Item</p>
                  <button class="btn-small" onclick="redeemReward(250, 'Free Item')">Redeem</button>
                </div>
                <div class="reward-option">
                  <p class="reward-cost">500 pts</p>
                  <p class="reward-desc">$25 Voucher</p>
                  <button class="btn-small" onclick="redeemReward(500, '$25 Voucher')">Redeem</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        
        <div id="settings" class="tab-content">
          <div class="content-header">
            <h2>Account Settings</h2>
          </div>

          <div class="settings-section">
            <h3>Communication Preferences</h3>
            <div class="setting-item">
              <label>
                <input type="checkbox" checked>
                <span>Receive promotional emails</span>
              </label>
            </div>
            <div class="setting-item">
              <label>
                <input type="checkbox" checked>
                <span>Order status updates</span>
              </label>
            </div>
          </div>

          <div class="settings-section">
            <h3>Security</h3>
            <button class="btn-secondary" onclick="alert('Redirect to change password page')">Change Password</button>
          </div>

          <div class="settings-section danger-zone">
            <h3>Danger Zone</h3>
            <button class="btn-danger" onclick="if(confirm('Are you sure? This cannot be undone.')) { window.location.href='delete_account.php'; }">Delete Account</button>
          </div>
        </div>

        
        <div id="rate" class="tab-content">
          <div class="content-header">
            <h2>Rate Greenfield Local Hub</h2>
          </div>

          <?php if (isset($_GET['rated'])): ?>
            <div style="background:#e8f5e9;color:#1b5e20;padding:12px 16px;border-radius:8px;margin-bottom:18px;border:1px solid #c8e6c9;" data-testid="rate-thanks">
              <i class="fas fa-check-circle" aria-hidden="true"></i> Thank you — your rating has been recorded!
            </div>
          <?php endif; ?>

          <div class="section-card">
            <h3><i class="fas fa-users" aria-hidden="true"></i> Community Rating</h3>
            <div style="display:flex;align-items:center;gap:18px;margin-top:10px;">
              <div style="font-size:48px;font-weight:700;color:#1b5e20;" data-testid="avg-rating"><?php echo number_format($ratingAvg, 1); ?></div>
              <div>
                <div style="color:#f4b400;font-size:24px;letter-spacing:3px;">
                  <?php
                    $full = floor($ratingAvg);
                    $half = ($ratingAvg - $full) >= 0.5 ? 1 : 0;
                    for ($i=0; $i<$full; $i++)  echo '<i class="fas fa-star" aria-hidden="true"></i>';
                    if ($half) echo '<i class="fas fa-star-half-alt" aria-hidden="true"></i>';
                    for ($i=$full+$half; $i<5; $i++) echo '<i class="far fa-star" aria-hidden="true"></i>';
                  ?>
                </div>
                <div style="color:#555;font-size:13px;margin-top:4px;" data-testid="rating-count"><?php echo $ratingCount; ?> rating<?php echo $ratingCount === 1 ? '' : 's'; ?> from our community</div>
              </div>
            </div>
          </div>

          <div class="section-card">
            <h3><i class="fas fa-star" aria-hidden="true"></i> Your Rating</h3>
            <?php if ($myRating): ?>
              <p style="color:#555;margin-bottom:12px;">You last rated us on <strong><?php echo date('F d, Y', strtotime($myRating['created_at'])); ?></strong>:</p>
              <div style="color:#f4b400;font-size:22px;letter-spacing:3px;margin-bottom:8px;">
                <?php for ($i=0; $i<(int)$myRating['stars']; $i++) echo '<i class="fas fa-star" aria-hidden="true"></i>'; ?>
                <?php for ($i=(int)$myRating['stars']; $i<5; $i++) echo '<i class="far fa-star" aria-hidden="true"></i>'; ?>
              </div>
              <?php if (!empty($myRating['comment'])): ?>
                <blockquote style="border-left:3px solid #1b5e20;padding:6px 14px;color:#444;margin:6px 0 18px;font-style:italic;">"<?php echo htmlspecialchars($myRating['comment']); ?>"</blockquote>
              <?php endif; ?>
              <p style="color:#777;font-size:13px;">You can submit a new rating below.</p>
            <?php endif; ?>

            <form action="rate.php" method="POST" data-testid="rate-form" style="margin-top:14px;">
              <label style="display:block;margin-bottom:8px;font-weight:600;color:#1b5e20;">How would you rate your experience?</label>
              <div class="star-input" style="font-size:36px;color:#ddd;cursor:pointer;letter-spacing:6px;user-select:none;">
                <?php for ($i=1; $i<=5; $i++): ?>
                  <i class="far fa-star rate-star" data-value="<?php echo $i; ?>" data-testid="rate-star-<?php echo $i; ?>"></i>
                <?php endfor; ?>
              </div>
              <input type="hidden" name="stars" id="stars-input" value="0" data-testid="rate-stars-value">

              <label for="rate-comment" style="display:block;margin-top:18px;margin-bottom:6px;font-weight:600;color:#1b5e20;">Share your experience (optional)</label>
              <textarea id="rate-comment" name="comment" rows="3" placeholder="What did you love? What could we do better?" style="width:100%;padding:10px;border:1px solid #cbd5c7;border-radius:6px;font-family:'Open Sans',sans-serif;" data-testid="rate-comment"></textarea>

              <button type="submit" class="btn-primary" style="margin-top:14px;" id="submit-rating" disabled data-testid="rate-submit">
                <i class="fas fa-paper-plane" aria-hidden="true"></i> Submit Rating
              </button>
            </form>

            <style>
              .rate-star{transition:color .15s ease, transform .15s ease}
              .rate-star:hover{transform:scale(1.1)}
              .rate-star.filled{color:#f4b400}
              #submit-rating:disabled{opacity:.5;cursor:not-allowed}
            </style>
            <script>
              // this script handles the interactive star rating system on the "Rate Us" tab. 
              // It allows users to hover over the stars to preview their rating and click to select a rating. 
              // The selected rating is stored in a hidden input field, and the submit button is enabled once a rating is selected.
              (function(){
                const stars = document.querySelectorAll('.rate-star');
                const hidden = document.getElementById('stars-input');
                const btn = document.getElementById('submit-rating');
                let current = 0;

                // the paint function updates the visual state of the stars based on the current rating. 
                // It adds the "filled" class to stars that are less than the selected rating and removes it from those that are greater or equal.
                function paint(n){
                  stars.forEach((s,i) => {
                    if (i < n) { s.classList.remove('far'); s.classList.add('fas','filled'); }
                    else { s.classList.remove('fas','filled'); s.classList.add('far'); }
                  });
                }

                // this section adds event listeners to each star icon. When a user hovers over a star, it calls the paint function to visually indicate the potential rating.
                // When the user moves the mouse away, it resets the stars to reflect the current selected rating. When
                stars.forEach((s,i) => {
                  s.addEventListener('mouseenter', () => paint(i+1));
                  s.addEventListener('mouseleave', () => paint(current));
                  s.addEventListener('click', () => {
                    current = i+1;
                    hidden.value = current;
                    btn.disabled = false;
                    paint(current);
                  });
                });
              })();
            </script>
          </div>
        </div>
      </div>
    </div>
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

  <script src="dashboard.js"></script>
</body>
</html>