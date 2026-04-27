// the dashboard.js file contains JavaScript functions to handle user interactions on the dashboard page, such as switching between tabs, tracking orders, and redeeming rewards. It also initializes the dashboard when the page loads.
// it assumes the HTML structure includes elements with classes like .menu-item for sidebar navigation and .tab-content for the different dashboard sections. 
// The functions use event listeners to respond to user clicks and make AJAX requests to the backend when redeeming rewards.

// Dashboard JavaScript for handling tab switching, order tracking, and reward redemption
document.addEventListener('DOMContentLoaded', function() {
  initializeDashboard();
  // Auto-activate tab from URL hash (e.g., ?rated=1#rate)
  const hash = window.location.hash.replace('#', '');
  if (hash && document.getElementById(hash)) {
    switchTab(hash);
  }
});

// Initialize dashboard by setting up event listeners for sidebar navigation
function initializeDashboard() {
  const sidebarItems = document.querySelectorAll('.menu-item');
  
  sidebarItems.forEach((item, index) => {
    item.addEventListener('click', function() {
      const tabName = this.textContent.trim().toLowerCase().split(' ')[0];
      switchTab(tabName === 'overview' ? 'overview' : tabName);
    });
  });
}

// this function handles switching between different tabs in the dashboard. 
// It hides all tab content sections and then shows the selected one based on the clicked menu item. 
// It also updates the active state of the menu items and scrolls to the top of the page for better user experience.
function switchTab(tabName) {
  // Hide all tab content
  document.querySelectorAll('.tab-content').forEach(tab => {
    tab.classList.remove('active');
  });

  // Mark all menu items inactive
  document.querySelectorAll('.menu-item').forEach(item => {
    item.classList.remove('active');
  });

  // Show selected tab content
  const selectedTab = document.getElementById(tabName);
  if (selectedTab) {
    selectedTab.classList.add('active');
  }

  // Mark menu item active
  document.querySelectorAll('.menu-item').forEach(item => {
    if (item.textContent.toLowerCase().includes(tabName)) {
      item.classList.add('active');
    }
  });

  // Scroll to top
  window.scrollTo(0, 0);
}

// this function simulates tracking an order. When a user clicks the "Track Order" button, it shows an alert with the order ID and indicates that they will be redirected to the tracking page.
// In a real application, this would redirect the user to a dedicated tracking page with more details about their order status.
function trackOrder(orderId) {
  alert(`Tracking order #${orderId}. Redirecting to tracking page...`);
  // window.location.href = `track_order.php?id=${orderId}`;
}

// this function handles redeeming rewards. When a user clicks the "Redeem" button for a reward, it first confirms the action with the user.
// If confirmed, it sends an AJAX POST request to the backend (redeem_reward.php) with the points and reward details. 
// The backend will process the redemption and return a JSON response indicating success or failure. Based on the response, it shows an alert to the user and reloads the page if the redemption was successful.
function redeemReward(points, reward) {
  if (confirm(`Redeem ${points} points for ${reward}?`)) {
    // this is a placeholder for the AJAX request to redeem the reward. #
    // In a real application, this would involve sending the redemption details to the server and updating the user's points balance accordingly.
    fetch('redeem_reward.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        points: points,
        reward: reward
      })
    })

    // Handle the response from the server
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert(`✓ ${reward} redeemed successfully!`);
        location.reload();
      } else {
        alert(`Error: ${data.message}`);
      }
    })

    // Handle any errors that occur during the fetch request
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred');
    });
  }
}