// the main JavaScript file for the checkout page, responsible for wiring up all interactivity and validation logic.
// Wait for the DOM to be fully loaded before initializing the checkout logic.
// This ensures that all elements we need to interact with are present in the DOM.

document.addEventListener('DOMContentLoaded', function() {
  initializeCheckout();
});

//  this function initializes all the interactive components of the checkout page, including delivery options, payment method selection, promo code handling, form validation, and the final order submission process.
// It serves as the main entry point for setting up the checkout page's behavior.
function initializeCheckout() {
  // Delivery radio buttons 
  initialiseDeliveryOptions();
  // Payment method radios 
  initializePaymentOptions();
  // Promo code input + "Apply" button.
  initialisePromoCode();
  // Per-field validation on blur/input.
  initialiseFormValidation();
  // Final Place Order button 
  initialisePlaceOrder();
}

// function that wires up the delivery options radios, allowing the user to select a shipping method and see the corresponding cost reflected in the order summary.
// It also visually highlights the selected option and triggers a summary update whenever the choice changes.
function initialiseDeliveryOptions() {
  // All delivery method radio buttons.
  const deliveryRadios = document.querySelectorAll('input[name="delivery"]');

  // Wire up a change handler on each radio button.
  deliveryRadios.forEach(radio => {
    radio.addEventListener('change', function() {
      // Clear the .gf-selected highlight from every option first.
      document.querySelectorAll('.gf-delivery-option').forEach(el => {
        el.classList.remove('gf-selected');
      });
      // Mark the parent <label> of the chosen radio as selected.
      this.closest('.gf-delivery-option').classList.add('gf-selected');
      // Shipping cost changed → recompute subtotal/tax/total.
      updateOrderSummary();
    });
  });
}

// This function sets up the payment method selection logic, allowing users to choose between different payment options (e.g., card, PayPal) and conditionally display the card details form when the card option is selected.
// It also visually highlights the selected payment method for better user experience.
function initialisePaymentOptions() {
  // All payment method radios.
  const paymentRadios = document.querySelectorAll('input[name="payment"]');
  // Card-details sub-form (hidden by default).
  const cardForm = document.getElementById('gf-cardForm');

  // Wire change handler on every payment radio.
  paymentRadios.forEach(radio => {
    radio.addEventListener('change', function() {
      // Clear the highlight on all payment options.
      document.querySelectorAll('.gf-payment-option').forEach(el => {
        el.classList.remove('gf-selected');
      });
      // Highlight the chosen one.
      this.closest('.gf-payment-option').classList.add('gf-selected');

      // Show the card form only when the user picks card.
      if (this.value === 'card') {
        cardForm.style.display = 'flex';
      } else {
        cardForm.style.display = 'none';
      }
    });
  });
}

// This function initializes the form validation logic for all required input fields in the checkout form. 
// It sets up event listeners to validate fields on blur (when the user leaves the field) and on input (to re-validate if the user starts correcting an error). 
// The validation rules include checks for empty fields, email format, ZIP code format, card number length, and CVC length. When a field fails validation, it is visually marked with an error state and an appropriate error message is displayed next to it.
function initialiseFormValidation() {
  // Reference to the outer checkout form.
  const form = document.getElementById('gf-checkoutForm');
  // All inputs marked as required.
  const inputs = form.querySelectorAll('input[required]');

  inputs.forEach(input => {
    // Validate when the user leaves the field.
    input.addEventListener('blur', function() {
      validateField(this);
    });

    // Live-revalidate if the field was previously in an error state.
    input.addEventListener('input', function() {
      if (this.classList.contains('gf-error')) {
        validateField(this);
      }
    });
  });
}

// This function performs validation on a single input field based on its type and ID. It checks for required fields, validates email format, ensures ZIP codes are 5 digits, card numbers are 16 digits, and CVC codes are 3 digits. 
// If the field is invalid, it adds an error class to the field and displays an appropriate error message in the associated error message slot. 
// If the field is valid, it clears any error state and hides the error message.
function validateField(field) {
  // Trimmed user value.
  const value = field.value.trim();
  // Raw input type (email / text / number / ...).
  const type = field.type;
  // DOM id — used to pick the right regex below.
  const id = field.id;
  // Result flag, optimistically true.
  let isValid = true;
  // Message shown to the user when invalid.
  let errorMsg = '';

  // the validation rules are applied in a specific order to ensure that the most critical issues are addressed first. 
  // The function checks for empty fields before applying more specific format validations, providing clear and immediate feedback to the user about what needs to be corrected.
  if (!value) {
    isValid = false;
    errorMsg = 'This field is required';
  // Rule 2: basic email format check for email inputs.
  } else if (type === 'email') {
    isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    errorMsg = 'Please enter a valid email';
  // Rule 3: ZIP must be exactly 5 digits.
  } else if (id === 'zip') {
    isValid = /^[0-9]{5}$/.test(value);
    errorMsg = 'ZIP code must be 5 digits';
  // Rule 4: card number must be exactly 16 digits.
  } else if (id === 'cardNumber') {
    isValid = /^[0-9]{16}$/.test(value);
    errorMsg = 'Card number must be 16 digits';
  // Rule 5: CVC must be exactly 3 digits.
  } else if (id === 'cardCVC') {
    isValid = /^[0-9]{3}$/.test(value);
    errorMsg = 'CVC must be 3 digits';
  }

  if (!isValid) {
    // Mark the field visually as errored.
    field.classList.add('gf-error');
    // Find the matching inline error slot and display the message.
    const errorDiv = document.getElementById(field.id + 'Error') || document.querySelector(`#${id}Error`);
    if (errorDiv) {
      errorDiv.textContent = errorMsg;
      errorDiv.classList.add('gf-show');
    }
  } else {
    // Clear any previous error state on the field.
    field.classList.remove('gf-error');
    // Hide the associated error message if one was showing.
    const errorDiv = document.querySelector(`.gf-error-message[data-field="${id}"]`);
    if (errorDiv) {
      errorDiv.classList.remove('gf-show');
    }
  }

  return isValid;
}

// This function sets up the promo code application logic. 
// It listens for clicks on the "Apply" button, validates the entered promo code against a predefined list of valid codes, and if valid, applies the corresponding discount to the order summary. 
// It also provides user feedback by displaying success or error messages based on whether the promo code was accepted or rejected.
function initializePromoCode() {
  // Button that triggers promo validation.
  const applyBtn = document.getElementById('gf-applyPromo');
  // Text field where the user types the code.
  const promoInput = document.getElementById('gf-promoCode');
  // Small message slot that tells the user success/error.
  const promoMessage = document.getElementById('gf-promoMessage');

  if (applyBtn) {
    applyBtn.addEventListener('click', function(e) {
      // Don't let the button submit the surrounding form.
      e.preventDefault();

      // Normalise code trim uppercase for case-insensitive match.
      const code = promoInput.value.trim().toUpperCase();

      // Empty input show an inline error and bail out.
      if (!code) {
        showPromoMessage('Please enter a promo code', 'gf-error');
        return;
      }

      // Mock promo codes replace with a real API call to the backend.
      const validCodes = {
        'WELCOME10': { discount: 0.10, label: '10% off' },
        'FRESH5':    { discount: 0.05, label: '5% off' },
        'SAVE15':    { discount: 0.15, label: '15% off' }
      };

      if (validCodes[code]) {
        // Pull out the matching discount entry.
        const { discount, label } = validCodes[code];
        // Apply the discount to the running summary.
        applyDiscount(discount);
        // Tell the user it worked.
        showPromoMessage(`✓ ${label} applied!`, 'gf-success');
        // Clear the input so they can't re-submit the same code.
        promoInput.value = '';
        // Reveal the discount applied row in the summary box.
        document.querySelector('.gf-promo-discount').style.display = 'flex';
      } else {
        // Unknown code show an error message.
        showPromoMessage('Invalid promo code', 'gf-error');
      }
    });
  }
}

// Utility function to show a message in the promo code area, with styling based on the type (error/success).
// @param {string} text - The message to display to the user.
// @param {string} type - The type of message, e.g., 'gf-error' or 'gf-success', which determines the styling.
function showPromoMessage(text, type) {
  const msg = document.getElementById('gf-promoMessage');
  msg.textContent = text;
  msg.className = `gf-promo-message ${type}`;
}

// This function applies a discount to the order summary based on the provided percentage.
// @param {number} discountPercent - The discount percentage to apply. For example, 0.10 for a 10% discount.
function applyDiscount(discountPercent) {
  // Read the current subtotal out of the DOM 
  const subtotal = parseFloat(document.querySelector('[data-testid="subtotal"]').textContent.replace('$', ''));
  // Absolute discount in dollars.
  const discountAmount = subtotal * discountPercent;

  // Show the discount with a leading minus sign.
  document.getElementById('gf-discount').textContent = `-$${discountAmount.toFixed(2)}`;
  // Recompute dependent totals (shipping/tax/total).
  updateOrderSummary();
}

// this function recalculates the order summary totals (shipping cost, tax, and final total) whenever there is a change in delivery method or when a promo code is applied. 
// It takes into account the current subtotal, any applied discounts, the selected shipping cost, and calculates tax based on the discounted subtotal. The updated totals are then reflected in the order summary section of the checkout page.
function updateOrderSummary() {
  // Hard-coded demo subtotal — swap with the real cart subtotal.
  const subtotal = 12.47;
  const deliveryRadio = document.querySelector('input[name="delivery"]:checked');
  const shippingCost = parseFloat(deliveryRadio.dataset.cost || 0);
  const discountText = document.getElementById('gf-discount').textContent;
  const discountAmount = discountText !== '$0.00' ? parseFloat(discountText.replace('-$', '')) : 0;
  const subtotalAfterDiscount = subtotal - discountAmount;
  const tax = (subtotalAfterDiscount * 0.08).toFixed(2);
  const total = (subtotalAfterDiscount + shippingCost + parseFloat(tax)).toFixed(2);

  document.getElementById('gf-shippingCost').textContent = shippingCost === 0 ? 'FREE' : `$${shippingCost.toFixed(2)}`;
  document.getElementById('gf-tax').textContent = `$${tax}`;
  document.getElementById('gf-total').textContent = `$${total}`;
  document.getElementById('gf-loyaltyPoints').textContent = Math.floor(total);
}

// script that handles the final "Place Order" button logic, including form validation, displaying a loading state, sending the order data to the server, and providing user feedback based on the success or failure of the order submission.
// When the user clicks the "Place Order" button, this function first validates all required fields in the form. If any field fails validation, it alerts the user to fix the errors. 
// If all fields are valid, it changes the button to a loading state and sends the order data to a backend endpoint (e.g., 'process_order.php') using a POST request with JSON payload.
// Depending on the response from the server, it either shows a success message and redirects to an order confirmation page or resets the button state and alerts the user of an error. 
function initializePlaceOrder() {
  const form = document.getElementById('gf-checkoutForm');
  const placeOrderBtn = document.querySelector('.gf-btn-place-order');

  //  Only wire up the handler if both the form and button are present in the DOM.
  if (form && placeOrderBtn) {
    form.addEventListener('submit', async function(e) {
      e.preventDefault();
      const requiredFields = form.querySelectorAll('input[required]');
      let allValid = true;

      // Before submitting, validate every required field. If any field is invalid, set allValid to false.
      requiredFields.forEach(field => {
        if (!validateField(field)) {
          allValid = false;
        }
      });

      // If any field failed validation, alert the user and stop the submission process.
      if (!allValid) {
        alert('Please fix the errors before proceeding');
        return;
      }
      // If we got here, all fields are valid. Show a loading state on the button while we process the order.

      const originalHTML = placeOrderBtn.innerHTML;
      placeOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
      placeOrderBtn.disabled = true;

      // Mock API call to the backend to process the order. Replace the URL and payload with your actual order processing logic.
      try {
        const formData = new FormData(form);
        const orderData = Object.fromEntries(formData);

        // Send the order data to the server using fetch. The server should return a success response if the order was processed correctly.
        const response = await fetch('process_order.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(orderData)
        });

        // Check the response status to determine if the order was processed successfully. If so, show a success message and redirect to the confirmation page. If not, throw an error to be caught in the catch block.
        if (response.ok) {
          placeOrderBtn.innerHTML = '<i class="fas fa-check"></i> Order Placed!';
          placeOrderBtn.style.background = 'var(--gf-success)';
          setTimeout(() => {
            window.location.href = 'order_confirmation.html';
          }, 1500);
        } else {
          throw new Error('Order processing failed');
        }
        // In a real implementation, you would also want to handle specific error messages returned from the server and display them to the user instead of a generic alert.
      } catch (error) {
        console.error('Order error:', error);
        placeOrderBtn.innerHTML = originalHTML;
        placeOrderBtn.disabled = false;
        alert('An error occurred. Please try again.');
      }
    });
  }
}