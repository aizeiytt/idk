// This JavaScript file handles the interactive functionality of the delivery tracking page, including FAQ toggling, driver communication, delivery option selection, and real-time order status updates.
// It enhances the user experience by providing dynamic interactions and real-time feedback on the delivery process.
// The script initializes event listeners for FAQ items, delivery option cards, and simulates real-time tracking by polling the backend for order status updates every 30 seconds.


// this function simulates tracking an order. When a user clicks the "Track Order" button, it shows an alert with the order ID and indicates that they will be redirected to the tracking page.
// In a real application, this would redirect the user to a dedicated tracking page with more details about their order status.
function trackOrder(orderId) {
  alert(`Tracking order: ${orderId}`);
  // In a real application, this would redirect to the tracking page
  // window.location.href = `tracking.html?order=${orderId}`;
}


// Initialize the delivery tracking page by setting up event listeners for FAQ toggling, driver communication, and delivery option selection.
document.addEventListener('DOMContentLoaded', function() {
  initializeOptions();
  // Additional initialization can be added here if needed
});

// Toggle FAQ answer visibility when a question is clicked. It ensures that only one FAQ answer is visible at a time by collapsing any previously opened answers.
function toggleFAQ(element) {
  const answer = element.nextElementSibling;
  const allItems = document.querySelectorAll('.faq-item');

}


document.addEventListener('DOMContentLoaded', function() {
  initializeOptions();
});

// Toggle FAQ answer visibility when a question is clicked. It ensures that only one FAQ answer is visible at a time by collapsing any previously opened answers.
// When a user clicks on a FAQ question, this function checks all FAQ items and collapses any that are not the clicked question. It then toggles the visibility of the clicked question's answer.
function toggleFAQ(element) {
  const answer = element.nextElementSibling;
  const allItems = document.querySelectorAll('.faq-item');

  // Collapse all other FAQ answers
  allItems.forEach(item => {
    if (item.querySelector('.faq-question') !== element) {
      item.querySelector('.faq-question').classList.remove('active');
      item.querySelector('.faq-answer').classList.remove('active');
    }
  });

  // Toggle the clicked FAQ answer
  element.classList.toggle('active');
  answer.classList.toggle('active');
}

// this function simulates calling the driver. When a user clicks the "Call Driver" button, it shows an alert indicating that a phone call would be initiated in a real application.
// In a real application, this would use the "tel:" protocol to open the phone dialer with the driver's number.
function callDriver() {
  alert('📞 Calling driver... This would open a phone call in a real app.');
  // window.location.href = 'tel:+15551234567';
}

// this function simulates messaging the driver. When a user clicks the "Message Driver" button, it shows an alert indicating that a messaging interface would be opened in a real application.
// In a real application, this could open a chat modal or redirect to a messaging service.
function messageDriver() {
  alert('💬 Opening messaging interface...');
  // Could open a chat modal or redirect to messaging service
}

// this function initializes the delivery option cards. When a user clicks on a delivery option card, it visually indicates that the card is selected by adding a "selected" class and removing it from any previously selected card.
// It enhances the user experience by allowing users to easily select their preferred delivery option and see which option is currently active.
function initializeOptions() {
  const optionCards = document.querySelectorAll('.option-card');

  optionCards.forEach(card => {
    card.addEventListener('click', function() {
      optionCards.forEach(c => c.classList.remove('selected'));
      this.classList.add('selected');
    });
  });
}

// the function initializes real-time tracking for an order by setting up a polling mechanism that fetches the latest order status from the backend every 30 seconds.
// It updates the delivery timeline and order information on the page based on the fetched status, providing users with up-to-date information about their delivery progress.
function initializeRealTimeTracking(orderId) {
  setInterval(function() {
    fetch(`get_order_status.php?id=${orderId}`)
      .then(response => response.json())
      .then(data => {
        updateTimeline(data.status);
        updateOrderInfo(data);
      })
      // In case of an error while fetching the order status, it logs the error to the console for debugging purposes.
      .catch(error => console.error('Error fetching status:', error));
  }, 30000); // Poll every 30 seconds
}

// the function updates the delivery timeline based on the current order status. 
// It maps the order status to a predefined set of statuses (pending, processing, shipped, delivered) and updates the visual state of the timeline steps accordingly.
// It adds the "completed" class to steps that are before the current status and the "active" class to the step that matches the current status, while removing these classes from future steps.
function updateTimeline(status) {
  const steps = document.querySelectorAll('.timeline-step');
  const statusMap = ['pending', 'processing', 'shipped', 'delivered'];
  const currentIndex = statusMap.indexOf(status);

  // the function iterates through each step in the delivery timeline and updates its visual state based on its position relative to the current order status.
  steps.forEach((step, index) => {
    if (index < currentIndex) {
      step.classList.add('completed');
      step.classList.remove('active');
    } else if (index === currentIndex) {
      step.classList.add('active');
      step.classList.remove('completed');
    } else {
      step.classList.remove('completed', 'active');
    }
  });
}

// the function updates the order information display based on the fetched data.
// It finds the appropriate status element and updates its text content with the new status label.
function updateOrderInfo(data) {
  const statusElement = document.querySelector('.status-shipped, .status-pending, .status-delivered');
  if (statusElement) {
    statusElement.textContent = data.status_label;
  }
}