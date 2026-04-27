// the index JavaScript file contains two main functions: initializeCounters and initializeScrollAnimations. 
// The initializeCounters function sets up an IntersectionObserver to animate three hero stat numbers when they scroll into view for the first time. 
// The animateCounter function is a helper that animates a single counter from 0 to a target number over a fixed duration.
// The initializeScrollAnimations function observes feature cards and fades/slides them in the first time they appear in the viewport, enhancing the visual appeal of the page as users scroll through it.
document.addEventListener('DOMContentLoaded', function() {
  // Initialize the hero stat counters and scroll animations when the page loads.
  initializeCounters();
  // This function sets up the IntersectionObserver that animates the three hero stat numbers the first time they scroll into view. It targets the stat headings under .farm-stat and .stat, and animates them to their target values with a suffix (e.g., '+', '%').
  initializeScrollAnimations();
});


// Sets up the IntersectionObserver that animates the three hero stat numbers
// the first time they scroll into view.
function initializeCounters() {
  // Grab every stat heading under .farm-stat (or legacy .stat).
  const stats = document.querySelectorAll('.farm-stat h3, .stat h3');
  // Guard so the animation only runs once.
  let animated = false;

  // Fire when at least 50% of the element is visible.
  const observerOptions = { threshold: 0.5 };
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting && !animated) {
        // Three targets, matching the three visible stat boxes.
        animateCounter(stats[0], 500, '+');   // Happy Customers
        animateCounter(stats[1], 25,  '+');   // Local Farms
        animateCounter(stats[2], 100, '%');   // Organic
        animated = true;
      }
    });
  }, observerOptions);

  // Only observe if we actually found the first stat element.
  if (stats[0]) observer.observe(stats[0]);
}


// Animate a single counter from 0 → target over a fixed duration.
function animateCounter(element, target, suffix) {
  // Total animation time in ms.
  const duration = 1500;
  // Timestamp when the animation started.
  const start = Date.now();

  // Repaint the number every ~10ms until we hit the target.
  const timer = setInterval(() => {
    // How much time has passed.
    const elapsed = Date.now() - start;
    // Clamp progress between 0 and 1.
    const progress = Math.min(elapsed / duration, 1);
    // Current value scaled by progress.
    const current = Math.floor(progress * target);

    // Push the new number (with its suffix) back into the DOM.
    element.textContent = current + suffix;

    // Stop the timer once we've reached the target.
    if (progress === 1) clearInterval(timer);
  }, 10);
}


// Observes feature cards and fades/slides them in the first time they appear.
function initializeScrollAnimations() {
  // Grab every feature/why card on the page.
  const cards = document.querySelectorAll('.feature-box, .why-card, .how-card');

  // Start the animation slightly before the card fully enters the viewport.
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        // Fade in + slide to its resting position.
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
        // Unobserve so we don't run the animation again on re-entry.
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  // Set every card to the hidden-below state, then start observing.
  cards.forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(card);
  });
}
