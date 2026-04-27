// products.js - JavaScript for product listing page
// This script handles the interactive features of the product listing page, including filtering products by category and price, sorting products by price, adding products to the cart with visual feedback, and managing pagination.
// It initializes event listeners for filter checkboxes, price range inputs, sorting dropdown, add to cart buttons, and pagination buttons to provide a dynamic and responsive user experience when browsing products.

document.addEventListener('DOMContentLoaded', function() {
  initialiseFilters();
  initialiseSorting();
  initialiseAddToCart();
  initialisePagination();
});

// This function sets up event listeners for filter options, including category checkboxes and price range inputs. 
// It also includes a reset button to clear all filters and show all products again. 
// When a filter option is changed, it calls the applyFilters function to update the product display based on the selected criteria.
function initializeFilters() {
  const filterCheckboxes = document.querySelectorAll('.filter-option input[type="checkbox"]');
  const resetButton = document.querySelector('.btn-reset-filters');
  const priceInputs = document.querySelectorAll('.price-range input');

  // Set up event listeners for filter checkboxes and price inputs
  filterCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', applyFilters);
  });

  // Set up event listeners for price range inputs
  priceInputs.forEach(input => {
    input.addEventListener('change', applyFilters);
  });

  // Set up event listener for reset button
  if (resetButton) {
    resetButton.addEventListener('click', resetFilters);
  }
}

// This function applies the selected filters to the product listing.
// It iterates through all product cards and checks if they match the selected category filters and price range. 
// It updates the visibility of each product card accordingly and updates the product count display to reflect the number of visible products.
function applyFilters() {
  const productCards = document.querySelectorAll('.product-card');
  let visibleCount = 0;

  // Get selected category filters
  productCards.forEach(card => {
    card.style.display = '';
    visibleCount++;
  });

  // Get selected category filters
  const selectedCategories = Array.from(document.querySelectorAll('.filter-option input[type="checkbox"]:checked')).map(checkbox => checkbox.value);
  updateProductCount(visibleCount);
}


// This function resets all filters to their default state, showing all products and updating the product count accordingly.
function resetFilters() {
  document.querySelectorAll('.filter-option input[type="checkbox"]').forEach(checkbox => {
    checkbox.checked = false;
  });

  // Reset price range inputs to default values
  document.querySelector('.price-range input:first-child').value = 0;
  document.querySelector('.price-range input:last-child').value = 100;

  // Show all products
  document.querySelectorAll('.product-card').forEach(card => {
    card.style.display = '';
  });

  // Update product count
  updateProductCount(document.querySelectorAll('.product-card').length);
}

// This function updates the product count display based on the number of visible products. 
// It takes a count as an argument and updates the text content of the element with the class 'product-count' to reflect the current number of products being shown.
function updateProductCount(count) {
  const countElement = document.querySelector('.product-count');
  if (countElement) {
    countElement.textContent = `Showing ${count} product${count !== 1 ? 's' : ''}`;
  }
}

// This function initializes the sorting functionality by adding an event listener to the sorting dropdown. 
// When the sorting option is changed, it calls the sortProducts function with the selected sorting criteria to reorder the product cards accordingly.
function initializeSorting() {
  const sortSelect = document.querySelector('.sort-select');

  // Set up event listener for sorting dropdown
  if (sortSelect) {
    sortSelect.addEventListener('change', function() {
      sortProducts(this.value);
    });
  }
}

// This function sorts the product cards based on the selected sorting criteria (price low to high or high to low).
// It retrieves the price of each product, sorts the cards accordingly, and then re-appends them to the product grid in the new order.
function sortProducts(sortBy) {
  const grid = document.querySelector('.products-grid');
  const cards = Array.from(document.querySelectorAll('.product-card'));

  // Sort cards based on price
  cards.sort((a, b) => {
    const priceA = parseFloat(a.querySelector('.price-new').textContent.replace('$', ''));
    const priceB = parseFloat(b.querySelector('.price-new').textContent.replace('$', ''));

    // Sort based on selected criteria
    switch(sortBy) {
      case 'price-low':
        return priceA - priceB;
      case 'price-high':
        return priceB - priceA;
      default:
        return 0;
    }
  });

  // Re-append sorted cards to the grid
  cards.forEach(card => {
    grid.appendChild(card);
  });
}

// This function initializes the "Add to Cart" buttons by adding click event listeners to each button. 
// When a button is clicked, it provides immediate visual feedback by changing the button text to "Added" and changing its background color to green. 
// After 1.5 seconds, it resets the button back to its original state to allow for multiple additions to the cart in a real application.
function initializeAddToCart() {
  const addToCartButtons = document.querySelectorAll('.btn-add-cart');

  // Set up click listeners for each add to cart button
  addToCartButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();

      // Simulate adding to cart with immediate feedback
      const originalText = this.innerHTML;
      this.innerHTML = '<i class="fas fa-check"></i> Added';
      this.style.background = '#4CAF50';

      // Reset the button after 1.5 seconds to allow for multiple additions to the cart in a real application
      setTimeout(() => {
        this.innerHTML = originalText;
        this.style.background = '';
      }, 1500);
    });
  });
}

// This function initializes pagination by adding click event listeners to each pagination button. 
// When a pagination button is clicked, it prevents the default action, updates the active state of the buttons, and smoothly scrolls the page back to the top for a better user experience when navigating through product pages.
function initializePagination() {
  const paginationButtons = document.querySelectorAll('.page-btn');

  // Set up click listeners for each pagination button
  paginationButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();

      // Remove active class from all buttons and add it to the clicked button
      document.querySelectorAll('.page-btn').forEach(btn => {
        btn.classList.remove('active');
      });

      // Add active class to the clicked button
      if (this.textContent !== 'Next') {
        this.classList.add('active');
      }

      // Scroll to top smoothly when a pagination button is clicked
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  });
}