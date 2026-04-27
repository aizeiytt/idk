// the loyalty.js file contains all the JavaScript functionality for the loyalty program page, including FAQ toggling and redeem button interactions.
// It initializes event listeners for FAQ items to allow users to expand and collapse answers, and for redeem buttons to provide immediate feedback when a reward is redeemed. 
// The script also includes functionality for upgrade buttons that inform users about how to unlock higher loyalty tiers.

document.addEventListener('DOMContentLoaded', function() {
  initializeFAQ();
  initializeRedeemButtons();
});

// Toggle FAQ answer visibility when a question is clicked. It ensures that only one FAQ answer is visible at a time by collapsing any previously opened answers.
// When a user clicks on a FAQ question, this function checks all FAQ items and collapses any that are not the clicked question. It then toggles the visibility of the clicked question's answer.
function initializeFAQ() {
  const faqItems = document.querySelectorAll('.faq-item');

  // Set up click listeners for each FAQ question
  faqItems.forEach(item => {
    const question = item.querySelector('.faq-question');

    question.addEventListener('click', function() {
      const answer = item.querySelector('.faq-answer');

      // Collapse all other FAQ answers
      faqItems.forEach(otherItem => {
        if (otherItem !== item) {
          otherItem.querySelector('.faq-question').classList.remove('active');
          otherItem.querySelector('.faq-answer').classList.remove('active');
        }
      });

      // Toggle current item
      question.classList.toggle('active');
      answer.classList.toggle('active');
    });
  });
}

// this function simulates redeeming a reward.
// When a user clicks the "Redeem" button, it changes the button text to "✓ Redeemed", disables the button, and then resets it back to its original state after 2 seconds to allow for multiple redemptions in a real application. 
function initializeRedeemButtons() {
  const redeemButtons = document.querySelectorAll('.btn-redeem');

  // Set up click listeners for each redeem button
  redeemButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();

      // Simulate redeeming the reward with immediate feedback
      const originalText = this.textContent;
      this.textContent = '✓ Redeemed';
      this.style.background = '#4CAF50';
      this.disabled = true;

      // Reset the button after 2 seconds to allow for multiple redemptions in a real application
      setTimeout(() => {
        this.textContent = originalText;
        this.style.background = '';
        this.disabled = false;
      }, 2000);
    });
  });
}

// this function simulates the upgrade button interactions. When a user clicks an "Upgrade" button, it shows an alert with information about how to unlock the next loyalty tier.
document.addEventListener('DOMContentLoaded', function() {
  const upgradeButtons = document.querySelectorAll('.btn-upgrade');

  // Set up click listeners for each upgrade button
  upgradeButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();

      // Get the tier name from the closest .tier-card element to provide specific feedback about which tier can be unlocked.
      const tierName = this.closest('.tier-card').querySelector('.tier-header h3').textContent;
      alert(`To unlock ${tierName} tier, continue shopping and earn more points!`);
    });
  });
});