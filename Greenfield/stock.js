// This file contains the main JavaScript logic for the stock management dashboard, including data handling, UI updates, and user interactions. 
// It defines the initial stock data, functions to populate the stock table and analytics sections, and handlers for filtering, sorting, and updating stock levels. 
// The code is structured to be modular and maintainable, allowing for easy updates and additions in the future.
const stockData = [
  { id: 1, name: 'Tomatoes', emoji: '🍅', category: 'Vegetables', current: 45, threshold: 20, unit: 'kg', status: 'ok', lastUpdated: '2 hours ago', trend: 'up', previous: 40 },
  { id: 2, name: 'Lettuce', emoji: '🥬', category: 'Vegetables', current: 5, threshold: 10, unit: 'heads', status: 'low', lastUpdated: '4 hours ago', trend: 'down', previous: 8 },
  { id: 3, name: 'Potatoes', emoji: '🥔', category: 'Vegetables', current: 0, threshold: 25, unit: 'kg', status: 'critical', lastUpdated: '3 days ago', trend: 'down', previous: 15 },
  { id: 4, name: 'Onions', emoji: '🧅', category: 'Vegetables', current: 22, threshold: 20, unit: 'kg', status: 'ok', lastUpdated: '5 hours ago', trend: 'up', previous: 18 },
  { id: 5, name: 'Apples', emoji: '🍎', category: 'Fruits', current: 15, threshold: 10, unit: 'kg', status: 'ok', lastUpdated: '12 hours ago', trend: 'down', previous: 20 },
  { id: 6, name: 'Carrots', emoji: '🥕', category: 'Vegetables', current: 35, threshold: 15, unit: 'kg', status: 'ok', lastUpdated: '6 hours ago', trend: 'down', previous: 40 },
  { id: 7, name: 'Cucumber', emoji: '🥒', category: 'Vegetables', current: 8, threshold: 15, unit: 'units', status: 'low', lastUpdated: '2 days ago', trend: 'down', previous: 12 },
  { id: 8, name: 'Bananas', emoji: '🍌', category: 'Fruits', current: 50, threshold: 20, unit: 'bunches', status: 'ok', lastUpdated: '1 hour ago', trend: 'up', previous: 45 },
  { id: 9, name: 'Milk', emoji: '🥛', category: 'Dairy', current: 25, threshold: 10, unit: 'liters', status: 'ok', lastUpdated: '3 hours ago', trend: 'up', previous: 20 },
  { id: 10, name: 'Eggs', emoji: '🥚', category: 'Dairy', current: 100, threshold: 50, unit: 'units', status: 'ok', lastUpdated: '8 hours ago', trend: 'down', previous: 110 },
  { id: 11, name: 'Broccoli', emoji: '🥦', category: 'Vegetables', current: 18, threshold: 12, unit: 'heads', status: 'ok', lastUpdated: '1 day ago', trend: 'down', previous: 22 },
  { id: 12, name: 'Spinach', emoji: '🍃', category: 'Vegetables', current: 3, threshold: 8, unit: 'bunches', status: 'low', lastUpdated: '5 hours ago', trend: 'down', previous: 5 },
  { id: 13, name: 'Oranges', emoji: '🍊', category: 'Fruits', current: 60, threshold: 25, unit: 'kg', status: 'ok', lastUpdated: '2 hours ago', trend: 'up', previous: 55 },
  { id: 14, name: 'Peppers', emoji: '🫑', category: 'Vegetables', current: 12, threshold: 15, unit: 'units', status: 'low', lastUpdated: '3 hours ago', trend: 'down', previous: 15 },
  { id: 15, name: 'Cheese', emoji: '🧀', category: 'Dairy', current: 8, threshold: 5, unit: 'kg', status: 'ok', lastUpdated: '4 hours ago', trend: 'down', previous: 10 },
];

// The script initializes the stock management dashboard by populating the stock table and various analytics sections with data from the `stockData` array.
let filteredData = [...stockData];
let currentFilter = 'all';
let selectedItems = new Set();

// The `DOMContentLoaded` event listener ensures that the functions to populate the stock table and analytics sections are called only after the entire DOM has been loaded,
// preventing any issues with trying to access elements that may not yet exist in the DOM.
document.addEventListener('DOMContentLoaded', function() {
  populateStockTable();
  updateStatsBar();
  populateLowStockList();
  populateForecastList();
  populateStatusDistribution();
  populateAlerts();
  populateHistoryTable();
});

// This function populates the stock table with rows generated from the `filteredData` array, which is initially a copy of the original `stockData`.
function populateStockTable() {
  const tbody = document.getElementById('stockTableBody');

  tbody.innerHTML = filteredData.map(item => `
    <tr>
      <td>
        <input type="checkbox" onchange="toggleItem(${item.id})">
      </td>
      <td>
        <div class="product-name">
          <span class="product-icon">${item.emoji}</span>
          <span>${item.name}</span>
        </div>
      </td>
      <td>${item.category}</td>
      <td>
        <div class="stock-quantity">${item.current} ${item.unit}</div>
      </td>
      <td>${item.threshold} ${item.unit}</td>
      <td>${item.unit}</td>
      <td>
        <span class="status-badge status-${item.status}">
          ${getStatusLabel(item.status)}
        </span>
      </td>
      <td>${item.lastUpdated}</td>
      <td>
        <div class="trend-indicator trend-${item.trend}">
          ${getTrendIcon(item.trend)} ${getTrendText(item.current, item.previous)}
        </div>
      </td>
      <td>
        <div class="action-buttons-cell">
          <button class="action-btn btn-adjust" onclick="openAdjustModal(${item.id})" title="Adjust Stock">
            <i class="fas fa-plus-minus"></i>
          </button>
          <button class="action-btn btn-restock" onclick="openRestockModal(${item.id})" title="Restock">
            <i class="fas fa-box"></i>
          </button>
        </div>
      </td>
    </tr>
  `).join('');
}

// This function returns a human-readable label for the stock status based on the status code ('ok', 'low', 'critical').
function getStatusLabel(status) {
  const labels = {
    'ok': '✓ In Stock',
    'low': '⚠ Low Stock',
    'critical': '✕ Out of Stock'
  };
  return labels[status] || 'Unknown';
}

// This function returns an HTML string representing the trend icon (up or down arrow) based on the trend value ('up' or 'down'), and also calculates the percentage change in stock levels to display alongside the icon.
function getTrendIcon(trend) {
  return trend === 'up' ? '<i class="fas fa-arrow-up"></i>' : '<i class="fas fa-arrow-down"></i>';
}

// This function calculates the percentage change in stock levels compared to the previous value and formats it as a string with a '+' or '-' sign, depending on whether the change is positive or negative.
function getTrendText(current, previous) {
  const change = current - previous;
  const percent = previous === 0 ? 0 : Math.abs(Math.round((change / previous) * 100));
  return (change > 0 ? '+' : '') + percent + '%';
}

// The `filterStock` function filters the stock data based on the search term entered by the user and the currently selected alert filter (e.g., 'all', 'ok', 'low', 'critical').
function filterStock() {
  const searchTerm = document.getElementById('searchInput').value.toLowerCase();

  // The filtering logic checks if each item's name includes the search term and if it matches the selected alert filter. 
  // The filtered results are stored in the `filteredData` array, which is then used to repopulate the stock table with only the items that match the search and filter criteria.
  filteredData = stockData.filter(item => {
    const matchesSearch = item.name.toLowerCase().includes(searchTerm);
    const matchesFilter = currentFilter === 'all' || item.status === currentFilter;
    return matchesSearch && matchesFilter;
  });

  populateStockTable();
}

// The `filterByAlert` function is called when a user clicks on one of the alert filter buttons (e.g., 'All', 'In Stock', 'Low Stock', 'Out of Stock').
function filterByAlert(status) {
  currentFilter = status;

  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.classList.remove('active');
  });
  event.target.classList.add('active');

  filterStock();
}

// The `sortStock` function sorts the `filteredData` array based on the selected sorting criteria (e.g., 'name', 'stock-low', 'stock-high', 'status').
function sortStock(sortBy) {
  filteredData.sort((a, b) => {
    switch(sortBy) {
      case 'name':
        return a.name.localeCompare(b.name);
      case 'stock-low':
        return a.current - b.current;
      case 'stock-high':
        return b.current - a.current;
      case 'status':
        const statusOrder = { 'critical': 0, 'low': 1, 'ok': 2 };
        return statusOrder[a.status] - statusOrder[b.status];
      default:
        return 0;
    }
  });

  populateStockTable();
}

// The `toggleSelectAll` function is called when the "Select All" checkbox in the table header is toggled.
// It checks or unchecks all individual item checkboxes based on the state of the "Select All" checkbox and updates the `selectedItems` set accordingly to keep track of which items are currently selected.
function toggleSelectAll() {
  const checkbox = document.getElementById('selectAll');

  // If the "Select All" checkbox is checked, all items in the `filteredData` array are added to the `selectedItems` set, and all individual checkboxes in the table are checked.
  if (checkbox.checked) {
    filteredData.forEach(item => selectedItems.add(item.id));
    document.querySelectorAll('tbody input[type="checkbox"]').forEach(cb => {
      cb.checked = true;
    });
    // If the "Select All" checkbox is unchecked, the `selectedItems` set is cleared, and all individual checkboxes in the table are unchecked.
  } else {
    selectedItems.clear();
    document.querySelectorAll('tbody input[type="checkbox"]').forEach(cb => {
      cb.checked = false;
    });
  }
}

// The `toggleItem` function is called when an individual item's checkbox is toggled. 
// It adds or removes the item's ID from the `selectedItems` set based on whether the checkbox is checked or unchecked, allowing the application to keep track of which items are currently selected for potential bulk actions.
function toggleItem(id) {
  if (selectedItems.has(id)) {
    selectedItems.delete(id);
  } else {
    selectedItems.add(id);
  }
}

// The `updateStatsBar` function calculates the total number of products, the count of products in each stock status category (in stock, low stock, out of stock), 
// and updates the corresponding elements in the stats bar to display these counts to the user.
function updateStatsBar() {
  const total = stockData.length;
  const inStock = stockData.filter(s => s.status === 'ok').length;
  const lowStock = stockData.filter(s => s.status === 'low').length;
  const outOfStock = stockData.filter(s => s.status === 'critical').length;

  // The calculated counts are then displayed in the stats bar by updating the text content of the elements with IDs 'totalProducts', 'inStockCount', 'lowStockCount', and 'outOfStockCount' to reflect the current stock status of all products.
  document.getElementById('totalProducts').textContent = total;
  document.getElementById('inStockCount').textContent = inStock;
  document.getElementById('lowStockCount').textContent = lowStock;
  document.getElementById('outOfStockCount').textContent = outOfStock;
}

// The `populateLowStockList` function generates a list of products that are currently low on stock or out of stock, and updates the corresponding section in the dashboard to display these items to the user.
function populateLowStockList() {
  const list = document.getElementById('lowStockList');
  const lowStockItems = stockData.filter(s => s.status === 'low' || s.status === 'critical');

  // If there are no items that are low on stock or out of stock, the function displays a message indicating that all items are well-stocked. 
  // Otherwise, it generates HTML for each low stock item, showing its name, current stock level, and threshold, and updates the 'lowStockList' element with this information.
  if (lowStockItems.length === 0) {
    list.innerHTML = '<div style="text-align: center; padding: 20px; color: var(--text-light);">All items are well-stocked</div>';
    return;
  }

  // The generated HTML for each low stock item includes the product's emoji, name, current stock level, and threshold, formatted in a way that highlights the urgency of restocking these items.
  list.innerHTML = lowStockItems.map(item => `
    <div class="list-item">
      <div class="list-item-name">
        <span style="font-size: 18px;">${item.emoji}</span>
        <strong>${item.name}</strong>
      </div>
      <div class="list-item-value">${item.current}/${item.threshold}</div>
    </div>
  `).join('');
}

// The `populateForecastList` function generates a list of products that are projected to need restocking soon based on their current stock levels and thresholds, 
// and updates the corresponding section in the dashboard to display these items to the user.
function populateForecastList() {
  const list = document.getElementById('forecastList');
  const needsRestock = stockData.filter(s => s.current < s.threshold).slice(0, 5);

  // If there are no items projected to need restocking soon, the function displays a message indicating that all items are sufficiently stocked for the near future.
  list.innerHTML = needsRestock.map(item => {
    const daysUntilNeeded = Math.ceil((item.threshold - item.current) / 2);
    return `
      <div class="list-item">
        <div class="list-item-name">
          <span style="font-size: 18px;">${item.emoji}</span>
          <strong>${item.name}</strong>
        </div>
        <div class="list-item-value" style="color: var(--info);">In ${daysUntilNeeded} days</div>
      </div>
    `;
  }).join('');
}

// The `populateStatusDistribution` function calculates the distribution of products across different stock status categories (in stock, low stock, out of stock) 
// and updates the corresponding section in the dashboard to visually represent this distribution to the user.
function populateStatusDistribution() {
  const dist = document.getElementById('statusDistribution');
  const ok = stockData.filter(s => s.status === 'ok').length;
  const low = stockData.filter(s => s.status === 'low').length;
  const critical = stockData.filter(s => s.status === 'critical').length;

  // The function generates HTML that displays the count of products in each stock status category, along with corresponding icons and colors to visually differentiate between the categories.
  dist.innerHTML = `
    <div class="distribution-item">
      <span style="font-size: 14px;">✓ In Stock</span>
      <span class="distribution-value" style="color: var(--success);">${ok}</span>
    </div>
    <div class="distribution-item">
      <span style="font-size: 14px;">⚠ Low Stock</span>
      <span class="distribution-value" style="color: var(--warning);">${low}</span>
    </div>
    <div class="distribution-item">
      <span style="font-size: 14px;">✕ Out of Stock</span>
      <span class="distribution-value" style="color: var(--danger);">${critical}</span>
    </div>
  `;
}



// The `populateAlerts` function generates a list of critical alerts for products that are either out of stock or low on stock, and updates the corresponding section in the dashboard to display these alerts to the user.
function populateAlerts() {
  const alerts = document.getElementById('alertsList');
  const criticalItems = stockData.filter(s => s.status === 'critical' || (s.status === 'low' && s.current <= 5));

  // If there are no critical alerts, the function displays a message indicating that there are currently no urgent stock issues.
  if (criticalItems.length === 0) {
    alerts.innerHTML = '<div style="text-align: center; padding: 20px; color: var(--text-light);">No critical alerts</div>';
    return;
  }

  // The generated HTML for each critical alert includes the product's emoji, name, and current stock level, formatted in a way that emphasizes the urgency of the situation 
  // and encourages the user to take action to restock these items as soon as possible.
  alerts.innerHTML = criticalItems.map(item => `
    <div class="alert-item">
      <span class="alert-item-text">
        <span style="font-size: 14px;">${item.emoji}</span>
        <strong>${item.name}</strong>
      </span>
      <span class="alert-item-value">Urgent: ${item.current} units</span>
    </div>
  `).join('');
}

// The `populateHistoryTable` function generates a table of recent stock adjustments, showing the product name, previous stock level, current stock level, the change in stock, who made the adjustment, and when it was made.
function populateHistoryTable() {
  const tbody = document.getElementById('historyTableBody');

  // The function uses a hardcoded array of recent stock adjustments for demonstration purposes. In a real application, this data would likely come from a database or API.
  const history = [
    { product: 'Tomatoes', previous: 40, current: 45, by: 'John Doe', date: 'Today, 2:00 PM' },
    { product: 'Lettuce', previous: 8, current: 5, by: 'Jane Smith', date: 'Today, 4:00 PM' },
    { product: 'Carrots', previous: 40, current: 35, by: 'John Doe', date: 'Yesterday, 10:30 AM' },
  ];

  // The generated HTML for each stock adjustment includes the product name, previous and current stock levels, the change in stock (with a '+' or '-' sign), who made the adjustment, and when it was made. 
  // The change in stock is also color-coded to visually indicate whether it was an increase (positive change) or a decrease (negative change).
  tbody.innerHTML = history.map(h => {
    const change = h.current - h.previous;
    const isPositive = change > 0;
    return `
      <tr>
        <td><strong>${h.product}</strong></td>
        <td>${h.previous}</td>
        <td>${h.current}</td>
        <td><span class="${isPositive ? 'change-positive' : 'change-negative'}">${isPositive ? '+' : ''}${change}</span></td>
        <td>${h.by}</td>
        <td>${h.date}</td>
      </tr>
    `;
  }).join('');
}

// The `openAdjustModal` function is called when the user clicks the "Adjust Stock" button for a specific product. 
// It retrieves the product data based on the provided product ID and generates a modal form that allows the user to either add or remove stock for that product, specify the quantity, and provide a reason for the adjustment.
function openAdjustModal(productId) {
  const product = stockData.find(p => p.id === productId);
  if (!product) return;

  // The generated HTML for the adjust stock modal includes the product's emoji and name, the current stock level, and a form with options to add or remove stock, specify the quantity, and provide a reason for the adjustment.
  const content = `
    <div style="padding: 25px;">
      <h2 style="color: var(--primary); margin-bottom: 10px; font-family: 'Montserrat';">
        <span style="font-size: 28px;">${product.emoji}</span> ${product.name}
      </h2>
      <p style="color: var(--text-light); margin-bottom: 20px;">Current: <strong>${product.current} ${product.unit}</strong></p>

      <form onsubmit="submitAdjustStock(event, ${productId})">
        <div style="margin-bottom: 15px;">
          <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--primary);">Type</label>
          <div style="display: flex; gap: 10px;">
            <label style="flex: 1;">
              <input type="radio" name="adjType" value="add" checked>
              <span>Add Stock</span>
            </label>
            <label style="flex: 1;">
              <input type="radio" name="adjType" value="remove">
              <span>Remove Stock</span>
            </label>
          </div>
        </div>

        <div style="margin-bottom: 15px;">
          <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--primary);">Quantity</label>
          <input type="number" name="quantity" min="1" required style="width: 100%; padding: 10px; border: 2px solid var(--border); border-radius: 6px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 20px;">
          <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--primary);">Reason</label>
          <textarea name="reason" style="width: 100%; padding: 10px; border: 2px solid var(--border); border-radius: 6px; font-family: 'Open Sans'; font-size: 13px; height: 80px;" placeholder="e.g., Fresh harvest, customer order..."></textarea>
        </div>

        <div style="display: flex; gap: 10px;">
          <button type="submit" class="btn-primary" style="flex: 1;">Update Stock</button>
          <button type="button" class="btn-secondary" style="flex: 1;" onclick="closeModal()">Cancel</button>
        </div>
      </form>
    </div>
  `;

  openModal(content);
}

// The `openRestockModal` function is called when the user clicks the "Restock" button for a specific product.
// It retrieves the product data based on the provided product ID and generates a modal form that allows the user to create a restock order for that product, specifying the quantity to restock,
// expected delivery date, and supplier information.
function openRestockModal(productId) {
  const product = stockData.find(p => p.id === productId);
  if (!product) return;

  // The generated HTML for the restock modal includes the product's emoji and name, the current stock level, and a form with fields to specify the quantity to restock, expected delivery date, and supplier information.
  const content = `
    <div style="padding: 25px;">
      <h2 style="color: var(--primary); margin-bottom: 10px; font-family: 'Montserrat';">
        <span style="font-size: 28px;">${product.emoji}</span> Restock ${product.name}
      </h2>
      <p style="color: var(--text-light); margin-bottom: 20px;">Current: <strong>${product.current} ${product.unit}</strong> | Target: <strong>${product.threshold} ${product.unit}</strong></p>

      <form onsubmit="submitRestock(event, ${productId})">
        <div style="margin-bottom: 15px;">
          <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--primary);">Quantity</label>
          <input type="number" name="restockQty" min="1" value="${Math.max(product.threshold - product.current, 0)}" required style="width: 100%; padding: 10px; border: 2px solid var(--border); border-radius: 6px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 15px;">
          <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--primary);">Expected Delivery</label>
          <input type="date" name="deliveryDate" required style="width: 100%; padding: 10px; border: 2px solid var(--border); border-radius: 6px; font-size: 14px;">
        </div>

        <div style="margin-bottom: 15px;">
          <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--primary);">Supplier</label>
          <input type="text" name="supplier" placeholder="Enter supplier name" style="width: 100%; padding: 10px; border: 2px solid var(--border); border-radius: 6px; font-size: 14px;">
        </div>

        <div style="display: flex; gap: 10px;">
          <button type="submit" class="btn-primary" style="flex: 1;">Create Order</button>
          <button type="button" class="btn-secondary" style="flex: 1;" onclick="closeModal()">Cancel</button>
        </div>
      </form>
    </div>
  `;

  openModal(content);
}

// The `submitAdjustStock` function is called when the user submits the adjust stock form in the modal. It retrieves the adjustment type (add or remove), quantity, and reason from the form,
// updates the stock levels for the specified product accordingly, and then updates the UI to reflect the changes, including showing a notification, closing the modal, and refreshing the stock table and analytics sections.
function submitAdjustStock(event, productId) {
  event.preventDefault();
  const form = event.target;
  const adjType = form.querySelector('input[name="adjType"]:checked').value;
  const quantity = parseInt(form.querySelector('input[name="quantity"]').value);

  // The function first finds the product in the `stockData` array based on the provided product ID. 
  // If the product is found, it calculates the new stock level by either adding or removing the specified quantity, ensuring that the stock level does not go below zero.
  const product = stockData.find(p => p.id === productId);
  if (product) {
    const oldStock = product.current;
    product.current = adjType === 'add' ? product.current + quantity : product.current - quantity;
    product.current = Math.max(0, product.current);
    product.lastUpdated = 'just now';

    // After updating the stock level, the function checks the new stock level against the product's threshold to determine the new stock status (ok, low, or critical) and updates it accordingly.
    if (product.current === 0) {
      product.status = 'critical';
    } else if (product.current <= product.threshold) {
      product.status = 'low';
    } else {
      product.status = 'ok';
    }

    // Finally, the function shows a notification to the user indicating whether the stock was increased or decreased, closes the modal, 
    // and calls functions to refresh the stock table and analytics sections to reflect the updated stock levels and statuses.
    showNotification(`Stock ${adjType === 'add' ? 'increased' : 'decreased'} for ${product.name}`, 'success');
    closeModal();
    populateStockTable();
    updateStatsBar();
    populateLowStockList();
    populateStatusDistribution();
    populateAlerts();
  }
}

// The `submitRestock` function is called when the user submits the restock form in the modal. It retrieves the quantity to restock, expected delivery date, and supplier information from the form,
// and then shows a notification to the user indicating that a restock order has been created with the specified details. The function also closes the modal after submission.
function submitRestock(event, productId) {
  event.preventDefault();
  const form = event.target;
  const quantity = form.querySelector('input[name="restockQty"]').value;
  const deliveryDate = form.querySelector('input[name="deliveryDate"]').value;
  const supplier = form.querySelector('input[name="supplier"]').value;

  // In a real application, this function would also send the restock order details to a backend server or API to create the order in the system.
  showNotification(`Restock order created for ${quantity} units. Delivery: ${deliveryDate}`, 'success');
  closeModal();
}

// The `openBulkUpdateModal` function is called when the user clicks the "Bulk Update" button. 
// It checks if any items are selected for bulk updating, and if so, generates a modal form that allows the user to choose a bulk action (add quantity, set quantity, or update threshold) and specify the value for that action.
function openBulkUpdateModal() {
  if (selectedItems.size === 0) {
    showNotification('Please select items to bulk update', 'warning');
    return;
  }

  // The generated HTML for the bulk update modal includes a header indicating that it's a bulk update, a message showing how many items are selected, and a form with options to choose the bulk action and specify the value for that action.
  const content = `
    <div style="padding: 25px;">
      <h2 style="color: var(--primary); margin-bottom: 10px; font-family: 'Montserrat';">Bulk Update</h2>
      <p style="color: var(--text-light); margin-bottom: 20px;">Updating <strong>${selectedItems.size}</strong> item(s)</p>

      <form onsubmit="submitBulkUpdate(event)">
        <div style="margin-bottom: 15px;">
          <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--primary);">Action</label>
          <select name="bulkAction" required style="width: 100%; padding: 10px; border: 2px solid var(--border); border-radius: 6px;">
            <option value="">Select action...</option>
            <option value="add">Add Quantity</option>
            <option value="set">Set Quantity</option>
            <option value="threshold">Update Threshold</option>
          </select>
        </div>

        <div style="margin-bottom: 15px;">
          <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--primary);">Value</label>
          <input type="number" name="bulkValue" min="0" required style="width: 100%; padding: 10px; border: 2px solid var(--border); border-radius: 6px;">
        </div>

        <div style="display: flex; gap: 10px;">
          <button type="submit" class="btn-primary" style="flex: 1;">Apply</button>
          <button type="button" class="btn-secondary" style="flex: 1;" onclick="closeModal()">Cancel</button>
        </div>
      </form>
    </div>
  `;

  openModal(content);
}

// The `submitBulkUpdate` function is called when the user submits the bulk update form in the modal. It retrieves the selected bulk action and value from the form,
// and then iterates over the selected items to apply the specified bulk action (adding quantity, setting quantity, or updating threshold) to each of the selected products.
function submitBulkUpdate(event) {
  event.preventDefault();
  const form = event.target;
  const action = form.querySelector('select[name="bulkAction"]').value;
  const value = parseInt(form.querySelector('input[name="bulkValue"]').value);

  // The function iterates over the `selectedItems` set, which contains the IDs of the products that were selected for bulk updating. 
  // For each selected product, it finds the corresponding product in the `stockData` array and applies the specified bulk action:
  // - If the action is "add", it adds the specified value to the current stock level of the product.
  // - If the action is "set", it sets the current stock level of the product to the specified value.
  // - If the action is "threshold", it updates the threshold value for the product to the specified value.
  selectedItems.forEach(id => {
    const product = stockData.find(p => p.id === id);
    if (product) {
      if (action === 'add') {
        product.current += value;
      } else if (action === 'set') {
        product.current = value;
      } else if (action === 'threshold') {
        product.threshold = value;
      }

      if (product.current === 0) {
        product.status = 'critical';
      } else if (product.current <= product.threshold) {
        product.status = 'low';
      } else {
        product.status = 'ok';
      }
    }
  });

  // After applying the bulk updates to all selected products, the function shows a notification to the user indicating that the bulk update was completed successfully, closes the modal, 
  // and refreshes the stock table and analytics sections to reflect the changes.
  selectedItems.clear();
  showNotification('Bulk update completed', 'success');
  closeModal();
  populateStockTable();
  updateStatsBar();
  populateLowStockList();
  populateStatusDistribution();
  populateAlerts();
}

// The `exportStockCSV` function generates a CSV string representation of the current stock data, including the product name, category, current stock level, threshold, status, and last updated time for each product.
function exportStockCSV() {
  let csv = 'Product,Category,Current Stock,Threshold,Status,Last Updated\n';

  // The function iterates over the `stockData` array and appends a line to the CSV string for each product, with the relevant data fields separated by commas.
  stockData.forEach(item => {
    csv += `${item.name},${item.category},${item.current},${item.threshold},${item.status},${item.lastUpdated}\n`;
  });

  // After generating the CSV string, the function calls the `downloadFile` utility function to trigger a download of the CSV file with the name "stock-report.csv" and the appropriate MIME type for CSV files.
  downloadFile(csv, 'stock-report.csv', 'text/csv');
}

// The `exportStockPDF` function is a placeholder for functionality that would allow the user to export the stock data as a PDF file. 
// In a real application, this function would likely use a library such as jsPDF to generate a PDF document based on the current stock data and trigger a download of that PDF file.
function exportStockPDF() {
  showNotification('PDF export would be implemented here', 'info');
}


// The `printStock` function is a simple utility that triggers the browser's print dialog, allowing the user to print the current view of the stock dashboard.
function printStock() {
  window.print();
}

// The `downloadFile` function is a utility function that creates a downloadable file from the provided content, filename, and MIME type. 
// It uses the Blob API to create a blob from the content, generates a URL for that blob, and then creates a temporary anchor element to trigger the download of the file when clicked.
function downloadFile(content, filename, type) {
  const blob = new Blob([content], { type: type });
  const url = window.URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = filename;
  link.click();
}

// The `updateThresholds` function is a placeholder for functionality that would allow the user to update the low and critical stock thresholds for products. 
// In a real application, this function would likely update the threshold values in the `stockData` array and refresh the stock table and analytics sections to reflect any changes in stock statuses based on the new thresholds.
function updateThresholds() {
  const lowThreshold = parseInt(document.getElementById('lowThreshold').value);
  const criticalThreshold = parseInt(document.getElementById('criticalThreshold').value);

  showNotification('Thresholds updated successfully', 'success');
}


// The `openModal` function is a utility function that displays a modal dialog with the provided content.
// It sets the inner HTML of the modal content area to the provided content and adds an "active" class to the modal overlay to make it visible.
function openModal(content) {
  const overlay = document.getElementById('modalOverlay');
  document.getElementById('modalContent').innerHTML = content;
  overlay.classList.add('active');
}

// The `closeModal` function is a utility function that hides the modal dialog by removing the "active" class from the modal overlay, effectively hiding the modal from view.
function closeModal() {
  document.getElementById('modalOverlay').classList.remove('active');
}

// This event listener is added to the entire document to listen for click events. If the user clicks on the modal overlay (the area outside the modal content), the `closeModal` function is called to close the modal dialog.
document.addEventListener('click', function(e) {
  const overlay = document.getElementById('modalOverlay');
  if (e.target === overlay) {
    closeModal();
  }
});

// The `showNotification` function is a simple utility that displays a notification message to the user. 
// It logs the message to the console with a specified type (e.g., 'info', 'success', 'warning', 'danger') and also shows an alert dialog with the message.
function showNotification(message, type = 'info') {
  console.log(`${type.toUpperCase()}: ${message}`);
  alert(message);
}