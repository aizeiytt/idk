// This JavaScript file is responsible for handling the interactive elements of the dashboard page, such as sidebar navigation, buttons, notifications, and settings form. 
// It uses event listeners to respond to user actions and manipulate the DOM accordingly.
// The script initializes the sidebar navigation to allow users to switch between different sections of the dashboard, 
// and sets up event listeners for various buttons to provide feedback and simulate functionality for editing products, deleting products, updating order status, and viewing order details. 
// It also includes a notification button that alerts users about new notifications and a settings form that simulates saving changes with visual feedback.

// The script is structured to ensure that all event listeners are set up once the DOM content is fully loaded, providing a responsive and interactive user experience on the dashboard page.
document.addEventListener('DOMContentLoaded', function() {
  initializeSidebarNavigation();
  initializeButtons();
});

// This function sets up the sidebar navigation by adding click event listeners to each navigation item. 
// When a navigation item is clicked, it prevents the default link behavior, removes the active class from all navigation items and sections, and then adds the active class to the clicked item and its corresponding section to display the relevant content.
function initializeSidebarNavigation() {
  const navItems = document.querySelectorAll('.nav-item');

  // Set up click listeners for each navigation item
  navItems.forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();

      // Get the target section ID from the clicked navigation item
      const sectionId = this.getAttribute('data-section');

      // Remove active class from all nav items and sections
      navItems.forEach(nav => nav.classList.remove('active'));
      document.querySelectorAll('.dashboard-section').forEach(section => {
        section.classList.remove('active');
      });

      // Add active class to clicked item and corresponding section
      this.classList.add('active');
      const targetSection = document.getElementById(sectionId);
      if (targetSection) {
        targetSection.classList.add('active');
      }
    });
  });
}

// This function initializes event listeners for various buttons on the dashboard, including edit, delete, update, and view buttons. 
// Each button simulates its respective functionality by displaying an alert message when clicked. 
// The delete button includes a confirmation prompt to prevent accidental deletions, while the other buttons simply provide feedback on the intended action.
function initializeButtons() {
  const editButtons = document.querySelectorAll('.btn-edit');
  const deleteButtons = document.querySelectorAll('.btn-delete');
  const updateButtons = document.querySelectorAll('.btn-update');
  const viewButtons = document.querySelectorAll('.btn-view');

  // Set up click listeners for each button type
  editButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      alert('Edit product functionality');
    });
  });

  // The delete button includes a confirmation prompt to prevent accidental deletions. If the user confirms, it simulates the deletion of a product by displaying an alert message.
  deleteButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      if (confirm('Are you sure you want to delete this product?')) {
        alert('Product deleted');
      }
    });
  });

  // The update and view buttons simulate their respective functionalities by displaying alert messages when clicked, providing feedback to the user about the intended action.
  updateButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      alert('Update order status functionality');
    });
  });

  // Set up click listeners for view buttons to simulate viewing order details
  viewButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      alert('View order details functionality');
    });
  });
}

// this event listner handles the sorting of products in the products grid based on the selected sorting option from a dropdown menu. 
// When the sorting option is changed, it calls the sortProducts function with the selected sorting criteria to reorder the product cards accordingly.
document.addEventListener('DOMContentLoaded', function() {
  const notificationBtn = document.querySelector('.btn-notification');

  // Set up click listener for notification button to simulate showing notifications
  if (notificationBtn) {
    notificationBtn.addEventListener('click', function() {
      alert('You have 3 new notifications');
    });
  }
});

//  This function initializes the settings form by adding click event listeners to the submit buttons. 
// When a submit button is clicked, it simulates saving the settings by changing the button text to "✓ Saved" and changing its background color to green. 
// After 2 seconds, it resets the button back to its original state to allow for multiple saves in a real application.
document.addEventListener('DOMContentLoaded', function() {
  const settingsForms = document.querySelectorAll('.settings-form');

  // Set up click listeners for each settings form submit button
  settingsForms.forEach(form => {
    const submitBtn = form.querySelector('button[type="submit"]');

    // Set up click listener for the submit button to simulate saving settings with immediate feedback
    if (submitBtn) {
      submitBtn.addEventListener('click', function(e) {
        e.preventDefault();

        // Simulate saving settings with immediate feedback
        const originalText = this.textContent;
        this.textContent = '✓ Saved';
        this.style.background = '#4CAF50';

        // Reset the button after 2 seconds to allow for multiple saves in a real application
        setTimeout(() => {
          this.textContent = originalText;
          this.style.background = '';
        }, 2000);
      });
    }
  });
});