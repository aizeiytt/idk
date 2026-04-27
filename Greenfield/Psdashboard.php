<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Producer Dashboard - Greenfield Local Hub</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="psdashboard.css">
</head>
<body>

  <header class="header-navbar" role="banner">
    <a href="index.html" class="header-home-link">
      <div class="header-left">
        <img src="images.jpg" alt="Greenfield Local Hub Logo" class="header-logo" />
        <span class="header-brand">Greenfield Local Hub</span>
      </div>
    </a>
    <nav aria-label="Primary">
      <ul class="header-links">
        <li><a href="index.html">Home</a></li>
        <li><a href="about.html">About us</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="checkout.php">Checkout</a></li>
      </ul>
    </nav>
    <div class="header-auth">
      <a href="login.php">Log in</a>
      <a href="register.php">Sign up</a>
    </div>
  </header>

  <main id="main" role="main">
    <div class="dashboard-wrapper">
      <!-- Sidebar -->
      <aside class="dashboard-sidebar">
        <div class="sidebar-header">
          <h3>Producer Menu</h3>
        </div>
        <nav class="sidebar-nav">
          <a href="#" class="nav-item active" data-section="overview">
            <i class="fas fa-chart-line" aria-hidden="true"></i> Overview
          </a>
          <a href="#" class="nav-item" data-section="products">
            <i class="fas fa-leaf" aria-hidden="true"></i> My Products
          </a>
          <a href="#" class="nav-item" data-section="orders">
            <i class="fas fa-shopping-bag" aria-hidden="true"></i> Orders
          </a>
          <a href="#" class="nav-item" data-section="sales">
            <i class="fas fa-dollar-sign" aria-hidden="true"></i> Sales
          </a>
          <a href="#" class="nav-item" data-section="reviews">
            <i class="fas fa-star" aria-hidden="true"></i> Reviews
          </a>
          <a href="#" class="nav-item" data-section="settings">
            <i class="fas fa-cog" aria-hidden="true"></i> Settings
          </a>
        </nav>
      </aside>


      <div class="dashboard-main">
        <!-- Top Bar -->
        <div class="dashboard-topbar">
          <h1>Producer Dashboard</h1>
          <div class="topbar-right">
            <button class="btn-notification">
              <i class="fas fa-bell" aria-hidden="true"></i>
              <span class="notification-badge">3</span>
            </button>
            <div class="user-profile">
              <img src="https://via.placeholder.com/40" alt="Profile">
              <span>John Smith</span>
            </div>
          </div>
        </div>

      
        <section id="overview" class="dashboard-section active">
          <div class="dashboard-container">
            <h2>Dashboard Overview</h2>
            
            <!-- Stats Grid -->
            <div class="stats-grid">
              <div class="stat-card">
                <div class="stat-icon revenue">
                  <i class="fas fa-dollar-sign" aria-hidden="true"></i>
                </div>
                <div class="stat-content">
                  <h4>Total Revenue</h4>
                  <p class="stat-value">$12,450</p>
                  <span class="stat-change positive">
                    <i class="fas fa-arrow-up" aria-hidden="true"></i> 12% from last month
                  </span>
                </div>
              </div>

              <div class="stat-card">
                <div class="stat-icon orders">
                  <i class="fas fa-shopping-bag" aria-hidden="true"></i>
                </div>
                <div class="stat-content">
                  <h4>Total Orders</h4>
                  <p class="stat-value">284</p>
                  <span class="stat-change positive">
                    <i class="fas fa-arrow-up" aria-hidden="true"></i> 8% from last month
                  </span>
                </div>
              </div>

              <div class="stat-card">
                <div class="stat-icon products">
                  <i class="fas fa-leaf" aria-hidden="true"></i>
                </div>
                <div class="stat-content">
                  <h4>Active Products</h4>
                  <p class="stat-value">18</p>
                  <span class="stat-change neutral">
                    <i class="fas fa-equals" aria-hidden="true"></i> 0 changes
                  </span>
                </div>
              </div>

              <div class="stat-card">
                <div class="stat-icon rating">
                  <i class="fas fa-star" aria-hidden="true"></i>
                </div>
                <div class="stat-content">
                  <h4>Average Rating</h4>
                  <p class="stat-value">4.8/5</p>
                  <span class="stat-change positive">
                    <i class="fas fa-arrow-up" aria-hidden="true"></i> From 48 reviews
                  </span>
                </div>
              </div>
            </div>

          
            <div class="charts-row">
              <div class="chart-card">
                <h3>Sales Trend</h3>
                <div class="chart-placeholder">
                  <i class="fas fa-chart-line" aria-hidden="true"></i>
                  <p>Sales chart would appear here</p>
                </div>
              </div>

              <div class="chart-card">
                <h3>Product Performance</h3>
                <div class="chart-placeholder">
                  <i class="fas fa-chart-pie" aria-hidden="true"></i>
                  <p>Performance chart would appear here</p>
                </div>
              </div>
            </div>
          </div>
        </section>

       
        <section id="products" class="dashboard-section">
          <div class="dashboard-container">
            <div class="section-header">
              <h2>My Products</h2>
              <button class="btn-primary">
                <i class="fas fa-plus" aria-hidden="true"></i> Add Product
              </button>
            </div>

            <div class="products-table-container">
              <table class="products-table">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Sales</th>
                    <th>Rating</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><strong>Fresh Lettuce</strong></td>
                    <td>$3.99</td>
                    <td><span class="badge in-stock">45 units</span></td>
                    <td>234</td>
                    <td><i class="fas fa-star" aria-hidden="true"></i> 4.8</td>
                    <td>
                      <button class="btn-edit">Edit</button>
                      <button class="btn-delete">Delete</button>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Organic Tomatoes</strong></td>
                    <td>$4.49</td>
                    <td><span class="badge in-stock">78 units</span></td>
                    <td>189</td>
                    <td><i class="fas fa-star" aria-hidden="true"></i> 4.9</td>
                    <td>
                      <button class="btn-edit">Edit</button>
                      <button class="btn-delete">Delete</button>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Broccoli</strong></td>
                    <td>$3.79</td>
                    <td><span class="badge low-stock">12 units</span></td>
                    <td>156</td>
                    <td><i class="fas fa-star" aria-hidden="true"></i> 4.7</td>
                    <td>
                      <button class="btn-edit">Edit</button>
                      <button class="btn-delete">Delete</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>

        
        <section id="orders" class="dashboard-section">
          <div class="dashboard-container">
            <h2>Recent Orders</h2>

            <div class="orders-container">
              <div class="order-card">
                <div class="order-header">
                  <div class="order-id">Order #GH-2024-0458</div>
                  <span class="order-status processing">Processing</span>
                </div>
                <div class="order-details">
                  <p><strong>Customer:</strong> Jane Doe</p>
                  <p><strong>Products:</strong> Fresh Lettuce (2x), Tomatoes (1x)</p>
                  <p><strong>Total:</strong> $12.47</p>
                  <p><strong>Date:</strong> Dec 15, 2024</p>
                </div>
                <div class="order-actions">
                  <button class="btn-view">View Details</button>
                  <button class="btn-update">Update Status</button>
                </div>
              </div>

              <div class="order-card">
                <div class="order-header">
                  <div class="order-id">Order #GH-2024-0457</div>
                  <span class="order-status delivered">Delivered</span>
                </div>
                <div class="order-details">
                  <p><strong>Customer:</strong> John Smith</p>
                  <p><strong>Products:</strong> Broccoli (3x), Carrots (2x)</p>
                  <p><strong>Total:</strong> $14.35</p>
                  <p><strong>Date:</strong> Dec 14, 2024</p>
                </div>
                <div class="order-actions">
                  <button class="btn-view">View Details</button>
                </div>
              </div>
            </div>
          </div>
        </section>

        
        <section id="sales" class="dashboard-section">
          <div class="dashboard-container">
            <h2>Sales Summary</h2>

            <div class="sales-summary">
              <div class="sales-card">
                <h3>This Month</h3>
                <p class="sales-amount">$12,450</p>
                <p class="sales-note">456 orders</p>
              </div>

              <div class="sales-card">
                <h3>Last Month</h3>
                <p class="sales-amount">$11,120</p>
                <p class="sales-note">421 orders</p>
              </div>

              <div class="sales-card">
                <h3>This Year</h3>
                <p class="sales-amount">$145,780</p>
                <p class="sales-note">5,234 orders</p>
              </div>
            </div>
          </div>
        </section>

        
        <section id="reviews" class="dashboard-section">
          <div class="dashboard-container">
            <h2>Customer Reviews</h2>

            <div class="reviews-container">
              <div class="review-card">
                <div class="review-header">
                  <div class="reviewer-info">
                    <strong>Sarah Johnson</strong>
                    <span class="review-date">2 days ago</span>
                  </div>
                  <div class="review-rating">
                    <i class="fas fa-star" aria-hidden="true"></i>
                    <i class="fas fa-star" aria-hidden="true"></i>
                    <i class="fas fa-star" aria-hidden="true"></i>
                    <i class="fas fa-star" aria-hidden="true"></i>
                    <i class="fas fa-star" aria-hidden="true"></i>
                  </div>
                </div>
                <p class="review-text">Excellent fresh produce! The lettuce was crisp and lasted a week in my fridge.</p>
              </div>

              <div class="review-card">
                <div class="review-header">
                  <div class="reviewer-info">
                    <strong>Mike Chen</strong>
                    <span class="review-date">5 days ago</span>
                  </div>
                  <div class="review-rating">
                    <i class="fas fa-star" aria-hidden="true"></i>
                    <i class="fas fa-star" aria-hidden="true"></i>
                    <i class="fas fa-star" aria-hidden="true"></i>
                    <i class="fas fa-star" aria-hidden="true"></i>
                  </div>
                </div>
                <p class="review-text">Great quality, very fresh. Tomatoes were ripe and perfect for my salad.</p>
              </div>
            </div>
          </div>
        </section>

      
        <section id="settings" class="dashboard-section">
          <div class="dashboard-container">
            <h2>Settings</h2>

            <div class="settings-section">
              <h3>Farm Information</h3>
              <form class="settings-form">
                <input type="text" placeholder="Farm Name" value="Green Valley Farm">
                <textarea placeholder="Farm Description" rows="4">Sustainable organic farming with 20 years of experience</textarea>
                <button type="submit" class="btn-primary">Save Changes</button>
              </form>
            </div>

            <div class="settings-section">
              <h3>Account Settings</h3>
              <form class="settings-form">
                <input type="email" placeholder="Email" value="farm@example.com">
                <input type="password" placeholder="Change Password">
                <button type="submit" class="btn-primary">Update Account</button>
              </form>
            </div>
          </div>
        </section>
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

  <script src="psdashboard.js"></script>
</body>
</html>