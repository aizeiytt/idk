<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Stock Management - Greenfield Local Hub</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="stock.css">
</head>
<body>


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

  <main id="main" class="stock-main" role="main">
    
    <div class="stock-page-header">
      <div class="header-content">
        <h1><i class="fas fa-warehouse" aria-hidden="true"></i> Stock Level Management</h1>
        <p>Monitor, manage, and optimise your inventory levels in real-time</p>
      </div>
      <button class="btn-primary" onclick="openBulkUpdateModal()">
        <i class="fas fa-arrows-alt-v" aria-hidden="true"></i> Bulk Update
      </button>
    </div>


    <div class="stats-bar">
      <div class="stat-item">
        <div class="stat-icon">
          <i class="fas fa-boxes" aria-hidden="true"></i>
        </div>
        <div class="stat-number" id="totalProducts">15</div>
        <div class="stat-label">Total Products</div>
      </div>
      <div class="stat-item success">
        <div class="stat-icon">
          <i class="fas fa-check-circle" aria-hidden="true"></i>
        </div>
        <div class="stat-number" id="inStockCount">12</div>
        <div class="stat-label">In Stock</div>
      </div>
      <div class="stat-item warning">
        <div class="stat-icon">
          <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
        </div>
        <div class="stat-number" id="lowStockCount">2</div>
        <div class="stat-label">Low Stock</div>
      </div>
      <div class="stat-item danger">
        <div class="stat-icon">
          <i class="fas fa-times-circle" aria-hidden="true"></i>
        </div>
        <div class="stat-number" id="outOfStockCount">1</div>
        <div class="stat-label">Out of Stock</div>
      </div>
    </div>

    <div class="stock-container">
   
      <aside class="control-panel">
        <div class="control-section">
          <h3><i class="fas fa-search" aria-hidden="true"></i> Filter & Search</h3>
          <div class="search-box">
            <i class="fas fa-search" aria-hidden="true"></i>
            <input type="text" id="searchInput" placeholder="Search products..." onkeyup="filterStock()">
          </div>
        </div>

        <div class="control-section">
          <h3><i class="fas fa-filter" aria-hidden="true"></i> Alert Level</h3>
          <div class="filter-buttons">
            <button class="filter-btn active" onclick="filterByAlert('all')">
              <i class="fas fa-list" aria-hidden="true"></i> Show All
            </button>
            <button class="filter-btn" onclick="filterByAlert('ok')">
              <i class="fas fa-check" aria-hidden="true"></i> In Stock
            </button>
            <button class="filter-btn" onclick="filterByAlert('low')">
              <i class="fas fa-exclamation" aria-hidden="true"></i> Low Stock
            </button>
            <button class="filter-btn" onclick="filterByAlert('critical')">
              <i class="fas fa-ban" aria-hidden="true"></i> Out of Stock
            </button>
          </div>
        </div>

        <div class="control-section">
          <h3><i class="fas fa-sliders-h" aria-hidden="true"></i> Set Thresholds</h3>
          <div class="threshold-settings">
            <label>
              <span>Low Stock Alert</span>
              <input type="number" id="lowThreshold" value="10" onchange="updateThresholds()" min="1">
            </label>
            <label>
              <span>Critical Level</span>
              <input type="number" id="criticalThreshold" value="0" onchange="updateThresholds()" min="0">
            </label>
            <button class="btn-secondary" onclick="updateThresholds()">
              <i class="fas fa-save" aria-hidden="true"></i> Save
            </button>
          </div>
        </div>

        <div class="control-section">
          <h3><i class="fas fa-download" aria-hidden="true"></i> Export</h3>
          <div class="action-buttons">
            <button class="btn-secondary" onclick="exportStockCSV()">
              <i class="fas fa-download" aria-hidden="true"></i> CSV
            </button>
            <button class="btn-secondary" onclick="exportStockPDF()">
              <i class="fas fa-file-pdf" aria-hidden="true"></i> PDF
            </button>
            <button class="btn-secondary" onclick="printStock()">
              <i class="fas fa-print" aria-hidden="true"></i> Print
            </button>
          </div>
        </div>
      </aside>

      
      <div class="stock-table-section">
        <div class="table-header">
          <h2>Inventory Overview</h2>
          <div class="sort-options">
            <select onchange="sortStock(this.value)">
              <option value="name">Sort by Name</option>
              <option value="stock-low">Stock (Low to High)</option>
              <option value="stock-high">Stock (High to Low)</option>
              <option value="status">Sort by Status</option>
            </select>
          </div>
        </div>

        <div class="table-responsive">
          <table class="stock-table" id="stockTable">
            <thead>
              <tr>
                <th style="width: 40px;">
                  <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                </th>
                <th>Product</th>
                <th>Category</th>
                <th>Stock Level</th>
                <th>Threshold</th>
                <th>Unit</th>
                <th>Status</th>
                <th>Updated</th>
                <th>Trend</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="stockTableBody">
              
            </tbody>
          </table>
        </div>
      </div>

      <
      <div class="analytics-section">
        <h2>Stock Analytics</h2>
        <div class="analytics-grid">
          <div class="analytics-card">
            <div class="chart-header">
              <h3>Low Stock Items</h3>
              <span class="chart-legend">action required</span>
            </div>
            <div class="low-stock-list" id="lowStockList">
            
            </div>
          </div>

          <div class="analytics-card">
            <div class="chart-header">
              <h3>Restock Forecast</h3>
              <span class="chart-legend">next 30 days</span>
            </div>
            <div class="forecast-list" id="forecastList">
           
            </div>
          </div>

          <div class="analytics-card">
            <div class="chart-header">
              <h3>Stock Status</h3>
              <span class="chart-legend">distribution</span>
            </div>
            <div class="status-distribution" id="statusDistribution">
             
            </div>
          </div>

          <div class="analytics-card">
            <div class="chart-header">
              <h3>Critical Alerts</h3>
              <span class="chart-legend">requires attention</span>
            </div>
            <div class="alerts-list" id="alertsList">
            
            </div>
          </div>
        </div>
      </div>

  
      <div class="history-section">
        <h2>Recent Updates</h2>
        <div class="history-table">
          <table>
            <thead>
              <tr>
                <th>Product</th>
                <th>Previous Stock</th>
                <th>New Stock</th>
                <th>Change</th>
                <th>Updated By</th>
                <th>Date & Time</th>
              </tr>
            </thead>
            <tbody id="historyTableBody">
              
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  
  <div class="modal-overlay" id="modalOverlay">
    <div class="modal" id="modal">
      <button class="modal-close" onclick="closeModal()">
        <i class="fas fa-times" aria-hidden="true"></i>
      </button>
      <div id="modalContent"></div>
    </div>
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

  <script src="stock.js"></script>
</body>
</html>